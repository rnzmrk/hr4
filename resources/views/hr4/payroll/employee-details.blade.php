@extends('layouts.app')

@section('page-title', 'Employee Details')
@section('page-subtitle', 'Manage employee information for payroll processing')
@section('breadcrumbs', 'Payroll / Employee Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-person-badge me-2"></i>Employee Details
                    </h5>
                    <div>
                        <button class="btn btn-light btn-sm me-2" id="exportExcel">
                            <i class="bi bi-file-earmark-excel me-1"></i>Export to Excel
                        </button>
                        <button class="btn btn-light btn-sm" id="refreshTable">
                            <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search Section -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('payroll.employee-details') }}" class="input-group" id="searchForm">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" name="search" id="searchInput" placeholder="Search employees..." value="{{ request('search') }}">
                            </form>
                        </div>
                        <div class="col-md-6 text-end">
                            <button class="btn btn-primary me-2" type="submit" form="searchForm">
                                <i class="bi bi-search me-1"></i>Search
                            </button>
                            <a class="btn btn-outline-secondary" href="{{ route('payroll.employee-details') }}">
                                <i class="bi bi-x-circle me-1"></i>Clear
                            </a>
                        </div>
                    </div>

                    <!-- Employee Details Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="employeeTable">
                            <thead>
                                <tr>
                                    <th>Employee Name</th>
                                    <th>Department</th>
                                    <th>Position</th>
                                    <th>Salary</th>
                                    <th>Benefits</th>
                                    <th>ATM Number</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="employeeTableBody">
                                @forelse($employees as $employee)
                                <tr>
                                    <td>{{ $employee['name'] }}</td>
                                    <td>{{ $employee['department'] }}</td>
                                    <td>{{ $employee['position'] }}</td>
                                    <td>{{ $employee['salary'] }}</td>
                                    <td>{{ $employee['benefits'] }}</td>
                                    <td>{{ $employee['atm_number'] }}</td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-outline-primary btn-sm me-1 view-employee" data-id="{{ $employee['id'] }}" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">No employees found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $employees->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Employee Details Modal -->
<div class="modal fade" id="employeeDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-person-badge me-2"></i>Employee Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="closeManualModal()"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border-primary">
                            <div class="card-header bg-light">
                                <h6 class="text-primary mb-0">
                                    <i class="bi bi-person me-1"></i>Employee Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6"><strong>Employee ID:</strong></div>
                                    <div class="col-6" id="modalEmployeeId">-</div>
                                </div>
                                <div class="row">
                                    <div class="col-6"><strong>Name:</strong></div>
                                    <div class="col-6" id="modalEmployeeName">-</div>
                                </div>
                                <div class="row">
                                    <div class="col-6"><strong>Department:</strong></div>
                                    <div class="col-6" id="modalDepartment">-</div>
                                </div>
                                <div class="row">
                                    <div class="col-6"><strong>Position:</strong></div>
                                    <div class="col-6" id="modalPosition">-</div>
                                </div>
                                <div class="row">
                                    <div class="col-6"><strong>ATM Number:</strong></div>
                                    <div class="col-6" id="modalAtmNumber">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-success">
                            <div class="card-header bg-light">
                                <h6 class="text-success mb-0">
                                    <i class="bi bi-cash-stack me-1"></i>Financial Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6"><strong>Salary:</strong></div>
                                    <div class="col-6" id="modalSalary">-</div>
                                </div>
                                <div class="row">
                                    <div class="col-6"><strong>Total Earnings:</strong></div>
                                    <div class="col-6" id="modalTotalEarnings">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-info">
                            <div class="card-header bg-light">
                                <h6 class="text-info mb-0">
                                    <i class="bi bi-shield-check me-1"></i>Benefits Coverage
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <strong>Benefits:</strong>
                                        <div id="modalBenefits" class="mt-2">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-success">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1">
                                        <i class="bi bi-info-circle me-2"></i>Employment Status
                                    </h5>
                                    <p class="mb-0" id="modalEmploymentStatus">Active employee with complete benefits coverage and competitive compensation package.</p>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="closeManualModal()">
                    <i class="bi bi-x-circle me-1"></i>Close
                </button>
                <button type="button" class="btn btn-primary" id="printEmployee">
                    <i class="bi bi-printer me-1"></i>Print Details
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Action button functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up employee button handlers');
    
    // Add click handlers to all view buttons
    function setupButtonHandlers() {
        const viewButtons = document.querySelectorAll('.view-employee');
        
        console.log('Found view buttons:', viewButtons.length);
        
        // Handle view buttons
        viewButtons.forEach((button) => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const employeeId = this.dataset.id;
                console.log('View button clicked for employee ID:', employeeId);
                
                if (!employeeId) {
                    console.error('No employee ID found on button');
                    showNotification('Employee ID not found', 'error');
                    return;
                }
                
                // Fetch employee details from API
                fetch(`/payroll/employee-details/${employeeId}`)
                    .then(response => {
                        console.log('Fetch response status:', response.status);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Employee data from API:', data);
                        if (data.error) {
                            showNotification(data.error, 'error');
                            return;
                        }
                        showEmployeeDetails(data);
                        showNotification(`Viewing details for ${data.name}`, 'info');
                    })
                    .catch(error => {
                        console.error('Error fetching employee details:', error);
                        showNotification('Error fetching employee details: ' + error.message, 'error');
                    });
            });
        });
    }
    
    // Initial setup
    setupButtonHandlers();
});

// Show employee details modal
function showEmployeeDetails(employee) {
    console.log('Showing employee details for:', employee);
    
    // Update modal content
    const modalEmployeeId = document.getElementById('modalEmployeeId');
    const modalEmployeeName = document.getElementById('modalEmployeeName');
    const modalDepartment = document.getElementById('modalDepartment');
    const modalPosition = document.getElementById('modalPosition');
    const modalAtmNumber = document.getElementById('modalAtmNumber');
    const modalSalary = document.getElementById('modalSalary');
    const modalTotalEarnings = document.getElementById('modalTotalEarnings');
    const modalBenefits = document.getElementById('modalBenefits');
    
    if (!modalEmployeeId || !modalEmployeeName || !modalDepartment || !modalPosition || 
        !modalAtmNumber || !modalSalary || !modalTotalEarnings || !modalBenefits) {
        console.error('Modal elements not found');
        showNotification('Modal elements not found', 'error');
        return;
    }
    
    modalEmployeeId.textContent = employee.employee_id;
    modalEmployeeName.textContent = employee.name;
    modalDepartment.textContent = employee.department;
    modalPosition.textContent = employee.position;
    modalAtmNumber.textContent = employee.atm_number;
    modalSalary.textContent = employee.salary;
    modalTotalEarnings.textContent = employee.salary; // Total earnings is just salary now
    
    // Format benefits as badges - handle both array and string
    let benefitsArray = [];
    if (Array.isArray(employee.benefits)) {
        benefitsArray = employee.benefits;
    } else if (typeof employee.benefits === 'string') {
        benefitsArray = employee.benefits.split(',').map(benefit => benefit.trim());
    }
    
    console.log('Benefits array:', benefitsArray);
    
    const benefitsHtml = benefitsArray.map(benefit => `<span class="badge bg-info me-1">${benefit}</span>`).join('');
    modalBenefits.innerHTML = benefitsHtml || '<span class="text-muted">No benefits</span>';
    
    // Show modal with fallback
    try {
        console.log('Attempting to show modal...');
        console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
        
        if (typeof bootstrap !== 'undefined') {
            console.log('Using Bootstrap 5 modal');
            const modalElement = document.getElementById('employeeDetailsModal');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        } else if (typeof $ !== 'undefined' && $.fn.modal) {
            console.log('Using jQuery modal');
            $('#employeeDetailsModal').modal('show');
        } else {
            console.error('Bootstrap not loaded, trying direct DOM manipulation');
            // Fallback: show modal manually
            const modalElement = document.getElementById('employeeDetailsModal');
            if (modalElement) {
                modalElement.style.display = 'block';
                modalElement.classList.add('show');
                modalElement.setAttribute('aria-modal', 'true');
                modalElement.setAttribute('role', 'dialog');
                
                // Add backdrop
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.id = 'modal-backdrop';
                document.body.appendChild(backdrop);
                document.body.classList.add('modal-open');
                
                console.log('Modal shown manually');
            } else {
                console.error('Modal element not found');
                showNotification('Modal element not found', 'error');
            }
        }
    } catch (error) {
        console.error('Error showing modal:', error);
        showNotification('Error showing modal: ' + error.message, 'error');
    }
}

// Print employee details
document.getElementById('printEmployee').addEventListener('click', function() {
    const employeeId = document.getElementById('modalEmployeeId').textContent;
    const employeeName = document.getElementById('modalEmployeeName').textContent;
    
    const printContent = `
        <div style="font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto;">
            <div style="text-align: center; border-bottom: 2px solid #007bff; padding-bottom: 20px; margin-bottom: 20px;">
                <h2 style="margin: 0; color: #007bff;">EMPLOYEE DETAILS</h2>
                <p style="margin: 5px 0;">Generated on ${new Date().toLocaleDateString()}</p>
            </div>
            
            <div style="margin-bottom: 30px;">
                <h4 style="margin: 0 0 10px 0; color: #333;">Employee Information</h4>
                <p style="margin: 5px 0;"><strong>ID:</strong> ${employeeId}</p>
                <p style="margin: 5px 0;"><strong>Name:</strong> ${employeeName}</p>
                <p style="margin: 5px 0;"><strong>Department:</strong> ${document.getElementById('modalDepartment').textContent}</p>
                <p style="margin: 5px 0;"><strong>Position:</strong> ${document.getElementById('modalPosition').textContent}</p>
                <p style="margin: 5px 0;"><strong>ATM Number:</strong> ${document.getElementById('modalAtmNumber').textContent}</p>
            </div>
            
            <div style="margin-bottom: 30px;">
                <h4 style="margin: 0 0 10px 0; color: #333;">Financial Information</h4>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr><td style="padding: 5px; border-bottom: 1px solid #ddd;">Salary:</td><td style="padding: 5px; text-align: right; border-bottom: 1px solid #ddd;">${document.getElementById('modalSalary').textContent}</td></tr>
                    <tr style="font-weight: bold;"><td style="padding: 5px; border-bottom: 2px solid #007bff;">Total Earnings:</td><td style="padding: 5px; text-align: right; border-bottom: 2px solid #007bff;">${document.getElementById('modalTotalEarnings').textContent}</td></tr>
                </table>
            </div>
            
            <div style="text-align: center; padding: 20px; background-color: #d4edda; border-radius: 5px;">
                <h3 style="margin: 0; color: #155724;">Active Employee</h3>
            </div>
            
            <div style="margin-top: 40px; text-align: center; color: #666; font-size: 12px;">
                <p>This is a computer-generated employee details report. No signature required.</p>
            </div>
        </div>
    `;
    
    // Create print window
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Employee Details - ${employeeName}</title>
            <style>
                @media print {
                    body { margin: 0; }
                    @page { margin: 20mm; }
                }
            </style>
        </head>
        <body>
            ${printContent}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
    
    showNotification('Employee details printed successfully!', 'success');
});

// Search functionality
// Search is handled server-side via GET form submission.

// Export to Excel functionality
document.getElementById('exportExcel').addEventListener('click', function() {
    const searchTerm = document.getElementById('searchInput') ? document.getElementById('searchInput').value : '';
    
    // Redirect to export endpoint with search parameter
    const url = `/payroll/employee-details/export${searchTerm ? '?search=' + encodeURIComponent(searchTerm) : ''}`;
    
    // Create a hidden link and trigger download
    const link = document.createElement('a');
    link.href = url;
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showNotification('Employee details exported successfully!', 'success');
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

// Close manual modal function
function closeManualModal() {
    const modalElement = document.getElementById('employeeDetailsModal');
    const backdrop = document.getElementById('modal-backdrop');
    
    if (modalElement) {
        modalElement.style.display = 'none';
        modalElement.classList.remove('show');
        modalElement.removeAttribute('aria-modal');
        modalElement.removeAttribute('role');
    }
    
    if (backdrop) {
        backdrop.remove();
    }
    
    document.body.classList.remove('modal-open');
}
</script>
@endsection
