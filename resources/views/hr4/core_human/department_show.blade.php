@extends('layouts.app')

@section('page-title', $department->name . ' Department')
@section('page-subtitle', 'Department details and employee information')
@section('breadcrumbs', 'Core Human / Departments / ' . $department->name)

@section('content')
<div class="container-xxl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">{{ $department->name }} Department</h4>
            <p class="text-muted mb-0">Position: {{ $department->position ?? 'Not specified' }}</p>
        </div>
        <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Departments
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Department Overview</h6>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Total Capacity:</span>
                            <span class="fw-semibold">{{ $details['total_employees'] }}/{{ $details['employee_limit'] }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <?php 
                            $percentage = $details['employee_limit'] > 0 ? 
                                ($details['total_employees'] / $details['employee_limit']) * 100 : 0; 
                            ?>
                            <div class="progress-bar bg-{{ $percentage >= 80 ? 'danger' : ($percentage >= 60 ? 'warning' : 'success') }}" 
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Current Employees:</span>
                        <span class="badge bg-success">{{ $details['total_employees'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Available Openings:</span>
                        <span class="badge bg-primary">{{ $details['openings'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Employee Limit:</span>
                        <span class="badge bg-info">{{ $details['employee_limit'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Employee Count by Position</h6>
                    @if(count($details['position_counts']) > 0)
                        <div class="row">
                            @foreach($details['position_counts'] as $position => $count)
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ $position }}</h6>
                                                <small class="text-muted">
                                                    @if(isset($details['position_limits'][$position]))
                                                        Limit: {{ $details['position_limits'][$position] }} | 
                                                    @endif
                                                    Current: {{ $count }}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-primary fs-6">{{ $count }}</span>
                                                @if(isset($details['position_limits'][$position]))
                                                    <?php $positionOpenings = max(0, $details['position_limits'][$position] - $count); ?>
                                                    @if($positionOpenings > 0)
                                                        <span class="badge bg-success ms-1">{{ $positionOpenings }} open</span>
                                                    @else
                                                        <span class="badge bg-secondary ms-1">Full</span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                        @if($count > 0)
                                            <div class="progress mt-2" style="height: 6px;">
                                                <?php 
                                                if(isset($details['position_limits'][$position]) && $details['position_limits'][$position] > 0) {
                                                    $posPercentage = ($count / $details['position_limits'][$position]) * 100;
                                                } elseif ($details['total_employees'] > 0) {
                                                    $posPercentage = ($count / $details['total_employees']) * 100;
                                                } else {
                                                    $posPercentage = 0;
                                                }
                                                ?>
                                                <div class="progress-bar bg-info" style="width: {{ min(100, $posPercentage) }}%"></div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-people fs-1 mb-3"></i>
                            <p>No position data available for this department.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($details['openings'] > 0)
    <div class="card mt-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Available Openings</h6>
                <span class="badge bg-success">{{ $details['openings'] }} positions available</span>
            </div>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                This department has {{ $details['openings'] }} opening(s) available.
            </div>
        </div>
    </div>
    @endif

    @if($details['openings'] == 0)
    <div class="card mt-4">
        <div class="card-body">
            <div class="text-center text-muted py-4">
                <i class="bi bi-check-circle fs-1 mb-3 text-success"></i>
                <h6>Department Fully Staffed</h6>
                <p>This department has reached its employee limit of {{ $details['employee_limit'] }} employees.</p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
