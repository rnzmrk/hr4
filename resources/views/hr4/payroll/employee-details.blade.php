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
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" id="searchInput" placeholder="Search employees...">
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <button class="btn btn-primary me-2" id="applySearch">
                                <i class="bi bi-search me-1"></i>Search
                            </button>
                            <button class="btn btn-outline-secondary" id="clearSearch">
                                <i class="bi bi-x-circle me-1"></i>Clear
                            </button>
                        </div>
                    </div>

                    <!-- Employee Details Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="employeeTable">
                            <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    <th>Employee Name</th>
                                    <th>Department</th>
                                    <th>Position</th>
                                    <th>Salary</th>
                                    <th>Benefits</th>
                                    <th>Incentives</th>
                                    <th>ATM Number</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="employeeTableBody">
                                <!-- Sample employee data -->
                                <tr>
                                    <td>EMP001</td>
                                    <td>Juan Dela Cruz</td>
                                    <td>IT</td>
                                    <td>Software Engineer</td>
                                    <td>₱45,000.00</td>
                                    <td>SSS, PhilHealth, Pag-IBIG</td>
                                    <td>₱5,000.00</td>
                                    <td>1234-5678-9012</td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-outline-primary btn-sm me-1" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>EMP002</td>
                                    <td>Maria Santos</td>
                                    <td>Human Resources</td>
                                    <td>HR Manager</td>
                                    <td>₱55,000.00</td>
                                    <td>SSS, PhilHealth, Pag-IBIG, HMO</td>
                                    <td>₱7,500.00</td>
                                    <td>2345-6789-0123</td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-outline-primary btn-sm me-1" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>EMP003</td>
                                    <td>Jose Reyes</td>
                                    <td>Finance</td>
                                    <td>Accountant</td>
                                    <td>₱40,000.00</td>
                                    <td>SSS, PhilHealth, Pag-IBIG</td>
                                    <td>₱3,000.00</td>
                                    <td>3456-7890-1234</td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-outline-primary btn-sm me-1" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>EMP004</td>
                                    <td>Ana Garcia</td>
                                    <td>Marketing</td>
                                    <td>Marketing Manager</td>
                                    <td>₱48,000.00</td>
                                    <td>SSS, PhilHealth, Pag-IBIG, HMO</td>
                                    <td>₱6,000.00</td>
                                    <td>4567-8901-2345</td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-outline-primary btn-sm me-1" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>EMP005</td>
                                    <td>Carlos Rodriguez</td>
                                    <td>Sales</td>
                                    <td>Sales Executive</td>
                                    <td>₱35,000.00</td>
                                    <td>SSS, PhilHealth, Pag-IBIG</td>
                                    <td>₱2,500.00</td>
                                    <td>5678-9012-3456</td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-outline-primary btn-sm me-1" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
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

<!-- Employee Details Modal -->
<div class="modal fade" id="employeeDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-person-badge me-2"></i>Employee Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
                                    <div class="col-6"><strong>Incentives:</strong></div>
                                    <div class="col-6" id="modalIncentives">-</div>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
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
        const viewButtons = document.querySelectorAll('button[title="View"]');
        
        console.log('Found view buttons:', viewButtons.length);
        
        // Handle view buttons
        viewButtons.forEach((button, index) => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('View button clicked:', index);
                
                // Get data from table row
                const row = this.closest('tr');
                if (!row) {
                    console.error('Could not find parent row');
                    return;
                }
                
                const cells = row.cells;
                const employeeRecord = {
                    employee_id: cells[0].textContent.trim(),
                    employee_name: cells[1].textContent.trim(),
                    department: cells[2].textContent.trim(),
                    position: cells[3].textContent.trim(),
                    salary: cells[4].textContent.trim(),
                    benefits: cells[5].textContent.trim(),
                    incentives: cells[6].textContent.trim(),
                    atm_number: cells[7].textContent.trim()
                };
                
                console.log('Employee record from table:', employeeRecord);
                
                // Show detailed view modal
                showEmployeeDetails(employeeRecord);
                
                // Show notification
                showNotification(`Viewing details for ${employeeRecord.employee_name}`, 'info');
            });
        });
    }
    
    // Initial setup
    setupButtonHandlers();
});

// Show employee details modal
function showEmployeeDetails(employee) {
    // Calculate total earnings (salary + incentives)
    const salaryValue = parseFloat(employee.salary.replace(/[₱,]/g, ''));
    const incentivesValue = parseFloat(employee.incentives.replace(/[₱,]/g, ''));
    const totalEarnings = salaryValue + incentivesValue;
    
    // Update modal content
    document.getElementById('modalEmployeeId').textContent = employee.employee_id;
    document.getElementById('modalEmployeeName').textContent = employee.employee_name;
    document.getElementById('modalDepartment').textContent = employee.department;
    document.getElementById('modalPosition').textContent = employee.position;
    document.getElementById('modalAtmNumber').textContent = employee.atm_number;
    document.getElementById('modalSalary').textContent = employee.salary;
    document.getElementById('modalIncentives').textContent = employee.incentives;
    document.getElementById('modalTotalEarnings').textContent = '₱' + totalEarnings.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    
    // Format benefits as badges
    const benefitsArray = employee.benefits.split(',').map(benefit => benefit.trim());
    const benefitsHtml = benefitsArray.map(benefit => `<span class="badge bg-info me-1">${benefit}</span>`).join('');
    document.getElementById('modalBenefits').innerHTML = benefitsHtml;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('employeeDetailsModal'));
    modal.show();
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
                    <tr><td style="padding: 5px; border-bottom: 1px solid #ddd;">Incentives:</td><td style="padding: 5px; text-align: right; border-bottom: 1px solid #ddd;">${document.getElementById('modalIncentives').textContent}</td></tr>
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
document.getElementById('applySearch').addEventListener('click', function() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#employeeTableBody tr');
    
    rows.forEach(row => {
        const employeeName = row.cells[1].textContent.toLowerCase();
        const employeeId = row.cells[0].textContent.toLowerCase();
        const shouldShow = !searchTerm || employeeName.includes(searchTerm) || employeeId.includes(searchTerm);
        row.style.display = shouldShow ? '' : 'none';
    });
    
    showNotification('Search applied successfully!', 'success');
});

document.getElementById('clearSearch').addEventListener('click', function() {
    document.getElementById('searchInput').value = '';
    
    const rows = document.querySelectorAll('#employeeTableBody tr');
    rows.forEach(row => {
        row.style.display = '';
    });
    
    showNotification('Search cleared!', 'info');
});

// Export to Excel functionality
document.getElementById('exportExcel').addEventListener('click', function() {
    let csvContent = 'Employee ID,Employee Name,Department,Position,Salary,Benefits,Incentives,ATM Number\n';
    
    const rows = document.querySelectorAll('#employeeTableBody tr');
    rows.forEach(row => {
        const cells = row.cells;
        const rowData = [
            cells[0].textContent.trim(),
            cells[1].textContent.trim(),
            cells[2].textContent.trim(),
            cells[3].textContent.trim(),
            cells[4].textContent.trim(),
            cells[5].textContent.trim(),
            cells[6].textContent.trim(),
            cells[7].textContent.trim()
        ];
        csvContent += rowData.join(',') + '\n';
    });
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `employee_details_${new Date().toISOString().slice(0, 10)}.csv`);
    link.style.visibility = 'hidden';
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
</script>
@endsection
