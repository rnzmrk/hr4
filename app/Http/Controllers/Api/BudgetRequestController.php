<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BudgetRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class BudgetRequestController extends Controller
{
    /**
     * API token for budget request operations
     */
    private $apiToken;
    
    public function __construct()
    {
        $this->apiToken = 'br_' . $this->generateAlphanumericToken(32);
    }
    
    /**
     * Generate alphanumeric token
     */
    private function generateAlphanumericToken($length = 32): string
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        
        return $randomString;
    }
    
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
                'message' => 'Budget requests retrieved successfully',
                'token' => $this->apiToken
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
                'message' => 'Budget request created successfully',
                'token' => $this->apiToken,
                'request_id' => 'BR_' . str_pad($budgetRequest->id, 6, '0', STR_PAD_LEFT)
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
                'message' => 'Budget request status updated successfully',
                'token' => $this->apiToken,
                'request_id' => 'BR_' . str_pad($budgetRequest->id, 6, '0', STR_PAD_LEFT),
                'updated_at' => $budgetRequest->updated_at->toISOString()
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

    /**
     * Generate a new API token for budget request operations.
     */
    public function generateToken(): JsonResponse
    {
        try {
            $newToken = 'br_' . $this->generateAlphanumericToken(32);
            
            return response()->json([
                'success' => true,
                'token' => $newToken,
                'token_type' => 'Bearer',
                'expires_in' => 3600, // 1 hour
                'message' => 'API token generated successfully',
                'generated_at' => now()->toISOString(),
                'token_format' => 'alphanumeric',
                'character_set' => '0-9, A-Z, a-z'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate API token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate API token.
     */
    public function validateToken(Request $request): JsonResponse
    {
        try {
            $token = $request->header('Authorization');
            
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authorization header missing'
                ], 401);
            }

            // Remove 'Bearer ' prefix if present
            $token = str_replace('Bearer ', '', $token);
            
            // Validate token format (should start with 'br_')
            if (!str_starts_with($token, 'br_') || strlen($token) !== 35) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token format'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'Token is valid',
                'token' => $token,
                'validated_at' => now()->toISOString()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token validation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get API token information.
     */
    public function getTokenInfo(): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'token' => $this->apiToken,
                    'token_type' => 'Bearer',
                    'prefix' => 'br_',
                    'length' => 35,
                    'purpose' => 'Budget Request API Operations',
                    'generated_at' => now()->toISOString(),
                    'version' => '1.0'
                ],
                'message' => 'Token information retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve token information',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
    