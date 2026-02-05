<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ItemRequestController extends Controller
{
    /**
     * Display a listing of item requests.
     */
    public function index()
    {
        try {
            // Fetch item requests from external API
            $response = Http::withoutVerifying()->get('https://logistics1.jetlougetravels-ph.com/api/item-request');
            
            if ($response->successful()) {
                return response()->json($response->json());
            }
            
            return response()->json(['error' => 'Failed to fetch item requests'], $response->status());
        } catch (\Exception $e) {
            Log::error('Error fetching item requests: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Store a newly created item request.
     */
    public function store(Request $request)
    {
        // Log incoming request data
        Log::info('ItemRequestController store method called');
        Log::info('Request data: ' . json_encode($request->all()));
        
        // Prepare data with department field
        $data = $request->all();
        
        // Add department from form if present
        if ($request->has('department_unit')) {
            $data['department'] = $request->input('department_unit');
        }
        
        try {
            $response = Http::withoutVerifying()->post('https://logistics1.jetlougetravels-ph.com/api/item-request', $data);
            
            Log::info('API Response Status: ' . $response->status());
            Log::info('API Response Body: ' . $response->body());
            
            if ($response->successful()) {
                return response()->json($response->json(), $response->status());
            }

            return response()->json([
                'error' => 'Failed to create item request',
                'details' => $response->json(),
            ], $response->status());
        } catch (\Exception $e) {
            Log::error('Exception in ItemRequestController store method: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified item request.
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->all();
            
            // Add department from form if present
            if ($request->has('department_unit')) {
                $data['department'] = $request->input('department_unit');
            }
            
            $response = Http::withoutVerifying()->patch("https://logistics1.jetlougetravels-ph.com/api/item-request/{$id}", $data);
            
            if ($response->successful()) {
                return response()->json($response->json());
            }
            
            return response()->json(['error' => 'Failed to update item request'], $response->status());
        } catch (\Exception $e) {
            Log::error('Error updating item request: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Approve an item request.
     */
    public function approve(Request $request)
    {
        try {
            $data = $request->all();
            $response = Http::withoutVerifying()->post('https://logistics1.jetlougetravels-ph.com/api/item-request/approve', $data);
            
            if ($response->successful()) {
                return response()->json($response->json());
            }
            
            return response()->json(['error' => 'Failed to approve item request'], $response->status());
        } catch (\Exception $e) {
            Log::error('Error approving item request: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Reject an item request.
     */
    public function reject(Request $request)
    {
        try {
            $data = $request->all();
            $response = Http::withoutVerifying()->post('https://logistics1.jetlougetravels-ph.com/api/item-request/reject', $data);
            
            if ($response->successful()) {
                return response()->json($response->json());
            }
            
            return response()->json(['error' => 'Failed to reject item request'], $response->status());
        } catch (\Exception $e) {
            Log::error('Error rejecting item request: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
