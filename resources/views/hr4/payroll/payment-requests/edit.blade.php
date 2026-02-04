@extends('layouts.app')

@section('page-title', 'Edit Payment Request')
@section('page-subtitle', 'Update payment request information')
@section('breadcrumbs', 'Payroll / Payment Requests / Edit')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil me-2"></i>Payment Request #{{ str_pad($paymentRequest->id, 4, '0', STR_PAD_LEFT) }}
                    </h5>
                </div>
                <div class="card-body">
                    @if($paymentRequest->status !== 'pending')
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            This request cannot be edited because it has been {{ $paymentRequest->status }}.
                        </div>
                        <div class="text-center">
                            <a href="{{ route('payroll.payment-requests.show', $paymentRequest) }}" class="btn btn-primary">
                                <i class="bi bi-arrow-left me-1"></i>Back to Request
                            </a>
                        </div>
                    @else
                    <form method="POST" action="{{ route('payroll.payment-requests.update', $paymentRequest) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description *</label>
                                    <input type="text" class="form-control @error('description') is-invalid @enderror" 
                                           id="description" name="description" value="{{ old('description', $paymentRequest->description) }}" 
                                           placeholder="Enter payment request description" required>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="request_date" class="form-label">Request Date *</label>
                                    <input type="date" class="form-control @error('request_date') is-invalid @enderror" 
                                           id="request_date" name="request_date" value="{{ old('request_date', $paymentRequest->request_date->format('Y-m-d')) }}" required>
                                    @error('request_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="employee_count" class="form-label">Employee Count *</label>
                                    <input type="number" class="form-control @error('employee_count') is-invalid @enderror" 
                                           id="employee_count" name="employee_count" value="{{ old('employee_count', $paymentRequest->employee_count) }}" 
                                           placeholder="Number of employees" min="1" required>
                                    @error('employee_count')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount (₱) *</label>
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                           id="amount" name="amount" value="{{ old('amount', $paymentRequest->amount) }}" 
                                           placeholder="0.00" step="0.01" min="0" required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Summary Card -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header bg-light">
                                        <h6 class="text-info mb-0">
                                            <i class="bi bi-info-circle me-1"></i>Request Summary
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>Status:</strong> <span class="badge bg-warning">Pending</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Employee Count:</strong> <span id="summary_employee_count">{{ $paymentRequest->employee_count }}</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Amount:</strong> <span id="summary_amount">{{ $paymentRequest->formatted_amount }}</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Per Employee:</strong> <span id="summary_per_employee">{{ $paymentRequest->employee_count > 0 ? '₱' . number_format($paymentRequest->amount / $paymentRequest->employee_count, 2) : '₱0.00' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('payroll.payment-requests.show', $paymentRequest) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Back to Request
                            </a>
                            <div>
                                <a href="{{ route('payroll.payment-requests.show', $paymentRequest) }}" class="btn btn-outline-secondary me-2">
                                    <i class="bi bi-x-circle me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-check-circle me-1"></i>Update Request
                                </button>
                            </div>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($paymentRequest->status === 'pending')
<script>
// Update summary in real-time
function updateSummary() {
    const employeeCount = parseInt(document.getElementById('employee_count').value) || 0;
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    const perEmployee = employeeCount > 0 ? amount / employeeCount : 0;
    
    document.getElementById('summary_employee_count').textContent = employeeCount;
    document.getElementById('summary_amount').textContent = '₱' + amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('summary_per_employee').textContent = '₱' + perEmployee.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

// Add event listeners
document.getElementById('employee_count').addEventListener('input', updateSummary);
document.getElementById('amount').addEventListener('input', updateSummary);

// Initialize summary on page load
updateSummary();

// Format amount input
document.getElementById('amount').addEventListener('blur', function() {
    const value = parseFloat(this.value);
    if (!isNaN(value)) {
        this.value = value.toFixed(2);
    }
});
</script>
@endif
@endsection
