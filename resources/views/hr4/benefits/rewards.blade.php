@extends('layouts.app')

@section('page-title', 'Rewards')
@section('page-subtitle', 'Benefits rewards setup')
@section('breadcrumbs', 'Benefits / Rewards')

@section('content')
<div class="container-xxl">
    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Add Reward</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('benefits.rewards.store') }}" class="row g-3">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">Name<span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Type<span class="text-danger">*</span></label>
                    <select name="type" class="form-select" required>
                        <option value="" disabled {{ old('type') ? '' : 'selected' }}>Select type</option>
                        <option value="Monetary" {{ old('type') === 'Monetary' ? 'selected' : '' }}>Monetary</option>
                        <option value="Non-Monetary" {{ old('type') === 'Non-Monetary' ? 'selected' : '' }}>Non-Monetary</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Benefit Details</label>
                    <input type="text" name="benefits" class="form-control" value="{{ old('benefits') }}" placeholder="Enter benefit details">
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">Save Reward</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">Rewards List</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Benefit Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rewards as $reward)
                            <tr>
                                <td class="fw-semibold">{{ $reward->name }}</td>
                                <td>{{ $reward->description ?? '—' }}</td>
                                <td>{{ $reward->type }}</td>
                                <td>{{ $reward->benefits ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No rewards created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
