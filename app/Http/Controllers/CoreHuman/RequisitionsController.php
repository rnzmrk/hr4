<?php

namespace App\Http\Controllers\CoreHuman;

use App\Http\Controllers\Controller;
use App\Models\Requisition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RequisitionsController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'requested_by' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'requisition_title' => 'required|string|max:255',   
            'requisition_description' => 'nullable|string',
            'requisition_type' => 'required|string|max:100',
            'requisition_arrangement' => 'nullable|string',
            'requisition_responsibilities' => 'nullable|string',
            'requisition_qualifications' => 'nullable|string',
            'opening' => 'required|integer|min:1',
            'status' => 'required|string|max:50',
        ]);

        try {
            // Ensure correct numeric type for API
            $validated['opening'] = (int) $validated['opening'];

            $response = Http::asJson()->post(
                'https://hr1.jetlougetravels-ph.com/api/requisitions',
                $validated
            );

            if ($response->successful()) {
                return back()->with('status', 'Requisition submitted.');
            }

            $msg = 'Failed to submit requisition.';
            $body = $response->json();
            if (is_array($body)) {
                // Try to surface message or first error
                $msg = $body['message'] ?? ($body['error'] ?? $msg);
            } else {
                $msg = $response->body() ?: $msg;
            }
            return back()->withErrors(['requisition' => $msg])->withInput();
        } catch (\Throwable $e) {
            return back()->withErrors(['requisition' => 'Service unavailable: '.$e->getMessage()])->withInput();
        }
    }

    public function quickAdd(Request $request)
    {
        $data = $request->validate([
            'department' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'openings' => 'required|integer|min:1',
            'status' => 'nullable|string|max:50',
        ]);

        \App\Models\Requisition::create([
            'requested_by' => auth()->user()->name ?? 'System',
            'department' => $data['department'],
            'title' => $data['title'],
            'openings' => (int)$data['openings'],
            'status' => $data['status'] ?? 'Open',
            'type' => null, 
            'arrangement' => null,
            'description' => null,
            'responsibilities' => null,
            'qualifications' => null,
        ]);

        return back()->with('status', 'Opening added to department.');
    }
}
