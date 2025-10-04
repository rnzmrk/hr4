<?php

namespace App\Http\Controllers\Benefits;

use App\Http\Controllers\Controller;
use App\Models\BenefitPlan;
use Illuminate\Http\Request;

class BenefitPlansController extends Controller
{
    public function index()
    {
        $plans = BenefitPlan::orderBy('name')->get()->toArray();
        return view('hr4.benefits.plans', compact('plans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'rate_type' => 'required|in:monthly,fixed',
            'employee_share' => 'required|numeric|min:0',
            'employer_share' => 'nullable|numeric|min:0',
            'active' => 'nullable|boolean',
        ]);
        $data['active'] = $request->boolean('active', true);
        BenefitPlan::create($data);
        return back()->with('status', 'Benefit plan created.');
    }
}
