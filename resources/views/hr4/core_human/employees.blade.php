@extends('layouts.app')

@section('page-title', 'Employees')
@section('page-subtitle', 'Core Human records')
@section('breadcrumbs', 'Core Human / Employees')

@section('content')
<div class="container-xxl">

  {{-- Top bar --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex gap-2">
      <input type="text" class="form-control" placeholder="Search employees..." style="max-width: 260px;">
    </div>
    
  </div>

  {{-- Flash message --}}
  @if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('status') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
              <th>Role</th>
              <th>Start Date</th>
              <th>Status</th>
              <th>Account</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($employees ?? []) as $emp)
            <tr>
              <td class="fw-semibold">{{ $emp['name'] }}</td>
              <td>{{ $emp['department'] }}</td>
              <td>{{ $emp['role'] }}</td>
              <td>{{ \Illuminate\Support\Carbon::parse($emp['start_date'])->format('M d, Y') }}</td>
              <td>
                @if(($emp['status'] ?? '') === 'Active')
                  <span class="badge bg-success">Active</span>
                @else
                  <span class="badge bg-secondary">{{ $emp['status'] ?? 'Unknown' }}</span>
                @endif
              </td>
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
                  <a href="{{ route('payroll.employee', ['employee_id' => $emp['id'] ?? null, 'employee' => $emp['name']]) }}" class="btn btn-outline-secondary btn-sm" title="View Payrolls">
                    <i class="bi bi-cash-stack"></i>
                  </a>
                  <button class="btn btn-light btn-sm" title="View"><i class="bi bi-eye"></i></button>
                  @if(!$hasEss)
                    <form method="POST" action="{{ route('accounts.from_employee') }}" class="d-inline me-1">
                      @csrf
                      <input type="hidden" name="name" value="{{ $emp['name'] }}">
                      <input type="hidden" name="email" value="{{ $emp['email'] ?? '' }}">
                      <input type="hidden" name="department" value="{{ $emp['department'] }}">
                      <button type="submit" class="btn btn-outline-primary btn-sm" title="Create ESS Account">
                        <i class="bi bi-person-plus"></i>
                      </button>
                    </form>
                  @endif
                  @if(!$hasSystem)
                    <button type="button" class="btn btn-primary btn-sm" title="Create System Account" data-bs-toggle="modal" data-bs-target="#createSystemAccountModal"
                      data-name="{{ $emp['name'] }}"
                      data-email="{{ $emp['email'] ?? '' }}"
                      data-department="{{ $emp['department'] }}"
                      data-role="{{ $emp['role'] }}">
                      <i class="bi bi-building-gear"></i>
                    </button>
                  @endif
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center text-muted">No employees found.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
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
                <label class="form-label small text-muted">Role</label>
                <input type="text" name="role" id="sys_role" class="form-control" placeholder="e.g., HR, Manager, Admin" required>
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
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const sysModal = document.getElementById('createSystemAccountModal');
  sysModal?.addEventListener('show.bs.modal', function(event){
    const btn = event.relatedTarget;
    if (!btn) return;
    document.getElementById('sys_name').value = btn.getAttribute('data-name') || '';
    document.getElementById('sys_email').value = btn.getAttribute('data-email') || '';
    const deptName = btn.getAttribute('data-department') || '';
    const deptSel = document.getElementById('sys_department');
    if (deptSel) {
      let matched = false;
      [...deptSel.options].forEach(o => { if (o.text === deptName) { o.selected = true; matched = true; } });
      if (!matched && deptSel.options.length) deptSel.selectedIndex = 0;
    }
    document.getElementById('sys_role').value = btn.getAttribute('data-role') || '';
    document.getElementById('sys_status').value = 'Active';
    document.getElementById('sys_password').value = '';
  });
});
</script>
@endsection
