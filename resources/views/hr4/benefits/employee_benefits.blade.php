@extends('layouts.app')

@section('page-title', 'Employee Benefits')
@section('page-subtitle', 'Assign HMO/Benefits to employees')
@section('breadcrumbs', 'Benefits / Employee Benefits')

@section('content')
<div class="container-xxl">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignBenefitModal">
      <i class="bi bi-plus-circle me-1"></i> Assign Benefit
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
              <th>Department</th>
              <th>Role</th>
              <th>Benefits</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($employees ?? []) as $e)
            <tr>
              <td class="fw-semibold">{{ $e['name'] }}</td>
              <td>{{ $e['department'] }}</td>
              <td>{{ $e['role'] }}</td>
              <td>
                @if(!empty($e['benefits']))
                  <ul class="mb-0">
                    @foreach($e['benefits'] as $b)
                      <li>{{ $b['plan'] }} ({{ $b['type'] }}) - Employee Share: ₱{{ number_format($b['employee_share'], 2) }}</li>
                    @endforeach
                  </ul>
                @else
                  <span class="text-muted">None</span>
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="4" class="text-center text-muted">No employees yet.</td>
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
          <h5 class="modal-title" id="assignBenefitModalLabel">Assign Benefit to Employee</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('benefits.employee.store') }}">
          @csrf
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small text-muted">Employee</label>
                <select name="employee_id" class="form-select" required>
                  @foreach(($employees ?? []) as $e)
                    <option value="{{ $e['id'] }}">{{ $e['name'] }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Benefit Plan</label>
                <select name="benefit_plan_id" class="form-select" required>
                  @foreach(($plans ?? []) as $p)
                    <option value="{{ $p->id }}">{{ $p->name }} (₱{{ number_format($p->employee_share,2) }})</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Employee Share Override (optional)</label>
                <input type="number" step="0.01" name="employee_share_override" class="form-control" placeholder="0.00">
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
