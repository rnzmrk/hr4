<?php

namespace App\Http\Controllers\Compensation;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;

class SalaryAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['department']);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        if ($request->filled('search')) {
            $search = strtolower($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(first_name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$search}%"]);
            });
        }

        $employees = $query->orderBy('last_name')->orderBy('first_name')->get();
        $departments = Department::orderBy('name')->get();

        return view('hr4.compensation.salary_adjustment', compact('employees', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'salary' => 'required|integer|min:0',
        ]);

        $employee = Employee::findOrFail($id);
        $employee->salary = $request->input('salary');
        $employee->save();

        return back()->with('status', 'Salary updated successfully.');
    }
}
