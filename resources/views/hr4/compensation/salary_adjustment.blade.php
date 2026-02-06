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
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($employees ?? []) as $employee)
            <tr>
              <td class="fw-semibold">{{ $employee->last_name }}, {{ $employee->first_name }}</td>
              <td>{{ $employee->department->name ?? '—' }}</td>
              <td>{{ $employee->position ?? '—' }}</td>
              <td>₱{{ number_format($employee->salary ?? 0, 2) }}</td>
              <td>
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateSalaryModal{{ $employee->id }}">
                  <i class="bi bi-pencil-square"></i> Update
                </button>
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
        <h5 class="modal-title" id="updateSalaryModalLabel{{ $employee->id }}">Update Salary</h5>
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
          <button type="submit" class="btn btn-primary">Update Salary</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach
@endsection
