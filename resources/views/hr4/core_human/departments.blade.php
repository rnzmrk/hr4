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
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
          <i class="bi bi-building-add me-1"></i> Add Department
        </button>
      </div>
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
            <select name="name" id="departmentName" class="form-select" required>
              <option value="">Select Department</option>
              <option value="Financial">Financial</option>
              <option value="Core">Core</option>
              <option value="Logistic">Logistic</option>
              <option value="Human Resource">Human Resource</option>
              <option value="Administrative">Administrative</option>
            </select>
          </div>
          <div class="mb-0">
            <label class="form-label small text-muted">Position Limits</label>
            <div id="positionLimitsContainer">
              <div class="text-muted small mb-2">Set limits for each position:</div>
              <div id="positionLimitsList"></div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Department</button>
        </div>
      </form>
    </div>
  </div>
  </div>

    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-end mb-2">
          <span class="badge bg-primary-subtle text-primary">Total Employees: <span class="fw-semibold"></span></span>
        </div>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Department</th>
                <th class="text-center">Employee Limit</th>
                <th class="text-center">Employees</th>
                <th class="text-center">Openings</th>
                <th class="text-end">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($data as $dept)
              <tr>
                <td class="fw-semibold">{{ $dept['name'] }}</td>
                <td class="text-center">
                  <span class="badge bg-info fs-6">
                    {{ $dept['employee_limit'] }}
                  </span>
                </td>
                <td class="text-center">
                  <span class="badge bg-success fs-6">
                    {{ $dept['employee_count'] }}
                  </span>
                </td>
                <td class="text-center">
                  @if($dept['openings'] > 0)
                    <span class="badge bg-warning text-dark fs-6">{{ $dept['openings'] }} openings</span>
                  @else
                    <span class="badge bg-secondary fs-6">No openings</span>
                  @endif
                </td>
                <td class="text-end">
                  <div class="btn-group">
                    <a href="{{ route('departments.show', ['departmentName' => $dept['name']]) }}" class="btn btn-outline-info btn-sm">
                      <i class="bi bi-eye me-1"></i> View
                    </a>
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
              <input type="text" name="requested_by" class="form-control" value="{{ auth()->user()->name ?? '' }}" readonly required>
            </div>
            <div class="col-md-6">
              <label class="form-label small text-muted">Department</label>
              <select name="department" class="form-select" required>
                @foreach ($data as $dept)
                    <option value="{{ $dept['name'] }}">{{ $dept['name'] }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Position</label>
              <select name="position" id="req_position" class="form-select" required>
                <option value="" disabled selected>Select position</option>
              </select>
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

  // Department -> positions mapping (same as DepartmentsController)
  const positionMappings = {
    'Financial': ['Financial Staff'],
    'Core': ['Travel Agent', 'Travel Staff'],
    'Logistic': ['Driver', 'Fleet Manager', 'Procurement Officer', 'Logistics Staff'],
    'Human Resource': ['Hr Manager', 'Hr Staff'],
    'Administrative': ['Administrative Staff']
  };

  function populateReqPositions(deptName, preselect) {
    const posSelect = modalEl.querySelector('#req_position');
    if (!posSelect) return;

    const positions = positionMappings[deptName] || [];
    posSelect.innerHTML = '<option value="" disabled>Select position</option>';

    positions.forEach(p => {
      const opt = document.createElement('option');
      opt.value = p;
      opt.textContent = p;
      posSelect.appendChild(opt);
    });

    if (preselect) {
      posSelect.value = preselect;
      if (!posSelect.value && positions.length === 1) {
        posSelect.value = positions[0];
      }
    } else if (positions.length === 1) {
      posSelect.value = positions[0];
    } else {
      posSelect.selectedIndex = 0;
    }
  }

  modalEl.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const dept = button ? button.getAttribute('data-dept') : null;
    const role = button ? button.getAttribute('data-role') : null;
    const deptSelect = modalEl.querySelector('select[name="department"]');

    if (dept && deptSelect) {
      deptSelect.value = dept;
      populateReqPositions(dept, role || null);
    } else if (deptSelect) {
      populateReqPositions(deptSelect.value || '', role || null);
    }
  });

  const deptSelect = modalEl.querySelector('select[name="department"]');
  if (deptSelect) {
    deptSelect.addEventListener('change', function(){
      populateReqPositions(this.value || '', null);
    });

    // Initial population on page load
    if (deptSelect.value) {
      populateReqPositions(deptSelect.value, null);
    }
  }
});
</script>

{{-- Department Details Modal --}}
<div class="modal fade" id="departmentDetailsModal" tabindex="-1" aria-labelledby="departmentDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="departmentDetailsModalLabel"><i class="bi bi-building me-2"></i>Department Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="departmentDetailsContent">
          <!-- Content will be loaded dynamically -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
function viewDepartmentDetails(departmentName) {
    fetch(`/departments/${encodeURIComponent(departmentName)}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            
            let content = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">Department</h6>
                        <p class="fw-semibold">${data.department}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Total Capacity</h6>
                        <p class="fw-semibold">${data.total_employees}/${data.employee_limit} Employees</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">Available Openings</h6>
                        <p class="fw-semibold"><span class="badge bg-success">${data.openings} openings</span></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Current Employees</h6>
                        <p class="fw-semibold">${data.total_employees} employees</p>
                    </div>
                </div>
            `;
            
            if (Object.keys(data.position_counts).length > 0) {
                content += `
                    <hr>
                    <h6 class="mb-3">Employee Count by Position</h6>
                    <div class="row">
                `;
                
                for (const [position, count] of Object.entries(data.position_counts)) {
                    content += `
                        <div class="col-md-6 mb-2">
                            <div class="d-flex justify-content-between align-items-center p-2 border rounded">
                                <span>${position}</span>
                                <span class="badge bg-primary">${count}</span>
                            </div>
                        </div>
                    `;
                }
                
                content += `</div>`;
            }
            
            document.getElementById('departmentDetailsContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('departmentDetailsModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading department details');
        });
}
</script>

<script>
// Dynamic position filtering for Add Department modal
const positionMappings = {
    'Financial': ['Financial Staff'],
    'Core': ['Travel Agent', 'Travel Staff'],
    'Logistic': ['Driver', 'Fleet Manager', 'Procurement Officer', 'Logistics Staff'],
    'Human Resource': ['Hr Manager', 'Hr Staff'],
    'Administrative': ['Administrative Staff']
};

document.addEventListener('DOMContentLoaded', function() {
    const departmentSelect = document.getElementById('departmentName');

    function updatePositions() {
        const selectedDepartment = departmentSelect.value;
        const positions = positionMappings[selectedDepartment] || [];

        // Clear position limits
        const positionLimitsList = document.getElementById('positionLimitsList');
        if (!positionLimitsList) return;
        positionLimitsList.innerHTML = '';
        
        // Add position limit inputs
        positions.forEach(position => {
            const limitDiv = document.createElement('div');
            limitDiv.className = 'row mb-2 align-items-center';
            limitDiv.innerHTML = `
                <div class="col-md-6">
                    <label class="form-label small mb-1">${position}</label>
                </div>
                <div class="col-md-6">
                    <input type="number" 
                           class="form-control form-control-sm" 
                           name="position_limits[${position}]" 
                           min="0" 
                           placeholder="0"
                           value="">
                    <small class="text-muted">Max employees</small>
                </div>
            `;
            positionLimitsList.appendChild(limitDiv);
        });
    }

    // Update positions when department changes
    departmentSelect.addEventListener('change', updatePositions);
    
    // Initialize with empty positions
    updatePositions();
});
</script>
@endsection
