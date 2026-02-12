<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SalaryComputationSecurity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user has already verified credentials for this session
        if ($request->session()->get('salary_computation_verified')) {
            return $next($request);
        }

        // Show verification form
        return response()->view('hr4.payroll.salary_computation_verify');
    }
}
