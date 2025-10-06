<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/employees', [App\Http\Controllers\Api\EmployeeController::class, 'store'])->name('api.employees.store');
