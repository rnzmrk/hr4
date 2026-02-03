<?php

namespace App\Http\Controllers\Benefits;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use Illuminate\Http\Request;

class RewardsController extends Controller
{
    public function index()
    {
        $rewards = Reward::latest()->get();

        return view('hr4.benefits.rewards', compact('rewards'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:255',
            'benefits' => 'nullable|string',
        ]);

        Reward::create($data);

        return back()->with('status', 'Reward created successfully.');
    }
}
