<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RequestController extends Controller
{
    public function index()
    {
        $items = [];
        $requests = [];
        
        // Fetch items from API
        $itemsResponse = Http::withoutVerifying()->get('https://logistics1.jetlougetravels-ph.com/api/item');
        if ($itemsResponse->successful()) {
            $items = $itemsResponse->json();
        }
        
        // Fetch item requests from API
        $requestsResponse = Http::withoutVerifying()->get('https://logistics1.jetlougetravels-ph.com/api/item-request');
        if ($requestsResponse->successful()) {
            $requests = $requestsResponse->json();
        }

        return view('hr4.request.request', compact('items', 'requests'));
    }

    public function getItems()
    {
        $response = Http::withoutVerifying()->get('https://logistics1.jetlougetravels-ph.com/api/item');
        return response()->json($response->json());
    }

    public function store(Request $request)
    {
        // Log incoming request data
        \Log::info('Store method called');
        \Log::info('Request data: ' . json_encode($request->all()));
        
        // Prepare data with department field
        $data = $request->all();
        
        // Add department from form if present
        if ($request->has('department_unit')) {
            $data['department'] = $request->input('department_unit');
        }
        
        try {
            $response = Http::withoutVerifying()->post('https://logistics1.jetlougetravels-ph.com/api/store-item-request', $data);
            
            \Log::info('API Response Status: ' . $response->status());
            \Log::info('API Response Body: ' . $response->body());
            
            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            \Log::error('Exception in store method: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
