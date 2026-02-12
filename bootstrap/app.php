<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'salary.security' => \App\Http\Middleware\SalaryAdjustmentSecurity::class,
            'accounts.security' => \App\Http\Middleware\AccountsSecurity::class,
            'employee.details.security' => \App\Http\Middleware\EmployeeDetailsSecurity::class,
            'salary.computation.security' => \App\Http\Middleware\SalaryComputationSecurity::class,
            'budget.request.security' => \App\Http\Middleware\BudgetRequestSecurity::class,
            'payment.request.security' => \App\Http\Middleware\PaymentRequestSecurity::class,
            'net.payout.security' => \App\Http\Middleware\NetPayoutSecurity::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
