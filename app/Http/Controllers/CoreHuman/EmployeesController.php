<?php

namespace App\Http\Controllers\CoreHuman;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Account;
use App\Models\Department;
use Illuminate\Http\Request;

class EmployeesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $employeesQuery = Employee::with('department')
            ->when($search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('middle_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('position', 'like', "%{$search}%")
                      ->orWhereHas('department', function($deptQuery) use ($search) {
                          $deptQuery->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->orderBy('created_at');

        $employees = $employeesQuery->paginate(5, ['*'], 'employeePage');

        // Transform the paginated collection
        $transformedEmployees = $employees->getCollection()->map(function (Employee $e) {
            return [
                'id' => $e->id,
                'name' => trim(($e->first_name ?? '') . ' ' . ($e->middle_name ?? '') . ' ' . ($e->last_name ?? '') . ' ' . ($e->suffix_name ?? '')),
                'department' => optional($e->department)->name ?? '—',
                'position' => $e->position,
                'date_hired' => optional($e->date_hired)->format('Y-m-d'),
                'employee_status' => $e->employee_status,
                'email' => $e->email,
            ];
        })->toArray();

        // Replace the collection in the paginator
        $employees->setCollection(collect($transformedEmployees));

        // Compatibility: employees view checks session('accounts')
        $accounts = Account::with('employee')->get()->map(function ($account) {
            $employee = $account->employee;
            return [
                'name' => trim(($employee->first_name ?? '') . ' ' . ($employee->middle_name ?? '') . ' ' . ($employee->last_name ?? '') . ' ' . ($employee->suffix_name ?? '')),
                'email' => $employee->email ?? '',
                'role' => $employee->position ?? '',
                'account_type' => $account->account_type
            ];
        })->toArray();
        session(['accounts' => $accounts]);

        $departments = Department::orderBy('name')->get(['id','name']);

        return view('hr4.core_human.employees', compact('employees','departments'));
    }

    public function show($id)
    {
        $employee = Employee::with('department')->find($id);
        
        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        // Calculate age from birth_date if not set
        if ($employee->birth_date && !$employee->age) {
            $employee->age = $employee->birth_date->age;
        }

        return response()->json($employee);
    }
}
