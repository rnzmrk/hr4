@extends('layouts.app')

@section('page-title', 'Edit Profile')
@section('page-subtitle', 'Update your profile information')
@section('breadcrumbs', 'Edit Profile')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil-square me-2"></i>Edit Profile Information
                    </h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @php
                        $employeeData = $employee ?? [];
                        $employeeName = $employeeData
                            ? trim(($employeeData['first_name'] ?? '') . ' ' . ($employeeData['middle_name'] ?? '') . ' ' . ($employeeData['last_name'] ?? '') . ' ' . ($employeeData['suffix_name'] ?? ''))
                            : ($user->name ?? '');
                        $departmentName = $employeeData['department']['name'] ?? null;
                        $position = $employeeData['position'] ?? null;
                        $rawProfilePic = $account['profile_picture'] ?? null;
                        $profilePic = $rawProfilePic
                            ? 'https://hr4.jetlougetravels-ph.com/storage/profile_pictures/' . $rawProfilePic
                            : asset('images/default-avatar.png');
                    @endphp

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        
                        <div class="row">
                            <!-- Profile Picture Column -->
                            <div class="col-md-4">
                                <div class="text-center mb-4">
                                    <div class="mb-3">
                                        <label for="profile_picture" class="form-label fw-bold">Profile Picture</label>
                                        <div class="position-relative d-inline-block">
                                            <img src="{{ $profilePic }}" 
                                                 alt="Profile Picture" 
                                                 class="rounded-circle border border-3 border-primary" 
                                                 style="width: 150px; height: 150px; object-fit: cover;"
                                                 id="profilePreview">
                                            <label for="profile_picture" class="position-absolute bottom-0 end-0 btn btn-primary btn-sm rounded-circle">
                                                <i class="bi bi-camera"></i>
                                            </label>
                                        </div>
                                        <input type="file" 
                                               class="form-control d-none" 
                                               id="profile_picture" 
                                               name="profile_picture" 
                                               accept="image/*"
                                               onchange="previewProfilePicture(event)">
                                        <div class="form-text">Click camera icon to change photo</div>
                                    </div>
                                </div>
                            </div>
                        
                            <!-- Form Fields Column -->
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label fw-bold">Full Name</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="name" 
                                                   name="name" 
                                                   value="{{ $employeeName }}" 
                                                   readonly>
                                            <div class="form-text">Cannot be modified</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label fw-bold">
                                                <i class="bi bi-pencil-square text-primary me-1"></i>Email Address
                                                <span class="badge bg-success ms-2">Editable</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-envelope"></i>
                                                </span>
                                                <input type="email" 
                                                       class="form-control border-success @error('email') is-invalid @enderror" 
                                                       id="email" 
                                                       name="email" 
                                                       value="{{ old('email', $employeeData['email'] ?? $user->email) }}" 
                                                       required>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-text">You can update your email address</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="employee_id" class="form-label fw-bold">Employee ID</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="employee_id" 
                                                   name="employee_id" 
                                                   value="{{ $employeeData['id'] ?? '' }}" 
                                                   readonly>
                                            <div class="form-text">Cannot be modified</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="department" class="form-label fw-bold">Department</label>
                                            <input type="text"
                                                   class="form-control"
                                                   id="department"
                                                   value="{{ $departmentName ?: 'N/A' }}"
                                                   readonly>
                                            <div class="form-text">Cannot be modified</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="position" class="form-label fw-bold">Position</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="position" 
                                                   name="position" 
                                                   value="{{ $position ?: 'N/A' }}" 
                                                   readonly>
                                            <div class="form-text">Cannot be modified</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label fw-bold">
                                                <i class="bi bi-pencil-square text-primary me-1"></i>Phone Number
                                                <span class="badge bg-success ms-2">Editable</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-telephone"></i>
                                                </span>
                                                <input type="tel" 
                                                       class="form-control border-success" 
                                                       id="phone" 
                                                       name="phone" 
                                                       value="{{ old('phone', $employeeData['phone'] ?? '') }}">
                                                @error('phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-text">You can update your phone number</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="address" class="form-label fw-bold">Address</label>
                                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                                      id="address" 
                                                      name="address" 
                                                      rows="3">{{ old('address', $employeeData['address'] ?? '') }}</textarea>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label fw-bold">New Password (optional)</label>
                                            <input type="password" 
                                                   class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" 
                                                   name="password" 
                                                   autocomplete="new-password">
                                            <div class="form-text">Leave blank to keep your current password.</div>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label fw-bold">Confirm New Password</label>
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="password_confirmation" 
                                                   name="password_confirmation" 
                                                   autocomplete="new-password">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-end mt-4">
                                    <a href="/profile" class="btn btn-secondary me-2">
                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Save Changes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('profile_picture').addEventListener('change', function previewProfilePicture(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('profilePreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}
</script>

@endsection
