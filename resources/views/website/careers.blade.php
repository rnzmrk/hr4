@extends('layouts.website')

@section('content')
<div class="py-5">
    <div class="d-flex align-items-center justify-content-between flex-column mb-5">
        <h1 class="display-3 text-center fw-medium pt-5">Apply Now and Be Part of Our Team</h1>
        <p class="display-6 text-center">Explore thousands of opportunities</p>
    </div>
    
    <div class="container-fluid px-3 px-md-5 pt-5">
        <h3 class="mb-5 px-2 px-md-0">All Available Jobs</h3>
        <div class="row g-4">
            <!-- Tour Guide -->
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h4 class="h5 mb-3">Tour Guide</h4>
                        <div class="mb-3">
                            <span class="badge bg-success me-1 mb-1">Full-time</span>
                            <span class="badge bg-primary">On-site</span>
                        </div>
                        <p class="mb-auto">Lead exciting tours and create unforgettable experiences for travelers from around the world.</p>
                        <a href="{{ route('apply-now', ['id' => 1]) }}" class="btn btn-primary mt-3">
                            Apply Now <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Travel Agent -->
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h4 class="h5 mb-3">Travel Agent</h4>
                        <div class="mb-3">
                            <span class="badge bg-success me-1 mb-1">Full-time</span>
                            <span class="badge bg-primary">Hybrid</span>
                        </div>
                        <p class="mb-auto">Help clients plan their perfect vacations and manage travel bookings with exceptional customer service.</p>
                        <a href="{{ route('apply-now', ['id' => 2]) }}" class="btn btn-primary mt-3">
                            Apply Now <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Customer Service -->
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h4 class="h5 mb-3">Customer Service Representative</h4>
                        <div class="mb-3">
                            <span class="badge bg-warning me-1 mb-1">Part-time</span>
                            <span class="badge bg-primary">Remote</span>
                        </div>
                        <p class="mb-auto">Provide excellent support to customers and assist with their travel needs and inquiries.</p>
                        <a href="{{ route('apply-now', ['id' => 3]) }}" class="btn btn-primary mt-3">
                            Apply Now <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Operations Manager -->
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h4 class="h5 mb-3">Operations Manager</h4>
                        <div class="mb-3">
                            <span class="badge bg-success me-1 mb-1">Full-time</span>
                            <span class="badge bg-primary">On-site</span>
                        </div>
                        <p class="mb-auto">Oversee daily operations and ensure smooth delivery of travel services to our clients.</p>
                        <a href="{{ route('apply-now', ['id' => 4]) }}" class="btn btn-primary mt-3">
                            Apply Now <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Marketing Specialist -->
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h4 class="h5 mb-3">Marketing Specialist</h4>
                        <div class="mb-3">
                            <span class="badge bg-success me-1 mb-1">Full-time</span>
                            <span class="badge bg-primary">Hybrid</span>
                        </div>
                        <p class="mb-auto">Develop and execute marketing strategies to promote our travel services and attract new clients.</p>
                        <a href="{{ route('apply-now', ['id' => 5]) }}" class="btn btn-primary mt-3">
                            Apply Now <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Driver -->
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h4 class="h5 mb-3">Transportation Driver</h4>
                        <div class="mb-3">
                            <span class="badge bg-warning me-1 mb-1">Part-time</span>
                            <span class="badge bg-primary">On-site</span>
                        </div>
                        <p class="mb-auto">Provide safe and reliable transportation services for tourists and travel groups.</p>
                        <a href="{{ route('apply-now', ['id' => 6]) }}" class="btn btn-primary mt-3">
                            Apply Now <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection