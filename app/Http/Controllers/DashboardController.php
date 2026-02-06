<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Carbon\Carbon;

// Employee statuses
const NEW_HIRE = 'new_hire';
const REGULAR = 'regular';
const RETIRED = 'retired';

class DashboardController extends Controller
{
    public function index()
    {
        // All employee counts
        $allEmployee = Employee::count();
        $newHire = Employee::where('employee_status', NEW_HIRE)->count();
        $regular = Employee::where('employee_status', REGULAR)->count();
        $retired = Employee::where('employee_status', RETIRED)->count();
        
        // Get all employees count
        $totalEmployees = Employee::count();
        
        // Get regular employees count
        $regularEmployees = Employee::where('employee_status', REGULAR)->count();
        
        // Get new hire employees count (all new hires, not just this month)
        $newHireEmployees = Employee::where('employee_status', NEW_HIRE)->count();
        
        return view('hr4.dashboard', compact(
            'totalEmployees',
            'regularEmployees',
            'newHireEmployees',
            'allEmployee',
            'newHire',
            'regular',
            'retired'
        ));
    }

    /**
     * Get payroll cost (net pay) for the last 6 months for the dashboard chart.
     */
    public function payrollCostData()
    {
        $end = Carbon::now()->startOfMonth();
        $start = $end->copy()->subMonths(5);

        $rows = Payroll::selectRaw('DATE_FORMAT(pay_date, "%Y-%m") as ym, SUM(net_pay) as total')
            ->whereDate('pay_date', '>=', $start->toDateString())
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->keyBy('ym');

        $labels = [];
        $totals = [];

        for ($i = 0; $i < 6; $i++) {
            $month = $start->copy()->addMonths($i);
            $key = $month->format('Y-m');

            $labels[] = $month->format('M');
            $totals[] = (float) optional($rows->get($key))->total ?? 0.0;
        }

        return response()->json([
            'labels' => $labels,
            'totals' => $totals,
        ]);
    }

    /**
     * Get salary distribution buckets: number of employees and total salary per range.
     */
    public function salaryDistributionData()
    {
        $ranges = [
            ['label' => '20-30k', 'min' => 20000, 'max' => 29999],
            ['label' => '30-40k', 'min' => 30000, 'max' => 39999],
            ['label' => '40-50k', 'min' => 40000, 'max' => 49999],
            ['label' => '50-60k', 'min' => 50000, 'max' => 59999],
            ['label' => '60-70k', 'min' => 60000, 'max' => 69999],
            ['label' => '70k+',  'min' => 70000, 'max' => null],
        ];

        $labels = [];
        $counts = [];
        $totals = [];

        foreach ($ranges as $range) {
            $query = Employee::query()->whereNotNull('salary');

            $query->where('salary', '>=', $range['min']);
            if (!is_null($range['max'])) {
                $query->where('salary', '<=', $range['max']);
            }

            $count = (clone $query)->count();
            $total = (clone $query)->sum('salary');

            $labels[] = $range['label'];
            $counts[] = (int) $count;
            $totals[] = (float) $total;
        }

        return response()->json([
            'labels' => $labels,
            'counts' => $counts,
            'totals' => $totals,
        ]);
    }

    /**
     * Get average salary grouped by department name.
     */
    public function salaryByDepartmentData()
    {
        $rows = Employee::query()
            ->whereNotNull('salary')
            ->whereNotNull('department_id')
            ->with('department')
            ->get()
            ->groupBy(function (Employee $employee) {
                return optional($employee->department)->name ?: 'Unassigned';
            })
            ->map(function ($group) {
                /** @var \Illuminate\Support\Collection $group */
                return [
                    'average_salary' => (float) $group->avg('salary'),
                ];
            });

        $labels = $rows->keys()->values()->all();
        $averages = $rows->pluck('average_salary')->values()->all();

        return response()->json([
            'labels' => $labels,
            'averages' => $averages,
        ]);
    }
}
