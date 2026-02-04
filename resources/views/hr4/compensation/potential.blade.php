@extends('layouts.app')

@section('page-title', 'Employee Potential')
@section('page-subtitle', 'View and manage high-potential employees')
@section('breadcrumbs', 'Compensation / Potential')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 mb-0">
                    <i class="bi bi-graph-up-arrow me-2"></i>Employee Potential
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

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Total High Potentials</h6>
                                    <h3 class="mb-0">{{ count($potentials) }}</h3>
                                </div>
                                <div class="text-primary">
                                    <i class="bi bi-people fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Ready for Promotion</h6>
                                    <h3 class="mb-0">{{ collect($potentials)->where('readiness_level', 'Ready for promotion')->count() }}</h3>
                                </div>
                                <div class="text-success">
                                    <i class="bi bi-arrow-up-circle fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Avg Performance Score</h6>
                                    <h3 class="mb-0">{{ number_format(collect($potentials)->avg('current_performance'), 1) }}</h3>
                                </div>
                                <div class="text-warning">
                                    <i class="bi bi-star fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Avg Potential Score</h6>
                                    <h3 class="mb-0">{{ number_format(collect($potentials)->avg('potential_score'), 1) }}</h3>
                                </div>
                                <div class="text-info">
                                    <i class="bi bi-trophy fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Potential Employees Table -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">High Potential Employees</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Position</th>
                                    <th>Department</th>
                                    <th>Performance Score</th>
                                    <th>Potential Score</th>
                                    <th>Readiness Level</th>
                                    <th>Development Needs</th>
                                    <th>Recommended Action</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($potentials as $potential)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                {{ substr($potential['name'], 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $potential['name'] }}</div>
                                                <small class="text-muted">ID: {{ $potential['id'] }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $potential['position'] }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $potential['department'] }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-success">{{ $potential['current_performance'] }}</span>
                                            <div class="progress ms-2" style="width: 60px; height: 6px;">
                                                <div class="progress-bar bg-success" style="width: {{ ($potential['current_performance'] / 5) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-primary">{{ $potential['potential_score'] }}</span>
                                            <div class="progress ms-2" style="width: 60px; height: 6px;">
                                                <div class="progress-bar bg-primary" style="width: {{ ($potential['potential_score'] / 5) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $readinessClass = match($potential['readiness_level']) {
                                                'Ready for promotion' => 'success',
                                                'Ready in 6 months' => 'warning',
                                                'High potential' => 'info',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $readinessClass }}">{{ $potential['readiness_level'] }}</span>
                                    </td>
                                    <td>{{ $potential['development_needs'] }}</td>
                                    <td>
                                        <span class="text-primary fw-medium">{{ $potential['recommended_action'] }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-success" data-bs-toggle="tooltip" title="Development Plan">
                                                <i class="bi bi-clipboard-data"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-info" data-bs-toggle="tooltip" title="Send Feedback">
                                                <i class="bi bi-chat-dots"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        No high potential employees found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
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
                <h5 class="modal-title">Filter Employees</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Department</label>
                    <select class="form-select" id="departmentFilter">
                        <option value="">All Departments</option>
                        <option value="IT">IT</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Sales">Sales</option>
                        <option value="HR">HR</option>
                        <option value="Finance">Finance</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Readiness Level</label>
                    <select class="form-select" id="readinessFilter">
                        <option value="">All Levels</option>
                        <option value="Ready for promotion">Ready for promotion</option>
                        <option value="Ready in 6 months">Ready in 6 months</option>
                        <option value="High potential">High potential</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Min Performance Score</label>
                    <input type="range" class="form-range" id="performanceFilter" min="0" max="5" step="0.1" value="0">
                    <div class="d-flex justify-content-between">
                        <small>0</small>
                        <span id="performanceValue">0</span>
                        <small>5</small>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Min Potential Score</label>
                    <input type="range" class="form-range" id="potentialFilter" min="0" max="5" step="0.1" value="0">
                    <div class="d-flex justify-content-between">
                        <small>0</small>
                        <span id="potentialValue">0</span>
                        <small>5</small>
                    </div>
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

<script>
// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});

// Range slider updates
document.getElementById('performanceFilter')?.addEventListener('input', function() {
    document.getElementById('performanceValue').textContent = this.value;
});

document.getElementById('potentialFilter')?.addEventListener('input', function() {
    document.getElementById('potentialValue').textContent = this.value;
});

// Filter functionality
document.getElementById('applyFilter')?.addEventListener('click', function() {
    // Apply filter logic here
    const modal = bootstrap.Modal.getInstance(document.getElementById('filterModal'));
    modal.hide();
});

document.getElementById('clearFilter')?.addEventListener('click', function() {
    document.getElementById('departmentFilter').value = '';
    document.getElementById('readinessFilter').value = '';
    document.getElementById('performanceFilter').value = 0;
    document.getElementById('potentialFilter').value = 0;
    document.getElementById('performanceValue').textContent = '0';
    document.getElementById('potentialValue').textContent = '0';
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('filterModal'));
    modal.hide();
});

// Export functionality
document.getElementById('exportBtn')?.addEventListener('click', function() {
    // Simple CSV export
    const table = document.querySelector('table');
    let csv = [];
    
    // Headers
    const headers = Array.from(table.querySelectorAll('thead th')).slice(0, -1).map(th => th.textContent);
    csv.push(headers.join(','));
    
    // Data rows
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('td')).slice(0, -1);
        const rowData = cells.map(cell => cell.textContent.trim());
        csv.push(rowData.join(','));
    });
    
    // Download CSV
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `employee_potential_${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
});
</script>
@endsection
