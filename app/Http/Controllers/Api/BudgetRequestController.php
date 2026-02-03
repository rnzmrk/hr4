<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BudgetRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class BudgetRequestController extends Controller
{
    /**
     * Display a listing of budget requests.
     */
    public function index(): JsonResponse
    {
        try {
            $budgetRequests = BudgetRequest::latest('date')->get();
            
            return response()->json([
                'success' => true,
                'data' => $budgetRequests,
                'message' => 'Budget requests retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve budget requests',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created budget request.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'details' => 'required|string|max:255',
                'amount' => 'required|integer|min:1|max:999999999',
                'date' => 'required|date|before_or_equal:today',
            ], [
                'details.required' => 'Budget details are required',
                'details.string' => 'Budget details must be a string',
                'details.max' => 'Budget details cannot exceed 255 characters',
                'amount.required' => 'Amount is required',
                'amount.integer' => 'Amount must be an integer',
                'amount.min' => 'Amount must be at least 1',
                'amount.max' => 'Amount cannot exceed 999,999,999',
                'date.required' => 'Date is required',
                'date.date' => 'Date must be a valid date',
                'date.before_or_equal' => 'Date cannot be in the future',
            ]);

            // Set default status to pending
            $validated['status'] = 'pending';

            $budgetRequest = BudgetRequest::create($validated);

            return response()->json([
                'success' => true,
                'data' => $budgetRequest,
                'message' => 'Budget request created successfully'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create budget request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the status of the specified budget request.
     */
    public function update(Request $request, BudgetRequest $budgetRequest): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
            ], [
                'status.required' => 'Status is required',
                'status.in' => 'Status must be pending, approved, or rejected',
            ]);

            $budgetRequest->update(['status' => $validated['status']]);

            return response()->json([
                'success' => true,
                'data' => $budgetRequest,
                'message' => 'Budget request status updated successfully'
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update budget request status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
    