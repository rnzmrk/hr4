@extends('layouts.app')

@section('page-title', 'Payslips')
@section('page-subtitle', 'Issued payslips for payrolls')
@section('breadcrumbs', 'Payroll / Payslips')

@section('content')
<div class="container-xxl">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex gap-2">
      <input type="text" class="form-control" placeholder="Search payslips..." style="max-width: 260px;">
    </div>

  {{-- View Payslip Modal --}}
  <div class="modal fade" id="viewPayslipModal" tabindex="-1" aria-labelledby="viewPayslipModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewPayslipModalLabel">Payslip</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2"><span class="fw-semibold">Payroll:</span> <span id="v_payroll"></span></div>
          <div class="mb-3"><span class="fw-semibold">Issued At:</span> <span id="v_issued"></span></div>
          <div class="fw-semibold mb-2">Summary</div>
          <div id="v_summary">
            <div class="text-muted">No content.</div>
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
      const vm = document.getElementById('viewPayslipModal');
      vm?.addEventListener('show.bs.modal', function(e){
        const b = e.relatedTarget; if (!b) return;
        vm.querySelector('#v_payroll').textContent = b.getAttribute('data-payroll') || '';
        vm.querySelector('#v_issued').textContent = b.getAttribute('data-issued') || '';
        const container = vm.querySelector('#v_summary');
        container.innerHTML = '';
        let content = {};
        try { content = JSON.parse(b.getAttribute('data-content') || '{}'); } catch (_) { content = {}; }
        const items = (content && content.items) ? content.items : [];
        if (!items.length) {
          container.innerHTML = '<div class="text-muted">No content.</div>';
        } else {
          const list = document.createElement('div');
          items.forEach(it => {
            const row = document.createElement('div');
            row.className = 'd-flex justify-content-between border-bottom py-1';
            row.innerHTML = `<span>${(it.label||'Item')}</span><span>₱${Number(it.amount||0).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2})}</span>`;
            list.appendChild(row);
          });
          container.appendChild(list);
        }
      });
    });
  </script>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPayslipModal">
      <i class="bi bi-receipt me-1"></i> Generate Payslip
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
              <th>Payroll</th>
              <th>Issued At</th>
              <th>Net Pay</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($payslips ?? []) as $s)
            <tr>
              <td class="fw-semibold">{{ $s['payroll'] }}</td>
              <td>{{ \Illuminate\Support\Carbon::parse($s['issued_at'])->format('M d, Y H:i') }}</td>
              <td>₱{{ number_format($s['net'], 2) }}</td>
              <td class="text-end">
                <div class="btn-group">
                  <button class="btn btn-light btn-sm" title="View" data-bs-toggle="modal" data-bs-target="#viewPayslipModal"
                    data-payroll="{{ $s['payroll'] }}"
                    data-issued="{{ \Illuminate\Support\Carbon::parse($s['issued_at'])->format('M d, Y H:i') }}"
                    data-content='@json($s['content'])'>
                    <i class="bi bi-eye"></i>
                  </button>
                  <button class="btn btn-light btn-sm" title="Download"><i class="bi bi-download"></i></button>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="4" class="text-center text-muted">No payslips found.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Generate Payslip Modal --}}
  <div class="modal fade" id="createPayslipModal" tabindex="-1" aria-labelledby="createPayslipModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createPayslipModalLabel">Generate Payslip</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('payroll.payslips.store') }}">
          @csrf
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small text-muted">Payroll (Employee & Period)</label>
                <select name="payroll_id" class="form-select" required>
                  @foreach(($payrollOptions ?? []) as $opt)
                    <option value="{{ $opt['id'] }}">{{ $opt['label'] }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Issued At</label>
                <input type="datetime-local" name="issued_at" class="form-control" value="{{ date('Y-m-d\TH:i') }}" required>
              </div>
              <div class="col-12">
                <label class="form-label small text-muted">Summary (JSON)</label>
                <textarea name="content" class="form-control" rows="4" placeholder='{"items":[{"label":"Basic Pay","amount":1000}]}'></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Generate</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
