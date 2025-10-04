@extends('layouts.app')

@section('page-title', 'Compensation Planning')
@section('page-subtitle', 'Salary adjustments applied to payroll rates')
@section('breadcrumbs', 'Compensation / Adjustments')

@section('content')
<div class="container-xxl">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAdjModal">
      <i class="bi bi-plus-circle me-1"></i> Add Adjustment
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
              <th>Employee</th>
              <th>Effective</th>
              <th>Rate Type</th>
              <th>Type</th>
              <th>Value</th>
              <th>Reason</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($adjustments ?? []) as $a)
            <tr>
              <td class="fw-semibold">{{ $a['employee'] }}</td>
              <td>{{ $a['effective_date'] }}</td>
              <td>{{ ucfirst($a['applied_rate_type']) }}</td>
              <td>{{ ucfirst($a['adjustment_type']) }}</td>
              <td>₱{{ number_format($a['value'], 2) }}</td>
              <td>{{ $a['reason'] ?? '—' }}</td>
              <td>
                @if(($a['status'] ?? 'approved') === 'approved')
                  <span class="badge bg-success">Approved</span>
                @elseif(($a['status'] ?? '') === 'pending')
                  <span class="badge bg-warning text-dark">Pending</span>
                @else
                  <span class="badge bg-secondary">Rejected</span>
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center text-muted">No adjustments yet.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Add Adjustment Modal --}}
  <div class="modal fade" id="createAdjModal" tabindex="-1" aria-labelledby="createAdjModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createAdjModalLabel">Add Compensation Adjustment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('compensation.store') }}">
          @csrf
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small text-muted">Employee</label>
                <select name="employee_id" class="form-select" required>
                  @foreach(($employees ?? []) as $e)
                    <option value="{{ $e->id }}">{{ $e->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label small text-muted">Effective Date</label>
                <input type="date" name="effective_date" class="form-control" value="{{ date('Y-m-d') }}" required>
              </div>
              <div class="col-md-3">
                <label class="form-label small text-muted">Rate Type</label>
                <select name="applied_rate_type" class="form-select" required>
                  <option value="monthly" selected>Monthly</option>
                  <option value="hourly">Hourly</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label small text-muted">Adjustment Type</label>
                <select name="adjustment_type" class="form-select" required>
                  <option value="set" selected>Set</option>
                  <option value="increase">Increase (+)</option>
                  <option value="decrease">Decrease (-)</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label small text-muted">Value</label>
                <input type="number" step="0.01" name="value" class="form-control" placeholder="0.00" required>
              </div>
              <div class="col-md-4">
                <label class="form-label small text-muted">Status</label>
                <select name="status" class="form-select" required>
                  <option value="approved" selected>Approved</option>
                  <option value="pending">Pending</option>
                  <option value="rejected">Rejected</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label small text-muted">Reason</label>
                <input type="text" name="reason" class="form-control" placeholder="Reason (optional)">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
