<?php

namespace App\Http\Controllers\Benefits;

use App\Http\Controllers\Controller;
use App\Models\BenefitPlan;
use App\Models\Employee;
use Illuminate\Http\Request;

class BenefitPlansController extends Controller
{
    public function index()
    {
        $plans = BenefitPlan::with('employee')->latest('assigned_date')->latest()->get();
        $employees = Employee::orderBy('last_name')->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'position']);

        return view('hr4.benefits.plans', compact('plans', 'employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'type' => 'required|array|min:1',
            'type.*' => ['required','string','max:50','regex:/^[A-Za-z \-]+$/'],
            'rate_type' => 'required|in:monthly,fixed',
            'employer_share' => 'nullable|numeric|min:0',
            'active' => 'nullable|in:0,1,on,off,true,false',
            'employee_id' => 'required|exists:employees,id',
            'assigned_date' => 'nullable|date',
        ]);
        $data['active'] = $request->boolean('active', true);
        $data['assigned_date'] = $data['assigned_date'] ?? now()->toDateString();
        $data['type'] = collect($data['type'])->map(fn($label) => trim($label))->filter()->implode(', ');

        if (empty($data['name'])) {
            $employee = Employee::find($data['employee_id']);
            $data['name'] = $employee ? trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '')) : 'Benefit Plan';
        }

        BenefitPlan::create($data);

        return back()->with('status', 'Benefit plan assigned successfully.');
    }
}
