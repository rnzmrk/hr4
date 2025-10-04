@extends('layouts.app')

@section('page-title', 'HR Analytics')
@section('page-subtitle', 'Snapshot across Core Human, Compensation, Benefits, Payroll')
@section('breadcrumbs', 'HR Analytics')

@section('content')
<div class="container-xxl">
  <div class="row g-3">
    @foreach(($cards ?? []) as $c)
    <div class="col-12 col-sm-6 col-lg-3">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">{{ $c['label'] }}</div>
          <div class="fs-4 fw-bold mt-1">{{ $c['value'] }}</div>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  <div class="card mt-4">
    <div class="card-body">
      <h6 class="fw-bold mb-3">Net Pay - Last 6 Months</h6>
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              @foreach(($trend ?? []) as $t)
                <th>{{ $t['label'] }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            <tr>
              @foreach(($trend ?? []) as $t)
                <td>₱{{ number_format($t['value'], 2) }}</td>
              @endforeach
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
