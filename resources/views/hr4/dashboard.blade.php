@extends('layouts.app')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Track employees across teams.')
@section('breadcrumbs', 'Dashboard')


@section('content')
<section>
    <div @class('container-fluid')>
        <div class="container-xxl">
            <style>
                .clickable-card {
                    transition: all 0.3s ease;
                    cursor: pointer;
                }
                .clickable-card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 20px rgba(0,0,0,0.15) !important;
                }
                .clickable-card:hover .card-body {
                    color: #212529;
                }
                
                /* Employee card colors */
                .employee-card-primary {
                    border-left: 4px solid #007bff;
                    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
                }
                .employee-card-primary:hover {
                    background: linear-gradient(135deg, #f8f9ff 0%, #e6f3ff 100%);
                    border-left-color: #0056b3;
                }
                
                .employee-card-success {
                    border-left: 4px solid #28a745;
                    background: linear-gradient(135deg, #ffffff 0%, #f8fff9 100%);
                }
                .employee-card-success:hover {
                    background: linear-gradient(135deg, #f8fff9 0%, #e6ffe6 100%);
                    border-left-color: #1e7e34;
                }
                
                .employee-card-info {
                    border-left: 4px solid #17a2b8;
                    background: linear-gradient(135deg, #ffffff 0%, #f8ffff 100%);
                }
                .employee-card-info:hover {
                    background: linear-gradient(135deg, #f8ffff 0%, #e6f7f7 100%);
                    border-left-color: #117a8b;
                }
                
                .employee-card-warning {
                    border-left: 4px solid #ffc107;
                    background: linear-gradient(135deg, #ffffff 0%, #fffdf7 100%);
                }
                .employee-card-warning:hover {
                    background: linear-gradient(135deg, #fffdf7 0%, #fff9e6 100%);
                    border-left-color: #e0a800;
                }
            </style>
            <div class="row g-3">
                <!-- Total Employees -->
                <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
                    <a href="{{ route('employees.index') }}" class="text-decoration-none">
                        <div class="card shadow-sm h-100 clickable-card employee-card-primary">
                            <div class="card-body">
                                <div class="text-muted small">Total Employees</div>
                                <div class="fs-4 fw-bold mt-1">{{ $totalEmployees }}</div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Regular Employees -->
                <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
                    <a href="{{ route('employees.index') }}?status=regular" class="text-decoration-none">
                        <div class="card shadow-sm h-100 clickable-card employee-card-success">
                            <div class="card-body">
                                <div class="text-muted small">Regular Employees</div>
                                <div class="fs-4 fw-bold mt-1">{{ $regularEmployees }}</div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- New Hires -->
                <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
                    <a href="{{ route('employees.index') }}?status=new_hire" class="text-decoration-none">
                        <div class="card shadow-sm h-100 clickable-card employee-card-info">
                            <div class="card-body">
                                <div class="text-muted small">New Hires</div>
                                <div class="fs-4 fw-bold mt-1">{{ $newHireEmployees }}</div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Departments -->
                <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
                    <a href="{{ route('departments.index') }}" class="text-decoration-none">
                        <div class="card shadow-sm h-100 clickable-card employee-card-warning">
                            <div class="card-body">
                                <div class="text-muted small">Departments</div>
                                <div class="fs-4 fw-bold mt-1">{{ $totalDepartments }}</div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>

            <!-- Performance Management Graphs -->
            <div class="row g-3 mt-4">
                <div class="col-12 col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Payroll Cost per Month</h6>
                            <div style="position: relative; height: 260px;">
                                <canvas id="payrollCostChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Salary Distribution</h6>
                            <div style="position: relative; height: 260px;">
                                <canvas id="salaryDistributionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Salary & Payroll Graphs -->
            <div class="row g-3 mt-4">
                <div class="col-12 col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Average Salary by Department</h6>
                            <div style="position: relative; height: 260px;">
                                <canvas id="salaryByDeptChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Performance Rating Distribution</h6>
                            <div style="position: relative; height: 260px;">
                                <canvas id="rewardDistributionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {


    // Performance Rating Distribution - Now showing Employee Rewards by Month
    const rewardDistributionCtx = document.getElementById('rewardDistributionChart').getContext('2d');

    fetch("{{ route('dashboard.reward-distribution') }}")
        .then(response => response.json())
        .then(payload => {
            const labels = payload.labels || [];
            const counts = payload.counts || [];

            new Chart(rewardDistributionCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Employees with Benefits',
                        data: counts,
                        backgroundColor: '#4e73df'
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: true
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const count = context.parsed.y || 0;
                                    return `Employees: ${count}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error loading reward distribution data:', error);
        });

    // Salary Distribution - Histogram (real data)
    const salaryDistributionCtx = document.getElementById('salaryDistributionChart').getContext('2d');

    fetch("{{ route('dashboard.salary-distribution') }}")
        .then(response => response.json())
        .then(payload => {
            const labels = payload.labels || [];
            const counts = payload.counts || [];
            const totals = payload.totals || [];

            new Chart(salaryDistributionCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Number of Employees',
                        data: counts,
                        backgroundColor: '#4e73df'
                    }]
                },
                options: {
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const index = context.dataIndex;
                                    const count = counts[index] ?? 0;
                                    const total = totals[index] ?? 0;
                                    const totalFormatted = `₱${Number(total).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                                    return [
                                        `Employees: ${count}`,
                                        `Total Salary: ${totalFormatted}`,
                                    ];
                                }
                            }
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error loading salary distribution data:', error);
        });

    // Average Salary by Department - Bar Chart (real data)
    const salaryByDeptCtx = document.getElementById('salaryByDeptChart').getContext('2d');

    fetch("{{ route('dashboard.salary-by-department') }}")
        .then(response => response.json())
        .then(payload => {
            const labels = payload.labels || [];
            const averages = payload.averages || [];

            new Chart(salaryByDeptCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Average Salary (₱)',
                        data: averages,
                        backgroundColor: '#1cc88a'
                    }]
                }
            });
        })
        .catch(error => {
            console.error('Error loading salary by department data:', error);
        });


    // Payroll Cost per Month - Area Chart (real data)
    const payrollCostCtx = document.getElementById('payrollCostChart').getContext('2d');

    fetch("{{ route('dashboard.payroll-cost') }}")
        .then(response => response.json())
        .then(payload => {
            const labels = payload.labels || [];
            const totals = payload.totals || [];

            new Chart(payrollCostCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Payroll Cost (₱)',
                        data: totals,
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.1)',
                        fill: true,
                        tension: 0.1
                    }]
                }
            });
        })
        .catch(error => {
            console.error('Error loading payroll cost data:', error);
        });
});
</script>
@endsection
