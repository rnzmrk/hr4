<?php

namespace App\Http\Controllers\Benefits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeeBenefitsController extends Controller
{
    public function index()
    {
        // Static sample data for UI demonstration (no database connection)
        $employees = [
            [
                'id' => 1,
                'name' => 'Juan Dela Cruz',
                'department' => 'IT',
                'role' => 'Software Engineer',
                'benefits' => [
                    [
                        'plan' => 'Health Insurance',
                        'type' => 'Health',
                        'employee_share' => 500
                    ],
                    [
                        'plan' => 'SSS',
                        'type' => 'Government',
                        'employee_share' => 1350
                    ]
                ]
            ],
            [
                'id' => 2,
                'name' => 'Maria Santos',
                'department' => 'Human Resources',
                'role' => 'HR Manager',
                'benefits' => [
                    [
                        'plan' => 'Health Insurance',
                        'type' => 'Health',
                        'employee_share' => 600
                    ],
                    [
                        'plan' => 'SSS',
                        'type' => 'Government',
                        'employee_share' => 1350
                    ],
                    [
                        'plan' => 'Pag-IBIG',
                        'type' => 'Housing',
                        'employee_share' => 100
                    ]
                ]
            ],
            [
                'id' => 3,
                'name' => 'Jose Reyes',
                'department' => 'Finance',
                'role' => 'Accountant',
                'benefits' => [
                    [
                        'plan' => 'Health Insurance',
                        'type' => 'Health',
                        'employee_share' => 450
                    ],
                    [
                        'plan' => 'SSS',
                        'type' => 'Government',
                        'employee_share' => 1350
                    ]
                ]
            ]
        ];

        $plans = [
            [
                'id' => 1,
                'name' => 'Health Insurance',
                'type' => 'Health',
                'employee_share' => 500,
                'active' => true
            ],
            [
                'id' => 2,
                'name' => 'SSS',
                'type' => 'Government',
                'employee_share' => 1350,
                'active' => true
            ],
            [
                'id' => 3,
                'name' => 'Pag-IBIG',
                'type' => 'Housing',
                'employee_share' => 100,
                'active' => true
            ],
            [
                'id' => 4,
                'name' => 'PhilHealth',
                'type' => 'Health',
                'employee_share' => 550,
                'active' => true
            ]
        ];

        return view('hr4.benefits.employee_benefits', [
            'employees' => $employees,
            'plans' => $plans,
        ]);
    }

    public function store(Request $request)
    {
        // For UI demo only - return success message without database operations
        return back()->with('status', 'Benefit assigned to employee (UI Demo Mode)');
    }
}
