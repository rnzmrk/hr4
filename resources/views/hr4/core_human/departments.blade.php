@extends('layouts.app')

@section('page-title', 'Departments')
@section('page-subtitle', 'Employee monitoring')
@section('breadcrumbs', 'Core Human / Departments')

@section('content')
<div class="container-xxl">
    {{-- Top actions --}}
    <div class="d-flex justify-content-between align-items-center mb-3 gap-2">
      <div class="d-flex align-items-center gap-2">
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
{{-- Add Opening Modal --}}
<div class="modal fade" id="addOpeningModal" tabindex="-1" aria-labelledby="addOpeningModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addOpeningModalLabel">Add Opening</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('requisitions.quick_add') }}">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small text-muted">Department</label>
            <input type="text" name="department" id="op_department" class="form-control" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label small text-muted">Role</label>
            <input type="text" name="title" id="op_title" class="form-control" placeholder="e.g., HR Specialist" required>
          </div>
          <div class="mb-0">
            <label class="form-label small text-muted">Openings</label>
            <input type="number" name="openings" id="op_openings" class="form-control" min="1" value="1" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Opening</button>
        </div>
      </form>
    </div>
  </div>
  </div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const addOpeningModal = document.getElementById('addOpeningModal');
  addOpeningModal?.addEventListener('show.bs.modal', function(e){
    const b = e.relatedTarget; if(!b) return;
    const dept = b.getAttribute('data-dept') || '';
    addOpeningModal.querySelector('#op_department').value = dept;
  });
});
</script>
{{-- Add Department Modal --}}
<div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-labelledby="addDepartmentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addDepartmentModalLabel">Add Department</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('departments.store') }}">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small text-muted">Department Name</label>
            <input type="text" name="name" class="form-control" placeholder="e.g., Finance" required>
          </div>
          <div class="mb-3">
            <label class="form-label small text-muted">Employees (override)</label>
            <input type="number" name="employee_count_override" class="form-control" min="0" value="0">
            <div class="form-text">Optional. If set, this value will display instead of counting employees by department.</div>
          </div>
          
          <div class="mb-0">
            <label class="form-label small text-muted">Description (optional)</label>
            <textarea name="description" class="form-control" rows="2" placeholder="Short description..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add</button>
        </div>
      </form>
    </div>
  </div>
  </div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
          <i class="bi bi-building-add me-1"></i> Add Department
        </button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#requisitionModal">
          <i class="bi bi-file-earmark-plus me-1"></i> Request Requisition
        </button>
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
        $totalEmployees = collect($list)->sum('employee_count');
    @endphp

    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-end mb-2">
          <span class="badge bg-primary-subtle text-primary">Total Employees: <span class="fw-semibold">{{ $totalEmployees }}</span></span>
        </div>
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
                  <div class="btn-group">
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addOpeningModal" data-dept="{{ $dept['name'] }}">
                      <i class="bi bi-plus-circle me-1"></i> Add Opening
                    </button>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#requisitionModal" data-dept="{{ $dept['name'] }}" data-role="{{ $dept['opening_role'] ?? '' }}">
                      <i class="bi bi-file-earmark-plus me-1"></i> Request Requisition
                    </button>
                  </div>
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
              <label class="form-label small text-muted">Openings</label>
              <input type="number" name="opening" class="form-control" min="1" value="1" required>
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
