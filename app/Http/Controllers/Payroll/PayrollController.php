<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
