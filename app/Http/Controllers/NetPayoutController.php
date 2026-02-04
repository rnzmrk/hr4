<?php

namespace App\Http\Controllers;

use App\Models\NetPayout;
use Illuminate\Http\Request;

class NetPayoutController extends Controller
{
    /**
     * Display net payout dashboard
     */
    public function index()
    {
        // Get recent net payouts
        $recentPayouts = NetPayout::orderBy('date', 'desc')
            ->take(10)
            ->get();

        // Get monthly totals for current month
        $currentMonth = now()->format('Y-m');
        $monthlyTotals = NetPayout::where('date', 'like', $currentMonth . '%')
            ->selectRaw('SUM(total_salary) as total_salary, SUM(total_sss) as total_sss, SUM(total_pagibig) as total_pagibig, SUM(total_philhealth) as total_philhealth, SUM(total_income_tax) as total_income_tax, SUM(total_net) as total_net, COUNT(*) as record_count')
            ->first();

        // Get top payouts by net amount
        $topPayouts = NetPayout::orderBy('total_net', 'desc')
            ->take(10)
            ->get();

        // Ensure monthlyTotals has default values if null
        $monthlyTotals = (object) [
            'total_salary' => $monthlyTotals->total_salary ?? 0,
            'total_sss' => $monthlyTotals->total_sss ?? 0,
            'total_pagibig' => $monthlyTotals->total_pagibig ?? 0,
            'total_philhealth' => $monthlyTotals->total_philhealth ?? 0,
            'total_income_tax' => $monthlyTotals->total_income_tax ?? 0,
            'total_net' => $monthlyTotals->total_net ?? 0,
            'record_count' => $monthlyTotals->record_count ?? 0
        ];

        return view('hr4.net-payout.index', compact('recentPayouts', 'monthlyTotals', 'topPayouts'));
    }

    /**
     * Display a specific net payout
     */
    public function show(NetPayout $netPayout)
    {
        return view('hr4.net-payout.show', compact('netPayout'));
    }

    /**
     * Store net payout from payroll calculation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'total_salary' => 'required|numeric|min:0',
            'total_sss' => 'required|numeric|min:0',
            'total_pagibig' => 'required|numeric|min:0',
            'total_philhealth' => 'required|numeric|min:0',
            'total_income_tax' => 'required|numeric|min:0',
            'total_net' => 'required|numeric|min:0',
            'date' => 'required|date'
        ]);

        NetPayout::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Net payout recorded successfully'
        ]);
    }

    /**
     * Get net payout statistics for sidebar
     */
    public function getSidebarStats()
    {
        $currentMonth = now()->format('Y-m');
        
        $stats = NetPayout::where('date', 'like', $currentMonth . '%')
            ->selectRaw('
                COUNT(*) as total_records,
                SUM(total_salary) as total_salary,
                SUM(total_net) as total_net,
                SUM(total_sss) as total_sss,
                SUM(total_pagibig) as total_pagibig,
                SUM(total_philhealth) as total_philhealth,
                SUM(total_income_tax) as total_income_tax
            ')
            ->first();

        return response()->json($stats);
    }

    /**
     * Get all net payouts with filtering
     */
    public function getPayouts(Request $request)
    {
        $query = NetPayout::orderBy('date', 'desc');

        // Apply filters
        if ($request->filled('month')) {
            $query->whereMonth('date', date('m', strtotime($request->month)))
                  ->whereYear('date', date('Y', strtotime($request->month)));
        }

        $payouts = $query->paginate(15);

        return response()->json($payouts);
    }
}
