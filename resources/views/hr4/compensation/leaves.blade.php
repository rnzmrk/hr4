@extends('layouts.app')

@section('page-title', 'Leave Records')
@section('page-subtitle', 'Approved/pending leaves considered in payroll')
@section('breadcrumbs', 'Compensation / Leaves')

@section('content')
<div class="container-xxl">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createLeaveModal">
      <i class="bi bi-plus-circle me-1"></i> Record Leave
    </button>
  </div>
  {{-- Edit Leave Modal --}}
  <div class="modal fade" id="editLeaveModal" tabindex="-1" aria-labelledby="editLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editLeaveModalLabel">Edit Leave</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('compensation.leaves.update') }}">
          @csrf
          <input type="hidden" name="id" id="edit_id">
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small text-muted">Employee</label>
                <select name="employee_id" id="edit_employee_id" class="form-select" required>
                  @foreach(($employees ?? []) as $e)
                    <option value="{{ $e->id }}">{{ $e->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label small text-muted">Type</label>
                <input type="text" name="leave_type" id="edit_leave_type" class="form-control" required>
              </div>
              <div class="col-md-3 d-flex align-items-end">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" id="edit_is_paid" name="is_paid">
                  <label class="form-check-label" for="edit_is_paid">Paid leave</label>
                </div>
              </div>
              <div class="col-md-3">
                <label class="form-label small text-muted">Start Date</label>
                <input type="date" name="start_date" id="edit_start_date" class="form-control" required>
              </div>
              <div class="col-md-3">
                <label class="form-label small text-muted">End Date</label>
                <input type="date" name="end_date" id="edit_end_date" class="form-control" required>
              </div>
              <div class="col-md-3">
                <label class="form-label small text-muted">Hours (optional)</label>
                <input type="number" step="0.01" name="hours" id="edit_hours" class="form-control" placeholder="8.00">
              </div>
              <div class="col-md-3">
                <label class="form-label small text-muted">Status</label>
                <select name="status" id="edit_status" class="form-select" required>
                  <option value="approved">Approved</option>
                  <option value="pending">Pending</option>
                  <option value="rejected">Rejected</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label small text-muted">Notes</label>
                <input type="text" name="notes" id="edit_notes" class="form-control" placeholder="Optional notes">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function(){
      const editModal = document.getElementById('editLeaveModal');
      editModal?.addEventListener('show.bs.modal', function(e){
        const b = e.relatedTarget;
        if (!b) return;
        // Set ID
        editModal.querySelector('#edit_id').value = b.getAttribute('data-id') || '';
        // Map employee by name to ID in the select, fallback to first option
        const empName = b.getAttribute('data-employee_name') || '';
        const sel = editModal.querySelector('#edit_employee_id');
        if (sel) {
          let matched = false;
          [...sel.options].forEach(o => {
            if (o.text === empName) { o.selected = true; matched = true; }
          });
          if (!matched && sel.options.length) sel.selectedIndex = 0;
        }
        // Other fields
        editModal.querySelector('#edit_leave_type').value = b.getAttribute('data-leave_type') || '';
        const isPaid = (b.getAttribute('data-is_paid') || '0') === '1';
        editModal.querySelector('#edit_is_paid').checked = isPaid;
        editModal.querySelector('#edit_start_date').value = b.getAttribute('data-start_date') || '';
        editModal.querySelector('#edit_end_date').value = b.getAttribute('data-end_date') || '';
        editModal.querySelector('#edit_hours').value = b.getAttribute('data-hours') || '';
        editModal.querySelector('#edit_status').value = (b.getAttribute('data-status') || 'approved');
        editModal.querySelector('#edit_notes').value = b.getAttribute('data-notes') || '';
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
                <div class="btn-group">
                  <button class="btn btn-light btn-sm" title="Edit"
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
                    <i class="bi bi-pencil"></i>
                  </button>
                  <form method="POST" action="{{ route('compensation.leaves.delete') }}" onsubmit="return confirm('Delete this leave record?');">
                    @csrf
                    <input type="hidden" name="id" value="{{ $l['id'] }}">
                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete"><i class="bi bi-trash"></i></button>
                  </form>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="9" class="text-center text-muted">No leave records yet.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Record Leave Modal --}}
  <div class="modal fade" id="createLeaveModal" tabindex="-1" aria-labelledby="createLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createLeaveModalLabel">Record Leave</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('compensation.leaves.store') }}">
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
                <label class="form-label small text-muted">Type</label>
                <input type="text" name="leave_type" class="form-control"  required>
              </div>
              <div class="col-md-3 d-flex align-items-end">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" id="is_paid" name="is_paid">
                  <label class="form-check-label" for="is_paid">Paid leave</label>
                </div>
              </div>
              <div class="col-md-3">
                <label class="form-label small text-muted">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
              </div>
              <div class="col-md-3">
                <label class="form-label small text-muted">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d') }}" required>
              </div>
              <div class="col-md-3">
                <label class="form-label small text-muted">Hours (optional)</label>
                <input type="number" step="0.01" name="hours" class="form-control" placeholder="8.00">
              </div>
              <div class="col-md-3">
                <label class="form-label small text-muted">Status</label>
                <select name="status" class="form-select" required>
                  <option value="approved" selected>Approved</option>
                  <option value="pending">Pending</option>
                  <option value="rejected">Rejected</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label small text-muted">Notes</label>
                <input type="text" name="notes" class="form-control" placeholder="Optional notes">
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
