@extends('layouts.app')

@section('page-title', 'Create Budget Request')
@section('page-subtitle', 'Submit a new budget request for approval')
@section('breadcrumbs', 'Budget / Create Request')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-plus-circle me-2"></i>Create Budget Request
                    </h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <form action="{{ route('payroll.budget-requests.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="details" class="form-label fw-bold">
                                        <i class="bi bi-text-paragraph me-1"></i>Budget Details *
                                    </label>
                                    <textarea class="form-control" id="details" name="details" rows="4" required
                                              placeholder="Enter detailed description of the budget request...">{{ old('details') }}</textarea>
                                    <div class="form-text">
                                        Please provide a comprehensive description of what this budget request is for.
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount" class="form-label fw-bold">
                                        <i class="bi bi-currency-peso me-1"></i>Amount *
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" id="amount" name="amount" 
                                               step="1" min="1" max="999999999" required 
                                               value="{{ old('amount') }}"
                                               placeholder="0">
                                    </div>
                                    <div class="form-text">
                                        Enter the amount requested
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date" class="form-label fw-bold">
                                        <i class="bi bi-calendar me-1"></i>Request Date *
                                    </label>
                                    <input type="date" class="form-control" id="date" name="date" required 
                                           value="{{ old('date') ?? date('Y-m-d') }}"
                                           max="{{ date('Y-m-d') }}">
                                    <div class="form-text">
                                        Select the date of this budget request
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('payroll.approval') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>Back to Budget Requests
                                    </a>
                                    <div>
                                        <button type="reset" class="btn btn-outline-secondary me-2">
                                            <i class="bi bi-x-circle me-2"></i>Clear Form
                                        </button>
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-plus-circle me-2"></i>Create Budget Request
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set max date to today for the date input
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('date').setAttribute('max', today);
    
    // Auto-resize textarea
    const textarea = document.getElementById('details');
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });
    
    // Format amount input
    const amountInput = document.getElementById('amount');
    amountInput.addEventListener('input', function() {
        // Remove any non-numeric characters except digits
        this.value = this.value.replace(/[^0-9]/g, '');
    });
});
</script>
@endsection
