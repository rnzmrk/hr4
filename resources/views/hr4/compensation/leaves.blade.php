@extends('layouts.app')

@section('page-title', 'Leave Records')
@section('page-subtitle', 'leaves records')
@section('breadcrumbs', 'Compensation / Leaves')

@section('content')
<div class="container-xxl">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Leave Management</h4>
  </div>
  {{-- View Leave Details Modal --}}
  <div class="modal fade" id="editLeaveModal" tabindex="-1" aria-labelledby="editLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editLeaveModalLabel">Leave Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small text-muted">Employee</label>
              <input type="text" id="view_employee_name" class="form-control" readonly>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted">Type</label>
              <input type="text" id="view_leave_type" class="form-control" readonly>
            </div>
            <div class="col-md-3 d-flex align-items-end">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="view_is_paid" disabled>
                <label class="form-check-label" for="view_is_paid">Paid leave</label>
              </div>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted">Start Date</label>
              <input type="date" id="view_start_date" class="form-control" readonly>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted">End Date</label>
              <input type="date" id="view_end_date" class="form-control" readonly>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted">Hours</label>
              <input type="text" id="view_hours" class="form-control" readonly>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted">Status</label>
              <input type="text" id="view_status" class="form-control" readonly>
            </div>
            <div class="col-12">
              <label class="form-label small text-muted">Notes</label>
              <input type="text" id="view_notes" class="form-control" readonly>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function(){
      const viewModal = document.getElementById('editLeaveModal');
      viewModal?.addEventListener('show.bs.modal', function(e){
        const b = e.relatedTarget;
        if (!b) return;
        
        // Populate view-only fields
        document.getElementById('view_employee_name').value = b.getAttribute('data-employee_name') || '';
        document.getElementById('view_leave_type').value = b.getAttribute('data-leave_type') || '';
        document.getElementById('view_start_date').value = b.getAttribute('data-start_date') || '';
        document.getElementById('view_end_date').value = b.getAttribute('data-end_date') || '';
        document.getElementById('view_hours').value = b.getAttribute('data-hours') || '';
        document.getElementById('view_status').value = b.getAttribute('data-status') || '';
        document.getElementById('view_notes').value = b.getAttribute('data-notes') || '';
        
        const isPaid = (b.getAttribute('data-is_paid') || '0') === '1';
        document.getElementById('view_is_paid').checked = isPaid;
      });
    });
  </script>

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
              <th>Type</th>
              <th>Paid</th>
              <th>Start</th>
              <th>End</th>
              <th>Hours</th>
              <th>Status</th>
              <th>Notes</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($leaves ?? []) as $l)
            <tr>
              <td class="fw-semibold">{{ $l['employee'] }}</td>
              <td>{{ ucfirst($l['leave_type']) }}</td>
              <td>
                @if(($l['is_paid'] ?? false))
                  <span class="badge bg-success">Paid</span>
                @else
                  <span class="badge bg-secondary">Unpaid</span>
                @endif
              </td>
              <td>{{ $l['start_date'] }}</td>
              <td>{{ $l['end_date'] }}</td>
              <td>{{ $l['hours'] ?? '—' }}</td>
              <td>{{ ucfirst($l['status']) }}</td>
              <td>{{ $l['notes'] ?? '—' }}</td>
              <td class="text-end">
                <button class="btn btn-light btn-sm" title="View Details"
                  data-bs-toggle="modal" data-bs-target="#editLeaveModal"
                  data-id="{{ $l['id'] }}"
                  data-employee_name="{{ $l['employee'] }}"
                  data-leave_type="{{ $l['leave_type'] }}"
                  data-is_paid="{{ ($l['is_paid'] ?? false) ? 1 : 0 }}"
                  data-start_date="{{ $l['start_date'] }}"
                  data-end_date="{{ $l['end_date'] }}"
                  data-hours="{{ $l['hours'] ?? '' }}"
                  data-status="{{ $l['status'] }}"
                  data-notes="{{ $l['notes'] ?? '' }}">
                  <i class="bi bi-eye"></i> View
                </button>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="9" class="text-center text-muted py-4">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                <div>No leave records found</div>
                <small class="text-muted">Leave records will appear here when available</small>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
