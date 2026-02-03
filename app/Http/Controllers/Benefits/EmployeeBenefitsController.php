<?php

namespace App\Http\Controllers\Benefits;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeBenefit;
use App\Models\Reward;
use Illuminate\Http\Request;

class EmployeeBenefitsController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeBenefit::with(['employee', 'reward']);

        if ($request->filled('search')) {
            $search = strtolower($request->input('search'));
            $query->whereHas('employee', function ($q) use ($search) {
                $q->whereRaw('LOWER(CONCAT(first_name, " ", last_name)) LIKE ?', ["%{$search}%"]);
            });
        }

        $employees = Employee::orderBy('last_name')->orderBy('first_name')->get(['id', 'first_name', 'last_name']);
        $rewards = Reward::orderBy('name')->get(['id', 'name', 'benefits']);
        $assignments = $query->latest()->get();

        return view('hr4.benefits.employee_benefits', compact('employees', 'rewards', 'assignments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'reward_id' => 'required|exists:rewards,id',
        ]);

        EmployeeBenefit::updateOrCreate(
            ['employee_id' => $data['employee_id'], 'reward_id' => $data['reward_id']],
            []
        );

        return back()->with('status', 'Reward assigned successfully.');
    }
}
