@extends('layouts.app')

@section('page-title', 'Accepted Contracts')
@section('page-subtitle', 'View-only from HR1, transform manually to Employees')
@section('breadcrumbs', 'Core Human / Accepted Contracts')

@section('content')
<div class="container-xxl">

  {{-- Top bar --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex gap-2">
      <input type="text" class="form-control" placeholder="Search contracts..." style="max-width: 260px;">
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createContractModal">
        <i class="bi bi-plus-circle me-1"></i> Create Sample Accepted Contract
      </button>
      <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-people me-1"></i> Go to Employees
      </a>
    </div>
  </div>

  {{-- Flash messages --}}
  @if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('status') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  {{-- Info note --}}
  <div class="alert alert-info">
    These contracts are fetched from HR1 via API and are <strong>read-only</strong> here. Use "Transform" to create an employee record.
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Department</th>
              <th>Role</th>
              <th>Start Date</th>
              <th class="text-end">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($contracts ?? []) as $c)
            <tr>
              <td class="fw-semibold">{{ $c['name'] }}</td>
              <td>{{ $c['email'] ?? '—' }}</td>
              <td>{{ $c['department'] }}</td>
              <td>{{ $c['role'] }}</td>
              <td>{{ \Illuminate\Support\Carbon::parse($c['start_date'])->format('M d, Y') }}</td>
              <td class="text-end">
                <form method="POST" action="{{ route('employees.transform') }}" class="d-inline">
                  @csrf
                  <input type="hidden" name="name" value="{{ $c['name'] }}">
                  <input type="hidden" name="email" value="{{ $c['email'] ?? '' }}">
                  <input type="hidden" name="department" value="{{ $c['department'] }}">
                  <input type="hidden" name="role" value="{{ $c['role'] }}">
                  <input type="hidden" name="start_date" value="{{ $c['start_date'] }}">
                  <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-arrow-right-circle me-1"></i> Transform to Employee
                  </button>
                </form>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="text-center text-muted">No accepted contracts found from HR1. Check your API settings.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
{{-- Create Sample Accepted Contract Modal --}}
<div class="modal fade" id="createContractModal" tabindex="-1" aria-labelledby="createContractModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createContractModalLabel">Create Sample Accepted Contract</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('contracts.sample_create') }}">
        @csrf
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small text-muted">Full Name</label>
              <input type="text" name="name" class="form-control" placeholder="Jane Doe" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small text-muted">Email</label>
              <input type="email" name="email" class="form-control" placeholder="jane@example.com">
            </div>
            <div class="col-md-6">
              <label class="form-label small text-muted">Department</label>
              <input type="text" name="department" class="form-control" placeholder="Operations" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small text-muted">Role</label>
              <input type="text" name="role" class="form-control" placeholder="Ops Associate" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small text-muted">Start Date</label>
              <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Contract</button>
        </div>
      </form>
    </div>
  </div>
  </div>
@endsection
