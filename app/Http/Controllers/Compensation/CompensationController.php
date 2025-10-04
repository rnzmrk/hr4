<?php

namespace App\Http\Controllers\Compensation;

use App\Http\Controllers\Controller;
use App\Models\CompensationAdjustment;
use App\Models\Employee;
use Illuminate\Http\Request;

class CompensationController extends Controller
{
    public function index()
    {
        $employees = Employee::orderBy('name')->get(['id','name']);

        $adjustments = CompensationAdjustment::with('employee')
            ->latest('effective_date')
            ->limit(200)
            ->get()
            ->map(function (CompensationAdjustment $a) {
                return [
                    'id' => $a->id,
                    'employee' => optional($a->employee)->name ?? '—',
                    'effective_date' => optional($a->effective_date)->format('Y-m-d'),
                    'applied_rate_type' => $a->applied_rate_type,
                    'adjustment_type' => $a->adjustment_type,
                    'value' => (float)$a->value,
                    'reason' => $a->reason,
                    'status' => $a->status,
                ];
            })->toArray();

        return view('hr4.compensation.index', compact('employees','adjustments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'effective_date' => 'required|date',
            'applied_rate_type' => 'required|in:hourly,monthly',
            'adjustment_type' => 'required|in:set,increase,decrease',
            'value' => 'required|numeric',
            'reason' => 'nullable|string',
            'status' => 'required|in:approved,pending,rejected',
        ]);

        CompensationAdjustment::create($data);
        return redirect()->route('compensation.index')->with('status', 'Compensation adjustment saved.');
    }
}
