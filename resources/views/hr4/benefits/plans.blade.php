@extends('layouts.app')

@section('page-title', 'Benefit Plans')
@section('page-subtitle', 'HMO/Benefits configuration')
@section('breadcrumbs', 'Benefits / Plans')

@section('content')
<div class="container-xxl">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPlanModal">
      <i class="bi bi-plus-circle me-1"></i> New Plan
    </button>
  </div>

  @if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('status') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Name</th>
              <th>Type</th>
              <th>Rate Type</th>
              <th>Employee Share</th>
              <th>Employer Share</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($plans ?? []) as $p)
            <tr>
              <td class="fw-semibold">{{ $p['name'] }}</td>
              <td>{{ $p['type'] }}</td>
              <td>{{ ucfirst($p['rate_type']) }}</td>
              <td>₱{{ number_format($p['employee_share'], 2) }}</td>
              <td>₱{{ number_format($p['employer_share'], 2) }}</td>
              <td>
                @if(($p['active'] ?? false))
                  <span class="badge bg-success">Active</span>
                @else
                  <span class="badge bg-secondary">Inactive</span>
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="text-center text-muted">No plans yet.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Create Plan Modal --}}
  <div class="modal fade" id="createPlanModal" tabindex="-1" aria-labelledby="createPlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createPlanModalLabel">New Benefit Plan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('benefits.plans.store') }}">
          @csrf
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small text-muted">Name</label>
                <input type="text" name="name" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Type</label>
                <input type="text" name="type" class="form-control" value="HMO" required>
              </div>
              <div class="col-md-4">
                <label class="form-label small text-muted">Rate Type</label>
                <select name="rate_type" class="form-select">
                  <option value="monthly" selected>Monthly</option>
                  <option value="fixed">Fixed</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label small text-muted">Employee Share</label>
                <input type="number" step="0.01" name="employee_share" class="form-control" required>
              </div>
              <div class="col-md-4">
                <label class="form-label small text-muted">Employer Share</label>
                <input type="number" step="0.01" name="employer_share" class="form-control">
              </div>
              <div class="col-12">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" name="active" id="activePlan" checked>
                  <label class="form-check-label" for="activePlan">Active</label>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Create</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
