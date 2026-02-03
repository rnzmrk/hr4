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
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return view('hr4.dashboard');
})->name('dashboard')->middleware('auth');

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

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

// Salary Adjustment
Route::get('/salary-adjustment', [SalaryAdjustmentController::class, 'index'])->name('salary.adjustment.index');
Route::put('/salary-adjustment/{id}', [SalaryAdjustmentController::class, 'update'])->name('salary.adjustment.update');

// Benefits (HMO/Benefits)
Route::get('/benefits/plans', [BenefitPlansController::class, 'index'])->name('benefits.plans');
Route::post('/benefits/plans', [BenefitPlansController::class, 'store'])->name('benefits.plans.store');
Route::get('/benefits/employees', [EmployeeBenefitsController::class, 'index'])->name('benefits.employee');
Route::post('/benefits/employees', [EmployeeBenefitsController::class, 'store'])->name('benefits.employee.store');
Route::get('/benefits/rewards', [RewardsController::class, 'index'])->name('benefits.rewards');
Route::post('/benefits/rewards', [RewardsController::class, 'store'])->name('benefits.rewards.store');

// Accounts
Route::get('/accounts', [AccountsController::class, 'index'])->name('accounts.index');
Route::post('/accounts', [AccountsController::class, 'store'])->name('accounts.store');
Route::post('/accounts/from-employee', [AccountsController::class, 'fromEmployee'])->name('accounts.from_employee');
Route::post('/accounts/update', [AccountsController::class, 'update'])->name('accounts.update');
Route::post('/accounts/block', [AccountsController::class, 'block'])->name('accounts.block');
Route::post('/accounts/delete', [AccountsController::class, 'delete'])->name('accounts.delete');

// Profile Management
Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change_password');

// Item Requests
Route::get('/request', [\App\Http\Controllers\RequestController::class, 'index'])->name('request.index');
Route::get('/api/items', [\App\Http\Controllers\RequestController::class, 'getItems'])->name('request.items');
Route::post('/api/item-request', [\App\Http\Controllers\RequestController::class, 'store'])->name('request.store');

// Payroll
Route::get('/payroll/employee-details', [\App\Http\Controllers\Payroll\PayrollController::class, 'employeeDetails'])->name('payroll.employee-details');
Route::get('/payroll/salary-computation', [\App\Http\Controllers\Payroll\PayrollController::class, 'salaryComputation'])->name('payroll.salary-computation');
Route::get('/payroll/attendance-record', [\App\Http\Controllers\Payroll\AttendanceRecordController::class, 'index'])->name('payroll.attendance-record');
Route::get('/payroll/payslips', [\App\Http\Controllers\Payroll\PayrollController::class, 'payslips'])->name('payroll.payslips');
Route::get('/payroll/disbursements', [\App\Http\Controllers\Payroll\PayrollController::class, 'disbursements'])->name('payroll.disbursements');
Route::get('/payroll/approval', [\App\Http\Controllers\Payroll\BudgetRequestController::class, 'index'])->name('payroll.approval');

// Budget Requests
Route::prefix('payroll/budget-requests')->name('payroll.budget-requests.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Payroll\BudgetRequestController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Payroll\BudgetRequestController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Payroll\BudgetRequestController::class, 'store'])->name('store');
    Route::get('/{budgetRequest}', [\App\Http\Controllers\Payroll\BudgetRequestController::class, 'show'])->name('show');
    Route::patch('/{budgetRequest}/status', [\App\Http\Controllers\Payroll\BudgetRequestController::class, 'updateStatus'])->name('update-status');
    Route::delete('/{budgetRequest}', [\App\Http\Controllers\Payroll\BudgetRequestController::class, 'destroy'])->name('destroy');
});

