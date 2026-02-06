<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Http\Request;
use OpenAI;

class PayrollController extends Controller
{
    /**
     * Display employee details for payroll.
     *
     * @return \Illuminate\View\View
     */
    public function employeeDetails()
    {
        return view('hr4.payroll.employee-details');
    }

    /**
     * Display salary computation page.
     *
     * @return \Illuminate\View\View
     */
    public function salaryComputation()
    {
        return view('hr4.payroll.salary-computation');
    }

    /**
     * Search employees for salary computation
     */
    public function searchEmployees(Request $request)
    {
        $search = $request->input('search');
        
        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $employees = Employee::with('department')
            ->where(function($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            })
            ->limit(10)
            ->get(['id', 'first_name', 'last_name', 'position', 'salary']);

        return response()->json($employees);
    }

    /**
     * Calculate payroll deductions based on Philippine government standards
     */
    public function calculatePayroll(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'base_salary' => 'required|numeric|min:0',
            'overtime' => 'nullable|numeric|min:0',
            'absent' => 'nullable|numeric|min:0',
            'additions' => 'nullable|numeric|min:0',
            'incentives' => 'nullable|numeric|min:0',
        ]);

        $employee = Employee::find($request->employee_id);
        $baseSalary = $request->base_salary;
        $overtime = $request->overtime ?? 0;
        $absent = $request->absent ?? 0;
        $additions = $request->additions ?? 0;
        $incentives = $request->incentives ?? 0;

        // Calculate gross salary
        $grossSalary = $baseSalary + $overtime + $additions + $incentives - $absent;

        // Calculate SSS Contribution (2025 rates)
        $sssContribution = $this->calculateSSS($baseSalary);

        // Calculate PhilHealth Contribution (2025 rates - 5% total, 2.5% employee share)
        $philHealthContribution = $this->calculatePhilHealth($baseSalary);

        // Calculate Pag-IBIG Contribution (2025 rates)
        $pagibigContribution = $this->calculatePagibig($baseSalary);

        // Calculate Income Tax (2025 BIR rates)
        // Taxable income = Gross salary - Government contributions (SSS, PhilHealth, Pag-IBIG)
        $taxableIncome = $grossSalary - $sssContribution - $philHealthContribution - $pagibigContribution;
        
        // Ensure taxable income is not negative
        $taxableIncome = max(0, $taxableIncome);
        
        $incomeTax = $this->calculateIncomeTax($taxableIncome);

        // Calculate net pay
        $totalDeductions = $sssContribution + $philHealthContribution + $pagibigContribution + $incomeTax;
        $netPay = $grossSalary - $totalDeductions;

        return response()->json([
            'gross_salary' => $grossSalary,
            'sss' => $sssContribution,
            'philhealth' => $philHealthContribution,
            'pagibig' => $pagibigContribution,
            'income_tax' => $incomeTax,
            'total_deductions' => $totalDeductions,
            'net_pay' => $netPay,
            'employee' => [
                'id' => $employee->id,
                'name' => trim($employee->first_name . ' ' . $employee->last_name),
                'position' => $employee->position,
                'department' => $employee->department->name ?? 'N/A'
            ]
        ]);
    }

    /**
     * Save payroll to database
     */
    public function savePayroll(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'base_salary' => 'required|numeric|min:0',
                'gross_salary' => 'required|numeric|min:0',
                'sss' => 'required|numeric|min:0',
                'philhealth' => 'required|numeric|min:0',
                'pagibig' => 'required|numeric|min:0',
                'income_tax' => 'required|numeric|min:0',
                'net_pay' => 'required|numeric|min:0',
                'incentives' => 'nullable|numeric|min:0',
                'pay_date' => 'required|date',
            ]);

            $employee = Employee::find($request->employee_id);
            
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found'
                ], 404);
            }

            $payroll = Payroll::create([
                'employee_id' => $request->employee_id,
                'employee_name' => trim($employee->first_name . ' ' . $employee->last_name),
                'position' => $employee->position ?? 'N/A',
                'department' => $employee->department ? $employee->department->name : 'N/A',
                'salary' => $request->base_salary,
                'sss' => $request->sss,
                'philhealth' => $request->philhealth,
                'pagibig' => $request->pagibig,
                'income_tax' => $request->income_tax,
                'incentives' => $request->incentives ?? 0,
                'net_pay' => $request->net_pay,
                'pay_date' => $request->pay_date,
                'period_start' => $request->pay_date,
                'period_end' => $request->pay_date,
                'status' => 'processed',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payroll saved successfully!',
                'payroll_id' => $payroll->id
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving payroll: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payroll records
     */
    public function getPayrollRecords(Request $request)
    {
        $query = Payroll::with('employee')
            ->orderByDesc('pay_date')
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('employee_name', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if ($request->filled('filter_type') && $request->filled('filter_date')) {
            $filterType = $request->filter_type;
            $filterDate = $request->filter_date;
            
            switch ($filterType) {
                case 'daily':
                    $query->whereDate('pay_date', $filterDate);
                    break;
                case 'monthly':
                    $query->whereMonth('pay_date', date('m', strtotime($filterDate)))
                          ->whereYear('pay_date', date('Y', strtotime($filterDate)));
                    break;
                case 'yearly':
                    $query->whereYear('pay_date', $filterDate);
                    break;
            }
        }

        $totalsQuery = (clone $query)->reorder();

        $totals = $totalsQuery->selectRaw('COALESCE(SUM(salary),0) as total_salary')
            ->selectRaw('COALESCE(SUM(incentives),0) as total_incentives')
            ->selectRaw('COALESCE(SUM(sss),0) as total_sss')
            ->selectRaw('COALESCE(SUM(philhealth),0) as total_philhealth')
            ->selectRaw('COALESCE(SUM(pagibig),0) as total_pagibig')
            ->selectRaw('COALESCE(SUM(income_tax),0) as total_income_tax')
            ->selectRaw('COALESCE(SUM(net_pay),0) as total_net_pay')
            ->first();

        $employeeCount = (clone $query)->reorder()->distinct('employee_id')->count('employee_id');

        $payrolls = $query->paginate(5)->appends($request->query());

        return response()->json(array_merge(
            $payrolls->toArray(),
            [
                'totals' => $totals,
                'employee_count' => $employeeCount,
            ]
        ));
    }

    /**
     * Get payroll details for modal
     */
    public function getPayrollDetails($id)
    {
        try {
            $payroll = Payroll::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'payroll' => $payroll
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payroll record not found'
            ], 404);
        }
    }

    /**
     * Calculate SSS Contribution based on 2025 rates
     */
    private function calculateSSS($salary)
    {
        // SSS Contribution Table 2025 - Maximum Monthly Salary Credit: ₱35,000
        if ($salary <= 4250) return 180;
        elseif ($salary <= 4750) return 202.50;
        elseif ($salary <= 5250) return 225;
        elseif ($salary <= 5750) return 247.50;
        elseif ($salary <= 6250) return 270;
        elseif ($salary <= 6750) return 292.50;
        elseif ($salary <= 7250) return 315;
        elseif ($salary <= 7750) return 337.50;
        elseif ($salary <= 8250) return 360;
        elseif ($salary <= 8750) return 382.50;
        elseif ($salary <= 9250) return 405;
        elseif ($salary <= 9750) return 427.50;
        elseif ($salary <= 10250) return 450;
        elseif ($salary <= 10750) return 472.50;
        elseif ($salary <= 11250) return 495;
        elseif ($salary <= 11750) return 517.50;
        elseif ($salary <= 12250) return 540;
        elseif ($salary <= 12750) return 562.50;
        elseif ($salary <= 13250) return 585;
        elseif ($salary <= 13750) return 607.50;
        elseif ($salary <= 14250) return 630;
        elseif ($salary <= 14750) return 652.50;
        elseif ($salary <= 15250) return 675;
        elseif ($salary <= 15750) return 697.50;
        elseif ($salary <= 16250) return 720;
        elseif ($salary <= 16750) return 742.50;
        elseif ($salary <= 17250) return 765;
        elseif ($salary <= 17750) return 787.50;
        elseif ($salary <= 18250) return 810;
        elseif ($salary <= 18750) return 832.50;
        elseif ($salary <= 19250) return 855;
        elseif ($salary <= 19750) return 877.50;
        elseif ($salary <= 20250) return 900;
        elseif ($salary <= 20750) return 922.50;
        elseif ($salary <= 21250) return 945;
        elseif ($salary <= 21750) return 967.50;
        elseif ($salary <= 22250) return 990;
        elseif ($salary <= 22750) return 1012.50;
        elseif ($salary <= 23250) return 1035;
        elseif ($salary <= 23750) return 1057.50;
        elseif ($salary <= 24250) return 1080;
        elseif ($salary <= 24750) return 1102.50;
        elseif ($salary <= 25250) return 1125;
        elseif ($salary <= 25750) return 1147.50;
        elseif ($salary <= 26250) return 1170;
        elseif ($salary <= 26750) return 1192.50;
        elseif ($salary <= 27250) return 1215;
        elseif ($salary <= 27750) return 1237.50;
        elseif ($salary <= 28250) return 1260;
        elseif ($salary <= 28750) return 1282.50;
        elseif ($salary <= 29250) return 1305;
        elseif ($salary <= 29750) return 1327.50;
        else return 1350; // Maximum for salaries above ₱29,750 (updated to ₱35,000 in 2025)
    }

    /**
     * Calculate PhilHealth Contribution based on 2025 rates
     */
    private function calculatePhilHealth($salary)
    {
        // PhilHealth 2025: 5% total (2.5% employee share)
        // Floor: ₱10,000, Ceiling: ₱100,000
        $baseSalary = min(max($salary, 10000), 100000);
        return $baseSalary * 0.025; // Employee share only
    }

    /**
     * Calculate Pag-IBIG Contribution based on 2025 rates
     */
    private function calculatePagibig($salary)
    {
        // Pag-IBIG 2025 rates
        if ($salary <= 1500) return $salary * 0.01; // 1%
        elseif ($salary <= 5000) return $salary * 0.02; // 2%
        else return 100; // Maximum for salaries above ₱5,000
    }

    /**
     * Calculate Income Tax using OpenAI for real-time accuracy
     * Falls back to local calculation if OpenAI is unavailable
     */
    private function calculateIncomeTax($taxableIncome)
    {
        // For now, use only local calculation to avoid JSON parsing issues
        return $this->calculateIncomeTaxLocal($taxableIncome);
        
        // Try OpenAI first for real-time tax calculation
        try {
            $openaiApiKey = env('OPENAI_API_KEY');
            if ($openaiApiKey && $openaiApiKey !== 'your_openai_api_key_here' && env('OPENAI_ENABLED', true)) {
                return $this->calculateIncomeTaxWithOpenAI($taxableIncome);
            }
        } catch (\Exception $e) {
            // Log error and fall back to local calculation
            \Log::warning('OpenAI tax calculation failed, using local calculation: ' . $e->getMessage());
        }
        
        // Fallback to local calculation
        return $this->calculateIncomeTaxLocal($taxableIncome);
    }
    
    /**
     * Calculate Income Tax using OpenAI API
     */
    private function calculateIncomeTaxWithOpenAI($taxableIncome)
    {
        $client = OpenAI::client(env('OPENAI_API_KEY'), env('OPENAI_ORGANIZATION_ID'));
        
        $prompt = "Calculate the Philippine income tax for a monthly taxable income of ₱" . number_format($taxableIncome, 2) . 
                  " using the latest BIR tax rates (TRAIN Law PIT Schedule 2, effective Jan 1, 2023)." .
                  "\n\nTax brackets (annual to monthly conversion):" .
                  "\n- ≤ ₱20,833/month: 0%" .
                  "\n- ₱20,834 - ₱33,333/month: 15% of excess over ₱20,833" .
                  "\n- ₱33,334 - ₱66,667/month: ₱1,875 + 20% of excess over ₱33,333" .
                  "\n- ₱66,668 - ₱166,667/month: ₱8,542 + 25% of excess over ₱66,667" .
                  "\n- ₱166,668 - ₱666,667/month: ₱33,542 + 30% of excess over ₱166,667" .
                  "\n- > ₱666,667/month: ₱183,542 + 35% of excess over ₱666,667" .
                  "\n\nPlease provide only the numerical tax amount in pesos, without any additional text or symbols.";
        
        $response = $client->chat()->create([
            'model' => env('OPENAI_DEFAULT_MODEL', 'gpt-3.5-turbo'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a Philippine tax calculation expert. Provide only numerical answers.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'max_tokens' => 50,
            'temperature' => 0,
        ]);
        
        $taxAmount = floatval(str_replace(['₱', ',', ' '], '', $response->choices[0]->message->content));
        
        // Validate the result is reasonable
        if ($taxAmount >= 0 && $taxAmount <= $taxableIncome) {
            return round($taxAmount, 2);
        }
        
        // If OpenAI returns invalid result, fall back to local calculation
        return $this->calculateIncomeTaxLocal($taxableIncome);
    }
    
    /**
     * Calculate Income Tax locally (fallback method)
     * Based on latest BIR rates (2023 onwards)
     */
    private function calculateIncomeTaxLocal($taxableIncome)
    {
        // Convert annual tax table to monthly computation
        // Annual PHP 250,000 = Monthly PHP 20,833
        // Annual PHP 400,000 = Monthly PHP 33,333
        // Annual PHP 800,000 = Monthly PHP 66,667
        // Annual PHP 2,000,000 = Monthly PHP 166,667
        // Annual PHP 8,000,000 = Monthly PHP 666,667
        
        if ($taxableIncome <= 20833) {
            return 0; // 0% for compensation income not over ₱20,833/month
        } elseif ($taxableIncome <= 33333) {
            // 15% of the excess over ₱20,833
            return ($taxableIncome - 20833) * 0.15;
        } elseif ($taxableIncome <= 66667) {
            // ₱1,875 + 20% of the excess over ₱33,333
            // (Annual: ₱22,500 ÷ 12 = ₱1,875)
            return 1875 + ($taxableIncome - 33333) * 0.20;
        } elseif ($taxableIncome <= 166667) {
            // ₱8,542 + 25% of the excess over ₱66,667
            // (Annual: ₱102,500 ÷ 12 = ₱8,541.67, rounded to ₱8,542)
            return 8542 + ($taxableIncome - 66667) * 0.25;
        } elseif ($taxableIncome <= 666667) {
            // ₱33,542 + 30% of the excess over ₱166,667
            // (Annual: ₱402,500 ÷ 12 = ₱33,541.67, rounded to ₱33,542)
            return 33542 + ($taxableIncome - 166667) * 0.30;
        } else {
            // ₱183,542 + 35% of the excess over ₱666,667
            // (Annual: ₱2,202,500 ÷ 12 = ₱183,541.67, rounded to ₱183,542)
            return 183542 + ($taxableIncome - 666667) * 0.35;
        }
    }

    /**
     * Display attendance record page.
     *
     * @return \Illuminate\View\View
     */
    public function attendanceRecord()
    {
        return view('hr4.payroll.attendance-record');
    }

    /**
     * Display payslips page.
     *
     * @return \Illuminate\View\View
     */
    public function payslips()
    {
        return view('hr4.payroll.payslips');
    }

    /**
     * Display disbursements page.
     *
     * @return \Illuminate\View\View
     */
    public function disbursements()
    {
        return view('hr4.payroll.disbursements');
    }

    /**
     * Display payroll approval page.
     *
     * @return \Illuminate\View\View
     */
    public function approval()
    {
        return view('hr4.payroll.approval');
    }
}
