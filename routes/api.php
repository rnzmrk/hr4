<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Employee API routes
Route::get('/employees', [App\Http\Controllers\Api\EmployeeController::class, 'index']);
Route::post('/employees', [App\Http\Controllers\Api\EmployeeController::class, 'store'])->name('api.employees.store');

// Department API routes
Route::get('/departments', [App\Http\Controllers\Api\DepartmentController::class, 'index']);
Route::get('/departments/{id}', [App\Http\Controllers\Api\DepartmentController::class, 'show']);

// Account API routes
Route::get('/accounts', [App\Http\Controllers\Api\AccountController::class, 'index']);
Route::get('/accounts/system', [App\Http\Controllers\Api\AccountController::class, 'systemAccounts']);
Route::get('/accounts/ess', [App\Http\Controllers\Api\AccountController::class, 'essAccounts']);
Route::patch('/accounts/system/{id}', [App\Http\Controllers\Api\AccountController::class, 'patchSystemAccount']);
Route::patch('/accounts/ess/{id}', [App\Http\Controllers\Api\AccountController::class, 'patchEssAccount']);
