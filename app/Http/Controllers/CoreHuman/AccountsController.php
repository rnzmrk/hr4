<?php

namespace App\Http\Controllers\CoreHuman;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $systemAccounts = Account::with('employee')
            ->where('account_type', 'system')
            ->when($search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('employee', function($subQuery) use ($search) {
                        $subQuery->where('first_name', 'like', "%{$search}%")
                                 ->orWhere('last_name', 'like', "%{$search}%")
                                 ->orWhere('email', 'like', "%{$search}%")
                                 ->orWhere('position', 'like', "%{$search}%")
                                 ->orWhereHas('department', function($deptQuery) use ($search) {
                                     $deptQuery->where('name', 'like', "%{$search}%");
                                 });
                    });
                });
            })
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'systemPage');
            
        $essAccounts = Account::with('employee')
            ->where('account_type', 'ess')
            ->when($search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('employee', function($subQuery) use ($search) {
                        $subQuery->where('first_name', 'like', "%{$search}%")
                                 ->orWhere('last_name', 'like', "%{$search}%")
                                 ->orWhere('email', 'like', "%{$search}%")
                                 ->orWhere('position', 'like', "%{$search}%")
                                 ->orWhereHas('department', function($deptQuery) use ($search) {
                                     $deptQuery->where('name', 'like', "%{$search}%");
                                 });
                    });
                });
            })
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'essPage');
            
        return view('hr4.core_human.accounts', compact('systemAccounts', 'essAccounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'account_type' => 'required|in:system,ess',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:6',
        ]);
        
        // Find employee by name to get the employee_id
        $employeeName = $data['name'];
        $employee = \App\Models\Employee::whereRaw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, ''), ' ', COALESCE(suffix_name, '')) = ?", [$employeeName])->first();
        
        if (!$employee) {
            return back()->withErrors(['name' => 'Employee not found.']);
        }
        
        Account::create([
            'employee_id' => $employee->id,
            'account_type' => $data['account_type'],
            'password' => $data['password'] ?? null, // Store plain text password
            'blocked' => false,
        ]);
        return redirect()->route('employees.index')->with('status', 'Account created.');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer|exists:accounts,id',  
            'password' => 'nullable|string|min:6',
        ]);

        $acc = Account::findOrFail($data['id']);
        if (!empty($data['password'])) {
            $acc->password = $data['password']; // Store plain text password
        }
        $acc->save();
        
        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Password updated successfully']);
        }
        
        return back()->with('status', 'Account updated.');
    }

    public function block(Request $request)
    {
        $data = $request->validate(['id' => 'required|integer|exists:accounts,id']);
        $acc = Account::findOrFail($data['id']);
        $acc->blocked = !$acc->blocked;
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
        
        // Find employee by name to get the employee_id
        $employeeName = $data['name'];
        $employee = \App\Models\Employee::whereRaw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(middle_name, ''), ' ', COALESCE(last_name, ''), ' ', COALESCE(suffix_name, '')) = ?", [$employeeName])->first();
        
        if (!$employee) {
            return back()->withErrors(['name' => 'Employee not found.']);
        }
        
        Account::create([
            'employee_id' => $employee->id,
            'account_type' => 'ess',
            'password_hashed' => null,
            'blocked' => false,
        ]);
        return back()->with('status', 'ESS account created.');
    }
}
