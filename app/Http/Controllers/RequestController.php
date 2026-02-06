<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $items = [];
        $requests = [];
        $search = $request->input('search');
        
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

        $requestsCollection = collect(is_array($requests) ? $requests : [])
            ->when($search, function ($collection) use ($search) {
                $s = mb_strtolower(trim($search));

                return $collection->filter(function ($row) use ($s) {
                    $haystack = implode(' ', array_filter([
                        $row['id'] ?? null,
                        $row['request_number'] ?? null,
                        $row['item_name'] ?? null,
                        $row['department'] ?? null,
                        $row['priority'] ?? null,
                        $row['status'] ?? null,
                        $row['storage_location'] ?? null,
                        $row['delivery_location'] ?? null,
                        $row['notes'] ?? null,
                        $row['created_at'] ?? null,
                        $row['request_date'] ?? null,
                    ]));

                    return str_contains(mb_strtolower($haystack), $s);
                })->values();
            })
            ->sortByDesc(function ($row) {
                return $row['created_at'] ?? $row['request_date'] ?? $row['id'] ?? null;
            })
            ->values();

        $perPage = 5;
        $page = (int) ($request->input('page', 1));
        $page = $page > 0 ? $page : 1;

        $requests = new LengthAwarePaginator(
            $requestsCollection->forPage($page, $perPage)->values(),
            $requestsCollection->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('hr4.request.request', compact('items', 'requests'));
    }

}
