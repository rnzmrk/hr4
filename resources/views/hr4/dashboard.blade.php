@extends('layouts.app')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Track employees across teams.')
@section('breadcrumbs', 'Dashboard')


@section('content')
<section>
    <div @class('container-fluid')>
        <div class="container-xxl">
            <div class="row g-3">
                <!-- Total Employees -->
                <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small">Total Employees</div>
                            <div class="fs-4 fw-bold mt-1">{{ $totalEmployees }}</div>
                        </div>
                    </div>
                </div>

                <!-- Regular Employees -->
                <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small">Regular Employees</div>
                            <div class="fs-4 fw-bold mt-1">{{ $regularEmployees }}</div>
                        </div>
                    </div>
                </div>

                <!-- New Hires -->
                <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small">New Hires</div>
                            <div class="fs-4 fw-bold mt-1">{{ $newHireEmployees }}</div>
                        </div>
                    </div>
                </div>

                <!-- Retired Employees -->
                <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small">Retired Employees</div>
                            <div class="fs-4 fw-bold mt-1">{{ $retired }}</div>
                        </div>
                    </div>
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
