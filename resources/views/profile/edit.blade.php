@extends('layouts.app')

@section('page-title', 'Edit Profile')
@section('page-subtitle', 'Update your personal information')
@section('breadcrumbs', 'Profile / Edit Profile')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-gear me-2"></i>Edit Profile
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="#" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <!-- Profile Picture -->
                            <div class="col-md-4">
                                <div class="text-center">
                                    <img src="{{ asset('images/default-avatar.png') }}" 
                                         id="profilePreview"
                                         alt="Profile Picture" 
                                         class="rounded-circle mb-3" 
                                         style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #007bff;">
                                    <div class="mb-3">
                                        <label for="profile_picture" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-camera me-2"></i>Change Photo
                                        </label>
                                        <input type="file" id="profile_picture" name="profile_picture" class="d-none" accept="image/*">
                                    </div>
                                    <small class="text-muted">JPG, PNG or GIF. Max size 2MB</small>
                                </div>
                            </div>
                            
                            <!-- Form Fields -->
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-primary mb-3">Personal Information</h6>
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full Name</label>
                                            <input type="text" class="form-control" 
                                                   id="name" name="name" value="John Doe" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control" 
                                                   id="email" name="email" value="john.doe@example.com" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" 
                                                   id="phone" name="phone" value="+63 912 345 6789">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h6 class="text-primary mb-3">Additional Information</h6>
                                        <div class="mb-3">
                                            <label for="address" class="form-label">Address</label>
                                            <textarea class="form-control" 
                                                      id="address" name="address" rows="3">123 Main Street, Manila, Philippines</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="birthdate" class="form-label">Birth Date</label>
                                            <input type="date" class="form-control" 
                                                   id="birthdate" name="birthdate" value="1990-01-01">
                                        </div>
                                        <div class="mb-3">
                                            <label for="gender" class="form-label">Gender</label>
                                            <select class="form-select" id="gender" name="gender">
                                                <option value="">Select Gender</option>
                                                <option value="male" selected>Male</option>
                                                <option value="female">Female</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <h6 class="text-primary mb-3">Change Password</h6>
                                        <p class="text-muted small">Leave blank if you don't want to change password</p>
                                        <div class="mb-3">
                                            <label for="current_password" class="form-label">Current Password</label>
                                            <input type="password" class="form-control" 
                                                   id="current_password" name="current_password">
                                        </div>
                                        <div class="mb-3">
                                            <label for="password" class="form-label">New Password</label>
                                            <input type="password" class="form-control" 
                                                   id="password" name="password">
                                        </div>
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                            <input type="password" class="form-control" 
                                                   id="password_confirmation" name="password_confirmation">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h6 class="text-primary mb-3">Preferences</h6>
                                        <div class="mb-3">
                                            <label for="timezone" class="form-label">Timezone</label>
                                            <select class="form-select" id="timezone" name="timezone">
                                                <option value="Asia/Manila" selected>Asia/Manila</option>
                                                <option value="UTC">UTC</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="language" class="form-label">Language</label>
                                            <select class="form-select" id="language" name="language">
                                                <option value="en" selected>English</option>
                                                <option value="es">Spanish</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="email_notifications" 
                                                       name="email_notifications" checked>
                                                <label class="form-check-label" for="email_notifications">
                                                    Email Notifications
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-end mt-4">
                                    <a href="{{ route('profile.index') }}" class="btn btn-secondary me-2">
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
document.getElementById('profile_picture').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profilePreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
