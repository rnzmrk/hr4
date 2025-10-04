@extends('layouts.app')

@section('page-title', 'Accounts')
@section('page-subtitle', 'Create accounts by role and ESS')
@section('breadcrumbs', 'Core Human / Accounts')

@section('content')
<div class="container-xxl">

  {{-- Top bar --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex gap-2">
      <input type="text" class="form-control" placeholder="Search accounts..." style="max-width: 260px;">
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createAccountModal" data-account-type="system">
        <i class="bi bi-building-gear me-1"></i> Create System Account (by department & role)
      </button>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAccountModal" data-account-type="ess">
        <i class="bi bi-person-plus me-1"></i> Create ESS Account (employees)
      </button>
    </div>
  </div>

  {{-- Flash message --}}
  @if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('status') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  {{-- Accounts table --}}
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Account Type</th>
              <th>Department</th>
              <th>Role</th>
              <th>Status</th>
              <th>Created</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($accounts ?? []) as $acc)
            <tr>
              <td class="fw-semibold">{{ $acc['name'] }}</td>
              <td>{{ $acc['email'] }}</td>
              <td>
                @if(($acc['account_type'] ?? '') === 'ess')
                  <span class="badge bg-info text-dark">ESS</span>
                @else
                  <span class="badge bg-secondary">System</span>
                @endif
              </td>
              <td>{{ $acc['department'] ?? '—' }}</td>
              <td>
                @if(strtoupper($acc['role']) === 'ESS')
                  <span class="badge bg-info text-dark">ESS</span>
                @else
                  <span class="badge bg-secondary">{{ $acc['role'] }}</span>
                @endif
              </td>
              <td>
                @if(($acc['status'] ?? '') === 'Active')
                  <span class="badge bg-success">Active</span>
                @else
                  <span class="badge bg-secondary">{{ $acc['status'] ?? 'Unknown' }}</span>
                @endif
              </td>
              <td>{{ $acc['created_at'] }}</td>
              <td class="text-end">
                <div class="btn-group">
                  <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#viewAccountModal"
                    data-id="{{ $acc['id'] }}"
                    data-name="{{ $acc['name'] }}"
                    data-email="{{ $acc['email'] }}"
                    data-type="{{ $acc['account_type'] ?? '' }}"
                    data-department="{{ $acc['department'] ?? '' }}"
                    data-role="{{ $acc['role'] }}"
                    data-status="{{ $acc['status'] ?? 'Active' }}"
                    data-created="{{ $acc['created_at'] }}"
                    data-blocked="{{ ($acc['blocked'] ?? false) ? 'Yes' : 'No' }}"
                    data-pass-plain="{{ $acc['password_plain'] ?? '' }}"
                    data-pass-hash="{{ $acc['password_hashed'] ?? '' }}"
                    title="View">
                    <i class="bi bi-eye"></i>
                  </button>
                  <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#editAccountModal"
                    data-id="{{ $acc['id'] }}"
                    data-name="{{ $acc['name'] }}"
                    data-email="{{ $acc['email'] }}"
                    data-role="{{ $acc['role'] }}"
                    data-department="{{ $acc['department'] ?? '' }}"
                    data-status="{{ $acc['status'] ?? 'Active' }}">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <form method="POST" action="{{ route('accounts.block') }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="id" value="{{ $acc['id'] }}">
                    <button type="submit" class="btn btn-light btn-sm" title="{{ ($acc['blocked'] ?? false) ? 'Unblock' : 'Block' }}">
                      @if(($acc['blocked'] ?? false))
                        <i class="bi bi-unlock"></i>
                      @else
                        <i class="bi bi-lock"></i>
                      @endif
                    </button>
                  </form>
                  <form method="POST" action="{{ route('accounts.delete') }}" class="d-inline" onsubmit="return confirm('Delete this account?');">
                    @csrf
                    <input type="hidden" name="id" value="{{ $acc['id'] }}">
                    <button type="submit" class="btn btn-light btn-sm text-danger" title="Delete">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="8" class="text-center text-muted">No accounts found.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- Create Account Modal --}}
<div class="modal fade" id="createAccountModal" tabindex="-1" aria-labelledby="createAccountModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createAccountModalLabel">Create Account</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('accounts.store') }}" id="createAccountForm">
        @csrf
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small text-muted">Account Type</label>
              <select name="account_type" id="account_type" class="form-select" required>
                <option value="system" selected>System (by role & department)</option>
                <option value="ess">ESS (employee self-service)</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small text-muted">Full Name</label>
              <input type="text" name="name" class="form-control" placeholder="Jane Doe" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small text-muted">Email</label>
              <input type="email" name="email" class="form-control" placeholder="jane@example.com" required>
            </div>
            <div class="col-md-6 system-only">
              <label class="form-label small text-muted">Role</label>
              <input type="text" name="role" id="role" class="form-control" placeholder="e.g., HR, Manager, Admin">
            </div>
            <div class="col-md-6 system-only">
              <label class="form-label small text-muted">Department</label>
              <input type="text" name="department" id="department" class="form-control" placeholder="Human Resources">
            </div>
            <div class="col-md-6">
              <select name="status" class="form-select">
                <option value="Active" selected>Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Blocked">Blocked</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small text-muted">Password (plain)</label>
              <input type="password" name="password" class="form-control" placeholder="Set password (optional)">
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

{{-- Edit Account Modal --}}
<div class="modal fade" id="editAccountModal" tabindex="-1" aria-labelledby="editAccountModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editAccountModalLabel">Edit Account</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('accounts.update') }}" id="editAccountForm">
        @csrf
        <input type="hidden" name="id" id="edit_id">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small text-muted">Full Name</label>
              <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small text-muted">Email</label>
              <input type="email" name="email" id="edit_email" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small text-muted">Role</label>
              <input type="text" name="role" id="edit_role" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label small text-muted">Department</label>
              <input type="text" name="department" id="edit_department" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label small text-muted">Status</label>
              <select name="status" id="edit_status" class="form-select">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Blocked">Blocked</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small text-muted">Password (plain)</label>
              <input type="password" name="password" id="edit_password" class="form-control" placeholder="Leave blank to keep current">
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
{{-- View Account Modal --}}
<div class="modal fade" id="viewAccountModal" tabindex="-1" aria-labelledby="viewAccountModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewAccountModalLabel">Account Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label small text-muted">ID</label>
            <input type="text" class="form-control" id="v_id" readonly>
          </div>
          <div class="col-md-6">
            <label class="form-label small text-muted">Created</label>
            <input type="text" class="form-control" id="v_created" readonly>
          </div>
          <div class="col-md-6">
            <label class="form-label small text-muted">Name</label>
            <input type="text" class="form-control" id="v_name" readonly>
          </div>
          <div class="col-md-6">
            <label class="form-label small text-muted">Email</label>
            <input type="text" class="form-control" id="v_email" readonly>
          </div>
          <div class="col-md-6">
            <label class="form-label small text-muted">Account Type</label>
            <input type="text" class="form-control" id="v_type" readonly>
          </div>
          <div class="col-md-6">
            <label class="form-label small text-muted">Status</label>
            <input type="text" class="form-control" id="v_status" readonly>
          </div>
          <div class="col-md-6">
            <label class="form-label small text-muted">Department</label>
            <input type="text" class="form-control" id="v_department" readonly>
          </div>
          <div class="col-md-6">
            <label class="form-label small text-muted">Role</label>
            <input type="text" class="form-control" id="v_role" readonly>
          </div>
          <div class="col-md-6">
            <label class="form-label small text-muted">Blocked</label>
            <input type="text" class="form-control" id="v_blocked" readonly>
          </div>
          <div class="col-md-6">
            <label class="form-label small text-muted">Password (plain, demo)</label>
            <input type="text" class="form-control" id="v_pass_plain" readonly>
          </div>
          <div class="col-12">
            <label class="form-label small text-muted">Password (hashed)</label>
            <input type="text" class="form-control" id="v_pass_hash" readonly>
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
  const typeSel = document.getElementById('account_type');
  const systemOnly = document.querySelectorAll('.system-only');
  const essHint = document.querySelector('.ess-hint');
  function toggleFields(){
    const isESS = typeSel.value === 'ess';
    systemOnly.forEach(el => el.style.display = isESS ? 'none' : 'block');
    if (essHint) essHint.style.display = isESS ? 'block' : 'none';
    // Required toggles
    document.getElementById('role')?.toggleAttribute('required', !isESS);
    document.getElementById('department')?.toggleAttribute('required', !isESS);
  }
  typeSel?.addEventListener('change', toggleFields);
  toggleFields();

  // Preselect account type based on the button clicked
  const modalEl = document.getElementById('createAccountModal');
  modalEl?.addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const t = btn ? btn.getAttribute('data-account-type') : null;
    if (t && typeSel) {
      typeSel.value = t;
      toggleFields();
    }
  });

  // Populate edit modal
  const editModal = document.getElementById('editAccountModal');
  editModal?.addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    if (!btn) return;
    document.getElementById('edit_id').value = btn.getAttribute('data-id');
    document.getElementById('edit_name').value = btn.getAttribute('data-name');
    document.getElementById('edit_email').value = btn.getAttribute('data-email');
    document.getElementById('edit_role').value = btn.getAttribute('data-role') || '';
    document.getElementById('edit_department').value = btn.getAttribute('data-department') || '';
    document.getElementById('edit_status').value = btn.getAttribute('data-status') || 'Active';
    document.getElementById('edit_password').value = '';
  });

  // Populate view modal
  const viewModal = document.getElementById('viewAccountModal');
  viewModal?.addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    if (!btn) return;
    document.getElementById('v_id').value = btn.getAttribute('data-id') || '';
    document.getElementById('v_created').value = btn.getAttribute('data-created') || '';
    document.getElementById('v_name').value = btn.getAttribute('data-name') || '';
    document.getElementById('v_email').value = btn.getAttribute('data-email') || '';
    document.getElementById('v_type').value = btn.getAttribute('data-type') || '';
    document.getElementById('v_status').value = btn.getAttribute('data-status') || '';
    document.getElementById('v_department').value = btn.getAttribute('data-department') || '';
    document.getElementById('v_role').value = btn.getAttribute('data-role') || '';
    document.getElementById('v_blocked').value = btn.getAttribute('data-blocked') || 'No';
    document.getElementById('v_pass_plain').value = btn.getAttribute('data-pass-plain') || '';
    document.getElementById('v_pass_hash').value = btn.getAttribute('data-pass-hash') || '';
  });
});
</script>
@endsection
