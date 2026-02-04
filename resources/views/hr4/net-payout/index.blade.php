@extends('layouts.app')

@section('page-title', 'Net Payout')
@section('page-subtitle', 'View and manage net payout summaries')
@section('breadcrumbs', 'Payroll / Net Payout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 mb-0">
                    <i class="bi bi-wallet2 me-2"></i>Net Payout Records
                </h2>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                        <i class="bi bi-funnel me-2"></i>Filter
                    </button>
                    <button type="button" class="btn btn-primary" id="exportBtn">
                        <i class="bi bi-download me-2"></i>Export
                    </button>
                </div>
            </div>

            <!-- Recent Payouts Table -->
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Net Payout Records</h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPayoutModal">
                        <i class="bi bi-plus-circle me-2"></i>Add Payout
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="payoutsTable">
                            <thead>
                                <tr>
                                    <th>Total Salary</th>
                                    <th>Total SSS</th>
                                    <th>Total Pag-IBIG</th>
                                    <th>Total PhilHealth</th>
                                    <th>Total Income Tax</th>
                                    <th>Total Net</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="payoutsTableBody">
                                @forelse($recentPayouts as $payout)
                                <tr>
                                    <td class="text-end">₱{{ number_format($payout->total_salary, 2) }}</td>
                                    <td class="text-end">₱{{ number_format($payout->total_sss, 2) }}</td>
                                    <td class="text-end">₱{{ number_format($payout->total_pagibig, 2) }}</td>
                                    <td class="text-end">₱{{ number_format($payout->total_philhealth, 2) }}</td>
                                    <td class="text-end">₱{{ number_format($payout->total_income_tax, 2) }}</td>
                                    <td class="text-end fw-bold">₱{{ number_format($payout->total_net, 2) }}</td>
                                    <td>{{ $payout->date->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('net-payout.show', $payout->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        No payout records found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted" id="paginationInfo">
                            Showing {{ $recentPayouts->count() }} records
                        </div>
                        <nav id="paginationLinks">
                            {{-- Pagination will be loaded dynamically --}}
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Payouts</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Month</label>
                    <input type="month" class="form-control" id="monthFilter">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="applyFilter">Apply Filter</button>
                <button type="button" class="btn btn-outline-secondary" id="clearFilter">Clear</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Payout Modal -->
<div class="modal fade" id="addPayoutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>Add Net Payout
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addPayoutForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Total Salary</label>
                                <input type="number" class="form-control" name="total_salary" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Total SSS</label>
                                <input type="number" class="form-control" name="total_sss" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Total Pag-IBIG</label>
                                <input type="number" class="form-control" name="total_pagibig" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Total PhilHealth</label>
                                <input type="number" class="form-control" name="total_philhealth" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Total Income Tax</label>
                                <input type="number" class="form-control" name="total_income_tax" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Total Net</label>
                                <input type="number" class="form-control" name="total_net" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" name="date" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="savePayoutBtn">
                    <i class="bi bi-save me-2"></i>Save Payout
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Load payouts on page load
document.addEventListener('DOMContentLoaded', function() {
    loadPayouts();
});

// Load payouts function
function loadPayouts(page = 1) {
    const month = document.getElementById('monthFilter')?.value || '';
    
    fetch(`/net-payout/data?page=${page}&month=${encodeURIComponent(month)}`)
        .then(response => response.json())
        .then(data => {
            updatePayoutsTable(data.data);
            updatePagination(data);
        })
        .catch(error => {
            console.error('Error loading payouts:', error);
        });
}

// Update payouts table
function updatePayoutsTable(payouts) {
    const tbody = document.getElementById('payoutsTableBody');
    
    if (payouts.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    No payout records found
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = payouts.map(payout => `
        <tr>
            <td class="text-end">₱${parseFloat(payout.total_salary).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td class="text-end">₱${parseFloat(payout.total_sss).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td class="text-end">₱${parseFloat(payout.total_pagibig).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td class="text-end">₱${parseFloat(payout.total_philhealth).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td class="text-end">₱${parseFloat(payout.total_income_tax).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td class="text-end fw-bold">₱${parseFloat(payout.total_net).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td>${new Date(payout.date).toLocaleDateString()}</td>
            <td>
                <a href="/net-payout/${payout.id}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye"></i>
                </a>
            </td>
        </tr>
    `).join('');
}

// Update pagination
function updatePagination(data) {
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationLinks = document.getElementById('paginationLinks');
    
    paginationInfo.textContent = `Showing ${data.data.length} of ${data.total} records`;
    
    if (data.last_page > 1) {
        let links = '<nav><ul class="pagination mb-0">';
        
        // Previous
        if (data.prev_page_url) {
            links += `<li class="page-item"><a class="page-link" href="#" data-page="${data.current_page - 1}">Previous</a></li>`;
        }
        
        // Page numbers
        for (let i = 1; i <= data.last_page; i++) {
            const active = i === data.current_page ? 'active' : '';
            links += `<li class="page-item ${active}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
        }
        
        // Next
        if (data.next_page_url) {
            links += `<li class="page-item"><a class="page-link" href="#" data-page="${data.current_page + 1}">Next</a></li>`;
        }
        
        links += '</ul></nav>';
        paginationLinks.innerHTML = links;
        
        // Add click handlers
        paginationLinks.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = parseInt(e.target.dataset.page);
                loadPayouts(page);
            });
        });
    } else {
        paginationLinks.innerHTML = '';
    }
}

// Filter handlers
document.getElementById('applyFilter')?.addEventListener('click', function() {
    loadPayouts();
    const modal = bootstrap.Modal.getInstance(document.getElementById('filterModal'));
    modal.hide();
});

document.getElementById('clearFilter')?.addEventListener('click', function() {
    document.getElementById('monthFilter').value = '';
    loadPayouts();
    const modal = bootstrap.Modal.getInstance(document.getElementById('filterModal'));
    modal.hide();
});

// Save payout
document.getElementById('savePayoutBtn')?.addEventListener('click', function() {
    const form = document.getElementById('addPayoutForm');
    const formData = new FormData(form);
    
    fetch('/net-payout/store', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addPayoutModal'));
            modal.hide();
            
            // Reset form
            form.reset();
            
            // Reload payouts
            loadPayouts();
            
            // Show success message
            alert('Payout saved successfully!');
        } else {
            alert('Error saving payout: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error saving payout:', error);
        alert('Error saving payout. Please try again.');
    });
});

// Export functionality
document.getElementById('exportBtn')?.addEventListener('click', function() {
    // Simple CSV export
    const table = document.getElementById('payoutsTable');
    let csv = [];
    
    // Headers
    const headers = Array.from(table.querySelectorAll('thead th')).slice(0, -1).map(th => th.textContent);
    csv.push(headers.join(','));
    
    // Data rows
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('td')).slice(0, -1);
        const rowData = cells.map(cell => cell.textContent.replace(/₱/g, '').replace(/,/g, ''));
        csv.push(rowData.join(','));
    });
    
    // Download CSV
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `net_payout_${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
});
</script>
@endsection
