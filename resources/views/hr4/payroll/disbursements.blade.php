@extends('layouts.app')

@section('page-title', 'Disbursements')
@section('page-subtitle', 'Payouts linked to payrolls')
@section('breadcrumbs', 'Payroll / Disbursements')

@section('content')
<div class="container-xxl">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex gap-2">
      <input type="text" class="form-control" placeholder="Search disbursements..." style="max-width: 260px;">
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDisModal">
      <i class="bi bi-cash-coin me-1"></i> New Disbursement
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
              <th>Method</th>
              <th>Reference</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Paid At</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($disbursements ?? []) as $d)
            <tr>
              <td class="fw-semibold">{{ $d['payroll'] }}</td>
              <td>{{ ucfirst($d['method']) }}</td>
              <td>{{ $d['reference'] ?? '—' }}</td>
              <td>₱{{ number_format($d['amount'], 2) }}</td>
              <td>
                @if(($d['status'] ?? 'pending') === 'paid')
                  <span class="badge bg-success">Paid</span>
                @else
                  <span class="badge bg-secondary">{{ ucfirst($d['status'] ?? 'pending') }}</span>
                @endif
              </td>
              <td>{{ $d['paid_at'] ? \Illuminate\Support\Carbon::parse($d['paid_at'])->format('M d, Y H:i') : '—' }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="text-center text-muted">No disbursements found.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Create Disbursement Modal --}}
  <div class="modal fade" id="createDisModal" tabindex="-1" aria-labelledby="createDisModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createDisModalLabel">New Disbursement</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('payroll.disbursements.store') }}">
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
              <div class="col-md-3">
                <label class="form-label small text-muted">Method</label>
                <select name="method" class="form-select">
                  <option value="bank">Bank</option>
                  <option value="cash">Cash</option>
                  <option value="mobile">Mobile</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label small text-muted">Amount</label>
                <input type="number" step="0.01" name="amount" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Reference</label>
                <input type="text" name="reference" class="form-control" placeholder="Txn ref (optional)">
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
@endsection
