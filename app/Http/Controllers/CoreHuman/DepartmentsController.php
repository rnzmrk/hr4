<?php

namespace App\Http\Controllers\CoreHuman;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Requisition;

class DepartmentsController extends Controller
{
    public function index()
    {
        $departments = Department::orderBy('name')->get();

        $data = [];
        foreach ($departments as $d) {
            $employeeCount = isset($d->employee_count_override)
                ? (int)$d->employee_count_override
                : $d->employees()->count();
            // Derive openings from requisitions only
            $openings = (int) Requisition::where('department', $d->name)->sum('openings');
            $latestReq = Requisition::where('department', $d->name)->latest()->first();
            $openingRole = $latestReq?->title;

            $data[] = [
                'name' => $d->name,
                'employee_count' => $employeeCount,
                'openings' => $openings,
                'opening_role' => $openingRole,
            ];
        }

        return view('hr4.core_human.departments', ['departments' => $data]);
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string',
            'employee_count_override' => 'nullable|integer|min:0',
        ]);

        Department::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'openings' => 0,
            'employee_count_override' => $data['employee_count_override'] ?? null,
        ]);

        return back()->with('status', 'Department added.');
    }
}
