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
        // Fetch leave data from external API
        $leaves = [];
        
        try {
            $response = Http::withoutVerifying()->get('https://hr2.jetlougetravels-ph.com/api/leave-applications');
            
            if ($response->successful()) {
                $leaveData = $response->json();
                
                // Get employee data for names
                $employeeResponse = Http::withoutVerifying()->get('https://hr4.jetlougetravels-ph.com/api/accounts');
                
                $employees = [];
                if ($employeeResponse->successful()) {
                    $employeePayload = $employeeResponse->json();
                    $systemAccounts = \Illuminate\Support\Arr::get($employeePayload, 'system_accounts', []);
                    
                    foreach ($systemAccounts as $account) {
                        if (($account['account_type'] ?? null) === 'system' && !($account['blocked'] ?? false)) {
                            $employee = $account['employee'] ?? null;
                            if ($employee) {
                                $employees[$employee['id']] = trim(($employee['first_name'] ?? '') . ' ' . ($employee['last_name'] ?? ''));
                            }
                        }
                    }
                }
                
                // Process leave data
                foreach ($leaveData as $leave) {
                    $employeeId = $leave['employee_id'] ?? null;
                    $employeeName = $employees[$employeeId] ?? 'Unknown Employee';
                    
                    $leaves[] = [
                        'id' => $leave['id'],
                        'employee' => $employeeName,
                        'leave_type' => $leave['leave_type'] ?? 'Leave',
                        'is_paid' => $leave['is_paid'] ?? false,
                        'start_date' => $leave['start_date'],
                        'end_date' => $leave['end_date'],
                        'hours' => $leave['hours'] ?? null,
                        'status' => $leave['status'] ?? 'pending',
                        'notes' => $leave['notes'] ?? null
                    ];
                }
            }
        } catch (\Exception $e) {
            // If API fails, return empty array
            $leaves = [];
        }

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
