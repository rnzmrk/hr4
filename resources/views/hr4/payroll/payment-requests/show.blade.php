@extends('layouts.app')

@section('page-title', 'Payment Request Details')
@section('page-subtitle', 'View payment request information and status')
@section('breadcrumbs', 'Payroll / Payment Requests / Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-cash-stack me-2"></i>Payment Request #{{ str_pad($paymentRequest->id, 4, '0', STR_PAD_LEFT) }}
                    </h5>
                    <div>
                        {!! $paymentRequest->status_badge !!}
                        <a href="{{ route('payroll.payment-requests.index') }}" class="btn btn-light btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Request Information -->
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-header bg-light">
                                    <h6 class="text-primary mb-0">
                                        <i class="bi bi-info-circle me-1"></i>Request Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Description:</strong></div>
                                        <div class="col-8">{{ $paymentRequest->description }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Employee Count:</strong></div>
                                        <div class="col-8">{{ $paymentRequest->employee_count }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Amount:</strong></div>
                                        <div class="col-8">{{ $paymentRequest->formatted_amount }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Per Employee:</strong></div>
                                        <div class="col-8">
                                            {{ $paymentRequest->employee_count > 0 ? '₱' . number_format($paymentRequest->amount / $paymentRequest->employee_count, 2) : '₱0.00' }}
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Request Date:</strong></div>
                                        <div class="col-8">{{ $paymentRequest->formatted_request_date }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Status:</strong></div>
                                        <div class="col-8">{!! $paymentRequest->status_badge !!}</div>
                                    </div>
                                    @if($paymentRequest->notes)
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Notes:</strong></div>
                                        <div class="col-8">{{ $paymentRequest->notes }}</div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- People Information -->
                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-header bg-light">
                                    <h6 class="text-info mb-0">
                                        <i class="bi bi-people me-1"></i>People Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Requested By:</strong></div>
                                        <div class="col-8">
                                            @if($paymentRequest->requestedBy)
                                                {{ $paymentRequest->requestedBy->first_name }} {{ $paymentRequest->requestedBy->last_name }}
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Created:</strong></div>
                                        <div class="col-8">{{ $paymentRequest->created_at->format('M d, Y h:i A') }}</div>
                                    </div>
                                    @if($paymentRequest->approvedBy)
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Approved By:</strong></div>
                                        <div class="col-8">{{ $paymentRequest->approvedBy->first_name }} {{ $paymentRequest->approvedBy->last_name }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Approved At:</strong></div>
                                        <div class="col-8">{{ $paymentRequest->approved_at->format('M d, Y h:i A') }}</div>
                                    </div>
                                    @endif
                                    @if($paymentRequest->rejection_reason)
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Rejection Reason:</strong></div>
                                        <div class="col-8 text-danger">{{ $paymentRequest->rejection_reason }}</div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Information -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-{{ $paymentRequest->status === 'approved' ? 'success' : ($paymentRequest->status === 'rejected' ? 'danger' : 'warning') }}">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-{{ $paymentRequest->status === 'approved' ? 'check-circle' : ($paymentRequest->status === 'rejected' ? 'x-circle' : 'clock') }} me-2"></i>
                                    <div>
                                        <strong>Request Status: {{ ucfirst($paymentRequest->status) }}</strong>
                                        <div class="small">
                                            @if($paymentRequest->status === 'pending')
                                                This request is waiting for approval.
                                            @elseif($paymentRequest->status === 'approved')
                                                This request has been approved and is ready for processing.
                                            @else
                                                This request has been rejected.
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
</script>
@endsection
