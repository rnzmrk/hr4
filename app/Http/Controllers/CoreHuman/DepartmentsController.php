<?php

namespace App\Http\Controllers\CoreHuman;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Requisition;
use App\Models\Employee;

class DepartmentsController extends Controller
{
    public function index()
    {
        $departments = Department::orderBy('name')->get();

        $data = [];
        foreach ($departments as $d) {
            // Get position limits
            $positionLimits = $d->position_limits ? json_decode($d->position_limits, true) : [];
            $totalLimit = array_sum($positionLimits);
            
            // Count employees
            $employeeCount = Employee::where('department_id', $d->id)->count();
            
            // Calculate openings
            $totalOpenings = max(0, $totalLimit - $employeeCount);
            
            // Get position counts
            $positionCounts = [];
            foreach ($positionLimits as $position => $limit) {
                $count = Employee::where('department_id', $d->id)
                                ->where('position', $position)
                                ->count();
                $positionCounts[$position] = [
                    'limit' => $limit,
                    'current' => $count,
                    'openings' => max(0, $limit - $count)
                ];
            }

            $data[] = [
                'name' => $d->name,
                'employee_limit' => $totalLimit,
                'employee_count' => $employeeCount,
                'openings' => $totalOpenings,
                'position_details' => $positionCounts,
            ];
        }

        return view('hr4.core_human.departments', ['data' => $data]);
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'position_limits' => 'required|array',
        ]);

        // Process position limits
        $positionLimits = [];
        foreach ($data['position_limits'] as $position => $limit) {
            if ($limit > 0) {
                $positionLimits[$position] = (int) $limit;
            }
        }

        if (empty($positionLimits)) {
            return back()->withErrors(['position_limits' => 'At least one position limit must be greater than 0.']);
        }

        Department::create([
            'name' => $data['name'],
            'position_limits' => json_encode($positionLimits),
            'openings' => 0,
        ]);

        return back()->with('status', 'Department added.');
    }

    public function show($departmentName)
    {
        $department = Department::where('name', $departmentName)->first();
        if (!$department) {
            abort(404, 'Department not found');
        }

        // Get department details using existing logic
        $details = $this->getDepartmentDetailsData($department);
        
        return view('hr4.core_human.department_show', [
            'department' => $department,
            'details' => $details
        ]);
    }

    private function getDepartmentDetailsData($department)
    {
        // Define position mappings for each department
        $positionMappings = [
            'Financial' => ['Financial Staff'],
            'Core' => ['Travel Agent', 'Travel Staff'],
            'Logistic' => ['Driver', 'Fleet Manager', 'Procurement Officer', 'Logistics Staff'],
            'Human Resource' => ['Hr Manager', 'Hr Staff'],
            'Administrative' => ['Administrative Staff']
        ];

        $positions = $positionMappings[$department->name] ?? [];
        $employeeCounts = [];

        foreach ($positions as $position) {
            $count = Employee::where('department_id', $department->id)
                            ->where('position', $position)
                            ->count();
            $employeeCounts[$position] = $count;
        }

        $totalEmployees = Employee::where('department_id', $department->id)->count();
        
        // Get position limits and calculate total
        $positionLimits = [];
        if ($department->position_limits) {
            $positionLimits = json_decode($department->position_limits, true) ?? [];
        }
        $employeeLimit = array_sum($positionLimits);
        
        $openings = max(0, $employeeLimit - $totalEmployees);

        return [
            'total_employees' => $totalEmployees,
            'employee_limit' => $employeeLimit,
            'openings' => $openings,
            'position_counts' => $employeeCounts,
            'position_limits' => $positionLimits
        ];
    }

    public function getDepartmentDetails($departmentName)
    {
        $department = Department::where('name', $departmentName)->first();
        if (!$department) {
            return response()->json(['error' => 'Department not found'], 404);
        }

        // Use the same position mappings as the show method
        $details = $this->getDepartmentDetailsData($department);

        return response()->json([
            'department' => $departmentName,
            'total_employees' => $details['total_employees'],
            'employee_limit' => $details['employee_limit'],
            'openings' => $details['openings'],
            'position_counts' => $details['position_counts']
        ]);
    }
}
