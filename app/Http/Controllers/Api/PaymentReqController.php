<?php

namespace App\Http\Controllers\Api;

use App\Models\PaymentRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentReqController extends Controller
{
    public function index()
    {
        $paymentRequests = PaymentRequest::orderBy('created_at', 'desc')->get();
        return response()->json($paymentRequests);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:0',
            'reason' => 'required|string|max:255',
            'request_date' => 'required|date',
        ]);

        $paymentRequest = PaymentRequest::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Payment request created successfully',
            'data' => $paymentRequest
        ]);
    }

    public function update(Request $request, $id)
    {
        $paymentRequest = PaymentRequest::findOrFail($id);
        
        $validated = $request->validate([
            'amount' => 'sometimes|numeric|min:0',
            'reason' => 'sometimes|string|max:255',
            'status' => 'sometimes|string|in:pending,approved,rejected',
        ]);

        $paymentRequest->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Payment request updated successfully',
            'data' => $paymentRequest
        ]);
    }

    public function approve(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:payment_requests,id',
            'notes' => 'nullable|string'
        ]);

        $paymentRequest = PaymentRequest::findOrFail($request->id);
        $paymentRequest->update([
            'status' => 'approved',
            'approved_at' => now(),
            'notes' => $request->notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment request approved successfully',
            'data' => $paymentRequest
        ]);
    }

    public function reject(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:payment_requests,id',
            'reason' => 'required|string'
        ]);

        $paymentRequest = PaymentRequest::findOrFail($request->id);
        $paymentRequest->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $request->reason
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment request rejected successfully',
            'data' => $paymentRequest
        ]);
    }

    public function generateToken(Request $request)
    {
        $request->validate([
            'payment_request_id' => 'required|exists:payment_requests,id',
        ]);

        $paymentRequest = PaymentRequest::findOrFail($request->payment_request_id);
        
        // Generate unique token
        $token = uniqid('pay_req_', true);
        
        // Store token (you might want to add a token column to your table)
        $paymentRequest->update([
            'token' => $token,
            'token_expires_at' => now()->addHours(24)
        ]);

        return response()->json([
            'success' => true,
            'token' => $token,
            'expires_at' => $paymentRequest->token_expires_at
        ]);
    }

    public function validateToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $paymentRequest = PaymentRequest::where('token', $request->token)
            ->where('token_expires_at', '>', now())
            ->first();

        if (!$paymentRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Token is valid',
            'payment_request_id' => $paymentRequest->id
        ]);
    }

    public function getTokenInfo(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $paymentRequest = PaymentRequest::where('token', $request->token)
            ->with('employee')
            ->first();

        if (!$paymentRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Token not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'payment_request' => $paymentRequest,
                'is_expired' => $paymentRequest->token_expires_at < now(),
                'expires_at' => $paymentRequest->token_expires_at
            ]
        ]);
    }
}
