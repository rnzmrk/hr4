@extends('layouts.app')

@section('page-title', 'Salary Adjustment')
@section('page-subtitle', 'Manage employee salaries')
@section('breadcrumbs', 'Compensation / Salary Adjustment')

@section('content')
<div class="container-xxl">
  @if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('status') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  {{-- Security Verification Status --}}
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <div class="d-flex align-items-center">
      <i class="bi bi-shield-check me-2 fs-5"></i>
      <div class="flex-grow-1">
        <strong>Security Verified</strong> - You have been granted access to Salary Adjustment
        @if(session('salary_adjustment_verified_at'))
          <br><small class="text-muted">Verified at: {{ session('salary_adjustment_verified_at')->format('g:i A, M j, Y') }}</small>
        @endif
      </div>
      <form method="POST" action="{{ route('salary.adjustment.clear_verification') }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('This will require you to verify credentials again on next access. Continue?')">
          <i class="bi bi-shield-x me-1"></i>Clear Verification
        </button>
      </form>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <form method="GET" action="{{ route('salary.adjustment.index') }}" class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Search Employee Name</label>
          <input type="text" name="search" class="form-control" placeholder="First or last name..." value="{{ request('search') }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Department</label>
          <select name="department_id" class="form-select">
            <option value="">All Departments</option>
            @foreach(($departments ?? []) as $department)
              <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                {{ $department->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4 d-flex align-items-end gap-2">
          <button type="submit" class="btn btn-primary">Filter</button>
          <a href="{{ route('salary.adjustment.index') }}" class="btn btn-outline-secondary">Clear</a>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Employee Name</th>
              <th>Department</th>
              <th>Position</th>
              <th>Salary</th>
              <th class="position-relative">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($employees ?? []) as $employee)
            <tr>
              <td class="fw-semibold">{{ $employee->last_name }}, {{ $employee->first_name }}</td>
              <td>{{ $employee->department->name ?? '—' }}</td>
              <td>{{ $employee->position ?? '—' }}</td>
              <td class="salary-cell">
                <span class="salary-value d-none">₱{{ number_format($employee->salary ?? 0, 2) }}</span>
                <span class="salary-hidden">────────</span>
              </td>
              <td>
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateSalaryModal{{ $employee->id }}">
                  <i class="bi bi-pencil-square"></i> Update
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary toggle-salary-btn ms-1">Show</button>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="text-center text-muted">No employees found.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- Update Salary Modals --}}
@foreach(($employees ?? []) as $employee)
<div class="modal fade" id="updateSalaryModal{{ $employee->id }}" tabindex="-1" aria-labelledby="updateSalaryModalLabel{{ $employee->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="updateSalaryModalLabel{{ $employee->id }}">Update Salary Adjustment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('salary.adjustment.update', $employee->id) }}">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Employee</label>
            <input type="text" class="form-control" value="{{ $employee->last_name }}, {{ $employee->first_name }}" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label">Current Salary</label>
            <input type="text" class="form-control" value="₱{{ number_format($employee->salary ?? 0, 2) }}" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label">New Salary<span class="text-danger">*</span></label>
            <input type="number" name="salary" class="form-control" value="{{ $employee->salary ?? 0 }}" min="0" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Position</label>
            <input type="text" name="position" class="form-control" value="{{ $employee->position ?? '' }}">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Adjustment</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach

<script>
document.addEventListener('DOMContentLoaded', function () {
  const rows = document.querySelectorAll('.salary-cell');

  // Hide salaries by default (show the line only)
  rows.forEach(function (cell) {
    const valueSpan = cell.querySelector('.salary-value');
    const hiddenSpan = cell.querySelector('.salary-hidden');
    if (valueSpan && hiddenSpan) {
      valueSpan.classList.add('d-none');
      hiddenSpan.classList.remove('d-none');
    }
  });

  // Attach click handler to each toggle button
  document.querySelectorAll('.toggle-salary-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const row = this.closest('tr');
      if (!row) return;

      const cell = row.querySelector('.salary-cell');
      if (!cell) return;

      const valueSpan = cell.querySelector('.salary-value');
      const hiddenSpan = cell.querySelector('.salary-hidden');

      if (!valueSpan || !hiddenSpan) return;

      const isHidden = valueSpan.classList.contains('d-none');

      if (isHidden) {
        // Show salary
        valueSpan.classList.remove('d-none');
        hiddenSpan.classList.add('d-none');
        this.textContent = 'Hide';
      } else {
        // Hide salary
        valueSpan.classList.add('d-none');
        hiddenSpan.classList.remove('d-none');
        this.textContent = 'Show';
      }
    });
  });
});
</script>

@endsection
