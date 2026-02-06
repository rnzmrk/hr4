<?php

namespace App\Http\Controllers\CoreHuman;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RequisitionsController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'requested_by' => 'required|string|max:255',
            'department'   => 'required|string|max:255',
            'position'     => 'required|string|max:255',
            'opening'      => 'required|integer',
        ]);

        $response = Http::asJson()->withoutVerifying()->post(
            'https://hr1.jetlougetravels-ph.com/api/requisitions',
            $validated
        );

        return back()->with('status', $response->successful() ? 'Requisition submitted.' : 'Failed to submit.');
    }
}
