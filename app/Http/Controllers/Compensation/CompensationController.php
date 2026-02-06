<?php

namespace App\Http\Controllers\Compensation;

use App\Http\Controllers\Controller;
use App\Models\CompensationAdjustment;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CompensationController extends Controller
{
    public function index(Request $request)
    {
        // Get search and sort parameters
        $search = $request->input('search');
        $sortBy = $request->input('sort', 'given_date');
        $sortOrder = $request->input('order', 'desc');
        $status = $request->input('status');
        
        // Fetch rewards from external API (bypass SSL verification for development)
        $response = Http::withoutVerifying()->get('https://hr1.jetlougetravels-ph.com/api/give-rewards');
        
        if ($response->successful()) {
            $allRewards = $response->json();
            
            // Filter by search
            if ($search) {
                $allRewards = array_filter($allRewards, function($reward) use ($search) {
                    $searchLower = strtolower($search);
                    return strpos(strtolower($reward['employee_name'] ?? ''), $searchLower) !== false ||
                           strpos(strtolower($reward['employee_email'] ?? ''), $searchLower) !== false ||
                           strpos(strtolower($reward['employee_position'] ?? ''), $searchLower) !== false ||
                           strpos(strtolower($reward['employee_department'] ?? ''), $searchLower) !== false ||
                           strpos(strtolower($reward['reward']['name'] ?? ''), $searchLower) !== false;
                });
            }
            
            // Filter by status
            if ($status && $status !== 'all') {
                $allRewards = array_filter($allRewards, function($reward) use ($status) {
                    return $reward['status'] === $status;
                });
            }
            
            // Sort the rewards
            usort($allRewards, function($a, $b) use ($sortBy, $sortOrder) {
                $aValue = $a[$sortBy] ?? '';
                $bValue = $b[$sortBy] ?? '';
                
                if ($sortOrder === 'desc') {
                    return strcasecmp($bValue, $aValue);
                } else {
                    return strcasecmp($aValue, $sortOrder);
                }
            });
            
            // Manual pagination
            $page = $request->get('page', 1);
            $perPage = 10;
            $total = count($allRewards);
            $offset = ($page - 1) * $perPage;
            $rewards = array_slice($allRewards, $offset, $perPage);
            
            $pagination = [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage),
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $total)
            ];
            
        } else {
            $rewards = [];
            $pagination = [
                'current_page' => 1,
                'per_page' => 10,
                'total' => 0,
                'last_page' => 1,
                'from' => 0,
                'to' => 0
            ];
        }

        return view('hr4.compensation.index', compact('rewards', 'pagination', 'search', 'sortBy', 'sortOrder', 'status'));
    }


    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|in:approved,pending,rejected',
        ]);

        // Update external reward status (bypass SSL verification for development)
        try {
            $response = Http::withoutVerifying()->patch("https://hr1.jetlougetravels-ph.com/api/give-rewards/{$id}/status", [
                'status' => $data['status']
            ]);

            if ($response->successful()) {
                return redirect()->route('compensation.index')->with('status', 'Reward status updated successfully.');
            } else {
                return redirect()->route('compensation.index')->with('error', 'Failed to update reward status.');
            }
        } catch (\Exception $e) {
            return redirect()->route('compensation.index')->with('error', 'Error updating reward: ' . $e->getMessage());
        }
    }

    public function potential()
    {
        // Fetch potential data from external API
        $potentials = [];
        
        try {
            // Get potential data
            $potentialResponse = Http::withoutVerifying()->get('https://hr2.jetlougetravels-ph.com/api/potential');
            
            if ($potentialResponse->successful()) {
                $potentialData = $potentialResponse->json();
                
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
                
                // Process potential data
                foreach ($potentialData as $potential) {
                    $employeeId = $potential['employee_id'] ?? null;
                    $employeeName = $employees[$employeeId] ?? 'Unknown Employee';
                    
                    $potentials[] = [
                        'id' => $potential['id'],
                        'employee_id' => $employeeId,
                        'name' => $employeeName,
                        'potential_role' => $potential['potential_role'],
                        'identified_date' => $potential['identified_date'],
                        'created_at' => $potential['created_at'],
                        'updated_at' => $potential['updated_at']
                    ];
                }
            }
        } catch (\Exception $e) {
            // If API fails, return empty array
            $potentials = [];
        }

        return view('hr4.compensation.potential', compact('potentials'));
    }
}
