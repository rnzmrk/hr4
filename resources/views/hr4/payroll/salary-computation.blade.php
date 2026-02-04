@extends('layouts.app')

@section('page-title', 'Salary Computation')
@section('page-subtitle', 'Calculate and manage employee salaries with AI-powered tax deductions')
@section('breadcrumbs', 'Payroll / Salary Computation')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-calculator me-2"></i>AI-Powered Salary Computation
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Salary Calculator Section -->
                    <div class="row">
                        <div class="col-md-5">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">
                                        <i class="bi bi-calculator me-2"></i>Salary Calculator
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <form id="salaryCalculatorForm">
                                        <div class="mb-3">
                                            <label for="employeeSearch" class="form-label fw-bold">Search Employee</label>
                                            <div class="input-group">
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="employeeSearch" 
                                                       placeholder="Type employee name..."
                                                       autocomplete="off">
                                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </div>
                                            <!-- Employee Search Results Dropdown -->
                                            <div id="employeeSearchResults" class="position-relative">
                                                <div class="dropdown-menu w-100 shadow" id="searchDropdown" style="display: none;">
                                                    <!-- Search results will be populated here -->
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Selected Employee Info -->
                                        <div class="mb-3" id="selectedEmployeeInfo" style="display: none;">
                                            <div class="alert alert-info py-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong id="selectedEmployeeName"></strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            <span id="selectedEmployeePosition"></span> in 
                                                            <span id="selectedEmployeeDepartment"></span>
                                                        </small>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" id="clearEmployee">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="baseSalary" class="form-label fw-bold">Base Monthly Salary</label>
                                            <div class="input-group">
                                                <span class="input-group-text">₱</span>
                                                <input type="number" 
                                                       class="form-control" 
                                                       id="baseSalary" 
                                                       placeholder="0.00"
                                                       step="0.01"
                                                       min="0">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Incentives</label>
                                            <div class="input-group">
                                                <span class="input-group-text">+</span>
                                                <input type="number" 
                                                       class="form-control" 
                                                       id="incentives" 
                                                       placeholder="0.00"
                                                       step="0.01"
                                                       min="0">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="payPeriod" class="form-label">Pay Period</label>
                                            <input type="month" class="form-control" id="payPeriod">
                                        </div>

                                        <button type="button" class="btn btn-primary w-100" id="generatePayroll">
                                            <i class="bi bi-calculator me-2"></i>Calculate Payroll
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="bi bi-receipt me-2"></i>Salary Breakdown
                                    </h6>
                                </div>
                                <div class="card-body" id="salaryBreakdown">
                                    <div class="text-center text-muted py-5">
                                        <i class="bi bi-calculator fs-1 d-block mb-3"></i>
                                        <p>Select an employee and click "Calculate Payroll"</p>
                                        <small class="text-muted">System will calculate SSS, Pag-IBIG, PhilHealth, and Tax based on Philippine 2025 rates</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Loading Indicator -->
                    <div id="loadingIndicator" class="text-center py-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Calculating with AI...</span>
                        </div>
                        <p class="mt-2 text-muted">AI is calculating Philippine tax deductions...</p>
                    </div>

                    <!-- AI Results Section -->
                    <div id="aiResults" class="mt-4" style="display: none;">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">
                                    <i class="bi bi-check-circle me-2"></i>AI Calculation Results
                                </h6>
                            </div>
                            <div class="card-body" id="aiCalculationResults">
                                <!-- AI results will be populated here -->
                            </div>
                        </div>
                    </div>

                    <!-- Payroll Records Table Section -->
                    <div class="mt-5">
                        <div class="card">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-cash-stack me-2"></i>Payroll Records
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
                                <!-- Filter Section -->
                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <label for="filterType" class="form-label fw-bold">Filter By</label>
                                        <select class="form-select" id="filterType">
                                            <option value="daily">Daily</option>
                                            <option value="monthly" selected>Monthly</option>
                                            <option value="yearly">Yearly</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="filterDate" class="form-label fw-bold">Select Date</label>
                                        <input type="date" class="form-control" id="filterDate">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="filterMonth" class="form-label fw-bold">Select Month</label>
                                        <input type="month" class="form-control" id="filterMonth">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="filterYear" class="form-label fw-bold">Select Year</label>
                                        <input type="number" class="form-control" id="filterYear" placeholder="2025" min="2020" max="2030">
                                    </div>
                                </div>

                                <!-- Search and Actions -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-search"></i>
                                            </span>
                                            <input type="text" class="form-control" id="tableSearch" placeholder="Search employee name...">
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <button class="btn btn-primary" id="applyFilter">
                                            <i class="bi bi-funnel me-1"></i>Apply Filter
                                        </button>
                                        <button class="btn btn-outline-secondary" id="clearFilter">
                                            <i class="bi bi-x-circle me-1"></i>Clear
                                        </button>
                                    </div>
                                </div>

                                <!-- Payroll Table -->
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="payrollTable">
                                        <thead>
                                            <tr>
                                                <th>Employee Name</th>
                                                <th>Department</th>
                                                <th>Position</th>
                                                <th>Base Salary</th>
                                                <th>Incentives</th>
                                                <th>SSS</th>
                                                <th>PhilHealth</th>
                                                <th>Pag-IBIG</th>
                                                <th>Income Tax</th>
                                                <th>Net Salary</th>
                                                <th>Pay Date</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="payrollTableBody">
                                            <!-- Data will be loaded from database via JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payroll Detail Modal -->
<div class="modal fade" id="payrollDetailModal" tabindex="-1" aria-labelledby="payrollDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="payrollDetailModalLabel">
                    <i class="bi bi-receipt me-2"></i>Payroll Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="payrollDetailContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading payroll details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="printPayslipBtn">
                    <i class="bi bi-printer me-2"></i>Print Payslip
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let selectedEmployee = null;

// Employee search functionality with API
document.getElementById('employeeSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const dropdown = document.getElementById('searchDropdown');
    
    if (searchTerm.length < 2) {
        dropdown.style.display = 'none';
        return;
    }
    
    // Call API to search employees
    fetch('/payroll/search-employees', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ search: searchTerm })
    })
    .then(response => response.json())
    .then(employees => {
        if (employees.length > 0) {
            dropdown.innerHTML = employees.map(emp => `
                <a href="#" class="dropdown-item employee-item" data-employee='${JSON.stringify(emp)}'>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${emp.first_name} ${emp.last_name}</strong>
                            <br>
                            <small class="text-muted">${emp.position || 'N/A'} - ${emp.department?.name || 'N/A'}</small>
                        </div>
                        <small class="text-primary">₱${(emp.salary || 0).toLocaleString()}</small>
                    </div>
                </a>
            `).join('');
            dropdown.style.display = 'block';
        } else {
            dropdown.innerHTML = '<a href="#" class="dropdown-item disabled">No employees found</a>';
            dropdown.style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Error searching employees:', error);
        dropdown.innerHTML = '<a href="#" class="dropdown-item disabled">Error searching employees</a>';
        dropdown.style.display = 'block';
    });
});

// Handle employee selection
document.addEventListener('click', function(e) {
    if (e.target.closest('.employee-item')) {
        e.preventDefault();
        const employeeData = JSON.parse(e.target.closest('.employee-item').dataset.employee);
        selectEmployee(employeeData);
    }
});

function selectEmployee(employee) {
    selectedEmployee = employee;
    
    // Update selected employee info
    document.getElementById('selectedEmployeeName').textContent = `${employee.first_name} ${employee.last_name}`;
    document.getElementById('selectedEmployeePosition').textContent = employee.position || 'N/A';
    document.getElementById('selectedEmployeeDepartment').textContent = employee.department?.name || 'N/A';
    document.getElementById('selectedEmployeeInfo').style.display = 'block';
    
    // Set base salary
    document.getElementById('baseSalary').value = employee.salary || 0;
    
    // Clear search
    document.getElementById('employeeSearch').value = '';
    document.getElementById('searchDropdown').style.display = 'none';
    
    // Calculate gross salary
    calculateGrossSalary();
}

// Clear employee selection
document.getElementById('clearEmployee').addEventListener('click', function() {
    selectedEmployee = null;
    document.getElementById('selectedEmployeeInfo').style.display = 'none';
    document.getElementById('employeeSearch').value = '';
    document.getElementById('baseSalary').value = '';
    document.getElementById('incentives').value = '';
    document.getElementById('salaryBreakdown').innerHTML = `
        <div class="text-center text-muted py-5">
            <i class="bi bi-calculator fs-1 d-block mb-3"></i>
            <p>Select an employee and click "Calculate Payroll"</p>
        </div>
    `;
    document.getElementById('aiResults').style.display = 'none';
});

// Clear search
document.getElementById('clearSearch').addEventListener('click', function() {
    document.getElementById('employeeSearch').value = '';
    document.getElementById('searchDropdown').style.display = 'none';
});

// Calculate gross salary
function calculateGrossSalary() {
    const baseSalary = parseFloat(document.getElementById('baseSalary').value) || 0;
    const incentives = parseFloat(document.getElementById('incentives').value) || 0;
    
    const grossSalary = baseSalary + incentives;
    return grossSalary;
}

// Update gross salary on input changes
document.getElementById('baseSalary').addEventListener('input', function() {
    // Gross salary is calculated dynamically in the calculatePayroll function
});

document.getElementById('incentives').addEventListener('input', function() {
    // Gross salary is calculated dynamically in the calculatePayroll function
});

// Generate AI-powered payroll calculation
document.getElementById('generatePayroll').addEventListener('click', async function() {
    if (!selectedEmployee) {
        alert('Please select an employee first');
        return;
    }
    
    const baseSalary = parseFloat(document.getElementById('baseSalary').value);
    
    if (baseSalary <= 0) {
        alert('Please enter a valid salary amount');
        return;
    }
    
    // Show loading
    document.getElementById('loadingIndicator').style.display = 'block';
    document.getElementById('aiResults').style.display = 'none';
    
    try {
        // Call API to calculate payroll
        const response = await fetch('/payroll/calculate-payroll', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                employee_id: selectedEmployee.id,
                base_salary: parseFloat(document.getElementById('baseSalary').value),
                overtime: 0, // Set to 0 since field is removed
                absent: 0, // Set to 0 since field is removed
                additions: 0, // Set to 0 since field is removed
                incentives: parseFloat(document.getElementById('incentives').value) || 0,
            })
        });
        
        const result = await response.json();
        
        if (response.ok) {
            displayAIResults(result);
        } else {
            throw new Error(result.message || 'Calculation failed');
        }
        
    } catch (error) {
        console.error('AI calculation error:', error);
        alert('Error calculating payroll: ' + error.message);
    } finally {
        document.getElementById('loadingIndicator').style.display = 'none';
    }
});

// Display AI calculation results
function displayAIResults(data) {
    const breakdownHtml = `
        <div class="salary-breakdown">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <h6 class="text-primary mb-1">Gross Salary</h6>
                            <h4 class="mb-0">₱${data.gross_salary.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <h6 class="text-success mb-1">Net Salary</h6>
                            <h4 class="mb-0">₱${data.net_pay.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</h4>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Government Deductions</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                        <div>
                                            <strong>SSS</strong>
                                            <br>
                                            <small class="text-muted">Social Security System</small>
                                        </div>
                                        <div class="text-end">
                                            <strong>₱${data.sss.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                        <div>
                                            <strong>PhilHealth</strong>
                                            <br>
                                            <small class="text-muted">Health Insurance</small>
                                        </div>
                                        <div class="text-end">
                                            <strong>₱${data.philhealth.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                        <div>
                                            <strong>Pag-IBIG</strong>
                                            <br>
                                            <small class="text-muted">Home Development Fund</small>
                                        </div>
                                        <div class="text-end">
                                            <strong>₱${data.pagibig.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                        <div>
                                            <strong>Income Tax</strong>
                                            <br>
                                            <small class="text-muted">BIR Income Tax</small>
                                        </div>
                                        <div class="text-end">
                                            <strong>₱${data.income_tax.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Total Deductions</h5>
                                        <h5 class="mb-0 text-danger">₱${data.total_deductions.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-3">
                <button type="button" class="btn btn-success btn-lg" onclick="savePayroll()">
                    <i class="bi bi-save me-2"></i>Save to Payroll Database
                </button>
            </div>
        </div>
    `;
    
    document.getElementById('salaryBreakdown').innerHTML = breakdownHtml;
    
    // Store calculation data for saving
    window.currentCalculation = data;
}

// Save payroll to database
function savePayroll() {
    if (!selectedEmployee || !window.currentCalculation) {
        alert('No calculation data available');
        return;
    }
    
    const payDate = document.getElementById('payPeriod').value;
    if (!payDate) {
        alert('Please select a pay period');
        return;
    }
    
    fetch('/payroll/save-payroll', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            employee_id: selectedEmployee.id,
            base_salary: parseFloat(document.getElementById('baseSalary').value),
            gross_salary: window.currentCalculation.gross_salary,
            sss: window.currentCalculation.sss,
            philhealth: window.currentCalculation.philhealth,
            pagibig: window.currentCalculation.pagibig,
            income_tax: window.currentCalculation.income_tax,
            incentives: parseFloat(document.getElementById('incentives').value) || 0,
            net_pay: window.currentCalculation.net_pay,
            pay_date: payDate + '-15', // Use 15th day of the month
        })
    })
    .then(response => {
        // Check if response is JSON (not HTML error page)
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Server returned non-JSON response. Please check server logs.');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Payroll saved successfully!');
            // Clear form
            document.getElementById('clearEmployee').click();
            // Refresh table
            loadPayrollRecords();
        } else {
            // Handle validation errors
            if (data.errors) {
                let errorMessage = 'Validation errors:\n';
                Object.keys(data.errors).forEach(key => {
                    errorMessage += `- ${key}: ${data.errors[key].join(', ')}\n`;
                });
                alert(errorMessage);
            } else {
                alert('Error saving payroll: ' + data.message);
            }
        }
    })
    .catch(error => {
        console.error('Error saving payroll:', error);
        alert('Error saving payroll: ' + error.message);
    });
}

// Load payroll records
function loadPayrollRecords() {
    const filterType = document.getElementById('filterType')?.value || 'monthly';
    const filterDate = document.getElementById('filterDate')?.value || document.getElementById('filterMonth')?.value || new Date().toISOString().split('T')[0];
    const tableSearch = document.getElementById('tableSearch')?.value || '';
    
    const params = new URLSearchParams();
    if (filterType) params.append('filter_type', filterType);
    if (filterDate) params.append('filter_date', filterDate);
    if (tableSearch) params.append('search', tableSearch);
    
    fetch(`/payroll/get-payroll-records?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            updatePayrollTable(data.data || []);
        })
        .catch(error => {
            console.error('Error loading payroll records:', error);
        });
}

// Update payroll table
function updatePayrollTable(payrolls) {
    const tbody = document.getElementById('payrollTableBody');
    
    if (payrolls.length === 0) {
        tbody.innerHTML = '<tr><td colspan="12" class="text-center py-4">No payroll records found</td></tr>';
        return;
    }
    
    tbody.innerHTML = payrolls.map(payroll => `
        <tr>
            <td>${payroll.employee_name}</td>
            <td>${payroll.department}</td>
            <td>${payroll.position}</td>
            <td>₱${parseFloat(payroll.salary).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td>₱${parseFloat(payroll.incentives || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td class="text-primary">₱${parseFloat(payroll.sss).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td class="text-info">₱${parseFloat(payroll.philhealth).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td class="text-success">₱${parseFloat(payroll.pagibig).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td class="text-warning">₱${parseFloat(payroll.income_tax).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td class="text-success fw-bold">₱${parseFloat(payroll.net_pay).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            <td>${payroll.pay_date}</td>
            <td class="text-end">
                <button type="button" class="btn btn-outline-primary btn-sm view-payroll-btn" title="View" data-bs-toggle="modal" data-bs-target="#payrollDetailModal" data-id="${payroll.id}">
                    <i class="bi bi-eye"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

// Filter and search functionality
document.getElementById('applyFilter')?.addEventListener('click', loadPayrollRecords);
document.getElementById('clearFilter')?.addEventListener('click', function() {
    document.getElementById('filterType').value = 'monthly';
    document.getElementById('filterMonth').value = new Date().toISOString().slice(0, 7);
    document.getElementById('tableSearch').value = '';
    loadPayrollRecords();
});

// Refresh table
document.getElementById('refreshTable')?.addEventListener('click', loadPayrollRecords);

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set current month as default
    const payPeriod = document.getElementById('payPeriod');
    if (payPeriod) {
        payPeriod.value = new Date().toISOString().slice(0, 7);
    }
    
    // Load payroll records
    loadPayrollRecords();
});

// Handle payroll detail modal
document.addEventListener('click', function(e) {
    if (e.target.closest('.view-payroll-btn')) {
        const button = e.target.closest('.view-payroll-btn');
        const payrollId = button.dataset.id;
        loadPayrollDetails(payrollId);
    }
});

// Load payroll details into modal
function loadPayrollDetails(payrollId) {
    const modalContent = document.getElementById('payrollDetailContent');
    
    // Show loading spinner
    modalContent.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading payroll details...</p>
        </div>
    `;
    
    // Fetch payroll details
    fetch(`/payroll/get-payroll-details/${payrollId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayPayrollDetails(data.payroll);
            } else {
                modalContent.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Error: ${data.message || 'Failed to load payroll details'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading payroll details:', error);
            modalContent.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error loading payroll details. Please try again.
                </div>
            `;
        });
}

// Display payroll details in modal
function displayPayrollDetails(payroll) {
    const modalContent = document.getElementById('payrollDetailContent');
    
    modalContent.innerHTML = `
        <div class="payslip-details">
            <!-- Employee Information -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-primary mb-3">Employee Information</h6>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td>${payroll.employee_name}</td>
                        </tr>
                        <tr>
                            <td><strong>Position:</strong></td>
                            <td>${payroll.position}</td>
                        </tr>
                        <tr>
                            <td><strong>Department:</strong></td>
                            <td>${payroll.department}</td>
                        </tr>
                        <tr>
                            <td><strong>Pay Date:</strong></td>
                            <td>${payroll.pay_date}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-success mb-3">Salary Summary</h6>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Base Salary:</strong></td>
                            <td class="text-end">₱${parseFloat(payroll.salary).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                        </tr>
                        <tr>
                            <td><strong>Incentives:</strong></td>
                            <td class="text-end">₱${parseFloat(payroll.incentives || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                        </tr>
                        <tr class="table-success">
                            <td><strong>Gross Salary:</strong></td>
                            <td class="text-end fw-bold">₱${(parseFloat(payroll.salary) + parseFloat(payroll.incentives || 0)).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Deductions -->
            <div class="row mb-4">
                <div class="col-12">
                    <h6 class="text-danger mb-3">Deductions</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>SSS:</strong></td>
                                    <td class="text-end">₱${parseFloat(payroll.sss).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                </tr>
                                <tr>
                                    <td><strong>PhilHealth:</strong></td>
                                    <td class="text-end">₱${parseFloat(payroll.philhealth).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                </tr>
                                <tr>
                                    <td><strong>Pag-IBIG:</strong></td>
                                    <td class="text-end">₱${parseFloat(payroll.pagibig).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Income Tax:</strong></td>
                                    <td class="text-end">₱${parseFloat(payroll.income_tax).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                </tr>
                                <tr class="table-danger">
                                    <td><strong>Total Deductions:</strong></td>
                                    <td class="text-end fw-bold">₱${(parseFloat(payroll.sss) + parseFloat(payroll.philhealth) + parseFloat(payroll.pagibig) + parseFloat(payroll.income_tax)).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Net Pay -->
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-success mb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Net Salary</h5>
                            <h4 class="mb-0">₱${parseFloat(payroll.net_pay).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Print payslip functionality
document.getElementById('printPayslipBtn')?.addEventListener('click', function() {
    const modalContent = document.getElementById('payrollDetailContent').innerHTML;
    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Payslip</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                .table td { padding: 8px; border-bottom: 1px solid #ddd; }
                .text-end { text-align: right; }
                .fw-bold { font-weight: bold; }
                .text-primary { color: #007bff; }
                .text-success { color: #28a745; }
                .text-danger { color: #dc3545; }
                h6 { margin-bottom: 10px; }
                .alert { padding: 15px; border-radius: 4px; margin-bottom: 20px; }
                .alert-success { background-color: #d4edda; border-color: #c3e6cb; color: #155724; }
            </style>
        </head>
        <body>
            ${modalContent}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.print();
});
</script>
@endsection
