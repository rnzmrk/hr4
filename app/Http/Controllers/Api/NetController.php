<?php

namespace App\Http\Controllers\Api;

use App\Models\NetPayout;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NetController extends Controller
{
    public function index()
    {
        $payouts = NetPayout::orderBy('date', 'desc')->get();
        return response()->json($payouts);
    }
}
