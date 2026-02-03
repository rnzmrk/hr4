@extends('layouts.app')

@section('page-title', 'Employee Benefits')
@section('page-subtitle', 'Assign HMO/Benefits to employees')
@section('breadcrumbs', 'Benefits / Employee Benefits')

@section('content')
<div class="container-xxl">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
    <form method="GET" action="{{ route('benefits.employee') }}" class="d-flex gap-2 flex-grow-1">
      <div class="input-group shadow-sm">
        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
        <input type="text" name="search" class="form-control border-start-0" placeholder="Search employee name..." value="{{ request('search') }}">
      </div>
      <button type="submit" class="btn btn-outline-primary">Search</button>
      @if(request('search'))
        <a href="{{ route('benefits.employee') }}" class="btn btn-outline-secondary">Clear</a>
      @endif
    </form>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignBenefitModal">
      <i class="bi bi-plus-circle me-1"></i> Assign Reward
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
        <table class="table align-middle" id="employeeBenefitsTable">
          <thead>
            <tr>
              <th>Employee</th>
              <th>Reward</th>
              <th>Benefit Details</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($assignments ?? []) as $assignment)
            <tr data-search="{{ strtolower(($assignment->employee->last_name ?? '') . ' ' . ($assignment->employee->first_name ?? '') . ' ' . ($assignment->reward->name ?? '')) }}">
              <td class="fw-semibold">
                {{ $assignment->employee ? $assignment->employee->last_name . ', ' . $assignment->employee->first_name : '—' }}
              </td>
              <td>{{ $assignment->reward->name ?? '—' }}</td>
              <td>{{ $assignment->reward->benefits ?? '—' }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="3" class="text-center text-muted">No reward assignments yet.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Assign Benefit Modal --}}
  <div class="modal fade" id="assignBenefitModal" tabindex="-1" aria-labelledby="assignBenefitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="assignBenefitModalLabel">Assign Reward to Employee</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('benefits.employee.store') }}">
          @csrf
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Employee<span class="text-danger">*</span></label>
                <div class="position-relative">
                  <input type="text" id="employeeSearchInput" class="form-control" placeholder="Search employee name" autocomplete="off" required>
                  <input type="hidden" name="employee_id" id="employeeIdInput" required>
                  <div class="list-group position-absolute w-100 shadow-sm d-none" id="employeeSearchResults" style="z-index:1056; max-height:240px; overflow:auto;"></div>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Reward<span class="text-danger">*</span></label>
                <select name="reward_id" class="form-select" required>
                  <option value="" disabled selected>Select reward</option>
                  @foreach(($rewards ?? []) as $reward)
                    <option value="{{ $reward->id }}">{{ $reward->name }}{{ $reward->benefits ? ' - ' . $reward->benefits : '' }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Assign</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const employees = @json($employees ?? []);
  const searchInput = document.querySelector('input[name="search"]');
  const tableRows = document.querySelectorAll('#employeeBenefitsTable tbody tr');
  const employeeSearchInput = document.getElementById('employeeSearchInput');
  const employeeResults = document.getElementById('employeeSearchResults');
  const employeeIdInput = document.getElementById('employeeIdInput');

  if (searchInput) {
    searchInput.addEventListener('input', () => {
      const query = searchInput.value.toLowerCase();
      tableRows.forEach(row => {
        const haystack = row.getAttribute('data-search') || '';
        row.style.display = haystack.includes(query) ? '' : 'none';
      });
    });
  }

  function renderEmployeeResults(matches, term = '') {
    employeeResults.innerHTML = '';
    if (!matches.length) {
      const empty = document.createElement('div');
      empty.className = 'list-group-item text-muted';
      empty.textContent = term ? 'No employees found.' : 'Start typing to search employees.';
      employeeResults.appendChild(empty);
      employeeResults.classList.remove('d-none');
      return;
    }

    matches.forEach(emp => {
      const item = document.createElement('button');
      item.type = 'button';
      item.className = 'list-group-item list-group-item-action';
      item.textContent = `${emp.last_name}, ${emp.first_name}`;
      item.addEventListener('click', () => {
        employeeIdInput.value = emp.id;
        employeeSearchInput.value = `${emp.first_name} ${emp.last_name}`;
        employeeResults.classList.add('d-none');
      });
      employeeResults.appendChild(item);
    });

    employeeResults.classList.remove('d-none');
  }

  function getEmployeeMatches(term) {
    if (!term) return [];
    const parts = term.split(' ').filter(Boolean);
    return employees.filter(emp => {
      const first = (emp.first_name || '').toLowerCase();
      const last = (emp.last_name || '').toLowerCase();
      return parts.every(part => first.includes(part) || last.includes(part));
    });
  }

  employeeSearchInput?.addEventListener('input', () => {
    const term = employeeSearchInput.value.toLowerCase();
    employeeIdInput.value = '';
    renderEmployeeResults(getEmployeeMatches(term).slice(0, 6), term);
  });

  employeeSearchInput?.addEventListener('focus', () => {
    const term = employeeSearchInput.value.toLowerCase();
    renderEmployeeResults(getEmployeeMatches(term).slice(0, 6), term);
  });

  employeeSearchInput?.addEventListener('keydown', (event) => {
    if (event.key === 'Enter') {
      event.preventDefault();
      const term = employeeSearchInput.value.toLowerCase();
      const matches = getEmployeeMatches(term);
      if (matches.length) {
        const match = matches[0];
        employeeIdInput.value = match.id;
        employeeSearchInput.value = `${match.first_name} ${match.last_name}`;
        employeeResults.classList.add('d-none');
      }
    }
  });

  employeeSearchInput?.addEventListener('blur', () => {
    if (employeeIdInput.value) return;
    const term = employeeSearchInput.value.toLowerCase();
    if (!term) return;
    const matches = getEmployeeMatches(term);
    const exact = matches.find(emp => `${emp.first_name} ${emp.last_name}`.toLowerCase() === term);
    const match = exact || matches[0];
    if (match) {
      employeeIdInput.value = match.id;
      employeeSearchInput.value = `${match.first_name} ${match.last_name}`;
      employeeResults.classList.add('d-none');
    }
  });

  document.addEventListener('click', (event) => {
    if (!employeeResults.contains(event.target) && event.target !== employeeSearchInput) {
      employeeResults.classList.add('d-none');
    }
  });
});
</script>
@endpush
