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
            $employeeCount = $d->employees()->count();
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
}
