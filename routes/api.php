<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Employee API routes
Route::get('/employees', [App\Http\Controllers\Api\EmployeeController::class, 'index']);
Route::post('/employees', [App\Http\Controllers\Api\EmployeeController::class, 'store'])->name('api.employees.store');
Route::post('/employees/{id}/profile', [App\Http\Controllers\Api\EmployeeController::class, 'updateProfile'])->name('api.employees.profile');

// Department API routes
Route::get('/departments', [App\Http\Controllers\Api\DepartmentController::class, 'index']);
Route::get('/departments/{id}', [App\Http\Controllers\Api\DepartmentController::class, 'show']);

// Account API routes
Route::get('/accounts', [App\Http\Controllers\Api\AccountController::class, 'index']);
Route::get('/accounts/system', [App\Http\Controllers\Api\AccountController::class, 'systemAccounts']);
Route::get('/accounts/ess', [App\Http\Controllers\Api\AccountController::class, 'essAccounts']);
Route::patch('/accounts/system/{id}', [App\Http\Controllers\Api\AccountController::class, 'patchSystemAccount']);
Route::patch('/accounts/ess/{id}', [App\Http\Controllers\Api\AccountController::class, 'patchEssAccount']);

// Budget Request API routes
Route::prefix('budget-request')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\BudgetRequestController::class, 'index']);
    Route::post('/', [App\Http\Controllers\Api\BudgetRequestController::class, 'store']);
    Route::patch('/{id}', [App\Http\Controllers\Api\BudgetRequestController::class, 'update']);
    Route::match(['get', 'post'], 'approve', [App\Http\Controllers\Api\BudgetRequestController::class, 'approve']);
    Route::match(['get', 'post'], 'reject', [App\Http\Controllers\Api\BudgetRequestController::class, 'reject']);
    
    // Token management routes
    Route::post('/token/generate', [App\Http\Controllers\Api\BudgetRequestController::class, 'generateToken']);
    Route::post('/token/validate', [App\Http\Controllers\Api\BudgetRequestController::class, 'validateToken']);
    Route::get('/token/info', [App\Http\Controllers\Api\BudgetRequestController::class, 'getTokenInfo']);
});
