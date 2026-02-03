@extends('layouts.app')

@section('page-title', 'Attendance Record')
@section('page-subtitle', 'View and manage employee attendance records')
@section('breadcrumbs', 'Payroll / Attendance Record')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-check me-2"></i>Attendance Records
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Search and Filter Section -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" id="searchInput" placeholder="Search employees...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <input type="month" class="form-control" id="monthFilter" value="2025-12">
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-primary me-2" id="applyFilters">
                                <i class="bi bi-funnel me-1"></i>Apply Filters
                            </button>
                            <button class="btn btn-outline-secondary" id="clearFilters">
                                <i class="bi bi-x-circle me-1"></i>Clear
                            </button>
                        </div>
                    </div>
                    
                    <!-- Attendance Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Employee Name</th>
                                    <th>Month Start</th>
                                    <th>Overtime Hours</th>
                                    <th>Present Days</th>
                                    <th>Absent Days</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($attendanceData->count() > 0)
                                    @foreach($attendanceData as $attendance)
                                        <tr class="attendance-row">
                                            <td>{{ $attendance['employee_name'] }}</td>
                                            <td>{{ $attendance['month_start_date'] }}</td>
                                            <td>{{ number_format($attendance['overtime_hours'], 2) }}</td>
                                            <td>{{ $attendance['present_days'] }}</td>
                                            <td>{{ $attendance['absent_days'] }}</td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-outline-primary btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#attendanceDetailsModal"
                                                        onclick="showAttendanceDetails({
                                                            employee_name: '{{ $attendance['employee_name'] }}',
                                                            department: '{{ $attendance['department'] }}',
                                                            month_start_date: '{{ $attendance['month_start_date'] }}',
                                                            generated_at: '{{ $attendance['generated_at'] }}',
                                                            overtime_hours: '{{ $attendance['overtime_hours'] }}',
                                                            present_days: {{ $attendance['present_days'] }},
                                                            absent_days: {{ $attendance['absent_days'] }}
                                                        })">
                                                    <i class="bi bi-eye"></i> View
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox me-2"></i>
                                            No attendance data available
                                        </td>
                                    </tr>
                                @endif
                                <tr id="noResultsRow" style="display: none;">
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-search me-2"></i>
                                        No results found matching your filters
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Details Modal -->
<div class="modal fade" id="attendanceDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-person-badge me-2"></i>Attendance Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-primary">Employee Information</h6>
                        <p><strong>Name:</strong> <span id="modalEmployeeName">-</span></p>
                        <p><strong>Department:</strong> <span id="modalDepartment">-</span></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-info">Period Information</h6>
                        <p><strong>Month Start:</strong> <span id="modalMonthStart">-</span></p>
                        <p><strong>Generated At:</strong> <span id="modalGeneratedAt">-</span></p>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-success">Attendance Summary</h6>
                        <p><strong>Present Days:</strong> <span id="modalPresentDays">-</span></p>
                        <p><strong>Absent Days:</strong> <span id="modalAbsentDays">-</span></p>
                        <p><strong>Overtime Hours:</strong> <span id="modalOvertimeHours">-</span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function showAttendanceDetails(attendance) {
    document.getElementById('modalEmployeeName').textContent = attendance.employee_name;
    document.getElementById('modalDepartment').textContent = attendance.department;
    document.getElementById('modalMonthStart').textContent = attendance.month_start_date;
    document.getElementById('modalGeneratedAt').textContent = attendance.generated_at;
    document.getElementById('modalPresentDays').textContent = attendance.present_days;
    document.getElementById('modalAbsentDays').textContent = attendance.absent_days;
    document.getElementById('modalOvertimeHours').textContent = attendance.overtime_hours + ' hours';
    
    new bootstrap.Modal(document.getElementById('attendanceDetailsModal')).show();
}

// Search and Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const applyFiltersBtn = document.getElementById('applyFilters');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const searchInput = document.getElementById('searchInput');
    const monthFilter = document.getElementById('monthFilter');

    // Apply filters
    applyFiltersBtn.addEventListener('click', function() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const monthValue = monthFilter.value;
        
        const rows = document.querySelectorAll('.attendance-row');
        const noResultsRow = document.getElementById('noResultsRow');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const employeeName = row.cells[0].textContent.toLowerCase();
            const monthStart = row.cells[1].textContent;
            
            const matchesSearch = !searchTerm || employeeName.includes(searchTerm);
            const matchesMonth = !monthValue || monthStart.startsWith(monthValue);
            
            const shouldShow = matchesSearch && matchesMonth;
            row.style.display = shouldShow ? '' : 'none';
            
            if (shouldShow) visibleCount++;
        });
        
        // Show/hide no results message
        if (visibleCount === 0 && rows.length > 0) {
            noResultsRow.style.display = '';
            showNotification('No records found matching your filters.', 'warning');
        } else {
            noResultsRow.style.display = 'none';
            showNotification(`Found ${visibleCount} records matching your filters.`, 'success');
        }
    });

    // Clear filters
    clearFiltersBtn.addEventListener('click', function() {
        searchInput.value = '';
        monthFilter.value = '2025-12';
        
        const rows = document.querySelectorAll('.attendance-row');
        const noResultsRow = document.getElementById('noResultsRow');
        
        rows.forEach(row => {
            row.style.display = '';
        });
        
        noResultsRow.style.display = 'none';
        showNotification('Filters cleared!', 'info');
    });

    // Search on Enter key
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            applyFiltersBtn.click();
        }
    });

    // Auto-apply filter when month changes
    monthFilter.addEventListener('change', function() {
        applyFiltersBtn.click();
    });
});

// Show notification function
function showNotification(message, type = 'info') {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHtml);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert:last-child');
        if (alert) {
            alert.remove();
        }
    }, 3000);
}
</script>
@endsection
