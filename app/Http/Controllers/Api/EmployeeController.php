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
                'employee_status' => $employee->employee_status,
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
        $validated = $request->validate([
            'start_date'      => 'nullable|date',
            'first_name'      => 'nullable|string|max:255',
            'middle_name'     => 'nullable|string|max:255',
            'last_name'       => 'nullable|string|max:255',
            'suffix_name'     => 'nullable|string|max:255',
            'address'         => 'nullable|string|max:255',
            'phone'           => 'nullable|string|max:50',
            'age'             => 'nullable|integer|min:0',
            'gender'          => 'nullable|string|max:50',
            'birth_date'      => 'nullable|date',
            'civil_status'    => 'nullable|string|max:50',
            'skills'          => 'nullable|string',
            'experience'      => 'nullable|string',
            'education'       => 'nullable|string',
            'position'        => 'nullable|string|max:255',
            'date_hired'      => 'nullable|date',
            'employee_status' => 'nullable|in:new_hire,regular,retired',
            'salary'          => 'nullable|integer|min:0',
            'atm_number'      => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:255',
            'department'   => 'nullable|integer|exists:department',
            'role'            => 'nullable|in:ess,user,admin,superadmin',
            'profile'         => 'nullable|string|max:255',
        ]);

        // Apply defaults when not provided
        if (!isset($validated['employee_status']) || $validated['employee_status'] === null) {
            $validated['employee_status'] = 'new_hire';
        }

        if (!isset($validated['role']) || $validated['role'] === null) {
            $validated['role'] = 'user';
        }

        $employee = Employee::create($validated);

        return response()->json([
            'success' => true,
            'data' => $employee,
        ], 201);
    }

    public function updateProfile(Request $request, $id)
    {
        $validated = $request->validate([
            'profile' => 'required|file|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $employee = Employee::findOrFail($id);

        if ($request->hasFile('profile')) {
            $file = $request->file('profile');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('profiles', $filename, 'public');
            
            $employee->profile = $path;
            $employee->save();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $employee->id,
                'profile' => $employee->profile,
                'profile_url' => $employee->profile ? asset('storage/' . $employee->profile) : null,
            ],
        ]);
    }
}
