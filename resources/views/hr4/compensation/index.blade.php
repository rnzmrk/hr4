@extends('layouts.app')

@section('page-title', 'Compensation Planning')
@section('page-subtitle', 'Salary adjustments applied to payroll rates')
@section('breadcrumbs', 'Compensation / Adjustments')

@section('content')
<div class="container-xxl">
  @if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('status') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  {{-- Search and Filters --}}
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('compensation.index') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
          <input type="text" name="search" class="form-control" placeholder="Search rewards..." value="{{ $search ?? '' }}">
        </div>
        <div class="col-md-2">
          <select name="status" class="form-select">
            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Status</option>
            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
          </select>
        </div>
        <div class="col-md-2">
          <select name="sort" class="form-select">
            <option value="given_date" {{ $sortBy === 'given_date' ? 'selected' : '' }}>Given Date</option>
            <option value="employee_name" {{ $sortBy === 'employee_name' ? 'selected' : '' }}>Employee Name</option>
            <option value="reward_id" {{ $sortBy === 'reward_id' ? 'selected' : '' }}>Reward ID</option>
          </select>
        </div>
        <div class="col-md-2">
          <select name="order" class="form-select">
            <option value="desc" {{ $sortOrder === 'desc' ? 'selected' : '' }}>Newest First</option>
            <option value="asc" {{ $sortOrder === 'asc' ? 'selected' : '' }}>Oldest First</option>
          </select>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary">
          <i class="bi bi-search"></i> Search
        </button>
        @if($search || $status !== 'all' || $sortBy !== 'given_date' || $sortOrder !== 'desc')
          <a href="{{ route('compensation.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-clockwise"></i> Clear
          </a>
        @endif
      </form>
    </div>
  </div>

  {{-- External Rewards Section --}}
  @if(!empty($rewards))
  <div class="card mb-4">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">
        <i class="bi bi-award me-2"></i>Rewards
      </h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Reward ID</th>
              <th>Reward Name</th>
              <th>Employee</th>
              <th>Email</th>
              <th>Position</th>
              <th>Department</th>
              <th>Given Date</th>
              <th>Given By</th>
              <th>Status</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($rewards as $reward)
            <tr>
              <td>{{ $reward['id'] ?? '—' }}</td>
              <td>{{ $reward['reward_id'] ?? '—' }}</td>
              <td>{{ $reward['reward']['name'] ?? '—' }}</td>
              <td>{{ $reward['employee_name'] ?? '—' }}</td>
              <td>{{ $reward['employee_email'] ?? '—' }}</td>
              <td>{{ $reward['employee_position'] ?? '—' }}</td>
              <td>{{ $reward['employee_department'] ?? '—' }}</td>
              <td>{{ \Carbon\Carbon::parse($reward['given_date'])->format('M d, Y') ?? '—' }}</td>
              <td>{{ $reward['given_by'] ?? '—' }}</td>
              <td>
                @if($reward['status'] === 'approved')
                  <span class="badge bg-success">Approved</span>
                @elseif($reward['status'] === 'pending')
                  <span class="badge bg-warning text-dark">Pending</span>
                @else
                  <span class="badge bg-secondary">Rejected</span>
                @endif
              </td>
              <td class="text-center">
                <form method="POST" action="{{ route('compensation.update', $reward['id']) }}" class="d-inline">
                  @csrf
                  @method('PATCH')
                  <div class="btn-group" role="group">
                    <select name="status" class="form-select form-select-sm">
                      <option value="pending" {{ $reward['status'] === 'pending' ? 'selected' : '' }}>Pending</option>
                      <option value="approved" {{ $reward['status'] === 'approved' ? 'selected' : '' }}>Approved</option>
                      <option value="rejected" {{ $reward['status'] === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                  </div>
                </form>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="12" class="text-center text-muted py-3">No external rewards found.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      
      {{-- Pagination --}}
      @if($pagination['total'] > $pagination['per_page'])
      <div class="d-flex justify-content-center mt-3">
        <nav>
          <ul class="pagination">
            {{-- Previous --}}
            @if($pagination['current_page'] > 1)
              <li class="page-item">
                <a class="page-link" href="?page={{ $pagination['current_page'] - 1 }}{{ $search ? '&search=' . urlencode($search) : '' }}{{ $status && $status !== 'all' ? '&status=' . $status : '' }}{{ $sortBy !== 'given_date' ? '&sort=' . $sortBy : '' }}{{ $sortOrder !== 'desc' ? '&order=' . $sortOrder : '' }}">Previous</a>
              </li>
            @endif
            
            {{-- Page Numbers --}}
            @for($i = 1; $i <= $pagination['last_page']; $i++)
              @if($i == $pagination['current_page'])
                <li class="page-item active">
                  <span class="page-link">{{ $i }}</span>
                </li>
              @else
                <li class="page-item">
                  <a class="page-link" href="?page={{ $i }}{{ $search ? '&search=' . urlencode($search) : '' }}{{ $status && $status !== 'all' ? '&status=' . $status : '' }}{{ $sortBy !== 'given_date' ? '&sort=' . $sortBy : '' }}{{ $sortOrder !== 'desc' ? '&order=' . $sortOrder : '' }}">{{ $i }}</a>
                </li>
              @endif
            @endfor
            
            {{-- Next --}}
            @if($pagination['current_page'] < $pagination['last_page'])
              <li class="page-item">
                <a class="page-link" href="?page={{ $pagination['current_page'] + 1 }}{{ $search ? '&search=' . urlencode($search) : '' }}{{ $status && $status !== 'all' ? '&status=' . $status : '' }}{{ $sortBy !== 'given_date' ? '&sort=' . $sortBy : '' }}{{ $sortOrder !== 'desc' ? '&order=' . $sortOrder : '' }}">Next</a>
              </li>
            @endif
          </ul>
        </nav>
      </div>
      @endif
    </div>
  </div>
  @else
  <div class="card mb-4">
    <div class="card-body">
      <div class="text-center py-5">
        <i class="bi bi-inbox fs-1 d-block text-muted mb-3"></i>
        <h5 class="text-muted">No External Rewards Data</h5>
        <p class="text-muted">Unable to fetch data from external API or no rewards available.</p>
      </div>
    </div>
  </div>
  @endif
</div>
@endsection
