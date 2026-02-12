<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\CoreHuman\EmployeesController;
use App\Http\Controllers\CoreHuman\DepartmentsController;
use App\Http\Controllers\CoreHuman\AccountsController;
use App\Http\Controllers\Benefits\BenefitPlansController;
use App\Http\Controllers\Benefits\EmployeeBenefitsController;
use App\Http\Controllers\Benefits\RewardsController;
use App\Http\Controllers\Analytics\HrAnalyticsController;
use App\Http\Controllers\Compensation\CompensationController;
use App\Http\Controllers\Compensation\LeaveController;
use App\Http\Controllers\Compensation\SalaryAdjustmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Payroll\PaymentRequestController;
use App\Http\Controllers\Payroll\EmployeeDetailsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NetPayoutController;
use App\Http\Controllers\VehicleReservationController;
use App\Http\Controllers\WebsiteController;

// Public website routes
Route::get('/', [WebsiteController::class, 'home'])->name('home');
Route::get('/about', [WebsiteController::class, 'about'])->name('about');
Route::get('/careers', [WebsiteController::class, 'careers'])->name('careers');
Route::get('/contact', [WebsiteController::class, 'contact'])->name('contact');
Route::get('/login-options', [WebsiteController::class, 'loginOptions'])->name('login-options');
Route::get('/apply-now/{id?}', [WebsiteController::class, 'applyNow'])->name('apply-now');

// Authentication routes (public)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected application routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/payroll-cost', [DashboardController::class, 'payrollCostData'])->name('dashboard.payroll-cost');
    Route::get('/dashboard/salary-distribution', [DashboardController::class, 'salaryDistributionData'])->name('dashboard.salary-distribution');
    Route::get('/dashboard/salary-by-department', [DashboardController::class, 'salaryByDepartmentData'])->name('dashboard.salary-by-department');
    Route::get('/dashboard/reward-distribution', [DashboardController::class, 'rewardDistributionData'])->name('dashboard.reward-distribution');

    // Departments page (Core Human)
    Route::get('/departments', [DepartmentsController::class, 'index'])->name('departments.index');
    Route::post('/departments', [DepartmentsController::class, 'store'])->name('departments.store');
    Route::get('/departments/{departmentName}/details', [DepartmentsController::class, 'getDepartmentDetails'])->name('departments.details');
    Route::get('/departments/{departmentName}/show', [DepartmentsController::class, 'show'])->name('departments.show');

Route::post('/requisitions', [\App\Http\Controllers\CoreHuman\RequisitionsController::class, 'store'])->name('requisitions.store');
Route::post('/requisitions/quick-add', [\App\Http\Controllers\CoreHuman\RequisitionsController::class, 'quickAdd'])->name('requisitions.quick_add');

Route::get('/employees', [EmployeesController::class, 'index'])->name('employees.index');
Route::get('/employees/{id}', [EmployeesController::class, 'show'])->name('employees.show');
// Accepted Contracts removed; flow simplified to employees only

    // HR Analytics
    Route::get('/hr-analytics', [HrAnalyticsController::class, 'index'])->name('hr_analytics.index');

    // Compensation Planning
    Route::get('/compensation', [CompensationController::class, 'index'])->name('compensation.index');
    Route::patch('/compensation/{id}', [CompensationController::class, 'update'])->name('compensation.update');
    Route::get('/compensation/api/rewards', [CompensationController::class, 'fetchGivenRewards'])->name('compensation.api.rewards');
    Route::get('/compensation/leaves', [LeaveController::class, 'index'])->name('compensation.leaves');
    Route::post('/compensation/leaves', [LeaveController::class, 'store'])->name('compensation.leaves.store');
    Route::post('/compensation/leaves/update', [LeaveController::class, 'update'])->name('compensation.leaves.update');
    Route::post('/compensation/leaves/delete', [LeaveController::class, 'delete'])->name('compensation.leaves.delete');
    Route::get('/compensation/potential', [CompensationController::class, 'potential'])->name('compensation.potential');

    // Salary Adjustment
    Route::get('/salary-adjustment', [SalaryAdjustmentController::class, 'index'])->name('salary.adjustment.index')->middleware('salary.security');
    Route::put('/salary-adjustment/{id}', [SalaryAdjustmentController::class, 'update'])->name('salary.adjustment.update')->middleware('salary.security');
    Route::post('/salary-adjustment/verify', function (Request $request) {
        // Handle verification form submission
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = trim($request->input('email'));
        $password = trim($request->input('password'));

        // Verify credentials against the API
        $response = Http::withoutVerifying()->get('https://hr4.jetlougetravels-ph.com/api/accounts');

        if (!$response->successful()) {
            return back()->withErrors(['verification' => 'Unable to contact accounts service. Please try again later.']);
        }

        $payload = $response->json();
        $systemAccounts = data_get($payload, 'system_accounts');
        if (!is_array($systemAccounts)) {
            $systemAccounts = data_get($payload, 'data.system_accounts', []);
        }

        $matched = collect($systemAccounts)->first(function ($account) use ($email, $password) {
            $employee = $account['employee'] ?? null;
            $apiEmail = isset($employee['email']) ? trim($employee['email']) : '';
            $apiPassword = isset($account['password']) ? trim($account['password']) : '';

            return ($account['account_type'] ?? null) === 'system'
                && !($account['blocked'] ?? false)
                && $employee
                && strcasecmp($apiEmail, $email) === 0
                && $apiPassword === $password;
        });

        if ($matched) {
            // Mark as verified for this session
            $request->session()->put('salary_adjustment_verified', true);
            $request->session()->put('salary_adjustment_verified_at', now());
            
            return redirect()->route('salary.adjustment.index');
        }

        return back()->withErrors(['verification' => 'The provided credentials do not match our records.']);
    })->name('salary.adjustment.verify');
    Route::post('/salary-adjustment/clear-verification', function (Request $request) {
        $request->session()->forget(['salary_adjustment_verified', 'salary_adjustment_verified_at']);
        return back()->with('status', 'Security verification cleared. You will need to verify again on next access.');
    })->name('salary.adjustment.clear_verification');

    // Benefits (HMO/Benefits)
    Route::get('/benefits/plans', [BenefitPlansController::class, 'index'])->name('benefits.plans');
    Route::post('/benefits/plans', [BenefitPlansController::class, 'store'])->name('benefits.plans.store');
    Route::get('/benefits/employees', [EmployeeBenefitsController::class, 'index'])->name('benefits.employee');
    Route::post('/benefits/employees', [EmployeeBenefitsController::class, 'store'])->name('benefits.employee.store');
    Route::get('/benefits/rewards', [RewardsController::class, 'index'])->name('benefits.rewards');
    Route::post('/benefits/rewards', [RewardsController::class, 'store'])->name('benefits.rewards.store');

    // Accounts
    Route::get('/accounts', [AccountsController::class, 'index'])->name('accounts.index')->middleware('accounts.security');
    Route::post('/accounts', [AccountsController::class, 'store'])->name('accounts.store')->middleware('accounts.security');
    Route::post('/accounts/from-employee', [AccountsController::class, 'fromEmployee'])->name('accounts.from_employee')->middleware('accounts.security');
    Route::post('/accounts/update', [AccountsController::class, 'update'])->name('accounts.update')->middleware('accounts.security');
    Route::post('/accounts/block', [AccountsController::class, 'block'])->name('accounts.block')->middleware('accounts.security');
    Route::post('/accounts/delete', [AccountsController::class, 'delete'])->name('accounts.delete')->middleware('accounts.security');
    Route::post('/accounts/verify', function (Request $request) {
        // Handle verification form submission
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = trim($request->input('email'));
        $password = trim($request->input('password'));

        // Verify credentials against the API
        $response = Http::withoutVerifying()->get('https://hr4.jetlougetravels-ph.com/api/accounts');

        if (!$response->successful()) {
            return back()->withErrors(['verification' => 'Unable to contact accounts service. Please try again later.']);
        }

        $payload = $response->json();
        $systemAccounts = data_get($payload, 'system_accounts');
        if (!is_array($systemAccounts)) {
            $systemAccounts = data_get($payload, 'data.system_accounts', []);
        }

        $matched = collect($systemAccounts)->first(function ($account) use ($email, $password) {
            $employee = $account['employee'] ?? null;
            $apiEmail = isset($employee['email']) ? trim($employee['email']) : '';
            $apiPassword = isset($account['password']) ? trim($account['password']) : '';

            return ($account['account_type'] ?? null) === 'system'
                && !($account['blocked'] ?? false)
                && $employee
                && strcasecmp($apiEmail, $email) === 0
                && $apiPassword === $password;
        });

        if ($matched) {
            // Mark as verified for this session
            $request->session()->put('accounts_verified', true);
            $request->session()->put('accounts_verified_at', now());
            
            return redirect()->route('accounts.index');
        }

        return back()->withErrors(['verification' => 'The provided credentials do not match our records.']);
    })->name('accounts.verify');
    Route::post('/accounts/clear-verification', function (Request $request) {
        $request->session()->forget(['accounts_verified', 'accounts_verified_at']);
        return back()->with('status', 'Security verification cleared. You will need to verify again on next access.');
    })->name('accounts.clear_verification');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change_password');

    // Item Requests
    Route::get('/request', [\App\Http\Controllers\RequestController::class, 'index'])->name('request.index');

    // Vehicle Reservation
    Route::get('/vehicle-reservation', [VehicleReservationController::class, 'index'])->name('vehicle.reservation');

    // Payroll
    Route::get('/payroll/employee-details', [EmployeeDetailsController::class, 'index'])->name('payroll.employee-details')->middleware('employee.details.security');
    Route::get('/payroll/employee-details/{id}', [EmployeeDetailsController::class, 'show'])->name('payroll.employee-details.show')->middleware('employee.details.security');
    Route::get('/payroll/employee-details/export', [EmployeeDetailsController::class, 'exportExcel'])->name('payroll.employee-details.export')->middleware('employee.details.security');
    
    // Employee Details verification routes
    Route::post('/payroll/employee-details/verify', function (Request $request) {
        // Handle verification form submission
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = trim($request->input('email'));
        $password = trim($request->input('password'));

        // Verify credentials against the API
        $response = Http::withoutVerifying()->get('https://hr4.jetlougetravels-ph.com/api/accounts');

        if (!$response->successful()) {
            return back()->withErrors(['verification' => 'Unable to contact accounts service. Please try again later.']);
        }

        $payload = $response->json();
        $systemAccounts = data_get($payload, 'system_accounts');
        if (!is_array($systemAccounts)) {
            $systemAccounts = data_get($payload, 'data.system_accounts', []);
        }

        $matched = collect($systemAccounts)->first(function ($account) use ($email, $password) {
            $employee = $account['employee'] ?? null;
            $apiEmail = isset($employee['email']) ? trim($employee['email']) : '';
            $apiPassword = isset($account['password']) ? trim($account['password']) : '';

            return ($account['account_type'] ?? null) === 'system'
                && !($account['blocked'] ?? false)
                && $employee
                && strcasecmp($apiEmail, $email) === 0
                && $apiPassword === $password;
        });

        if ($matched) {
            // Mark as verified for this session
            $request->session()->put('employee_details_verified', true);
            $request->session()->put('employee_details_verified_at', now());
            
            return redirect()->route('payroll.employee-details');
        }

        return back()->withErrors(['verification' => 'The provided credentials do not match our records.']);
    })->name('payroll.employee-details.verify');
    Route::post('/payroll/employee-details/clear-verification', function (Request $request) {
        $request->session()->forget(['employee_details_verified', 'employee_details_verified_at']);
        return back()->with('status', 'Security verification cleared. You will need to verify again on next access.');
    })->name('payroll.employee-details.clear_verification');

    // Payment Requests
    Route::get('/payroll/payment-requests', [PaymentRequestController::class, 'index'])->name('payroll.payment-requests.index')->middleware('payment.request.security');
    Route::get('/payroll/payment-requests/create', [PaymentRequestController::class, 'create'])->name('payroll.payment-requests.create')->middleware('payment.request.security');
    Route::post('/payroll/payment-requests', [PaymentRequestController::class, 'store'])->name('payroll.payment-requests.store')->middleware('payment.request.security');
    Route::get('/payroll/payment-requests/{paymentRequest}', [PaymentRequestController::class, 'show'])->name('payroll.payment-requests.show')->middleware('payment.request.security');
    
    // Payment Request verification routes
    Route::post('/payroll/payment-requests/verify', function (Request $request) {
        // Handle verification form submission
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = trim($request->input('email'));
        $password = trim($request->input('password'));

        // Verify credentials against the API
        $response = Http::withoutVerifying()->get('https://hr4.jetlougetravels-ph.com/api/accounts');

        if (!$response->successful()) {
            return back()->withErrors(['verification' => 'Unable to contact accounts service. Please try again later.']);
        }

        $payload = $response->json();
        $systemAccounts = data_get($payload, 'system_accounts');
        if (!is_array($systemAccounts)) {
            $systemAccounts = data_get($payload, 'data.system_accounts', []);
        }

        $matched = collect($systemAccounts)->first(function ($account) use ($email, $password) {
            $employee = $account['employee'] ?? null;
            $apiEmail = isset($employee['email']) ? trim($employee['email']) : '';
            $apiPassword = isset($account['password']) ? trim($account['password']) : '';

            return ($account['account_type'] ?? null) === 'system'
                && !($account['blocked'] ?? false)
                && $employee
                && strcasecmp($apiEmail, $email) === 0
                && $apiPassword === $password;
        });

        if ($matched) {
            // Mark as verified for this session
            $request->session()->put('payment_request_verified', true);
            $request->session()->put('payment_request_verified_at', now());
            
            return redirect()->route('payroll.payment-requests.index');
        }

        return back()->withErrors(['verification' => 'The provided credentials do not match our records.']);
    })->name('payroll.payment-requests.verify');
    Route::post('/payroll/payment-requests/clear-verification', function (Request $request) {
        $request->session()->forget(['payment_request_verified', 'payment_request_verified_at']);
        return back()->with('status', 'Security verification cleared. You will need to verify again on next access.');
    })->name('payroll.payment-requests.clear_verification');

    Route::get('/payroll/salary-computation', [\App\Http\Controllers\Payroll\PayrollController::class, 'salaryComputation'])->name('payroll.salary-computation')->middleware('salary.computation.security');
    Route::post('/payroll/search-employees', [\App\Http\Controllers\Payroll\PayrollController::class, 'searchEmployees'])->name('payroll.search-employees')->middleware('salary.computation.security');
    Route::post('/payroll/calculate-payroll', [\App\Http\Controllers\Payroll\PayrollController::class, 'calculatePayroll'])->name('payroll.calculate-payroll')->middleware('salary.computation.security');
    Route::post('/payroll/save-payroll', [\App\Http\Controllers\Payroll\PayrollController::class, 'savePayroll'])->name('payroll.save-payroll')->middleware('salary.computation.security');
    Route::get('/payroll/get-payroll-records', [\App\Http\Controllers\Payroll\PayrollController::class, 'getPayrollRecords'])->name('payroll.get-payroll-records')->middleware('salary.computation.security');
    Route::get('/payroll/get-payroll-details/{id}', [\App\Http\Controllers\Payroll\PayrollController::class, 'getPayrollDetails'])->name('payroll.get-payroll-details')->middleware('salary.computation.security');
    
    // Salary Computation verification routes
    Route::post('/payroll/salary-computation/verify', function (Request $request) {
        // Handle verification form submission
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = trim($request->input('email'));
        $password = trim($request->input('password'));

        // Verify credentials against the API
        $response = Http::withoutVerifying()->get('https://hr4.jetlougetravels-ph.com/api/accounts');

        if (!$response->successful()) {
            return back()->withErrors(['verification' => 'Unable to contact accounts service. Please try again later.']);
        }

        $payload = $response->json();
        $systemAccounts = data_get($payload, 'system_accounts');
        if (!is_array($systemAccounts)) {
            $systemAccounts = data_get($payload, 'data.system_accounts', []);
        }

        $matched = collect($systemAccounts)->first(function ($account) use ($email, $password) {
            $employee = $account['employee'] ?? null;
            $apiEmail = isset($employee['email']) ? trim($employee['email']) : '';
            $apiPassword = isset($account['password']) ? trim($account['password']) : '';

            return ($account['account_type'] ?? null) === 'system'
                && !($account['blocked'] ?? false)
                && $employee
                && strcasecmp($apiEmail, $email) === 0
                && $apiPassword === $password;
        });

        if ($matched) {
            // Mark as verified for this session
            $request->session()->put('salary_computation_verified', true);
            $request->session()->put('salary_computation_verified_at', now());
            
            return redirect()->route('payroll.salary-computation');
        }

        return back()->withErrors(['verification' => 'The provided credentials do not match our records.']);
    })->name('payroll.salary-computation.verify');
    Route::post('/payroll/salary-computation/clear-verification', function (Request $request) {
        $request->session()->forget(['salary_computation_verified', 'salary_computation_verified_at']);
        return back()->with('status', 'Security verification cleared. You will need to verify again on next access.');
    })->name('payroll.salary-computation.clear_verification');
    Route::get('/payroll/attendance-record', [\App\Http\Controllers\Payroll\PayrollController::class, 'attendanceRecord'])->name('payroll.attendance-record');
    Route::get('/payroll/payslips', [\App\Http\Controllers\Payroll\PayrollController::class, 'payslips'])->name('payroll.payslips');
    Route::get('/payroll/disbursements', [\App\Http\Controllers\Payroll\PayrollController::class, 'disbursements'])->name('payroll.disbursements');
    Route::get('/payroll/approval', [\App\Http\Controllers\Payroll\BudgetRequestController::class, 'index'])->name('payroll.approval');

    // Net Payout
    Route::get('/net-payout', [NetPayoutController::class, 'index'])->name('net-payout.index')->middleware('net.payout.security');
    Route::get('/net-payout/{netPayout}', [NetPayoutController::class, 'show'])->name('net-payout.show')->middleware('net.payout.security');
    Route::post('/net-payout/store', [NetPayoutController::class, 'store'])->name('net-payout.store')->middleware('net.payout.security');
    Route::get('/net-payout/stats', [NetPayoutController::class, 'getSidebarStats'])->name('net-payout.stats')->middleware('net.payout.security');
    Route::get('/net-payout/data', [NetPayoutController::class, 'getPayouts'])->name('net-payout.data')->middleware('net.payout.security');
    
    // Net Payout verification routes
    Route::post('/net-payout/verify', function (Request $request) {
        // Handle verification form submission
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = trim($request->input('email'));
        $password = trim($request->input('password'));

        // Verify credentials against the API
        $response = Http::withoutVerifying()->get('https://hr4.jetlougetravels-ph.com/api/accounts');

        if (!$response->successful()) {
            return back()->withErrors(['verification' => 'Unable to contact accounts service. Please try again later.']);
        }

        $payload = $response->json();
        $systemAccounts = data_get($payload, 'system_accounts');
        if (!is_array($systemAccounts)) {
            $systemAccounts = data_get($payload, 'data.system_accounts', []);
        }

        $matched = collect($systemAccounts)->first(function ($account) use ($email, $password) {
            $employee = $account['employee'] ?? null;
            $apiEmail = isset($employee['email']) ? trim($employee['email']) : '';
            $apiPassword = isset($account['password']) ? trim($account['password']) : '';

            return ($account['account_type'] ?? null) === 'system'
                && !($account['blocked'] ?? false)
                && $employee
                && strcasecmp($apiEmail, $email) === 0
                && $apiPassword === $password;
        });

        if ($matched) {
            // Mark as verified for this session
            $request->session()->put('net_payout_verified', true);
            $request->session()->put('net_payout_verified_at', now());
            
            return redirect()->route('net-payout.index');
        }

        return back()->withErrors(['verification' => 'The provided credentials do not match our records.']);
    })->name('net-payout.verify');
    Route::post('/net-payout/clear-verification', function (Request $request) {
        $request->session()->forget(['net_payout_verified', 'net_payout_verified_at']);
        return back()->with('status', 'Security verification cleared. You will need to verify again on next access.');
    })->name('net-payout.clear_verification');

    // Budget Requests
    Route::prefix('payroll/budget-requests')->name('payroll.budget-requests.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Payroll\BudgetRequestController::class, 'index'])->name('index')->middleware('budget.request.security');
        Route::get('/create', [\App\Http\Controllers\Payroll\BudgetRequestController::class, 'create'])->name('create')->middleware('budget.request.security');
        Route::post('/', [\App\Http\Controllers\Payroll\BudgetRequestController::class, 'store'])->name('store')->middleware('budget.request.security');
        Route::get('/{budgetRequest}', [\App\Http\Controllers\Payroll\BudgetRequestController::class, 'show'])->name('show')->middleware('budget.request.security');
        Route::patch('/{budgetRequest}/status', [\App\Http\Controllers\Payroll\BudgetRequestController::class, 'updateStatus'])->name('update-status')->middleware('budget.request.security');
        Route::delete('/{budgetRequest}', [\App\Http\Controllers\Payroll\BudgetRequestController::class, 'destroy'])->name('destroy')->middleware('budget.request.security');
    });
    
    // Budget Request verification routes
    Route::post('/payroll/budget-requests/verify', function (Request $request) {
        // Handle verification form submission
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = trim($request->input('email'));
        $password = trim($request->input('password'));

        // Verify credentials against the API
        $response = Http::withoutVerifying()->get('https://hr4.jetlougetravels-ph.com/api/accounts');

        if (!$response->successful()) {
            return back()->withErrors(['verification' => 'Unable to contact accounts service. Please try again later.']);
        }

        $payload = $response->json();
        $systemAccounts = data_get($payload, 'system_accounts');
        if (!is_array($systemAccounts)) {
            $systemAccounts = data_get($payload, 'data.system_accounts', []);
        }

        $matched = collect($systemAccounts)->first(function ($account) use ($email, $password) {
            $employee = $account['employee'] ?? null;
            $apiEmail = isset($employee['email']) ? trim($employee['email']) : '';
            $apiPassword = isset($account['password']) ? trim($account['password']) : '';

            return ($account['account_type'] ?? null) === 'system'
                && !($account['blocked'] ?? false)
                && $employee
                && strcasecmp($apiEmail, $email) === 0
                && $apiPassword === $password;
        });

        if ($matched) {
            // Mark as verified for this session
            $request->session()->put('budget_request_verified', true);
            $request->session()->put('budget_request_verified_at', now());
            
            return redirect()->route('payroll.budget-requests.index');
        }

        return back()->withErrors(['verification' => 'The provided credentials do not match our records.']);
    })->name('payroll.budget-requests.verify');
    Route::post('/payroll/budget-requests/clear-verification', function (Request $request) {
        $request->session()->forget(['budget_request_verified', 'budget_request_verified_at']);
        return back()->with('status', 'Security verification cleared. You will need to verify again on next access.');
    })->name('payroll.budget-requests.clear_verification');
});


use App\Http\Controllers\PdoController;

// PDO demo routes remain public
Route::get('/pdo-sessions', [PdoController::class, 'getSessions']);
Route::post('/pdo-sessions', [PdoController::class, 'addSession']);

