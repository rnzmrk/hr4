<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\BudgetRequest;
use Illuminate\Http\Request;

class BudgetRequestController extends Controller
{
    /**
     * Display the budget requests index page.
     */
    public function index()
    {
        $budgetRequests = BudgetRequest::latest('date')->get();
        return view('hr4.payroll.approval', compact('budgetRequests'));
    }

    /**
     * Show the form for creating a new budget request.
     */
    public function create()
    {
        return view('hr4.payroll.budget-request-create');
    }

    /**
     * Store a newly created budget request in storage.
     */
    public function store(Request $request)
    {
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

        return redirect()
            ->route('payroll.budget-requests.index')
            ->with('success', 'Budget request created successfully!');
    }

    /**
     * Display the specified budget request.
     */
    public function show(BudgetRequest $budgetRequest)
    {
        return view('hr4.payroll.budget-request-show', compact('budgetRequest'));
    }

    /**
     * Update the status of the specified budget request.
     */
    public function updateStatus(Request $request, BudgetRequest $budgetRequest)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
        ], [
            'status.required' => 'Status is required',
            'status.in' => 'Status must be pending, approved, or rejected',
        ]);

        $budgetRequest->update(['status' => $validated['status']]);

        return redirect()
            ->route('payroll.budget-requests.index')
            ->with('success', "Budget request {$validated['status']} successfully!");
    }

    /**
     * Remove the specified budget request from storage.
     */
    public function destroy(BudgetRequest $budgetRequest)
    {
        $budgetRequest->delete();

        return redirect()
            ->route('payroll.budget-requests.index')
            ->with('success', 'Budget request deleted successfully!');
    }
}
