<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'All accounts retrieved',
            'data' => [
                'system_accounts' => Account::with('employee.department')->where('account_type', 'system')->get(),
                'ess_accounts' => Account::with('employee.department')->where('account_type', 'ess')->get()
            ]
        ]);
    }

    public function systemAccounts()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'System accounts retrieved',
            'data' => Account::with('employee.department')->where('account_type', 'system')->get()
        ]);
    }

    public function essAccounts()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'ESS accounts retrieved',
            'data' => Account::with('employee.department')->where('account_type', 'ess')->get()
        ]);
    }

    public function patchSystemAccount(Request $request, $id)
    {
        // Validate that password, name, or profile_picture is sent
        $request->validate([
            'password' => 'sometimes|required|string',
            'name' => 'sometimes|required|string',
            'profile_picture' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Find the specific system account
        $account = Account::where('id', $id)->where('account_type', 'system')->first();
        if (!$account) {
            return response()->json([
                'status' => 'error',
                'message' => 'System account not found'
            ], 404);
        }

        // Update password if provided
        if ($request->has('password')) {
            $account->password = $request->password;
        }

        // Update employee name if provided
        if ($request->has('name') && $account->employee) {
            $account->employee->name = $request->name;
            $account->employee->save();
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('profile_pictures', $fileName, 'public');
            $account->profile_picture = $fileName;
        }

        // Set default profile picture if not exists and no upload
        if (!$account->profile_picture && $account->employee) {
            $firstName = explode(' ', $account->employee->name)[0];
            $account->profile_picture = $firstName . '_default.png';
        }

        $account->save();

        return response()->json([
            'message' => 'System account updated!',
            'data' => $account->fresh()->load('employee.department')
        ]);
    }

    public function patchEssAccount(Request $request, $id)
    {
        // Validate that password, name, or profile_picture is sent
        $request->validate([
            'password' => 'sometimes|required|string',
            'name' => 'sometimes|required|string',
            'profile_picture' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Find the specific ESS account
        $account = Account::where('id', $id)->where('account_type', 'ess')->first();
        if (!$account) {
            return response()->json([
                'status' => 'error',
                'message' => 'ESS account not found'
            ], 404);
        }

        // Update password if provided
        if ($request->has('password')) {
            $account->password = $request->password;
        }

        // Update employee name if provided
        if ($request->has('name') && $account->employee) {
            $account->employee->name = $request->name;
            $account->employee->save();
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('profile_pictures', $fileName, 'public');
            $account->profile_picture = $fileName;
        }

        // Set default profile picture if not exists and no upload
        if (!$account->profile_picture && $account->employee) {
            $firstName = explode(' ', $account->employee->name)[0];
            $account->profile_picture = $firstName . '_default.png';
        }

        $account->save();

        return response()->json([
            'message' => 'ESS account updated!',
            'data' => $account->fresh()->load('employee.department')
        ]);
    }

}

