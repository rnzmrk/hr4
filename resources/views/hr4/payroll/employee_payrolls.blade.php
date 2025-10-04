@extends('layouts.app')

@section('page-title', 'Employee Payrolls')
@section('page-subtitle', 'Role/hour-based payrolls')
@section('breadcrumbs', 'Payroll / Employee Payrolls')

@section('content')
<div class="container-xxl">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex gap-2">
      <input type="text" class="form-control" placeholder="Search payrolls..." style="max-width: 260px;">
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPayrollModal">
      <i class="bi bi-plus-circle me-1"></i> Create Payroll
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
              <th>Period</th>
              <th>Role</th>
              <th>Hours</th>
              <th>Rate</th>
              <th>Gross</th>
              <th>Deductions</th>
              <th>Net</th>
              <th>Status</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($payrolls ?? []) as $p)
            <tr>
              <td class="fw-semibold">{{ $p['employee'] }}</td>
              <td>{{ $p['period'] }}</td>
              <td>{{ $p['role'] }}</td>
              <td>{{ number_format($p['hours'], 2) }}</td>
              <td>{{ $p['rate_type'] === 'hourly' ? ('₱'.number_format($p['rate'],2).'/hr') : ('₱'.number_format($p['rate'],2).'/mo') }}</td>
              <td>₱{{ number_format($p['gross'], 2) }}</td>
              <td>₱{{ number_format($p['deductions'], 2) }}</td>
              <td class="fw-semibold">₱{{ number_format($p['net'], 2) }}</td>
              <td>
                @if(($p['status'] ?? 'Draft') === 'Paid')
                  <span class="badge bg-success">Paid</span>
                @else
                  <span class="badge bg-secondary">{{ $p['status'] ?? 'Draft' }}</span>
                @endif
              </td>
              <td class="text-end">
                <div class="btn-group">
                  <button class="btn btn-light btn-sm" title="View Calculation"
                    data-bs-toggle="modal" data-bs-target="#calcModal"
                    data-employee="{{ $p['employee'] }}"
                    data-gross="{{ number_format($p['gross'], 2) }}"
                    data-sss="{{ number_format($p['breakdown']['sss'] ?? 0, 2) }}"
                    data-philhealth="{{ number_format($p['breakdown']['philhealth'] ?? 0, 2) }}"
                    data-pagibig="{{ number_format($p['breakdown']['pagibig'] ?? 0, 2) }}"
                    data-wtax="{{ number_format($p['breakdown']['withholding_tax'] ?? 0, 2) }}"
                    data-totalded="{{ number_format(($p['breakdown']['sss'] ?? 0)+($p['breakdown']['philhealth'] ?? 0)+($p['breakdown']['pagibig'] ?? 0)+($p['breakdown']['withholding_tax'] ?? 0), 2) }}">
                    <i class="bi bi-eye"></i>
                  </button>
                  <a href="{{ route('payroll.disbursements') }}" class="btn btn-light btn-sm" title="Disburse"><i class="bi bi-cash-coin"></i></a>
                  <a href="{{ route('payroll.payslips') }}" class="btn btn-light btn-sm" title="Payslips"><i class="bi bi-receipt"></i></a>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="10" class="text-center text-muted">No payrolls found.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Create Payroll Modal --}}
  <div class="modal fade" id="createPayrollModal" tabindex="-1" aria-labelledby="createPayrollModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createPayrollModalLabel">Create Payroll</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('payroll.employee.store') }}">
          @csrf
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small text-muted">Employee</label>
                <select name="employee_id" class="form-select" required>
                  <option value="" disabled {{ old('employee_id', request('employee_id')) ? '' : 'selected' }}>Select employee</option>
                  @foreach(($employees ?? []) as $e)
                    <option value="{{ $e->id }}" {{ (string)old('employee_id', request('employee_id')) === (string)$e->id || (request('employee') === $e->name) ? 'selected' : '' }}>
                      {{ $e->name }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label small text-muted">Period Start</label>
                <input type="date" name="period_start" class="form-control" value="{{ date('Y-m-01') }}" required>
              </div>
              <div class="col-md-3">
                <label class="form-label small text-muted">Period End</label>
                <input type="date" name="period_end" class="form-control" value="{{ date('Y-m-t') }}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Role</label>
                <input type="text" name="role" class="form-control" placeholder="e.g., HR Specialist" required>
              </div>
              <div class="col-md-3">
                <label class="form-label small text-muted">Rate Type</label>
                <select name="rate_type" class="form-select">
                  <option value="monthly" selected>Monthly</option>
                  <option value="hourly">Hourly</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label small text-muted">Rate</label>
                <input type="number" step="0.01" name="rate" class="form-control" placeholder="0.00" required>
              </div>
              <div class="col-md-3">
                <label class="form-label small text-muted">Hours Worked</label>
                <input type="number" step="0.01" name="hours_worked" class="form-control" placeholder="0">
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
  {{-- Calculation Modal --}}
  <div class="modal fade" id="calcModal" tabindex="-1" aria-labelledby="calcModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="calcModalLabel">Payroll Calculation Results</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2"><a id="calcEmp" class="fw-semibold"></a></div>
          <div class="row">
            <div class="col-md-6">
              <div class="d-flex justify-content-between"><span class="fw-semibold">Gross Salary</span><span id="calcGross" class="text-success fw-bold"></span></div>
            </div>
            <div class="col-md-6">
              <div class="fw-semibold mb-2">Deductions</div>
              <div class="d-flex justify-content-between"><span>SSS</span><span id="calcSSS"></span></div>
              <div class="d-flex justify-content-between"><span>PhilHealth</span><span id="calcPH"></span></div>
              <div class="d-flex justify-content-between"><span>Pag-IBIG</span><span id="calcPI"></span></div>
              <div class="d-flex justify-content-between"><span>Withholding Tax</span><span id="calcWT"></span></div>
              <hr>
              <div class="d-flex justify-content-between fw-semibold"><span>Total Deductions</span><span id="calcTot"></span></div>
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
      const m = document.getElementById('calcModal');
      m?.addEventListener('show.bs.modal', function(e){
        const b = e.relatedTarget;
        if (!b) return;
        m.querySelector('#calcEmp').textContent = b.getAttribute('data-employee') || '';
        m.querySelector('#calcGross').textContent = '₱' + (b.getAttribute('data-gross') || '0.00');
        m.querySelector('#calcSSS').textContent = '₱' + (b.getAttribute('data-sss') || '0.00');
        m.querySelector('#calcPH').textContent = '₱' + (b.getAttribute('data-philhealth') || '0.00');
        m.querySelector('#calcPI').textContent = '₱' + (b.getAttribute('data-pagibig') || '0.00');
        m.querySelector('#calcWT').textContent = '₱' + (b.getAttribute('data-wtax') || '0.00');
        m.querySelector('#calcTot').textContent = '₱' + (b.getAttribute('data-totalded') || '0.00');
      });
    });
  </script>
@endsection
