<?php

namespace App\Http\Controllers\CoreHuman;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountsController extends Controller
{
    public function index()
    {
        $accounts = Account::orderByDesc('created_at')->get()->toArray();
        return view('hr4.core_human.accounts', compact('accounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'account_type' => 'required|in:system,ess',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'role' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6',
        ]);
        $type = $data['account_type'];
        $role = $type === 'ess' ? 'ESS' : ($data['role'] ?? 'User');
        $hashed = !empty($data['password']) ? Hash::make($data['password']) : null;

        Account::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $role,
            'account_type' => $type,
            'department' => $type === 'system' ? ($data['department'] ?? 'General') : ($data['department'] ?? null),
            'status' => $data['status'] ?? 'Active',
            'password_hashed' => $hashed,
            'blocked' => false,
        ]);
        return redirect()->route('accounts.index')->with('status', 'Account created.');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer|exists:accounts,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'role' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6',
        ]);

        $acc = Account::findOrFail($data['id']);
        $acc->name = $data['name'];
        $acc->email = $data['email'];
        if (!empty($data['role'])) $acc->role = $data['role'];
        if (!empty($data['department'])) $acc->department = $data['department'];
        if (!empty($data['status'])) $acc->status = $data['status'];
        if (!empty($data['password'])) {
            $acc->password_hashed = Hash::make($data['password']);
        }
        $acc->save();
        return back()->with('status', 'Account updated.');
    }

    public function block(Request $request)
    {
        $data = $request->validate(['id' => 'required|integer|exists:accounts,id']);
        $acc = Account::findOrFail($data['id']);
        $acc->blocked = !$acc->blocked;
        $acc->status = $acc->blocked ? 'Blocked' : ($acc->status ?: 'Active');
        $acc->save();
        return back()->with('status', $acc->blocked ? 'Account blocked.' : 'Account unblocked.');
    }

    public function delete(Request $request)
    {
        $data = $request->validate(['id' => 'required|integer|exists:accounts,id']);
        Account::where('id', $data['id'])->delete();
        return back()->with('status', 'Account deleted.');
    }

    public function fromEmployee(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'department' => 'nullable|string|max:255',
        ]);
        Account::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? strtolower(str_replace(' ', '.', $data['name'])).'@example.com',
            'role' => 'ESS',
            'account_type' => 'ess',
            'department' => $data['department'] ?? null,
            'status' => 'Active',
            'password_hashed' => null,
            'blocked' => false,
        ]);
        return back()->with('status', 'ESS account created.');
    }
}
