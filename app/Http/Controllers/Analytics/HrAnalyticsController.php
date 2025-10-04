<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Account;
use App\Models\AcceptedContract;
use App\Models\Department;
use App\Models\Payroll;
use App\Models\Disbursement;
use App\Models\Payslip;
use App\Models\BenefitPlan;
use App\Models\EmployeeBenefit;
use App\Models\CompensationAdjustment;
use App\Models\LeaveRecord;
use App\Models\Requisition;
use Illuminate\Support\Carbon;

class HrAnalyticsController extends Controller
{
    public function index()
    {
        // Core Human
        $employeesCount = Employee::count();
        $departmentsCount = Department::count();
        $accountsCount = Account::count();
        $contractsCount = AcceptedContract::count();

        // Benefits
        $benefitPlansCount = BenefitPlan::count();
        $employeesWithBenefits = EmployeeBenefit::distinct('employee_id')->count('employee_id');

        // Compensation
        $approvedAdjustments = CompensationAdjustment::where('status', 'approved')->count();
        $leavesApproved = LeaveRecord::where('status', 'approved')->count();

        // Payroll
        $payrollsThisMonth = Payroll::whereMonth('period_end', now()->month)->whereYear('period_end', now()->year)->count();
        $totalNetThisMonth = (float) Payroll::whereMonth('period_end', now()->month)->whereYear('period_end', now()->year)->sum('net_pay');
        $paidDisbursementsThisMonth = Disbursement::whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->where('status', 'paid')->sum('amount');
        $payslipsThisMonth = Payslip::whereMonth('issued_at', now()->month)->whereYear('issued_at', now()->year)->count();

        // Requisitions and openings (basic)
        $openings = (int) Requisition::sum('openings');

        // Compose dataset for the view
        $cards = [
            ['label' => 'Employees', 'value' => $employeesCount],
            ['label' => 'Departments', 'value' => $departmentsCount],
            ['label' => 'Accounts', 'value' => $accountsCount],
            ['label' => 'Accepted Contracts', 'value' => $contractsCount],
            ['label' => 'Benefit Plans', 'value' => $benefitPlansCount],
            ['label' => 'Employees w/ Benefits', 'value' => $employeesWithBenefits],
            ['label' => 'Approved Adjustments', 'value' => $approvedAdjustments],
            ['label' => 'Approved Leaves', 'value' => $leavesApproved],
            ['label' => 'Payrolls (This Month)', 'value' => $payrollsThisMonth],
            ['label' => 'Total Net (This Month)', 'value' => number_format($totalNetThisMonth, 2)],
            ['label' => 'Disbursed (This Month)', 'value' => number_format((float)$paidDisbursementsThisMonth, 2)],
            ['label' => 'Payslips (This Month)', 'value' => $payslipsThisMonth],
            ['label' => 'Openings', 'value' => $openings],
        ];

        // Simple trend: last 6 months payroll net
        $trend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $sum = (float) Payroll::whereMonth('period_end', $month->month)->whereYear('period_end', $month->year)->sum('net_pay');
            $trend[] = [
                'label' => $month->format('M Y'),
                'value' => round($sum, 2),
            ];
        }

        return view('hr4.hr_analytics.index', compact('cards','trend'));
    }
}
