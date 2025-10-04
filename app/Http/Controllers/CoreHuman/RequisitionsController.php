<?php

namespace App\Http\Controllers\CoreHuman;

use App\Http\Controllers\Controller;
use App\Models\Requisition;
use Illuminate\Http\Request;

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
            'opening' => 'required|date',
            'status' => 'required|string|max:50',
        ]);

        try {
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
}
