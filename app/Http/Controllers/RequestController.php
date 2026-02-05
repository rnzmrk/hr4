<?php

namespace App\Http\Controllers;

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

}
