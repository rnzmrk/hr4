<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Load latest account/employee data from external API using logged-in email
        $account = $this->fetchExternalAccountByEmail($user->email);
        $employee = $account['employee'] ?? null;

        return view('hr4.profile.index', compact('user', 'account', 'employee'));
    }

    public function edit()
    {
        $user = Auth::user();

        $account = $this->fetchExternalAccountByEmail($user->email);
        $employee = $account['employee'] ?? null;

        return view('hr4.profile.edit', compact('user', 'account', 'employee'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'email' => 'required|email',
            'password' => 'nullable|string|min:8|confirmed',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        // Find external system account for this user
        $account = $this->fetchExternalAccountByEmail($user->email);

        if (!$account || ($account['account_type'] ?? null) !== 'system') {
            return back()->withErrors(['email' => 'Unable to find your system account in the external service.']);
        }

        $accountId = $account['id'];

        $data = [
            'email' => $request->input('email'),
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->input('password');
        }

        $url = rtrim(config('app.external_accounts_url', 'https://hr4.jetlougetravels-ph.com/api/accounts/system'), '/') . '/' . $accountId;

        // Build HTTP request, attaching profile picture if provided
        $http = Http::withoutVerifying();

        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $http = $http->attach('profile_picture', file_get_contents($file->getRealPath()), $file->getClientOriginalName());
        }

        $response = $http->patch($url, $data);

        if (!$response->successful()) {
            return back()->withErrors(['email' => 'Failed to update profile in external system. Please try again.'])->withInput();
        }

        // Keep local user email/password in sync for consistency
        $user->email = $request->input('email');
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }
        $user->save();

        return redirect()->route('profile.index')->with('success', 'Profile updated successfully!');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        // Update password in external system
        $account = $this->fetchExternalAccountByEmail($user->email);

        if (!$account || ($account['account_type'] ?? null) !== 'system') {
            return back()->withErrors(['current_password' => 'Unable to find your system account in the external service.']);
        }

        $accountId = $account['id'];
        $url = rtrim(config('app.external_accounts_url', 'https://hr4.jetlougetravels-ph.com/api/accounts/system'), '/') . '/' . $accountId;

        $response = Http::withoutVerifying()->patch($url, [
            'password' => $request->input('password'),
        ]);

        if (!$response->successful()) {
            return back()->withErrors(['password' => 'Failed to update password in external system. Please try again.']);
        }

        // Also update local password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.index')->with('success', 'Password changed successfully!');
    }

    /**
     * Fetch the external system account for a given email.
     */
    protected function fetchExternalAccountByEmail(string $email): ?array
    {
        $response = Http::withoutVerifying()->get('https://hr4.jetlougetravels-ph.com/api/accounts');

        if (!$response->successful()) {
            return null;
        }

        $payload = $response->json();

        $systemAccounts = data_get($payload, 'system_accounts');
        if (!is_array($systemAccounts)) {
            $systemAccounts = data_get($payload, 'data.system_accounts', []);
        }

        $matched = collect($systemAccounts)->first(function ($account) use ($email) {
            $employee = $account['employee'] ?? null;
            $apiEmail = isset($employee['email']) ? trim($employee['email']) : '';

            return ($account['account_type'] ?? null) === 'system'
                && !($account['blocked'] ?? false)
                && $employee
                && strcasecmp($apiEmail, trim($email)) === 0;
        });

        return $matched ?: null;
    }
}
