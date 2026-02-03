@extends('layouts.app')

@section('page-title', 'My Profile')
@section('page-subtitle', 'View your personal information')
@section('breadcrumbs', 'Profile / My Profile')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person me-2"></i>My Profile
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Profile Information -->
                        <div class="col-md-4">
                            <div class="text-center">
                                <img src="{{ asset('images/default-avatar.png') }}" 
                                     alt="Profile Picture" 
                                     class="rounded-circle mb-3" 
                                     style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #007bff;">
                                <h4>John Doe</h4>
                                <p class="text-muted">john.doe@example.com</p>
                                <span class="badge bg-success">Administrator</span>
                            </div>
                        </div>
                        
                        <!-- Personal Details -->
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">Personal Information</h6>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Full Name</label>
                                        <p class="form-control-plaintext">John Doe</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Email Address</label>
                                        <p class="form-control-plaintext">john.doe@example.com</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Phone Number</label>
                                        <p class="form-control-plaintext">+63 912 345 6789</p>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">Employment Information</h6>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Employee ID</label>
                                        <p class="form-control-plaintext">EMP001</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Position</label>
                                        <p class="form-control-plaintext">Administrator</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Department</label>
                                        <p class="form-control-plaintext">Human Resources</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">Account Information</h6>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Username</label>
                                        <p class="form-control-plaintext">johndoe</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Date Joined</label>
                                        <p class="form-control-plaintext">January 1, 2024</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Last Login</label>
                                        <p class="form-control-plaintext">Today at 2:30 PM</p>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">System Access</h6>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Account Status</label>
                                        <p><span class="badge bg-success">Active</span></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Role</label>
                                        <p class="form-control-plaintext">Administrator</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Permissions</label>
                                        <p class="form-control-plaintext">Full Access</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-end mt-4">
                                <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                                    <i class="bi bi-pencil me-2"></i>Edit Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
