@extends('layouts.app')

@section('page-title', 'My Profile')
@section('page-subtitle', 'View your profile information')
@section('breadcrumbs', 'Profile')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                       <i class="bi bi-person-circle me-2"></i>Profile Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center mb-4">
                                <img src="{{ asset('images/default-avatar.png') }}" 
                                     alt="Profile Picture" 
                                     class="rounded-circle img-fluid" 
                                     style="width: 150px; height: 150px; object-fit: cover; border: 4px solid var(--jetlouge-primary);">
                                <h4 class="mt-3 mb-1">John Doe</h4>
                                <p class="text-muted">Administrator</p>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Employee ID</label>
                                        <p class="form-control-plaintext">EMP001</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Email Address</label>
                                        <p class="form-control-plaintext">john.doe@example.com</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Department</label>
                                        <p class="form-control-plaintext">Human Resources</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Position</label>
                                        <p class="form-control-plaintext">Administrator</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Phone Number</label>
                                        <p class="form-control-plaintext">+63 912 345 6789</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Date Joined</label>
                                        <p class="form-control-plaintext">January 1, 2024</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Address</label>
                                        <p class="form-control-plaintext">123 Main Street, Manila, Philippines</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-end mt-4">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="/profile/edit" class="btn btn-primary">
                                        <i class="bi bi-pencil me-2"></i>Edit Profile
                                    </a>
                                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                        <i class="bi bi-key me-2"></i>Change Password
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-key me-2"></i>Change Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="#" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" required minlength="8">
                        <div class="form-text">Password must be at least 8 characters long.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
