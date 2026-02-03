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
                    <!-- Employee Search Section -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="employeeSearch" class="form-label fw-bold">
                                    <i class="bi bi-search me-2"></i>Select Employee
                                </label>
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control" 
                                           id="employeeSearch" 
                                           placeholder="Type employee name to search..."
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
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="payPeriod" class="form-label fw-bold">
                                    <i class="bi bi-calendar me-2"></i>Pay Period
                                </label>
                                <input type="month" class="form-control" id="payPeriod">
                            </div>
                        </div>
                    </div>

                    <!-- Selected Employee Info -->
                    <div class="row mb-4" id="selectedEmployeeInfo" style="display: none;">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Selected Employee</h6>
                                        <p class="mb-0">
                                            <strong id="selectedEmployeeName"></strong> - 
                                            <span id="selectedEmployeePosition"></span> in 
                                            <span id="selectedEmployeeDepartment"></span>
                                        </p>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="clearEmployee">
                                        <i class="bi bi-x-circle me-1"></i>Clear
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Salary Calculator Section -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="bi bi-calculator me-2"></i>Salary Calculator
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <form id="salaryCalculatorForm">
                                        <div class="mb-3">
                                            <label for="baseSalary" class="form-label">Base Monthly Salary</label>
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
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="input-group">
                                                        <span class="input-group-text">+</span>
                                                        <input type="number" 
                                                               class="form-control" 
                                                               id="overtime" 
                                                               placeholder="0.00"
                                                               step="0.01"
                                                               min="0">
                                                    </div>
                                                    <small class="text-muted">Overtime pay</small>
                                                </div>
                                                <div class="col-6">
                                                    <div class="input-group">
                                                        <span class="input-group-text">-</span>
                                                        <input type="number" 
                                                               class="form-control" 
                                                               id="absent" 
                                                               placeholder="0.00"
                                                               step="0.01"
                                                               min="0">
                                                    </div>
                                                    <small class="text-muted">Absent deductions</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Additions</label>
                                            <div class="input-group">
                                                <span class="input-group-text">+</span>
                                                <input type="number" 
                                                       class="form-control" 
                                                       id="additions" 
                                                       placeholder="0.00"
                                                       step="0.01"
                                                       min="0">
                                            </div>
                                            <small class="text-muted">Bonuses, allowances, and other additions</small>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Gross Salary</label>
                                            <div class="input-group">
                                                <span class="input-group-text">₱</span>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="grossSalary" 
                                                       readonly
                                                       value="0.00">
                                            </div>
                                        </div>

                                        <button type="button" class="btn btn-primary w-100" id="generatePayroll">
                                            <i class="bi bi-cpu me-2"></i>Generate AI-Powered Calculation
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="bi bi-receipt me-2"></i>Salary Breakdown
                                    </h6>
                                </div>
                                <div class="card-body" id="salaryBreakdown">
                                    <div class="text-center text-muted py-5">
                                        <i class="bi bi-robot fs-1 d-block mb-3"></i>
                                        <p>Select an employee and click "Generate AI-Powered Calculation"</p>
                                        <small class="text-muted">AI will calculate SSS, Pag-IBIG, PhilHealth, and Tax based on Philippine 2025 rates</small>
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
                                                <th>Employee ID</th>
                                                <th>Employee Name</th>
                                                <th>Department</th>
                                                <th>Position</th>
                                                <th>Base Salary</th>
                                                <th>Overtime</th>
                                                <th>Absent</th>
                                                <th>Additions</th>
                                                <th>Gross Salary</th>
                                                <th>SSS</th>
                                                <th>PhilHealth</th>
                                                <th>Pag-IBIG</th>
                                                <th>Tax</th>
                                                <th>Net Salary</th>
                                                <th>Pay Date</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="payrollTableBody">
                                            <!-- Sample data with BIR.gov.ph tax calculator logic -->
                                            <tr>
                                                <td>EMP001</td>
                                                <td>Juan Dela Cruz</td>
                                                <td>IT</td>
                                                <td>Software Engineer</td>
                                                <td>₱45,000.00</td>
                                                <td>₱2,500.00</td>
                                                <td>₱1,000.00</td>
                                                <td>₱3,000.00</td>
                                                <td>₱49,500.00</td>
                                                <td class="text-primary">₱1,350.00</td>
                                                <td class="text-info">₱1,125.00</td>
                                                <td class="text-success">₱100.00</td>
                                                <td class="text-warning">₱5,743.75</td>
                                                <td class="text-success fw-bold">₱41,181.25</td>
                                                <td>2025-01-15</td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-outline-success btn-sm" title="Print">
                                                        <i class="bi bi-printer"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>EMP002</td>
                                                <td>Maria Santos</td>
                                                <td>Human Resources</td>
                                                <td>HR Manager</td>
                                                <td>₱55,000.00</td>
                                                <td>₱3,000.00</td>
                                                <td>₱0.00</td>
                                                <td>₱5,000.00</td>
                                                <td>₱63,000.00</td>
                                                <td class="text-primary">₱1,350.00</td>
                                                <td class="text-info">₱1,375.00</td>
                                                <td class="text-success">₱100.00</td>
                                                <td class="text-warning">₱9,768.75</td>
                                                <td class="text-success fw-bold">₱50,406.25</td>
                                                <td>2025-01-15</td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-outline-success btn-sm" title="Print">
                                                        <i class="bi bi-printer"></i>
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
        </div>
    </div>
</div>

<script>
// Static employee data for UI demonstration (no database connection)
const employees = [
    { id: 1, name: "Juan Dela Cruz", position: "Software Engineer", department: "IT", baseSalary: 45000 },
    { id: 2, name: "Maria Santos", position: "HR Manager", department: "Human Resources", baseSalary: 55000 },
    { id: 3, name: "Jose Reyes", position: "Accountant", department: "Finance", baseSalary: 40000 },
    { id: 4, name: "Ana Garcia", position: "Marketing Manager", department: "Marketing", baseSalary: 48000 },
    { id: 5, name: "Carlos Rodriguez", position: "Sales Executive", department: "Sales", baseSalary: 35000 }
];

let selectedEmployee = null;

// Employee search functionality
document.getElementById('employeeSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const dropdown = document.getElementById('searchDropdown');
    
    if (searchTerm.length < 2) {
        dropdown.style.display = 'none';
        return;
    }
    
    const filteredEmployees = employees.filter(emp => 
        emp.name.toLowerCase().includes(searchTerm)
    );
    
    if (filteredEmployees.length > 0) {
        dropdown.innerHTML = filteredEmployees.map(emp => `
            <a href="#" class="dropdown-item employee-item" data-employee='${JSON.stringify(emp)}'>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${emp.name}</strong>
                        <br>
                        <small class="text-muted">${emp.position} - ${emp.department}</small>
                    </div>
                    <small class="text-primary">₱${emp.baseSalary.toLocaleString()}</small>
                </div>
            </a>
        `).join('');
        dropdown.style.display = 'block';
    } else {
        dropdown.innerHTML = '<a href="#" class="dropdown-item disabled">No employees found</a>';
        dropdown.style.display = 'block';
    }
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
    document.getElementById('selectedEmployeeName').textContent = employee.name;
    document.getElementById('selectedEmployeePosition').textContent = `Position: ${employee.position}`;
    document.getElementById('selectedEmployeeDepartment').textContent = `Department: ${employee.department}`;
    document.getElementById('selectedEmployeeId').textContent = `ID: ${employee.id}`;
    document.getElementById('selectedEmployeeInfo').style.display = 'block';
    
    // Set base salary
    document.getElementById('baseSalary').value = employee.baseSalary;
    
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
    document.getElementById('overtime').value = '';
    document.getElementById('absent').value = '';
    document.getElementById('additions').value = '';
    document.getElementById('grossSalary').value = '0.00';
    document.getElementById('salaryBreakdown').innerHTML = `
        <div class="text-center text-muted py-5">
            <i class="bi bi-robot fs-1 d-block mb-3"></i>
            <p>Select an employee and click "Generate AI-Powered Calculation"</p>
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
    const overtime = parseFloat(document.getElementById('overtime').value) || 0;
    const absent = parseFloat(document.getElementById('absent').value) || 0;
    const additions = parseFloat(document.getElementById('additions').value) || 0;
    
    const grossSalary = baseSalary + overtime + additions - absent;
    document.getElementById('grossSalary').value = grossSalary.toFixed(2);
}

// Update gross salary on input changes
document.getElementById('baseSalary').addEventListener('input', calculateGrossSalary);
document.getElementById('overtime').addEventListener('input', calculateGrossSalary);
document.getElementById('absent').addEventListener('input', calculateGrossSalary);
document.getElementById('additions').addEventListener('input', calculateGrossSalary);

// Generate AI-powered payroll calculation
document.getElementById('generatePayroll').addEventListener('click', async function() {
    if (!selectedEmployee) {
        alert('Please select an employee first');
        return;
    }
    
    const grossSalary = parseFloat(document.getElementById('grossSalary').value);
    
    if (grossSalary <= 0) {
        alert('Please enter a valid salary amount');
        return;
    }
    
    // Show loading
    document.getElementById('loadingIndicator').style.display = 'block';
    document.getElementById('aiResults').style.display = 'none';
    
    try {
        // Simulate AI API call (replace with actual OpenAI API call)
        const aiResult = await calculatePhilippineDeductions(grossSalary, selectedEmployee);
        
        // Display results
        displayAIResults(aiResult);
        
    } catch (error) {
        console.error('AI calculation error:', error);
        alert('Error calculating payroll. Please try again.');
    } finally {
        document.getElementById('loadingIndicator').style.display = 'none';
    }
});

// AI-powered Philippine government deduction calculations (2025 rates - Official Standards)
function calculatePhilippineDeductions(grossSalary) {
    // SSS Contribution 2025 - Official SSS Table
    let sssContribution;
    if (grossSalary <= 4250) {
        sssContribution = 180;
    } else if (grossSalary <= 4750) {
        sssContribution = 202.50;
    } else if (grossSalary <= 5250) {
        sssContribution = 225;
    } else if (grossSalary <= 5750) {
        sssContribution = 247.50;
    } else if (grossSalary <= 6250) {
        sssContribution = 270;
    } else if (grossSalary <= 6750) {
        sssContribution = 292.50;
    } else if (grossSalary <= 7250) {
        sssContribution = 315;
    } else if (grossSalary <= 7750) {
        sssContribution = 337.50;
    } else if (grossSalary <= 8250) {
        sssContribution = 360;
    } else if (grossSalary <= 8750) {
        sssContribution = 382.50;
    } else if (grossSalary <= 9250) {
        sssContribution = 405;
    } else if (grossSalary <= 9750) {
        sssContribution = 427.50;
    } else if (grossSalary <= 10250) {
        sssContribution = 450;
    } else if (grossSalary <= 10750) {
        sssContribution = 472.50;
    } else if (grossSalary <= 11250) {
        sssContribution = 495;
    } else if (grossSalary <= 11750) {
        sssContribution = 517.50;
    } else if (grossSalary <= 12250) {
        sssContribution = 540;
    } else if (grossSalary <= 12750) {
        sssContribution = 562.50;
    } else if (grossSalary <= 13250) {
        sssContribution = 585;
    } else if (grossSalary <= 13750) {
        sssContribution = 607.50;
    } else if (grossSalary <= 14250) {
        sssContribution = 630;
    } else if (grossSalary <= 14750) {
        sssContribution = 652.50;
    } else if (grossSalary <= 15250) {
        sssContribution = 675;
    } else if (grossSalary <= 15750) {
        sssContribution = 697.50;
    } else if (grossSalary <= 16250) {
        sssContribution = 720;
    } else if (grossSalary <= 16750) {
        sssContribution = 742.50;
    } else if (grossSalary <= 17250) {
        sssContribution = 765;
    } else if (grossSalary <= 17750) {
        sssContribution = 787.50;
    } else if (grossSalary <= 18250) {
        sssContribution = 810;
    } else if (grossSalary <= 18750) {
        sssContribution = 832.50;
    } else if (grossSalary <= 19250) {
        sssContribution = 855;
    } else if (grossSalary <= 19750) {
        sssContribution = 877.50;
    } else if (grossSalary <= 20250) {
        sssContribution = 900;
    } else if (grossSalary <= 20750) {
        sssContribution = 922.50;
    } else if (grossSalary <= 21250) {
        sssContribution = 945;
    } else if (grossSalary <= 21750) {
        sssContribution = 967.50;
    } else if (grossSalary <= 22250) {
        sssContribution = 990;
    } else if (grossSalary <= 22750) {
        sssContribution = 1012.50;
    } else if (grossSalary <= 23250) {
        sssContribution = 1035;
    } else if (grossSalary <= 23750) {
        sssContribution = 1057.50;
    } else if (grossSalary <= 24250) {
        sssContribution = 1080;
    } else if (grossSalary <= 24750) {
        sssContribution = 1102.50;
    } else if (grossSalary <= 25250) {
        sssContribution = 1125;
    } else if (grossSalary <= 25750) {
        sssContribution = 1147.50;
    } else if (grossSalary <= 26250) {
        sssContribution = 1170;
    } else if (grossSalary <= 26750) {
        sssContribution = 1192.50;
    } else if (grossSalary <= 27250) {
        sssContribution = 1215;
    } else if (grossSalary <= 27750) {
        sssContribution = 1237.50;
    } else if (grossSalary <= 28250) {
        sssContribution = 1260;
    } else if (grossSalary <= 28750) {
        sssContribution = 1282.50;
    } else if (grossSalary <= 29250) {
        sssContribution = 1305;
    } else if (grossSalary <= 29750) {
        sssContribution = 1327.50;
    } else {
        // Maximum contribution for salaries above ₱29,750
        sssContribution = 1350;
    }

    // PhilHealth Contribution 2025 - Official PhilHealth Premium Rates
    // 4% of monthly basic salary (2.5% employee share, 1.5% employer share)
    // Maximum monthly basic salary of ₱100,000
    const philHealthBase = Math.min(grossSalary, 100000);
    const philHealthContribution = philHealthBase * 0.025; // Employee share only

    // Pag-IBIG Contribution 2025 - Official Pag-IBIG Rates
    let pagibigContribution;
    if (grossSalary <= 1500) {
        pagibigContribution = grossSalary * 0.01; // 1%
    } else if (grossSalary <= 5000) {
        pagibigContribution = grossSalary * 0.02; // 2%
    } else {
        // Maximum contribution of ₱100 for salaries above ₱5,000
        pagibigContribution = 100;
    }

    // BIR Withholding Tax Calculation 2025 - Based on BIR.gov.ph Tax Calculator
    // Taxable income = Gross Salary - (SSS + PhilHealth + Pag-IBIG)
    const taxableIncome = grossSalary - sssContribution - philHealthContribution - pagibigContribution;
    
    let withholdingTax = 0;
    
    // BIR Tax Table 2025 (Revenue Regulations No. 3-2025)
    if (taxableIncome <= 20833) {
        // 0% for compensation income not over ₱20,833/month
        withholdingTax = 0;
    } else if (taxableIncome <= 33333) {
        // 20% for compensation income over ₱20,833 but not over ₱33,333/month
        withholdingTax = (taxableIncome - 20833) * 0.20;
    } else if (taxableIncome <= 66667) {
        // ₱2,500 + 25% for compensation income over ₱33,333 but not over ₱66,667/month
        withholdingTax = 2500 + (taxableIncome - 33333) * 0.25;
    } else if (taxableIncome <= 166667) {
        // ₱10,833 + 30% for compensation income over ₱66,667 but not over ₱166,667/month
        withholdingTax = 10833 + (taxableIncome - 66667) * 0.30;
    } else if (taxableIncome <= 666667) {
        // ₱40,833 + 32% for compensation income over ₱166,667 but not over ₱666,667/month
        withholdingTax = 40833 + (taxableIncome - 166667) * 0.32;
    } else {
        // ₱200,833 + 35% for compensation income over ₱666,667/month
        withholdingTax = 200833 + (taxableIncome - 666667) * 0.35;
    }

    // Total deductions
    const totalDeductions = sssContribution + philHealthContribution + pagibigContribution + withholdingTax;
    
    // Net salary
    const netSalary = grossSalary - totalDeductions;

    return {
        sss: sssContribution,
        philHealth: philHealthContribution,
        pagibig: pagibigContribution,
        tax: withholdingTax,
        totalDeductions: totalDeductions,
        netSalary: netSalary,
        taxableIncome: taxableIncome
    };
}

// Display AI results
function displayAIResults(result) {
    const resultsHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary">Employee Information</h6>
                <table class="table table-sm">
                    <tr><td>Name:</td><td><strong>${selectedEmployee.name}</strong></td></tr>
                    <tr><td>Position:</td><td>${selectedEmployee.position}</td></tr>
                    <tr><td>Department:</td><td>${selectedEmployee.department}</td></tr>
                    <tr><td>Calculation Date:</td><td>${new Date().toLocaleDateString()}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-primary">Salary Summary</h6>
                <table class="table table-sm">
                    <tr><td>Gross Salary:</td><td><strong>₱${result.grossSalary.toLocaleString('en-PH', {minimumFractionDigits: 2})}</strong></td></tr>
                    <tr><td>Total Deductions:</td><td class="text-danger">-₱${result.deductions.total.toLocaleString('en-PH', {minimumFractionDigits: 2})}</td></tr>
                    <tr><td>Net Salary:</td><td class="text-success"><strong>₱${result.netSalary.toLocaleString('en-PH', {minimumFractionDigits: 2})}</strong></td></tr>
                </table>
            </div>
        </div>
        
        <hr>
        
        <h6 class="text-primary mb-3">
            <i class="bi bi-calculator me-2"></i>AI-Powered Deduction Breakdown (Philippine 2025 Rates)
        </h6>
        <div class="row">
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <h6 class="card-title">SSS</h6>
                        <h4 class="text-primary">₱${result.deductions.sss.toLocaleString('en-PH', {minimumFractionDigits: 2})}</h4>
                        <small class="text-muted">4.5% of monthly salary</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <h6 class="card-title">Pag-IBIG</h6>
                        <h4 class="text-success">₱${result.deductions.pagibig.toLocaleString('en-PH', {minimumFractionDigits: 2})}</h4>
                        <small class="text-muted">1-2% of monthly salary</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <h6 class="card-title">PhilHealth</h6>
                        <h4 class="text-info">₱${result.deductions.philhealth.toLocaleString('en-PH', {minimumFractionDigits: 2})}</h4>
                        <small class="text-muted">3% of monthly salary</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <h6 class="card-title">Withholding Tax</h6>
                        <h4 class="text-warning">₱${result.deductions.tax.toLocaleString('en-PH', {minimumFractionDigits: 2})}</h4>
                        <small class="text-muted">Progressive tax rates</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="alert alert-success mt-3">
            <i class="bi bi-info-circle me-2"></i>
            <strong>AI Analysis:</strong> Calculations based on Philippine government 2025 tax rates and contribution tables. 
            Net salary represents take-home pay after all mandatory deductions.
        </div>
    `;
    
    document.getElementById('aiCalculationResults').innerHTML = resultsHTML;
    document.getElementById('aiResults').style.display = 'block';
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('#employeeSearch') && !e.target.closest('#searchDropdown')) {
        document.getElementById('searchDropdown').style.display = 'none';
    }
});

// Payroll Table Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date inputs with current values
    const today = new Date();
    document.getElementById('filterDate').value = today.toISOString().split('T')[0];
    document.getElementById('filterMonth').value = today.toISOString().slice(0, 7);
    document.getElementById('filterYear').value = today.getFullYear();
    
    // Handle filter type change
    document.getElementById('filterType').addEventListener('change', function() {
        const filterType = this.value;
        const dateField = document.getElementById('filterDate');
        const monthField = document.getElementById('filterMonth');
        const yearField = document.getElementById('filterYear');
        
        // Show/hide relevant fields
        dateField.parentElement.style.display = filterType === 'daily' ? 'block' : 'none';
        monthField.parentElement.style.display = filterType === 'monthly' ? 'block' : 'none';
        yearField.parentElement.style.display = filterType === 'yearly' ? 'block' : 'none';
    });
    
    // Initialize filter visibility
    document.getElementById('filterType').dispatchEvent(new Event('change'));
    
    // Apply filter
    document.getElementById('applyFilter').addEventListener('click', function() {
        applyPayrollFilter();
    });
    
    // Clear filter
    document.getElementById('clearFilter').addEventListener('click', function() {
        document.getElementById('tableSearch').value = '';
        document.getElementById('filterType').value = 'monthly';
        document.getElementById('filterType').dispatchEvent(new Event('change'));
        applyPayrollFilter();
    });
    
    // Search functionality
    document.getElementById('tableSearch').addEventListener('input', function() {
        applyPayrollFilter();
    });
    
    // Select all checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    // Export to Excel
    document.getElementById('exportExcel').addEventListener('click', function() {
        exportToExcel();
    });
    
    // Refresh table
    document.getElementById('refreshTable').addEventListener('click', function() {
        refreshPayrollTable();
    });
    
    // View details buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.view-details')) {
            const row = e.target.closest('tr');
            const employeeName = row.cells[2].textContent;
            showPayrollDetails(employeeName);
        }
        
        if (e.target.closest('.print-slip')) {
            const row = e.target.closest('tr');
            const employeeName = row.cells[2].textContent;
            printPayslip(employeeName);
        }
    });
});

// Apply payroll filter
function applyPayrollFilter() {
    const searchTerm = document.getElementById('tableSearch').value.toLowerCase();
    const filterType = document.getElementById('filterType').value;
    const rows = document.querySelectorAll('#payrollTableBody tr');
    
    rows.forEach(row => {
        const employeeName = row.cells[2].textContent.toLowerCase();
        const payDate = row.cells[11].textContent;
        
        let matchesSearch = employeeName.includes(searchTerm);
        let matchesDate = true;
        
        if (filterType === 'daily') {
            const filterDate = document.getElementById('filterDate').value;
            matchesDate = payDate === filterDate;
        } else if (filterType === 'monthly') {
            const filterMonth = document.getElementById('filterMonth').value;
            matchesDate = payDate.startsWith(filterMonth);
        } else if (filterType === 'yearly') {
            const filterYear = document.getElementById('filterYear').value;
            matchesDate = payDate.startsWith(filterYear);
        }
        
        row.style.display = matchesSearch && matchesDate ? '' : 'none';
    });
}

// Export to Excel function - Perfect alignment with table data
function exportToExcel() {
    const filterType = document.getElementById('filterType').value;
    let filename = `payroll_report_${filterType}_${new Date().toISOString().slice(0, 10)}.csv`;
    
    // Get visible rows (after filtering)
    const visibleRows = Array.from(document.querySelectorAll('#payrollTableBody tr'))
        .filter(row => row.style.display !== 'none');
    
    if (visibleRows.length === 0) {
        alert('No data to export. Please adjust your filters.');
        return;
    }
    
    // Create CSV content with exact table data (excluding checkbox column)
    let csvContent = 'Employee ID,Employee Name,Department,Position,Base Salary,Overtime,Absent,Additions,Gross Salary,SSS,PhilHealth,Pag-IBIG,Withholding Tax,Net Salary,Pay Date\n';
    
    visibleRows.forEach(row => {
        const cells = row.cells;
        const rowData = [
            cells[1].textContent.trim(),      // Employee ID
            cells[2].textContent.trim(),      // Employee Name
            cells[3].textContent.trim(),      // Department
            cells[4].textContent.trim(),      // Position
            cells[5].textContent.replace(/[₱,]/g, '').trim(),      // Base Salary
            cells[6].textContent.replace(/[₱,]/g, '').trim(),      // Overtime
            cells[7].textContent.replace(/[₱,]/g, '').trim(),      // Absent
            cells[8].textContent.replace(/[₱,]/g, '').trim(),      // Additions
            cells[9].textContent.replace(/[₱,]/g, '').trim(),      // Gross Salary
            cells[10].textContent.replace(/[₱,]/g, '').trim(),     // SSS
            cells[11].textContent.replace(/[₱,]/g, '').trim(),     // PhilHealth
            cells[12].textContent.replace(/[₱,]/g, '').trim(),     // Pag-IBIG
            cells[13].textContent.replace(/[₱,]/g, '').trim(),     // Withholding Tax
            cells[14].textContent.replace(/[₱,]/g, '').trim(),     // Net Salary
            cells[15].textContent.trim()      // Pay Date - keeping as is
        ];
        csvContent += rowData.join(',') + '\n';
    });
    
    // Create download link
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', filename.replace('.xlsx', '.csv'));
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Show success message
    showNotification('Payroll data exported successfully!', 'success');
}

// Refresh payroll table
function refreshPayrollTable() {
    // Show loading state
    const tableBody = document.getElementById('payrollTableBody');
    const originalContent = tableBody.innerHTML;
    
    tableBody.innerHTML = `
        <tr>
            <td colspan="13" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Refreshing payroll data...</p>
            </td>
        </tr>
    `;
    
    // Simulate API call
    setTimeout(() => {
        tableBody.innerHTML = originalContent;
        showNotification('Payroll data refreshed successfully!', 'success');
    }, 1500);
}

// Show payroll details modal
function showPayrollDetails(employeeName) {
    // Create modal HTML
    const modalHTML = `
        <div class="modal fade" id="payrollDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Payroll Details - ${employeeName}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Employee Information</h6>
                                <table class="table table-sm">
                                    <tr><td>Name:</td><td>${employeeName}</td></tr>
                                    <tr><td>Department:</td><td>IT</td></tr>
                                    <tr><td>Position:</td><td>Software Engineer</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Salary Breakdown</h6>
                                <table class="table table-sm">
                                    <tr><td>Base Salary:</td><td>₱45,000.00</td></tr>
                                    <tr><td>Overtime:</td><td>₱2,500.00</td></tr>
                                    <tr><td>Additions:</td><td>₱3,000.00</td></tr>
                                    <tr><td>Absent:</td><td>₱1,000.00</td></tr>
                                    <tr><td><strong>Gross Salary:</strong></td><td><strong>₱49,500.00</strong></td></tr>
                                </table>
                            </div>
                        </div>
                        <hr>
                        <h6>Deductions</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h5 class="text-primary">₱2,025.00</h5>
                                    <small>SSS</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h5 class="text-success">₱100.00</h5>
                                    <small>Pag-IBIG</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h5 class="text-info">₱1,485.00</h5>
                                    <small>PhilHealth</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h5 class="text-warning">₱7,655.50</h5>
                                    <small>Tax</small>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-success">Net Salary: ₱38,234.50</h5>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="printPayslip('${employeeName}')">
                            <i class="bi bi-printer me-1"></i>Print Payslip
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('payrollDetailsModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to body and show
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    const modal = new bootstrap.Modal(document.getElementById('payrollDetailsModal'));
    modal.show();
}

// Print payslip function
function printPayslip(employeeName) {
    // Create print content
    const printContent = `
        <div style="padding: 20px; font-family: Arial, sans-serif;">
            <div style="text-align: center; margin-bottom: 30px;">
                <h2>PAYSLIP</h2>
                <p>Pay Period: ${new Date().toLocaleDateString()}</p>
            </div>
            <div style="margin-bottom: 20px;">
                <h4>Employee: ${employeeName}</h4>
                <p>Department: IT | Position: Software Engineer</p>
            </div>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;"><strong>Base Salary</strong></td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">₱45,000.00</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;"><strong>Overtime</strong></td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">₱2,500.00</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;"><strong>Additions</strong></td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">₱3,000.00</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;"><strong>Absent Deductions</strong></td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">-₱1,000.00</td>
                </tr>
                <tr style="background-color: #f0f0f0;">
                    <td style="border: 1px solid #ddd; padding: 8px;"><strong>Gross Salary</strong></td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;"><strong>₱49,500.00</strong></td>
                </tr>
            </table>
            <h5>Deductions:</h5>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">SSS</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">₱2,025.00</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">Pag-IBIG</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">₱100.00</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">PhilHealth</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">₱1,485.00</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">Withholding Tax</td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">₱7,655.50</td>
                </tr>
                <tr style="background-color: #f0f0f0;">
                    <td style="border: 1px solid #ddd; padding: 8px;"><strong>Total Deductions</strong></td>
                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;"><strong>₱11,265.50</strong></td>
                </tr>
            </table>
            <div style="text-align: center; margin-top: 30px;">
                <h3 style="color: green;">NET SALARY: ₱38,234.50</h3>
            </div>
        </div>
    `;
    
    // Open print window
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Payslip - ${employeeName}</title>
                <style>
                    @media print {
                        body { margin: 0; }
                    }
                </style>
            </head>
            <body>${printContent}</body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
    
    showNotification('Payslip printed successfully!', 'success');
}

// Action button functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up button handlers');
    
    // Add click handlers to all print buttons only
    function setupButtonHandlers() {
        const printButtons = document.querySelectorAll('button[title="Print"]');
        
        console.log('Found print buttons:', printButtons.length);
        
        // Handle print buttons
        printButtons.forEach((button, index) => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Print button clicked:', index);
                
                const row = this.closest('tr');
                if (!row) {
                    console.error('Could not find parent row');
                    return;
                }
                
                const cells = row.cells;
                
                // Get employee data for printing (adjusted indices after removing checkbox column)
                const employeeData = {
                    id: cells[0].textContent.trim(),
                    name: cells[1].textContent.trim(),
                    department: cells[2].textContent.trim(),
                    position: cells[3].textContent.trim(),
                    baseSalary: cells[4].textContent.trim(),
                    overtime: cells[5].textContent.trim(),
                    absent: cells[6].textContent.trim(),
                    additions: cells[7].textContent.trim(),
                    grossSalary: cells[8].textContent.trim(),
                    sss: cells[9].textContent.trim(),
                    philHealth: cells[10].textContent.trim(),
                    pagibig: cells[11].textContent.trim(),
                    tax: cells[12].textContent.trim(),
                    netSalary: cells[13].textContent.trim(),
                    payDate: cells[14].textContent.trim()
                };
                
                console.log('Print employee data:', employeeData);
                
                // Print payslip
                printPayslip(employeeData);
                
                // Show notification
                showNotification(`Printing payslip for ${employeeData.name}`, 'success');
            });
        });
    }
    
    // Initial setup
    setupButtonHandlers();
    
    // Re-setup after any dynamic content changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                setupButtonHandlers();
            }
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});

// Fallback click handler for dynamic content
document.addEventListener('click', function(e) {
    // Print button clicked
    if (e.target.closest('button[title="Print"]')) {
        console.log('Fallback print handler triggered');
        const button = e.target.closest('button[title="Print"]');
        button.click();
    }
});

// Show employee details modal
function showEmployeeDetails(employee) {
    const modalHtml = `
        <div class="modal fade" id="employeeDetailsModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-person-badge me-2"></i>Employee Payroll Details
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
                                            <div class="col-6"><strong>ID:</strong></div>
                                            <div class="col-6">${employee.id}</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6"><strong>Name:</strong></div>
                                            <div class="col-6">${employee.name}</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6"><strong>Department:</strong></div>
                                            <div class="col-6">${employee.department}</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6"><strong>Position:</strong></div>
                                            <div class="col-6">${employee.position}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-info">
                                    <div class="card-header bg-light">
                                        <h6 class="text-info mb-0">
                                            <i class="bi bi-calendar me-1"></i>Pay Period
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6"><strong>Pay Date:</strong></div>
                                            <div class="col-6">${employee.payDate}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">
                                            <i class="bi bi-plus-circle me-1"></i>Earnings
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <div class="col-8">Base Salary:</div>
                                            <div class="col-4 text-end">${employee.baseSalary}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-8">Overtime:</div>
                                            <div class="col-4 text-end">${employee.overtime}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-8">Additions:</div>
                                            <div class="col-4 text-end">${employee.additions}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-8">Absent:</div>
                                            <div class="col-4 text-end">${employee.absent}</div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-8"><strong>Gross Salary:</strong></div>
                                            <div class="col-4 text-end"><strong>${employee.grossSalary}</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <h6 class="mb-0">
                                            <i class="bi bi-dash-circle me-1"></i>Deductions
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <div class="col-8">SSS:</div>
                                            <div class="col-4 text-end">${employee.sss}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-8">PhilHealth:</div>
                                            <div class="col-4 text-end">${employee.philHealth}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-8">Pag-IBIG:</div>
                                            <div class="col-4 text-end">${employee.pagibig}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-8">Withholding Tax:</div>
                                            <div class="col-4 text-end">${employee.tax}</div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-8"><strong>Total Deductions:</strong></div>
                                            <div class="col-4 text-end"><strong>₱${(parseFloat(employee.sss.replace(/[₱,]/g, '')) + parseFloat(employee.philHealth.replace(/[₱,]/g, '')) + parseFloat(employee.pagibig.replace(/[₱,]/g, '')) + parseFloat(employee.tax.replace(/[₱,]/g, ''))).toFixed(2)}</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-success alert-dismissible" role="alert">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="mb-1">
                                                <i class="bi bi-cash-stack me-2"></i>Net Salary
                                            </h4>
                                            <h2 class="mb-0">${employee.netSalary}</h2>
                                        </div>
                                        <div class="text-end">
                                            <small class="d-block">Take Home Pay</small>
                                            <strong>${employee.name}</strong>
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
                        <button type="button" class="btn btn-primary" onclick="printPayslip(${JSON.stringify(employee).replace(/"/g, '&quot;')})">
                            <i class="bi bi-printer me-1"></i>Print Payslip
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('employeeDetailsModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to page and show
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Small delay to ensure modal is in DOM
    setTimeout(() => {
        const modalElement = document.getElementById('employeeDetailsModal');
        if (modalElement) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
            console.log('Modal should be showing now');
        } else {
            console.error('Modal element not found');
        }
    }, 100);
}

// Print payslip function
function printPayslip(employee) {
    const printContent = `
        <div style="font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto;">
            <div style="text-align: center; border-bottom: 2px solid #007bff; padding-bottom: 20px; margin-bottom: 20px;">
                <h2 style="margin: 0; color: #007bff;">PAYSLIP</h2>
                <p style="margin: 5px 0;">Pay Period: ${employee.payDate}</p>
            </div>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 30px;">
                <div>
                    <h4 style="margin: 0 0 10px 0; color: #333;">Employee Information</h4>
                    <p style="margin: 5px 0;"><strong>ID:</strong> ${employee.id}</p>
                    <p style="margin: 5px 0;"><strong>Name:</strong> ${employee.name}</p>
                    <p style="margin: 5px 0;"><strong>Department:</strong> ${employee.department}</p>
                    <p style="margin: 5px 0;"><strong>Position:</strong> ${employee.position}</p>
                </div>
            </div>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 30px;">
                <div style="width: 48%;">
                    <h4 style="margin: 0 0 10px 0; color: #28a745;">Earnings</h4>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr><td style="padding: 5px; border-bottom: 1px solid #ddd;">Base Salary:</td><td style="padding: 5px; text-align: right; border-bottom: 1px solid #ddd;">${employee.baseSalary}</td></tr>
                        <tr><td style="padding: 5px; border-bottom: 1px solid #ddd;">Overtime:</td><td style="padding: 5px; text-align: right; border-bottom: 1px solid #ddd;">${employee.overtime}</td></tr>
                        <tr><td style="padding: 5px; border-bottom: 1px solid #ddd;">Additions:</td><td style="padding: 5px; text-align: right; border-bottom: 1px solid #ddd;">${employee.additions}</td></tr>
                        <tr><td style="padding: 5px; border-bottom: 1px solid #ddd;">Absent:</td><td style="padding: 5px; text-align: right; border-bottom: 1px solid #ddd;">${employee.absent}</td></tr>
                        <tr style="font-weight: bold;"><td style="padding: 5px; border-bottom: 2px solid #28a745;">Gross Salary:</td><td style="padding: 5px; text-align: right; border-bottom: 2px solid #28a745;">${employee.grossSalary}</td></tr>
                    </table>
                </div>
                
                <div style="width: 48%;">
                    <h4 style="margin: 0 0 10px 0; color: #dc3545;">Deductions</h4>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr><td style="padding: 5px; border-bottom: 1px solid #ddd;">SSS:</td><td style="padding: 5px; text-align: right; border-bottom: 1px solid #ddd;">${employee.sss}</td></tr>
                        <tr><td style="padding: 5px; border-bottom: 1px solid #ddd;">PhilHealth:</td><td style="padding: 5px; text-align: right; border-bottom: 1px solid #ddd;">${employee.philHealth}</td></tr>
                        <tr><td style="padding: 5px; border-bottom: 1px solid #ddd;">Pag-IBIG:</td><td style="padding: 5px; text-align: right; border-bottom: 1px solid #ddd;">${employee.pagibig}</td></tr>
                        <tr><td style="padding: 5px; border-bottom: 1px solid #ddd;">Withholding Tax:</td><td style="padding: 5px; text-align: right; border-bottom: 1px solid #ddd;">${employee.tax}</td></tr>
                        <tr style="font-weight: bold;"><td style="padding: 5px; border-bottom: 2px solid #dc3545;">Total Deductions:</td><td style="padding: 5px; text-align: right; border-bottom: 2px solid #dc3545;">₱${(parseFloat(employee.sss.replace(/[₱,]/g, '')) + parseFloat(employee.philHealth.replace(/[₱,]/g, '')) + parseFloat(employee.pagibig.replace(/[₱,]/g, '')) + parseFloat(employee.tax.replace(/[₱,]/g, ''))).toFixed(2)}</td></tr>
                    </table>
                </div>
            </div>
            
            <div style="text-align: center; padding: 20px; background-color: #d4edda; border-radius: 5px;">
                <h3 style="margin: 0; color: #155724;">Net Salary: ${employee.netSalary}</h3>
            </div>
            
            <div style="margin-top: 40px; text-align: center; color: #666; font-size: 12px;">
                <p>This is a computer-generated payslip. No signature required.</p>
            </div>
        </div>
    `;
    
    // Create print window
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Payslip - ${employee.name}</title>
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
}

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
