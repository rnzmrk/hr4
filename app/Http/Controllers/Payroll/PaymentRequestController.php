<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\PaymentRequest;
use App\Models\Employee;
use Illuminate\Http\Request;

class PaymentRequestController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status', 'all');
        
        $paymentRequestsQuery = PaymentRequest::with(['requestedBy', 'approvedBy'])
            ->when($search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhereHas('requestedBy', function($empQuery) use ($search) {
                          $empQuery->where('first_name', 'like', "%{$search}%")
                                   ->orWhere('last_name', 'like', "%{$search}%");
                      });
                });
            })
            ->when($status !== 'all', function($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest('created_at');

        $paymentRequests = $paymentRequestsQuery->paginate(10);

        return view('hr4.payroll.payment-requests.index', compact('paymentRequests', 'status', 'search'));
    }

    public function create()
    {
        return view('hr4.payroll.payment-requests.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'description' => 'required|string|max:255',
            'employee_count' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'request_date' => 'required|date',
        ]);

        $data['status'] = 'pending';
        
        PaymentRequest::create($data);

        return redirect()->route('payroll.payment-requests.index')
                        ->with('status', 'Payment request created successfully!');
    }

    public function show(PaymentRequest $paymentRequest)
    {
        $paymentRequest->load(['requestedBy', 'approvedBy']);
        
        return view('hr4.payroll.payment-requests.show', compact('paymentRequest'));
    }
}
