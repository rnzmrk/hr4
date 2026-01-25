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
                            <div class="fs-4 fw-bold mt-1">247</div>
                        </div>
                    </div>
                </div>

                <!-- Active Employees -->
                <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small">Active Employees</div>
                            <div class="fs-4 fw-bold mt-1">235</div>
                        </div>
                    </div>
                </div>

                <!-- New Hires (This Month) -->
                <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small">New Hires (This Month)</div>
                            <div class="fs-4 fw-bold mt-1">12</div>
                        </div>
                    </div>
                </div>


                <!-- Open Positions -->
                <div class="col-12 col-sm-6 col-lg-4 col-xl-2">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small">Open Positions</div>
                            <div class="fs-4 fw-bold mt-1">18</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Management Graphs -->
            <div class="row g-3 mt-4">
                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Performance Rating Distribution</h6>
                            <canvas id="performanceRatingChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Attendance Trend</h6>
                            <canvas id="attendanceTrendChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance & Leave Graphs -->
            <div class="row g-3 mt-4">
                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Absenteeism Rate by Department</h6>
                            <canvas id="absenteeismChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Leave Type Distribution</h6>
                            <canvas id="leaveTypeChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Salary & Payroll Graphs -->
            <div class="row g-3 mt-4">
                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Salary Distribution</h6>
                            <canvas id="salaryDistributionChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Average Salary by Department</h6>
                            <canvas id="salaryByDeptChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-4">
                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Overtime Cost Trend</h6>
                            <canvas id="overtimeCostChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Payroll Cost per Month</h6>
                            <canvas id="payrollCostChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {


    // Performance Rating Distribution - Bar Chart
    const performanceRatingCtx = document.getElementById('performanceRatingChart').getContext('2d');
    new Chart(performanceRatingCtx, {
        type: 'bar',
        data: {
            labels: ['Rating 1', 'Rating 2', 'Rating 3', 'Rating 4', 'Rating 5'],
            datasets: [{
                label: 'Number of Employees',
                data: [5, 15, 45, 120, 62],
                backgroundColor: '#4e73df'
            }]
        }
    });


    // Attendance Trend - Line Chart
    const attendanceTrendCtx = document.getElementById('attendanceTrendChart').getContext('2d');
    new Chart(attendanceTrendCtx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
            datasets: [{
                label: 'Attendance Rate %',
                data: [95, 96, 94, 97, 93],
                borderColor: '#1cc88a',
                tension: 0.1
            }]
        }
    });

    // Absenteeism Rate by Department - Bar Chart
    const absenteeismCtx = document.getElementById('absenteeismChart').getContext('2d');
    new Chart(absenteeismCtx, {
        type: 'bar',
        data: {
            labels: ['IT', 'HR', 'Finance', 'Sales', 'Operations'],
            datasets: [{
                label: 'Absenteeism Rate %',
                data: [3.2, 2.8, 2.5, 4.1, 3.5],
                backgroundColor: '#e74a3b'
            }]
        }
    });

    // Leave Type Distribution - Pie Chart
    const leaveTypeCtx = document.getElementById('leaveTypeChart').getContext('2d');
    new Chart(leaveTypeCtx, {
        type: 'pie',
        data: {
            labels: ['Sick', 'Vacation', 'Emergency'],
            datasets: [{
                data: [40, 45, 15],
                backgroundColor: ['#f6c23e', '#1cc88a', '#e74a3b']
            }]
        }
    });


    // Salary Distribution - Histogram
    const salaryDistributionCtx = document.getElementById('salaryDistributionChart').getContext('2d');
    new Chart(salaryDistributionCtx, {
        type: 'bar',
        data: {
            labels: ['20-30k', '30-40k', '40-50k', '50-60k', '60-70k', '70k+'],
            datasets: [{
                label: 'Number of Employees',
                data: [25, 45, 68, 52, 35, 22],
                backgroundColor: '#4e73df'
            }]
        }
    });

    // Average Salary by Department - Bar Chart
    const salaryByDeptCtx = document.getElementById('salaryByDeptChart').getContext('2d');
    new Chart(salaryByDeptCtx, {
        type: 'bar',
        data: {
            labels: ['IT', 'HR', 'Finance', 'Sales', 'Operations'],
            datasets: [{
                label: 'Average Salary (₱)',
                data: [65000, 45000, 58000, 52000, 48000],
                backgroundColor: '#1cc88a'
            }]
        }
    });

    // Overtime Cost Trend - Line Chart
    const overtimeCostCtx = document.getElementById('overtimeCostChart').getContext('2d');
    new Chart(overtimeCostCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Overtime Cost (₱)',
                data: [120000, 135000, 110000, 145000, 125000, 130000],
                borderColor: '#e74a3b',
                tension: 0.1
            }]
        }
    });

    // Payroll Cost per Month - Area Chart
    const payrollCostCtx = document.getElementById('payrollCostChart').getContext('2d');
    new Chart(payrollCostCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Payroll Cost (₱)',
                data: [2800000, 2850000, 2900000, 2920000, 2950000, 2980000],
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                fill: true,
                tension: 0.1
            }]
        }
    });
});
</script>
@endsection
