@extends('layouts.guest')

@section('content')
<div class="login-container">
    <div class="row g-0">
        <!-- Left Side - Welcome -->
        <div class="col-lg-6 login-left">
            <div class="floating-shapes">
                <div class="shape"></div>
                <div class="shape"></div>
                <div class="shape"></div>
            </div>

            <div class="logo-container">
                <div class="logo-box">
                    <img src="{{ asset('images/logo.png') }}" alt="Jetlouge Travels">
                </div>
                <h1 class="brand-text">Jetlouge Travels</h1>
                <p class="brand-subtitle">Employee Portal</p>
            </div>

            <h2 class="welcome-text">Welcome Back!</h2>
            <p class="welcome-subtitle">
                Access your HR dashboard to manage employee records,
                streamline recruitment, and support organizational growth.
            </p>

            <ul class="feature-list">
                <li>
                    <i class="bi bi-check"></i>
                    <span>Manage employee profiles & roles</span>
                </li>
                <li>
                    <i class="bi bi-check"></i>
                    <span>Track job applications & interview schedules</span>
                </li>
                <li>
                    <i class="bi bi-check"></i>
                    <span>Monitor performance reviews & feedback</span>
                </li>
                <li>
                    <i class="bi bi-check"></i>
                    <span>Secure access to HR tools & workflows</span>
                </li>
            </ul>
        </div>

        <!-- Right Side - Login Form -->
        <div class="col-lg-6 login-right">
            <h3 class="text-center mb-4" style="color: var(--jetlouge-primary); font-weight: 700;">
                Sign In to Your Account
            </h3>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session()->has('otp_code'))
                {{-- OTP verification step --}}
                @if (session('status'))
                    <div class="alert alert-info">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="otpForm">
                    @csrf

                    <input type="hidden" name="email" value="{{ old('email', session('otp_email')) }}">

                    <div class="mb-3">
                        <label for="otp" class="form-label">Verification Code</label>
                        <input type="text"
                               class="form-control @error('otp') is-invalid @enderror"
                               id="otp"
                               name="otp"
                               placeholder="Enter 6-digit code"
                               maxlength="6"
                               pattern="\d*"
                               required
                               autofocus>
                        @error('otp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            We sent a 6-digit code to <strong>{{ session('otp_email') }}</strong>. Enter it here to complete sign in.
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-login">
                            <i class="bi bi-shield-lock me-2"></i>
                            Verify & Sign In
                        </button>
                    </div>
                </form>
            @else
                {{-- Email/password step --}}
                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="Enter your email"
                                   required
                                   autofocus>
                        </div>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Enter your password"
                                   required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye" id="passwordIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-login" id="loginBtn">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            <span id="btnText">Sign In</span>
                        </button>
                    </div>

                    <div class="text-center">
                        <a href="#" class="btn-forgot">Forgot Password?</a>
                    </div>
                </form>
            @endif

            <div class="text-center mt-4">
                <hr>
                <p class="mb-2">New to Jetlouge Travels?</p>
                <a href="#" class="btn btn-outline-primary">Request Account Access</a>
            </div>
        </div>
    </div>
</div>
@endsection
