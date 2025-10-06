<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Payroll\EmployeePayrollController;
use App\Http\Controllers\Payroll\DisbursementController;
use App\Http\Controllers\Payroll\PayslipController;
use App\Http\Controllers\CoreHuman\EmployeesController;
use App\Http\Controllers\CoreHuman\DepartmentsController;
use App\Http\Controllers\CoreHuman\AccountsController;
use App\Http\Controllers\Benefits\BenefitPlansController;
use App\Http\Controllers\Benefits\EmployeeBenefitsController;
use App\Http\Controllers\Analytics\HrAnalyticsController;
use App\Http\Controllers\Compensation\CompensationController;
use App\Http\Controllers\Compensation\LeaveController;

Route::get('/', function () {
    return view('hr4.dashboard');
})->name('dashboard');

// Departments page (Core Human)
Route::get('/departments', [DepartmentsController::class, 'index'])->name('departments.index');
Route::post('/departments', [DepartmentsController::class, 'store'])->name('departments.store');

Route::post('/requisitions', [\App\Http\Controllers\CoreHuman\RequisitionsController::class, 'store'])->name('requisitions.store');
Route::post('/requisitions/quick-add', [\App\Http\Controllers\CoreHuman\RequisitionsController::class, 'quickAdd'])->name('requisitions.quick_add');

Route::get('/employees', [EmployeesController::class, 'index'])->name('employees.index');
// Accepted Contracts removed; flow simplified to employees only

// HR Analytics
Route::get('/hr-analytics', [HrAnalyticsController::class, 'index'])->name('hr_analytics.index');

// Compensation Planning
Route::get('/compensation', [CompensationController::class, 'index'])->name('compensation.index');
Route::post('/compensation', [CompensationController::class, 'store'])->name('compensation.store');
Route::get('/compensation/leaves', [LeaveController::class, 'index'])->name('compensation.leaves');
Route::post('/compensation/leaves', [LeaveController::class, 'store'])->name('compensation.leaves.store');
Route::post('/compensation/leaves/update', [LeaveController::class, 'update'])->name('compensation.leaves.update');
Route::post('/compensation/leaves/delete', [LeaveController::class, 'delete'])->name('compensation.leaves.delete');

// Benefits (HMO/Benefits)
Route::get('/benefits/plans', [BenefitPlansController::class, 'index'])->name('benefits.plans');
Route::post('/benefits/plans', [BenefitPlansController::class, 'store'])->name('benefits.plans.store');
Route::get('/benefits/employees', [EmployeeBenefitsController::class, 'index'])->name('benefits.employee');
Route::post('/benefits/employees', [EmployeeBenefitsController::class, 'store'])->name('benefits.employee.store');

// Accounts
Route::get('/accounts', [AccountsController::class, 'index'])->name('accounts.index');
Route::post('/accounts', [AccountsController::class, 'store'])->name('accounts.store');
Route::post('/accounts/from-employee', [AccountsController::class, 'fromEmployee'])->name('accounts.from_employee');
Route::post('/accounts/update', [AccountsController::class, 'update'])->name('accounts.update');
Route::post('/accounts/block', [AccountsController::class, 'block'])->name('accounts.block');
Route::post('/accounts/delete', [AccountsController::class, 'delete'])->name('accounts.delete');

// Payroll
Route::get('/payroll/employee', [EmployeePayrollController::class, 'index'])->name('payroll.employee');
Route::post('/payroll/employee', [EmployeePayrollController::class, 'store'])->name('payroll.employee.store');
Route::get('/payroll/disbursements', [DisbursementController::class, 'index'])->name('payroll.disbursements');
Route::post('/payroll/disbursements', [DisbursementController::class, 'store'])->name('payroll.disbursements.store');
Route::get('/payroll/payslips', [\App\Http\Controllers\Payroll\PayslipController::class, 'index'])->name('payroll.payslips');
Route::post('/payroll/payslips', [\App\Http\Controllers\Payroll\PayslipController::class, 'store'])->name('payroll.payslips.store');
