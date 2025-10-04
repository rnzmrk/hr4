<?php

namespace App\Http\Controllers\CoreHuman;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Account;
use Illuminate\Http\Request;

class EmployeesController extends Controller
{
    public function index()
    {
        $employees = Employee::orderBy('name')
            ->get()
            ->map(function (Employee $e) {
                return [
                    'name' => $e->name,
                    'department' => optional($e->department)->name ?? '—',
                    'role' => $e->role,
                    'start_date' => optional($e->start_date)->format('Y-m-d'),
                    'status' => $e->status,
                    'email' => $e->email,
                ];
            })->toArray();

        // Compatibility: employees view checks session('accounts')
        session(['accounts' => Account::select('name','email','role','account_type')->get()->toArray()]);

        return view('hr4.core_human.employees', compact('employees'));
    }
}
