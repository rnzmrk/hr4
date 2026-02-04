@extends('layouts.app')

@section('page-title', 'Create Payment Request')
@section('page-subtitle', 'Submit a new payment request for approval')
@section('breadcrumbs', 'Payroll / Payment Requests / Create')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-plus-circle me-2"></i>Create Payment Request
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('payroll.payment-requests.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description *</label>
                                    <input type="text" class="form-control @error('description') is-invalid @enderror" 
                                           id="description" name="description" value="{{ old('description') }}" 
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
                                           id="request_date" name="request_date" value="{{ old('request_date') ?? now()->format('Y-m-d') }}" required>
                                    @error('request_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount (₱) *</label>
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                           id="amount" name="amount" value="{{ old('amount') }}" 
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
                                            <div class="col-md-4">
                                                <strong>Status:</strong> <span class="badge bg-warning">Pending</span>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Amount:</strong> <span id="summary_amount">₱0.00</span>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Request Date:</strong> <span id="summary_request_date">{{ now()->format('M d, Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('payroll.payment-requests.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Back to Requests
                            </a>
                            <div>
                                <button type="reset" class="btn btn-outline-secondary me-2">
                                    <i class="bi bi-x-circle me-1"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i>Submit Request
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Update summary in real-time
function updateSummary() {
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    
    document.getElementById('summary_amount').textContent = '₱' + amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

// Add event listeners
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
@endsection
