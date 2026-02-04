<?php

namespace App\Http\Controllers\Compensation;

use App\Http\Controllers\Controller;
use App\Models\LeaveRecord;
use App\Models\Employee;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index()
    {
        // Hardcoded sample data for leaves
        $leaves = [
            [
                'id' => 1,
                'employee' => 'John Doe',
                'leave_type' => 'Sick Leave',
                'is_paid' => true,
                'start_date' => '2024-01-15',
                'end_date' => '2024-01-17',
                'hours' => 24,
                'status' => 'approved',
                'notes' => 'Flu symptoms'
            ],
            [
                'id' => 2,
                'employee' => 'Jane Smith',
                'leave_type' => 'Vacation Leave',
                'is_paid' => true,
                'start_date' => '2024-02-10',
                'end_date' => '2024-02-14',
                'hours' => 40,
                'status' => 'approved',
                'notes' => 'Family vacation'
            ],
            [
                'id' => 3,
                'employee' => 'Mike Johnson',
                'leave_type' => 'Personal Leave',
                'is_paid' => false,
                'start_date' => '2024-03-05',
                'end_date' => '2024-03-05',
                'hours' => 8,
                'status' => 'pending',
                'notes' => 'Personal matter'
            ],
            [
                'id' => 4,
                'employee' => 'Sarah Williams',
                'leave_type' => 'Maternity Leave',
                'is_paid' => true,
                'start_date' => '2024-04-01',
                'end_date' => '2024-06-30',
                'hours' => 480,
                'status' => 'approved',
                'notes' => 'Maternity leave period'
            ],
            [
                'id' => 5,
                'employee' => 'Robert Brown',
                'leave_type' => 'Emergency Leave',
                'is_paid' => true,
                'start_date' => '2024-03-20',
                'end_date' => '2024-03-21',
                'hours' => 16,
                'status' => 'approved',
                'notes' => 'Family emergency'
            ]
        ];

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
