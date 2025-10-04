@extends('layouts.app')

@section('page-title', 'Departments')
@section('page-subtitle', 'Employee monitoring')
@section('breadcrumbs', 'Core Human / Departments')

@section('content')
<div class="container-xxl">
    {{-- Top actions --}}
    <div class="d-flex justify-content-start align-items-center mb-3 gap-2">
        <input type="text" class="form-control" placeholder="Search departments..." style="max-width: 260px;">
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                Filter: All
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">All</a></li>
                <li><a class="dropdown-item" href="#">With Openings</a></li>
                <li><a class="dropdown-item" href="#">No Openings</a></li>
            </ul>
        </div>
    </div>

    {{-- Departments monitoring table --}}
    @php
        // Expect $departments from controller/route, but provide safe default
        $list = $departments ?? [
            ['name' => 'Human Resources', 'employee_count' => 8, 'openings' => 2, 'opening_role' => 'HR Specialist'],
            ['name' => 'Finance', 'employee_count' => 6, 'openings' => 1, 'opening_role' => 'Accountant'],
            ['name' => 'Marketing', 'employee_count' => 10, 'openings' => 0, 'opening_role' => null],
            ['name' => 'Operations', 'employee_count' => 15, 'openings' => 3, 'opening_role' => 'Operations Associate'],
        ];
    @endphp

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Department</th>
                <th class="text-center">Employees</th>
                <th class="text-center">Role</th>
                <th class="text-center">Openings</th>
                <th class="text-end">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($list as $dept)
              <tr>
                <td class="fw-semibold">{{ $dept['name'] }}</td>
                <td class="text-center">{{ $dept['employee_count'] }}</td>
                <td class="text-center">
                  @if(($dept['openings'] ?? 0) > 0 && !empty($dept['opening_role']))
                    <span class="badge bg-info text-dark">{{ $dept['opening_role'] }}</span>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td class="text-center">
                  @if(($dept['openings'] ?? 0) > 0)
                    <span class="badge bg-warning text-dark">{{ $dept['openings'] }} openings</span>
                  @else
                    <span class="badge bg-secondary">No openings</span>
                  @endif
                </td>
                <td class="text-end">
                  @if(($dept['openings'] ?? 0) > 0)
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#requisitionModal" data-dept="{{ $dept['name'] }}" data-role="{{ $dept['opening_role'] ?? '' }}">
                      <i class="bi bi-file-earmark-plus me-1"></i> Request Requisition
                    </button>
                  @else
                    <button class="btn btn-outline-secondary btn-sm" disabled>
                      <i class="bi bi-check2-circle me-1"></i> Sufficient staffing
                    </button>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
</div>

{{-- Requisition Request Modal --}}
<div class="modal fade" id="requisitionModal" tabindex="-1" aria-labelledby="requisitionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="requisitionModalLabel"><i class="bi bi-briefcase-fill me-2"></i>Requisition Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('requisitions.store') }}">
        @csrf
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small text-muted">Requested By</label>
              <input type="text" name="requested_by" class="form-control" placeholder="Alice Smith" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small text-muted">Department</label>
              <select name="department" class="form-select" required>
                @foreach ($list as $dept)
                    <option value="{{ $dept['name'] }}">{{ $dept['name'] }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Role</label>
              <input type="text" name="requisition_title" class="form-control" placeholder="HR Specialist" required>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted">Opening Date</label>
              <input type="date" name="opening" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-3">
              <label class="form-label small text-muted">Status</label>
              <select name="status" class="form-select" required>
                <option>Draft</option>
                <option>In-Progress</option>
                <option>Closed</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Type</label>
              <select name="requisition_type" class="form-select" required>
                <option>Full-Time</option>
                <option>Part-Time</option>
                <option>Contract</option>
                <option>Internship</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small text-muted">Arrangement</label>
              <select name="requisition_arrangement" class="form-select">
                <option>On-Site</option>
                <option>Hybrid</option>
                <option>Remote</option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label small text-muted">Description</label>
              <textarea name="requisition_description" class="form-control" rows="2" placeholder="Role overview..."></textarea>
            </div>
            <div class="col-12">
              <label class="form-label small text-muted">Responsibilities</label>
              <textarea name="requisition_responsibilities" class="form-control" rows="2" placeholder="Key responsibilities..."></textarea>
            </div>
            <div class="col-12">
              <label class="form-label small text-muted">Qualifications</label>
              <textarea name="requisition_qualifications" class="form-control" rows="2" placeholder="Required qualifications..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Submit Request</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const modalEl = document.getElementById('requisitionModal');
  if (!modalEl) return;
  modalEl.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const dept = button ? button.getAttribute('data-dept') : null;
    const role = button ? button.getAttribute('data-role') : null;
    if (dept) {
      const select = modalEl.querySelector('select[name="department"]');
      if (select) select.value = dept;
    }
    if (role) {
      const titleInput = modalEl.querySelector('input[name="requisition_title"]');
      if (titleInput) titleInput.value = role;
    }
  });
});
</script>
@endsection
