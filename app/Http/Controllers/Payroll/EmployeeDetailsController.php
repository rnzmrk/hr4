<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\BenefitPlan;
use Illuminate\Http\Request;

class EmployeeDetailsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $employeesQuery = Employee::with(['department', 'benefitPlans'])
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
            ->orderBy('last_name')
            ->orderBy('first_name');

        $employees = $employeesQuery->get();

        // Transform employees data for the view
        $transformedEmployees = $employees->map(function (Employee $employee) {
            $benefits = $employee->benefitPlans->pluck('type')->flatten()->unique()->filter()->implode(', ');
            
            return [
                'id' => $employee->id,
                'employee_id' => 'EMP' . str_pad($employee->id, 3, '0', STR_PAD_LEFT),
                'name' => trim(($employee->first_name ?? '') . ' ' . ($employee->middle_name ?? '') . ' ' . ($employee->last_name ?? '') . ' ' . ($employee->suffix_name ?? '')),
                'department' => optional($employee->department)->name ?? '—',
                'position' => $employee->position ?? '—',
                'salary' => $employee->salary ? '₱' . number_format($employee->salary, 2) : '₱0.00',
                'benefits' => $benefits ?: '—',
                'atm_number' => $employee->atm_number ?? '—',
            ];
        });

        return view('hr4.payroll.employee-details', [
            'employees' => $transformedEmployees
        ]);
    }

    public function show($id)
    {
        $employee = Employee::with(['department', 'benefitPlans'])->find($id);
        
        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        // Get benefits information
        $benefits = $employee->benefitPlans->pluck('type')->flatten()->unique()->filter()->values();
        
        $employeeData = [
            'id' => $employee->id,
            'employee_id' => 'EMP' . str_pad($employee->id, 3, '0', STR_PAD_LEFT),
            'name' => trim(($employee->first_name ?? '') . ' ' . ($employee->middle_name ?? '') . ' ' . ($employee->last_name ?? '') . ' ' . ($employee->suffix_name ?? '')),
            'department' => optional($employee->department)->name ?? '—',
            'position' => $employee->position ?? '—',
            'salary' => $employee->salary ? '₱' . number_format($employee->salary, 2) : '₱0.00',
            'salary_raw' => $employee->salary ?? 0,
            'benefits' => $benefits,
            'benefits_string' => $benefits->implode(', ') ?: '—',
            'atm_number' => $employee->atm_number ?? '—',
            'employee_status' => $employee->employee_status ?? '—',
            'date_hired' => $employee->date_hired ? $employee->date_hired->format('Y-m-d') : '—',
        ];

        return response()->json($employeeData);
    }

    public function exportExcel(Request $request)
    {
        $search = $request->input('search');
        
        $employeesQuery = Employee::with(['department', 'benefitPlans'])
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
            ->orderBy('last_name')
            ->orderBy('first_name');

        $employees = $employeesQuery->get();

        // Transform for CSV export
        $csvData = $employees->map(function (Employee $employee) {
            $benefits = $employee->benefitPlans->pluck('type')->flatten()->unique()->filter()->implode(', ');
            
            return [
                'EMP' . str_pad($employee->id, 3, '0', STR_PAD_LEFT),
                trim(($employee->first_name ?? '') . ' ' . ($employee->middle_name ?? '') . ' ' . ($employee->last_name ?? '') . ' ' . ($employee->suffix_name ?? '')),
                optional($employee->department)->name ?? '—',
                $employee->position ?? '—',
                $employee->salary ? number_format($employee->salary, 2) : '0.00',
                $benefits ?: '—',
                $employee->atm_number ?? '—',
            ];
        });

        $filename = 'employee_details_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['Employee ID', 'Employee Name', 'Department', 'Position', 'Salary', 'Benefits', 'ATM Number']);
            
            // Data rows
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
