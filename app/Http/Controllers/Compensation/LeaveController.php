<?php

namespace App\Http\Controllers\Compensation;

use App\Http\Controllers\Controller;
use App\Models\LeaveRecord;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LeaveController extends Controller
{
    public function index()
    {
        // Return empty leaves array - no hardcoded data
        $leaves = [];

        return view('hr4.compensation.leaves', compact('leaves'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type' => 'required|string|max:100',
            'is_paid' => 'nullable|boolean',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'hours' => 'nullable|numeric|min:0',
            'status' => 'required|in:approved,pending,rejected',
            'notes' => 'nullable|string',
        ]);
        $data['is_paid'] = $request->boolean('is_paid');
        LeaveRecord::create($data);
        return redirect()->route('compensation.leaves')->with('status', 'Leave recorded.');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:leave_records,id',
            'employee_id' => 'required|exists:employees,id',
            'leave_type' => 'required|string|max:100',
            'is_paid' => 'nullable|boolean',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'hours' => 'nullable|numeric|min:0',
            'status' => 'required|in:approved,pending,rejected',
            'notes' => 'nullable|string',
        ]);
        $rec = LeaveRecord::findOrFail($data['id']);
        $data['is_paid'] = $request->boolean('is_paid');
        $rec->update($data);
        return redirect()->route('compensation.leaves')->with('status', 'Leave updated.');
    }

    public function delete(Request $request)
    {
        $request->validate(['id' => 'required|exists:leave_records,id']);
        LeaveRecord::where('id', $request->id)->delete();
        return redirect()->route('compensation.leaves')->with('status', 'Leave deleted.');
    }
}
