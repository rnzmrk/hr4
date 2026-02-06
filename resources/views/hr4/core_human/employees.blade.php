@extends('layouts.app')

@section('page-title', 'Employees')
@section('page-subtitle', 'Core Human records')
@section('breadcrumbs', 'Core Human / Employees')

@section('content')
<div class="container-xxl">

  {{-- Top bar --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex gap-2">
      <form method="GET" action="{{ route('employees.index') }}" class="d-flex gap-2">
        <input type="text" name="search" class="form-control" placeholder="Search employees..." style="max-width: 260px;" value="{{ request('search') }}">
        <button type="submit" class="btn btn-outline-primary">
          <i class="bi bi-search"></i> Search
        </button>
        @if(request('search'))
          <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-x"></i> Clear
          </a>
        @endif
      </form>
    </div>
    
  </div>

  {{-- Flash message --}}
  @if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('status') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if($notif = session('account_notification'))
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;">
      <div id="accountNotificationToast" class="toast align-items-center {{ $notif['sent'] ? 'text-bg-success' : 'text-bg-warning text-dark' }} border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="7000">
        <div class="d-flex">
          <div class="toast-body">
            <div class="fw-semibold mb-1">{{ $notif['account_type'] }} account for {{ $notif['name'] }}</div>
            <div class="small">Email: <strong>{{ $notif['email'] ?? 'n/a' }}</strong></div>
            <div class="small">Password: <code>{{ $notif['password'] }}</code></div>
            <span class="badge {{ $notif['sent'] ? 'bg-light text-success' : 'bg-dark text-warning' }} mt-2">
              {{ $notif['sent'] ? 'Credentials email sent' : 'Email not sent - check SMTP' }}
            </span>
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    </div>
  @endif

  {{-- Employees table --}}
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        @php
          // Build quick index of existing accounts by email/name and type
          $accs = session('accounts', []);
          $accMap = [];
          foreach ($accs as $a) {
            $keyEmail = !empty($a['email']) ? strtolower($a['email']) : null;
            $keyName = !empty($a['name']) ? strtolower($a['name']) : null;
            $type = $a['account_type'] ?? (strtoupper($a['role'] ?? '') === 'ESS' ? 'ess' : 'system');
            if ($keyEmail) {
              $accMap[$keyEmail][$type] = true;
            }
            if ($keyName) {
              $accMap[$keyName][$type] = true;
            }
          }
        @endphp
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Name</th>
              <th>Department</th>
              <th>Position</th>
              <th>Hired Date</th>
              <th>Account type</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($employees as $emp)
            <tr>
              <td class="fw-semibold">{{ $emp['name'] }}</td>
              <td>{{ $emp['department'] ?? '—' }}</td>
              <td>{{ $emp['position'] ?? '—' }}</td>
              <td>{{ $emp['date_hired'] ? \Illuminate\Support\Carbon::parse($emp['date_hired'])->format('M d, Y') : '—' }}</td>
              @php
                $eEmail = isset($emp['email']) ? strtolower($emp['email']) : null;
                $eName = strtolower($emp['name']);
                $m = $accMap[$eEmail] ?? $accMap[$eName] ?? [];
                $hasEss = isset($m['ess']);
                $hasSystem = isset($m['system']);
              @endphp
              <td>
                @if($hasEss)
                  <span class="badge bg-info text-dark me-1">ESS</span>
                @endif
                @if($hasSystem)
                  <span class="badge bg-secondary me-1">System</span>
                @endif
                @if(!$hasEss && !$hasSystem)
                  <span class="badge bg-warning text-dark">No account</span>
                @endif
              </td>
              <td class="text-end">
                <div class="btn-group">
                  <button type="button" class="btn btn-light btn-sm" title="View Employee Details" data-bs-toggle="modal" data-bs-target="#viewEmployeeModal" data-employee-id="{{ $emp['id'] }}">
                    <i class="bi bi-eye"></i>
                  </button>
                  @if(!$hasEss)
                    <button type="button" class="btn btn-outline-primary btn-sm" title="Create ESS Account" data-bs-toggle="modal" data-bs-target="#createEssAccountModal"
                      data-name="{{ $emp['name'] }}"
                      data-email="{{ $emp['email'] ?? '' }}"
                      data-department="{{ $emp['department'] }}"
                      data-position="{{ $emp['position'] }}">
                      <i class="bi bi-person-plus"></i>
                    </button>
                  @endif
                  @if(!$hasSystem)
                    <button type="button" class="btn btn-primary btn-sm" title="Create System Account" data-bs-toggle="modal" data-bs-target="#createSystemAccountModal"
                      data-name="{{ $emp['name'] }}"
                      data-email="{{ $emp['email'] ?? '' }}"
                      data-department="{{ $emp['department'] }}"
                      data-position="{{ $emp['position'] }}">
                      <i class="bi bi-building-gear"></i>
                    </button>
                  @endif
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="text-center text-muted">No employees found.</td>
            </tr> 
            @endforelse
          </tbody>
        </table>
      </div>
      
      {{-- Employees Pagination --}}
      <div class="d-flex justify-content-center">
        {{ $employees->links('pagination::bootstrap-4', ['pageName' => 'employeePage']) }}
      </div>
    </div>
  </div>
  {{-- Create ESS Account Modal --}}
  <div class="modal fade" id="createEssAccountModal" tabindex="-1" aria-labelledby="createEssAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createEssAccountModalLabel">Create ESS Account for Employee</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('accounts.store') }}" id="createEssAccountForm">
          @csrf
          <input type="hidden" name="account_type" value="ess">
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small text-muted">Full Name</label>
                <input type="text" name="name" id="ess_name" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Email</label>
                <input type="email" name="email" id="ess_email" class="form-control" placeholder="user@example.com" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Department</label>
                <input type="text" name="department" id="ess_department" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Position</label>
                <select name="position" id="ess_position" class="form-select" required>
                  <option value="" disabled selected>Select position</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Status</label>
                <select name="status" id="ess_status" class="form-select">
                  <option value="Active" selected>Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Password</label>
                <input type="password" name="password" id="ess_password" class="form-control" placeholder="Set password (optional)">
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

  {{-- Create System Account Modal --}}
  <div class="modal fade" id="createSystemAccountModal" tabindex="-1" aria-labelledby="createSystemAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createSystemAccountModalLabel">Create System Account for Employee</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('accounts.store') }}" id="createSystemAccountForm">
          @csrf
          <input type="hidden" name="account_type" value="system">
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small text-muted">Full Name</label>
                <input type="text" name="name" id="sys_name" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Email</label>
                <input type="email" name="email" id="sys_email" class="form-control" placeholder="user@example.com" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Department</label>
                <select name="department" id="sys_department" class="form-select" required>
                  <option value="" disabled selected>Select department</option>
                  @foreach(($departments ?? []) as $d)
                    <option value="{{ $d->name }}">{{ $d->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Position</label>
                <select name="position" id="sys_position" class="form-select" required>
                  <option value="" disabled selected>Select position</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Status</label>
                <select name="status" id="sys_status" class="form-select">
                  <option value="Active" selected>Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Password (plain)</label>
                <input type="password" name="password" id="sys_password" class="form-control" placeholder="Set password (optional)">
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

  {{-- View Employee Details Modal --}}
  <div class="modal fade" id="viewEmployeeModal" tabindex="-1" aria-labelledby="viewEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewEmployeeModalLabel">Employee Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="employeeDetailsContent">
            <div class="text-center">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const toastEl = document.getElementById('accountNotificationToast');
  if (toastEl && window.bootstrap?.Toast) {
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
  }
  // Shared department -> positions mapping (same as DepartmentsController)
  const positionMappings = {
    'Financial': ['Financial Staff'],
    'Core': ['Travel Agent', 'Travel Staff'],
    'Logistic': ['Driver', 'Fleet Manager', 'Procurement Officer', 'Logistics Staff'],
    'Human Resource': ['Hr Manager', 'Hr Staff'],
    'Administrative': ['Administrative Staff']
  };

  function populatePositions(selectEl, deptName, preselect) {
    if (!selectEl) return;
    const positions = positionMappings[deptName] || [];
    selectEl.innerHTML = '<option value="" disabled>Select position</option>';

    positions.forEach(p => {
      const opt = document.createElement('option');
      opt.value = p;
      opt.textContent = p;
      selectEl.appendChild(opt);
    });

    if (preselect) {
      selectEl.value = preselect;
      if (!selectEl.value && positions.length === 1) {
        selectEl.value = positions[0];
      }
    } else if (positions.length === 1) {
      selectEl.value = positions[0];
    } else {
      selectEl.selectedIndex = 0;
    }
  }

  const sysModal = document.getElementById('createSystemAccountModal');
  sysModal?.addEventListener('show.bs.modal', function(event){
    const btn = event.relatedTarget;
    if (!btn) return;
    document.getElementById('sys_name').value = btn.getAttribute('data-name') || '';
    document.getElementById('sys_email').value = btn.getAttribute('data-email') || '';
    const deptName = btn.getAttribute('data-department') || '';
    const deptSel = document.getElementById('sys_department');
    const posSel = document.getElementById('sys_position');
    if (deptSel) {
      let matched = false;
      [...deptSel.options].forEach(o => { if (o.text === deptName) { o.selected = true; matched = true; } });
      if (!matched && deptSel.options.length) deptSel.selectedIndex = 0;
    }
    const effectiveDept = deptSel ? deptSel.value : deptName;
    populatePositions(posSel, effectiveDept || '', btn.getAttribute('data-position') || '');
    document.getElementById('sys_status').value = 'Active';
    document.getElementById('sys_password').value = '';
  });

  // View Employee Details Modal
  const viewModal = document.getElementById('viewEmployeeModal');
  viewModal?.addEventListener('show.bs.modal', function(event){
    const btn = event.relatedTarget;
    if (!btn) return;
    
    const employeeId = btn.getAttribute('data-employee-id');
    const contentDiv = document.getElementById('employeeDetailsContent');
    
    // Show loading spinner
    contentDiv.innerHTML = `
      <div class="text-center">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    `;
    
    // Fetch employee details
    fetch(`/employees/${employeeId}`)
      .then(response => response.json())
      .then(data => {
        if (data.error) {
          contentDiv.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
          return;
        }
        
        const fullName = `${data.first_name || ''} ${data.middle_name || ''} ${data.last_name || ''} ${data.suffix_name || ''}`.trim();
        const statusLabel = {
          'new_hire': 'New Hire',
          'regular': 'Regular', 
          'retired': 'Retired'
        }[data.employee_status] || 'Unknown';
        
        const statusColor = {
          'new_hire': 'bg-info',
          'regular': 'bg-success',
          'retired': 'bg-secondary'
        }[data.employee_status] || 'bg-warning text-dark';
        
        contentDiv.innerHTML = `
          <div class="row">
            <div class="col-md-12">
              <div class="d-flex align-items-center mb-3">
                <div class="me-3">
                  <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 24px;">
                    ${fullName.charAt(0).toUpperCase()}
                  </div>
                </div>
                <div>
                  <h5 class="mb-1">${fullName}</h5>
                  <p class="text-muted mb-0">${data.position || 'No Position'}</p>
                  <span class="badge ${statusColor}">${statusLabel}</span>
                </div>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <h6 class="text-muted mb-3">Personal Information</h6>
              <table class="table table-sm">
                <tr><td><strong>First Name:</strong></td><td>${data.first_name || '—'}</td></tr>
                <tr><td><strong>Middle Name:</strong></td><td>${data.middle_name || '—'}</td></tr>
                <tr><td><strong>Last Name:</strong></td><td>${data.last_name || '—'}</td></tr>
                <tr><td><strong>Suffix:</strong></td><td>${data.suffix_name || '—'}</td></tr>
                <tr><td><strong>Age:</strong></td><td>${data.age || '—'}</td></tr>
                <tr><td><strong>Gender:</strong></td><td>${data.gender || '—'}</td></tr>
                <tr><td><strong>Birth Date:</strong></td><td>${data.birth_date ? new Date(data.birth_date).toLocaleDateString() : '—'}</td></tr>
                <tr><td><strong>Civil Status:</strong></td><td>${data.civil_status || '—'}</td></tr>
              </table>
            </div>
            <div class="col-md-6">
              <h6 class="text-muted mb-3">Contact & Work Information</h6>
              <table class="table table-sm">
                <tr><td><strong>Address:</strong></td><td>${data.address || '—'}</td></tr>
                <tr><td><strong>Phone:</strong></td><td>${data.phone || '—'}</td></tr>
                <tr><td><strong>Email:</strong></td><td>${data.email || '—'}</td></tr>
                <tr><td><strong>Department:</strong></td><td>${data.department?.name || '—'}</td></tr>
                <tr><td><strong>Position:</strong></td><td>${data.position || '—'}</td></tr>
                <tr><td><strong>Start Date:</strong></td><td>${data.start_date ? new Date(data.start_date).toLocaleDateString() : '—'}</td></tr>
                <tr><td><strong>Date Hired:</strong></td><td>${data.date_hired ? new Date(data.date_hired).toLocaleDateString() : '—'}</td></tr>
              </table>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-12">
              <h6 class="text-muted mb-3">Additional Information</h6>
              <div class="row">
                <div class="col-md-4">
                  <strong>Skills:</strong>
                  <p class="text-muted">${data.skills || 'No skills listed'}</p>
                </div>
                <div class="col-md-4">
                  <strong>Experience:</strong>
                  <p class="text-muted">${data.experience || 'No experience listed'}</p>
                </div>
                <div class="col-md-4">
                  <strong>Education:</strong>
                  <p class="text-muted">${data.education || 'No education listed'}</p>
                </div>
              </div>
            </div>
          </div>
        `;
      })
      .catch(error => {
        console.error('Error fetching employee details:', error);
        contentDiv.innerHTML = `<div class="alert alert-danger">Error loading employee details. Please try again.</div>`;
      });
  });

  // Populate ESS Account modal
  const createEssModal = document.getElementById('createEssAccountModal');
  if (createEssModal) {
    createEssModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      document.getElementById('ess_name').value = button.getAttribute('data-name') || '';
      document.getElementById('ess_email').value = button.getAttribute('data-email') || '';
      const deptInput = document.getElementById('ess_department');
      const posSel = document.getElementById('ess_position');
      const deptName = button.getAttribute('data-department') || '';
      if (deptInput) deptInput.value = deptName;
      populatePositions(posSel, deptName || '', button.getAttribute('data-position') || '');
    });
  }
});
</script>
@endsection
