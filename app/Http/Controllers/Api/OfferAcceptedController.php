<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OfferAcceptedController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'candidate_id' => 'required|integer',
            'offer_date' => 'nullable|date',
            'offer_status' => 'required|in:Pending,Accepted,Declined',
            'candidate_job_title' => 'required|string|max:255',
            'candidate_last_name' => 'required|string|max:255',
            'candidate_first_name' => 'required|string|max:255',
            'candidate_middle_name' => 'required|string|max:255',
            'candidate_suffix_name' => 'nullable|string|max:255',
            'candidate_address' => 'required|string|max:255',
            'candidate_email' => 'required|email|max:255',
            'candidate_phone' => 'required|string|max:50',
            'candidate_age' => 'required|integer|min:18',
            'candidate_gender' => 'required|string|max:50',
            'candidate_birth_date' => 'required|date',
            'candidate_civil_status' => 'required|string|max:50',
            'skills' => 'required|string',
            'experience' => 'required|string',
            'education' => 'required|string',
            'interviewDate' => 'required|date',
            'interviewTime' => 'required',
            'status' => 'required|in:Scheduled,Initial,Final,Passed,Rejected',
        ]);

        $candidate = Candidate::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $candidate,
        ], 201);
    }

}
