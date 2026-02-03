<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
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
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        try {
            // Get accounts from API
            $response = \Http::withoutVerifying()->get('https://hr4.jetlougetravels-ph.com/api/accounts');
            
            \Log::info('API Response Status: ' . $response->status());
            \Log::info('API Response Body: ' . $response->body());
            
            if (!$response->successful()) {
                \Log::error('API call failed with status: ' . $response->status());
                throw ValidationException::withMessages([
                    'email' => ['Unable to connect to authentication service.'],
                ]);
            }

            $accountsData = $response->json();
            \Log::info('Parsed API Response: ' . json_encode($accountsData));
            
            // Try different possible response structures
            $systemAccounts = [];
            
            // Option 1: Check if response has 'system_accounts' key
            if (isset($accountsData['system_accounts']) && is_array($accountsData['system_accounts'])) {
                $systemAccounts = $accountsData['system_accounts'];
                \Log::info('Found system_accounts key with ' . count($systemAccounts) . ' accounts');
            }
            // Option 2: Check if response is directly an array of accounts
            elseif (is_array($accountsData)) {
                $systemAccounts = $accountsData;
                \Log::info('Response is directly an array with ' . count($systemAccounts) . ' accounts');
            }
            // Option 3: Check if response has 'data' key
            elseif (isset($accountsData['data']) && is_array($accountsData['data'])) {
                $systemAccounts = $accountsData['data'];
                \Log::info('Found data key with ' . count($systemAccounts) . ' accounts');
            }
            
            if (empty($systemAccounts)) {
                \Log::error('No system accounts found in API response');
                throw ValidationException::withMessages([
                    'email' => ['No accounts found in authentication service.'],
                ]);
            }

            \Log::info('Looking for email: ' . $request->email);
            \Log::info('Total accounts to check: ' . count($systemAccounts));

            // Find matching account by email and system account type
            $matchingAccount = null;
            foreach ($systemAccounts as $index => $account) {
                \Log::info("Checking account {$index}: " . json_encode($account));
                
                // Check if this is a system account
                $isSystemAccount = false;
                if (isset($account['account_type']) && $account['account_type'] === 'system') {
                    $isSystemAccount = true;
                }
                
                // Check if not blocked
                $isBlocked = isset($account['blocked']) && $account['blocked'] === true;
                
                // Check email match
                $emailMatch = false;
                if (isset($account['employee']['email'])) {
                    $emailMatch = strtolower($account['employee']['email']) === strtolower($request->email);
                }
                
                \Log::info("Account {$index} - System: {$isSystemAccount}, Blocked: {$isBlocked}, Email Match: {$emailMatch}");
                
                if ($isSystemAccount && !$isBlocked && $emailMatch) {
                    $matchingAccount = $account;
                    \Log::info('Found matching account for employee: ' . ($account['employee']['first_name'] ?? 'Unknown') . ' ' . ($account['employee']['last_name'] ?? 'Unknown'));
                    break;
                }
            }

            if (!$matchingAccount) {
                \Log::error('No matching account found for email: ' . $request->email);
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials do not match our records.'],
                ]);
            }

            // Check password - handle both hashed and plain text passwords
            $passwordValid = false;
            
            // First try to check as hashed password
            try {
                $passwordValid = Hash::check($request->password, $matchingAccount['password']);
                \Log::info('Hash check result: ' . ($passwordValid ? 'valid' : 'invalid'));
            } catch (\Exception $e) {
                \Log::info('Hash check failed, trying plain text comparison');
                // If hash check fails, try direct comparison (plain text)
                $passwordValid = ($request->password === $matchingAccount['password']);
                \Log::info('Plain text comparison result: ' . ($passwordValid ? 'valid' : 'invalid'));
            }

            if (!$passwordValid) {
                \Log::error('Password validation failed for email: ' . $request->email);
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials do not match our records.'],
                ]);
            }

            \Log::info('Password validated, checking user table for employee_id: ' . $matchingAccount['employee_id']);

            // Check if user exists in users table using employee_id
            $user = DB::table('users')
                ->where('employee_id', $matchingAccount['employee_id'])
                ->first();

            if (!$user) {
                \Log::error('No user found in local database for employee_id: ' . $matchingAccount['employee_id']);
                throw ValidationException::withMessages([
                    'email' => ['Account not found in user database.'],
                ]);
            }

            \Log::info('Found local user with email: ' . $user->email);

            // Authenticate the user using user email for Laravel Auth
            if (Auth::attempt(['email' => $user->email, 'password' => $request->password], $request->boolean('remember'))) {
                \Log::info('Laravel Auth successful for user: ' . $user->email);
                $request->session()->regenerate();

                // Store account and employee information in session
                session([
                    'account_info' => $matchingAccount,
                    'employee_info' => $matchingAccount['employee'],
                    'user_info' => $user
                ]);

                return redirect()->intended(route('dashboard'));
            }

            \Log::error('Laravel Auth failed for user: ' . $user->email);
            throw ValidationException::withMessages([
                'email' => ['Authentication failed.'],
            ]);

        } catch (\Exception $e) {
            \Log::error('Login error: ' . $e->getMessage());
            \Log::error('Login error trace: ' . $e->getTraceAsString());
            
            throw ValidationException::withMessages([
                'email' => ['Authentication service error. Please try again.'],
            ]);
        }
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

        // Clear account information from session
        session()->forget('account_info');

        return redirect('/login');
    }
}
