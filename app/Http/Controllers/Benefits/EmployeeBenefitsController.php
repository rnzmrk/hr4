<?php

namespace App\Http\Controllers\Benefits;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\BenefitPlan;
use App\Models\EmployeeBenefit;
use Illuminate\Http\Request;

class EmployeeBenefitsController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['department'])->orderBy('name')->get();
        $plans = BenefitPlan::where('active', true)->orderBy('name')->get();

        $rows = [];
        foreach ($employees as $e) {
            $benefits = EmployeeBenefit::where('employee_id', $e->id)
                ->with('plan')
                ->get()
                ->map(function ($eb) {
                    return [
                        'plan' => $eb->plan?->name,
                        'type' => $eb->plan?->type,
                        'employee_share' => $eb->employee_share_override ?? $eb->plan?->employee_share,
                    ];
                })->toArray();
            $rows[] = [
                'id' => $e->id,
                'name' => $e->name,
                'department' => $e->department?->name ?? '—',
                'role' => $e->role,
                'benefits' => $benefits,
            ];
        }

        return view('hr4.benefits.employee_benefits', [
            'employees' => $rows,
            'plans' => $plans,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'benefit_plan_id' => 'required|exists:benefit_plans,id',
            'employee_share_override' => 'nullable|numeric|min:0',
        ]);

        EmployeeBenefit::updateOrCreate(
            [
                'employee_id' => $data['employee_id'],
                'benefit_plan_id' => $data['benefit_plan_id'],
            ],
            [
                'employee_share_override' => $data['employee_share_override'] ?? null,
            ]
        );

        return back()->with('status', 'Benefit assigned to employee.');
    }
}
