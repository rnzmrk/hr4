<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        // Step 2: verify OTP if present
        if ($request->filled('otp')) {
            $request->validate([
                'otp' => ['required', 'digits:6'],
            ]);

            $session = $request->session();
            $expectedCode = $session->get('otp_code');
            $expiresAt = $session->get('otp_expires_at');
            $account = $session->get('otp_account');

            if (!$expectedCode || !$expiresAt || !$account) {
                throw ValidationException::withMessages([
                    'otp' => ['Verification code has expired. Please sign in again.'],
                ]);
            }

            if (now()->greaterThan($expiresAt)) {
                $session->forget(['otp_code', 'otp_expires_at', 'otp_account', 'otp_email']);

                throw ValidationException::withMessages([
                    'otp' => ['Verification code has expired. Please sign in again.'],
                ]);
            }

            if ($request->input('otp') !== $expectedCode) {
                throw ValidationException::withMessages([
                    'otp' => ['The verification code is incorrect.'],
                ]);
            }

            // OTP is valid: complete login using local User model
            $email = $session->get('otp_email');
            $name = trim((data_get($account, 'employee.first_name') ?? '') . ' ' . (data_get($account, 'employee.last_name') ?? ''));

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name ?: $email,
                    'password' => Hash::make(Str::random(32)),
                ]
            );

            $session->forget(['otp_code', 'otp_expires_at', 'otp_account', 'otp_email']);

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        // Step 1: validate credentials against external API and send OTP
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Normalize input to avoid issues with stray spaces/newlines
        $email = trim($request->input('email'));
        $password = trim($request->input('password'));

        $response = Http::withoutVerifying()->get('https://hr4.jetlougetravels-ph.com/api/accounts');

        if (!$response->successful()) {
            throw ValidationException::withMessages([
                'email' => ['Unable to contact accounts service. Please try again later.'],
            ]);
        }

        $payload = $response->json();

        // Support both flat and nested response shapes from the API
        // e.g. { "system_accounts": [...] } or { "data": { "system_accounts": [...] } }
        $systemAccounts = data_get($payload, 'system_accounts');
        if (!is_array($systemAccounts)) {
            $systemAccounts = data_get($payload, 'data.system_accounts', []);
        }

        $matched = collect($systemAccounts)->first(function ($account) use ($email, $password) {
            $employee = $account['employee'] ?? null;

            $apiEmail = isset($employee['email']) ? trim($employee['email']) : '';
            $apiPassword = isset($account['password']) ? trim($account['password']) : '';

            return ($account['account_type'] ?? null) === 'system'
                && !($account['blocked'] ?? false)
                && $employee
                && strcasecmp($apiEmail, $email) === 0
                && $apiPassword === $password;
        });

        if (!$matched) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        // Generate OTP and send via email
        $otpCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $request->session()->put('otp_code', $otpCode);
        $request->session()->put('otp_expires_at', now()->addMinutes(10));
        $request->session()->put('otp_account', $matched);
        $request->session()->put('otp_email', $email);

        try {
            Mail::raw('Your Jetlouge Travels verification code is: ' . $otpCode, function ($message) use ($email) {
                $message->to($email)->subject('Your login verification code');
            });
        } catch (\Throwable $e) {
            // If email fails, still allow OTP entry using code in session.
        }

        return back()->with('status', 'We have sent a 6-digit verification code to your email. Please enter it below to continue.')
            ->withInput($request->only('email'));
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
