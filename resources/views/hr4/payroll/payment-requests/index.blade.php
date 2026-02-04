@extends('layouts.app')

@section('page-title', 'Payment Requests')
@section('page-subtitle', 'Manage and approve payment requests for payroll processing')
@section('breadcrumbs', 'Payroll / Payment Requests')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-cash-stack me-2"></i>Payment Requests
                    </h5>
                    <div>
                        <a href="{{ route('payroll.payment-requests.create') }}" class="btn btn-light btn-sm me-2">
                            <i class="bi bi-plus-circle me-1"></i>New Request
                        </a>
                        <button class="btn btn-light btn-sm" id="refreshTable">
                            <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search and Filter Section -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" id="searchInput" placeholder="Search requests..." value="{{ $search }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Status</option>
                                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-5 text-end">
                            <button class="btn btn-primary me-2" id="applyFilters">
                                <i class="bi bi-search me-1"></i>Search
                            </button>
                            <button class="btn btn-outline-secondary" id="clearFilters">
                                <i class="bi bi-x-circle me-1"></i>Clear
                            </button>
                        </div>
                    </div>

                    <!-- Payment Requests Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Description</th>
                                    <th>Employee Count</th>
                                    <th>Amount</th>
                                    <th>Request Date</th>
                                    <th>Status</th>
                                    <th>Requested By</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paymentRequests as $paymentRequest)
                                <tr>
                                    <td>#{{ str_pad($paymentRequest->id, 4, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ $paymentRequest->description }}</td>
                                    <td>{{ $paymentRequest->employee_count }}</td>
                                    <td>{{ $paymentRequest->formatted_amount }}</td>
                                    <td>{{ $paymentRequest->formatted_request_date }}</td>
                                    <td>{!! $paymentRequest->status_badge !!}</td>
                                    <td>{{ $paymentRequest->requestedBy ? $paymentRequest->requestedBy->first_name . ' ' . $paymentRequest->requestedBy->last_name : 'N/A' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('payroll.payment-requests.show', $paymentRequest) }}" class="btn btn-outline-primary btn-sm" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">No payment requests found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Showing {{ $paymentRequests->firstItem() }} to {{ $paymentRequests->lastItem() }} of {{ $paymentRequests->total() }} entries
                        </div>
                        {{ $paymentRequests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Filter functionality
document.getElementById('applyFilters').addEventListener('click', function() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (status !== 'all') params.append('status', status);
    
    window.location.href = '{{ route("payroll.payment-requests.index") }}?' + params.toString();
});

document.getElementById('clearFilters').addEventListener('click', function() {
    window.location.href = '{{ route("payroll.payment-requests.index") }}';
});

// Refresh table
document.getElementById('refreshTable').addEventListener('click', function() {
    window.location.reload();
});
</script>
@endsection
