<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

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
}
