<?php

namespace App\Http\Controllers\CoreHuman;

use App\Http\Controllers\Controller;
use App\Models\AcceptedContract;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class AcceptedContractsController extends Controller
{
    public function index()
    {
        $baseUrl = config('services.hr1.base_url', env('HR1_API_BASE_URL'));
        $token = config('services.hr1.token', env('HR1_API_TOKEN'));

        $insertedContracts = 0;
        $insertedEmployees = 0;

        // Fetch from HR1 and upsert into DB, also auto-insert employees
        try {
            if ($baseUrl && $token) {
                $response = Http::timeout(10)->withToken($token)->get(rtrim($baseUrl, '/').'/contracts/accepted');
                if ($response->successful()) {
                    $records = $response->json('data') ?? $response->json();
                    foreach ($records as $r) {
                        $extId = $r['id'] ?? null;
                        $name = $r['candidate']['full_name'] ?? ($r['full_name'] ?? 'Unknown');
                        $email = $r['candidate']['email'] ?? ($r['email'] ?? null);
                        $department = $r['department'] ?? 'Unknown';
                        $role = $r['role'] ?? $r['position'] ?? 'Employee';
                        $start = $r['start_date'] ?? ($r['contract_start'] ?? date('Y-m-d'));

                        $ac = AcceptedContract::firstOrCreate(
                            ['external_id' => $extId],
                            [
                                'name' => $name,
                                'email' => $email,
                                'department' => $department,
                                'role' => $role,
                                'start_date' => $start,
                            ]
                        );
                        if ($ac->wasRecentlyCreated) {
                            $insertedContracts++;
                        }

                        // Auto-create employee if not existing by email or name
                        $exists = Employee::query()
                            ->when($email, fn($q) => $q->orWhere('email', $email))
                            ->orWhere('name', $name)
                            ->exists();
                        if (!$exists) {
                            Employee::create([
                                'name' => $name,
                                'email' => $email,
                                'role' => $role,
                                'start_date' => $start,
                                'status' => 'Active',
                            ]);
                            $insertedEmployees++;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // ignore API errors for now
        }

        $contracts = AcceptedContract::orderByDesc('created_at')->get()->map(function (AcceptedContract $c) {
            // Build full name from candidate fields when available
            $parts = array_filter([
                $c->candidate_first_name ?? null,
                $c->candidate_middle_name ?? null,
                $c->candidate_last_name ?? null,
                $c->candidate_suffix_name ?? null,
            ]);
            $fullName = trim(implode(' ', $parts)) ?: ($c->name ?? '');

            return [
                'id' => $c->id,
                'name' => $fullName,
                'email' => $c->candidate_email ?? $c->email ?? null,
                // Department is not part of new schema; leave empty to let user choose on transform
                'department' => '',
                'role' => $c->candidate_job_title ?? $c->role ?? 'Employee',
                'start_date' => ($c->offer_date ?? $c->start_date) ?: date('Y-m-d'),
            ];
        })->toArray();

        if ($insertedContracts || $insertedEmployees) {
            session()->flash('status', sprintf('Synced: %d new contract(s), %d new employee(s).', $insertedContracts, $insertedEmployees));
        }

        $departments = \App\Models\Department::orderBy('name')->get(['id','name']);
        return view('hr4.core_human.accepted_contracts', compact('contracts','departments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'department' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'start_date' => 'required|date',
        ]);

        AcceptedContract::create([
            'external_id' => null,
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'department' => $data['department'],
            'role' => $data['role'],
            'start_date' => $data['start_date'],
        ]);

        return redirect()->route('contracts.accepted')->with('status', 'Demo accepted contract added.');
    }

    public function transformToEmployee(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'role' => 'required|string|max:255',
            'start_date' => 'required|date',
            'email' => 'nullable|email',
        ]);

        $exists = Employee::query()
            ->when($data['email'] ?? null, fn($q) => $q->orWhere('email', $data['email']))
            ->orWhere('name', $data['name'])
            ->exists();
        if (!$exists) {
            $payload = [
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'role' => $data['role'],
                'start_date' => $data['start_date'],
                'status' => 'Active',
            ];
            // Optionally attach department if provided and exists
            if (!empty($data['department'])) {
                $dept = \App\Models\Department::firstOrCreate(['name' => $data['department']]);
                $payload['department_id'] = $dept->id;
            }
            Employee::create($payload);
        }

        return redirect()->route('employees.index')->with('status', 'Employee record created.');
    }
}
