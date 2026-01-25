<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Department;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('department')->get()->map(function($employee) {
            return [
                'id' => $employee->id,
                'first_name' => $employee->first_name,
                'middle_name' => $employee->middle_name,
                'last_name' => $employee->last_name,
                'suffix_name' => $employee->suffix_name,
                'full_name' => trim(implode(' ', array_filter([
                    $employee->first_name,
                    $employee->middle_name,
                    $employee->last_name,
                    $employee->suffix_name
                ]))),
                'email' => $employee->email,
                'phone' => $employee->phone,
                'position' => $employee->position,
                'role' => $employee->role,
                'status' => $employee->status,
                'date_hired' => $employee->date_hired,
                'start_date' => $employee->start_date,
                'age' => $employee->age,
                'gender' => $employee->gender,
                'birth_date' => $employee->birth_date,
                'civil_status' => $employee->civil_status,
                'address' => $employee->address,
                'skills' => $employee->skills,
                'experience' => $employee->experience,
                'education' => $employee->education,
                'external_employee_id' => $employee->external_employee_id,
                'department' => $employee->department ? [
                    'id' => $employee->department->id,
                    'name' => $employee->department->name,
                    'description' => $employee->department->description ?? null
                ] : null,
                'created_at' => $employee->created_at,
                'updated_at' => $employee->updated_at
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $employees
        ]);
    }

    public function store(Request $request)
    {
        // Accept external payload with candidate_* and offer_* keys
        $validated = $request->validate([
            'candidate_id' => 'required|integer',
            'offer_date' => 'nullable|date',
            'offer_status' => 'nullable|string|max:100',
            'candidate_job_title' => 'nullable|string|max:255',
            'candidate_last_name' => 'required|string|max:255',
            'candidate_first_name' => 'required|string|max:255',
            'candidate_middle_name' => 'nullable|string|max:255',
            'candidate_suffix_name' => 'nullable|string|max:255',
            'candidate_address' => 'nullable|string|max:255',
            'candidate_email' => 'nullable|email|max:255',
            'candidate_phone' => 'nullable|string|max:50',
            'candidate_age' => 'nullable|integer|min:0',
            'candidate_gender' => 'nullable|string|max:50',
            'candidate_birth_date' => 'nullable|date',
            'candidate_civil_status' => 'nullable|string|max:50',
            'skills' => 'nullable|string',
            'experience' => 'nullable|string',
            'education' => 'nullable|string',
            'status' => 'nullable|string|max:50',
            // Optional department name if provided by external system
            'department' => 'nullable|string|max:255',
        ]);

        // Build full name from candidate fields
        $fullName = trim(implode(' ', array_filter([
            $validated['candidate_first_name'] ?? null,
            $validated['candidate_middle_name'] ?? null,
            $validated['candidate_last_name'] ?? null,
            $validated['candidate_suffix_name'] ?? null,
        ])));

        // Map department name (optional)
        $deptId = null;
        if (!empty($validated['department'])) {
            $dept = Department::firstOrCreate(['name' => $validated['department']]);
            $deptId = $dept->id;
        }

        // Persist to Employee with normalized columns
        $employee = Employee::create([
            'email' => $validated['candidate_email'] ?? null,
            'department_id' => $deptId,
            'role' => $validated['candidate_job_title'] ?? null,
            'start_date' => $validated['offer_date'] ?? null,
            'status' => $validated['status'] ?? 'Active',
            // Store additional details
            'first_name' => $validated['candidate_first_name'] ?? null,
            'middle_name' => $validated['candidate_middle_name'] ?? null,
            'last_name' => $validated['candidate_last_name'] ?? null,
            'suffix_name' => $validated['candidate_suffix_name'] ?? null,
            'address' => $validated['candidate_address'] ?? null,
            'phone' => $validated['candidate_phone'] ?? null,
            'age' => $validated['candidate_age'] ?? null,
            'gender' => $validated['candidate_gender'] ?? null,
            'birth_date' => $validated['candidate_birth_date'] ?? null,
            'civil_status' => $validated['candidate_civil_status'] ?? null,
            'skills' => $validated['skills'] ?? null,
            'experience' => $validated['experience'] ?? null,
            'education' => $validated['education'] ?? null,
            'position' => $validated['candidate_position'] ?? null,
            'date_hired' => $validated['offer_date'] ?? null,
            'external_employee_id' => (string)$validated['candidate_id'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $employee,
        ], 201);

    }
}
