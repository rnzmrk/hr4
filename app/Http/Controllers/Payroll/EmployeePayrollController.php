<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\RoleRate;
use App\Models\EmployeeBenefit;
use App\Models\BenefitPlan;
use App\Models\CompensationAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EmployeePayrollController extends Controller
{
    public function index(Request $request)
    {
        $rows = [];
        $builder = Payroll::with('employee')->latest('period_end');
        if ($request->filled('employee_id')) {
            $builder->where('employee_id', $request->integer('employee_id'));
        } elseif ($request->filled('employee')) {
            $name = $request->string('employee');
            $builder->whereHas('employee', function($q) use ($name){
                $q->where('name', $name);
            });
        }
        $items = $builder->limit(50)->get();

        foreach ($items as $p) {
            $gross = (float)$p->gross_pay;
            // recompute statutory contributions for display (based on gross)
            $sss = $this->computeSSS($gross);
            $philhealth = $this->computePhilHealth($gross);
            $pagibig = $this->computePagIbig($gross);
            $taxable = max(0, $gross - $sss - $philhealth - $pagibig);
            $wtax = $this->computeWithholdingTaxMonthly($taxable);

            $rows[] = [
                'employee' => optional($p->employee)->name ?? '—',
                'period' => $p->period_start?->format('M d, Y') . '–' . $p->period_end?->format('M d, Y'),
                'role' => $p->role,
                'hours' => (float)$p->hours_worked,
                'rate_type' => $p->rate_type,
                'rate' => (float)$p->rate,
                'gross' => $gross,
                'deductions' => (float)$p->deductions,
                'net' => (float)$p->net_pay,
                'status' => $p->status,
                'breakdown' => [
                    'sss' => round($sss, 2),
                    'philhealth' => round($philhealth, 2),
                    'pagibig' => round($pagibig, 2),
                    'withholding_tax' => round($wtax, 2),
                ],
            ];
        }

        $payrolls = $rows;
        $employees = Employee::orderBy('name')->get(['id','name']);

        return view('hr4.payroll.employee_payrolls', compact('payrolls','employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
            'employee' => 'nullable|string|max:255',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'role' => 'required|string|max:255',
            'rate_type' => 'required|in:hourly,monthly',
            'rate' => 'required|numeric|min:0',
            'hours_worked' => 'nullable|numeric|min:0',
        ]);

        $employee = null;
        if (!empty($data['employee_id'])) {
            $employee = Employee::find($data['employee_id']);
        }
        if (!$employee && !empty($data['employee'])) {
            $employee = Employee::where('name', $data['employee'])->first();
        }
        if (!$employee) {
            return back()->with('status', 'Employee not found. Please create the employee first.');
        }
        $rateType = $data['rate_type'];
        $rate = (float)$data['rate'];
        $hours = (float)($data['hours_worked'] ?? 0);

        // Apply latest approved compensation adjustment effective for this period
        $latestAdj = CompensationAdjustment::where('employee_id', $employee->id)
            ->where('applied_rate_type', $rateType)
            ->where('status', 'approved')
            ->whereDate('effective_date', '<=', $data['period_end'])
            ->orderByDesc('effective_date')
            ->first();
        if ($latestAdj) {
            $val = (float)$latestAdj->value;
            switch ($latestAdj->adjustment_type) {
                case 'set':
                    $rate = $val; break;
                case 'increase':
                    $rate = max(0, $rate + $val); break;
                case 'decrease':
                    $rate = max(0, $rate - $val); break;
            }
        }

        // Calculate gross based on rate type
        if ($rateType === 'hourly') {
            $gross = $rate * $hours;
        } else { // monthly
            // naive: full month
            $gross = $rate;
        }
        // Benefits: sum employee shares for active plans
        $benefitTotal = EmployeeBenefit::where('employee_id', $employee->id)
            ->join('benefit_plans', 'benefit_plans.id', '=', 'employee_benefits.benefit_plan_id')
            ->where('benefit_plans.active', true)
            ->sum(\DB::raw('COALESCE(employee_benefits.employee_share_override, benefit_plans.employee_share)'));

        // Statutory contributions
        $sss = $this->computeSSS($gross);
        $philhealth = $this->computePhilHealth($gross);
        $pagibig = $this->computePagIbig($gross);
        // Withholding tax uses taxable income after statutory contributions
        $taxable = max(0, $gross - $sss - $philhealth - $pagibig);
        $wtax = $this->computeWithholdingTaxMonthly($taxable);

        // Total deductions: benefits + statutory + withholding tax
        $deductions = (float)$benefitTotal + $sss + $philhealth + $pagibig + $wtax;
        $net = $gross - $deductions;

        Payroll::create([
            'employee_id' => $employee->id,
            'period_start' => Carbon::parse($data['period_start']),
            'period_end' => Carbon::parse($data['period_end']),
            'role' => $data['role'],
            'hours_worked' => $hours,
            'rate_type' => $rateType,
            'rate' => $rate,
            'gross_pay' => $gross,
            'deductions' => $deductions,
            'net_pay' => $net,
            'status' => 'Draft',
        ]);

        return redirect()->route('payroll.employee')->with('status', 'Payroll created.');
    }

    // === Helpers: Statutory computation (simplified 2024 PH rules) ===
    private function computeSSS(float $salary): float
    {
        // Employee share ~4.5% of MSC with floor 4,000 and ceiling 30,000
        $msc = min(max($salary, 4000), 30000);
        return round($msc * 0.045, 2);
    }

    private function computePhilHealth(float $salary): float
    {
        // Total premium 5% with floor 10,000 and ceiling 90,000; employee pays 50%
        $base = min(max($salary, 10000), 90000);
        $total = $base * 0.05;
        return round($total / 2, 2);
    }

    private function computePagIbig(float $salary): float
    {
        // Employee share: 1% if <=1500 otherwise 2%; ceiling 5,000
        $base = min($salary, 5000);
        $rate = ($salary <= 1500) ? 0.01 : 0.02;
        return round($base * $rate, 2);
    }

    private function computeWithholdingTaxMonthly(float $taxable): float
    {
        // TRAIN law monthly tables (simplified)
        if ($taxable <= 20833) return 0.0;
        if ($taxable <= 33333) return round(($taxable - 20833) * 0.20, 2);
        if ($taxable <= 66667) return round(2500 + ($taxable - 33333) * 0.25, 2);
        if ($taxable <= 166667) return round(10833 + ($taxable - 66667) * 0.30, 2);
        if ($taxable <= 666667) return round(40833.33 + ($taxable - 166667) * 0.32, 2);
        return round(200833.33 + ($taxable - 666667) * 0.35, 2);
    }
}
