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
        // Sample data for potential employees
        $potentials = [
            [
                'id' => 1,
                'name' => 'John Doe',
                'position' => 'Senior Developer',
                'department' => 'IT',
                'current_performance' => 4.5,
                'potential_score' => 4.8,
                'readiness_level' => 'Ready for promotion',
                'development_needs' => 'Leadership training',
                'recommended_action' => 'Promote to Team Lead'
            ],
            [
                'id' => 2,
                'name' => 'Jane Smith',
                'position' => 'Marketing Specialist',
                'department' => 'Marketing',
                'current_performance' => 4.2,
                'potential_score' => 4.6,
                'readiness_level' => 'Ready in 6 months',
                'development_needs' => 'Project management',
                'recommended_action' => 'Assign to lead project'
            ],
            [
                'id' => 3,
                'name' => 'Mike Johnson',
                'position' => 'Sales Executive',
                'department' => 'Sales',
                'current_performance' => 4.7,
                'potential_score' => 4.9,
                'readiness_level' => 'High potential',
                'development_needs' => 'Strategic planning',
                'recommended_action' => 'Fast-track to management'
            ]
        ];

        return view('hr4.compensation.potential', compact('potentials'));
    }
}
