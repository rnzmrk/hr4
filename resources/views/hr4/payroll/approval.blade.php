@extends('layouts.app')

@section('page-title', 'Budget Approval')
@section('page-subtitle', 'Review and approve budget requests')
@section('breadcrumbs', 'Budget / Approval')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-check-circle me-2"></i>Budget Approval
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
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Budget requests awaiting approval will be displayed here.
                    </div>
                    
                    <!-- Approval Filters -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" id="dateFilter" placeholder="Request Date">
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary" onclick="filterBudgetRequests()">
                                <i class="bi bi-funnel me-2"></i>Filter
                            </button>
                            <button class="btn btn-secondary ms-2" onclick="clearFilters()">
                                <i class="bi bi-x-circle me-2"></i>Clear
                            </button>
                        </div>
                        <div class="col-md-3 text-end">
                            <a href="{{ route('payroll.budget-requests.create') }}" class="btn btn-success">
                                <i class="bi bi-plus-circle me-2"></i>Add Budget Request
                            </a>
                        </div>
                    </div>
                    
                    <!-- Budget Approval Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="budgetRequestsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Details</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="budgetRequestsBody">
                                @if($budgetRequests->count() > 0)
                                    @foreach($budgetRequests as $request)
                                        <tr data-status="{{ $request->status }}" data-date="{{ $request->date }}">
                                            <td>#{{ $request->id }}</td>
                                            <td>{{ $request->details }}</td>
                                            <td class="text-end">₱{{ number_format($request->amount) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($request->date)->format('M d, Y') }}</td>
                                            <td>{!! getStatusBadge($request->status) !!}</td>
                                            <td>
                                                @if($request->status === 'pending')
                                                    <button class="btn btn-sm btn-primary" onclick="openApprovalModal({{ $request->id }}, '{{ $request->details }}', {{ $request->amount }}, '{{ $request->date }}')">
                                                        <i class="bi bi-pencil-square me-1"></i>Review
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-secondary" disabled>
                                                        <i class="bi bi-check-circle me-1"></i>{{ getStatusLabel($request->status) }}
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox me-2"></i>
                                            No budget requests found.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle me-2"></i>Approve/Reject Budget Request
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('payroll.budget-requests.update-status', ['budgetRequest' => ':id:']) }}" method="POST" id="approvalForm">
                @csrf
                @method('PATCH')
                <input type="hidden" id="requestId" name="id">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Request Details:</label>
                        <p id="modalDetails" class="form-control-plaintext"></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Amount:</label>
                        <p id="modalAmount" class="form-control-plaintext"></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Date:</label>
                        <p id="modalDate" class="form-control-plaintext"></p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label fw-bold">Action:</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="">Select Action</option>
                            <option value="approved">Approve</option>
                            <option value="rejected">Reject</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Filter budget requests
function filterBudgetRequests() {
    const statusFilter = document.getElementById('statusFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    const rows = document.querySelectorAll('#budgetRequestsBody tr');
    
    rows.forEach(row => {
        if (row.querySelector('td[colspan]')) return; // Skip "no results" row
        
        const status = row.getAttribute('data-status');
        const date = row.getAttribute('data-date');
        
        let showRow = true;
        
        if (statusFilter && status !== statusFilter) {
            showRow = false;
        }
        
        if (dateFilter && date !== dateFilter) {
            showRow = false;
        }
        
        row.style.display = showRow ? '' : 'none';
    });
}

// Clear filters
function clearFilters() {
    document.getElementById('statusFilter').value = '';
    document.getElementById('dateFilter').value = '';
    
    const rows = document.querySelectorAll('#budgetRequestsBody tr');
    rows.forEach(row => {
        row.style.display = '';
    });
}

// Open approval modal
function openApprovalModal(requestId, details, amount, date) {
    document.getElementById('requestId').value = requestId;
    document.getElementById('modalDetails').textContent = details;
    document.getElementById('modalAmount').textContent = '₱' + parseInt(amount).toLocaleString('en-PH');
    document.getElementById('modalDate').textContent = formatDate(date);
    document.getElementById('status').value = '';
    
    // Update form action with the correct budgetRequest ID
    const form = document.getElementById('approvalForm');
    const currentAction = form.getAttribute('action');
    form.setAttribute('action', currentAction.replace(':id:', requestId));
    
    new bootstrap.Modal(document.getElementById('approvalModal')).show();
}

// Helper functions
function getStatusBadge(status) {
    const badges = {
        pending: '<span class="badge bg-warning">Pending</span>',
        approved: '<span class="badge bg-success">Approved</span>',
        rejected: '<span class="badge bg-danger">Rejected</span>'
    };
    return badges[status] || status;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
}
</script>

@php
function getStatusBadge($status) {
    $classes = [
        'pending' => 'badge bg-warning',
        'approved' => 'badge bg-success', 
        'rejected' => 'badge bg-danger'
    ];
    
    $labels = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected'
    ];
    
    $class = $classes[$status] ?? 'badge bg-secondary';
    $label = $labels[$status] ?? ucfirst($status);
    
    return '<span class="' . $class . '">' . $label . '</span>';
}

function getStatusLabel($status) {
    return match ($status) {
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        default => ucfirst($status),
    };
}
@endphp
@endsection
