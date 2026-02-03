{{-- Navbar --}}
<nav @class('navbar navbar-expand-lg navbar-dark fixed-top') style="background-color: var(--jetlouge-primary);">
    <div @class('container-fluid')>
        <button id="desktop-toggle" title="Toggle Sidebar" @class('sidebar-toggle desktop-toggle me-3')>
            <i @class('bi bi-list fs-5')></i>
        </button>

        <a href="#" @class('navbar-brand fw-bold')>
            <i @class('bi bi-airplane me-2')></i>Jetlouge Travels
        </a>

        <div @class('d-flex align-items-center ms-auto')>
            <!-- Profile Dropdown -->
            <div class="dropdown">
                <button class="btn btn-link text-white text-decoration-none dropdown-toggle d-flex align-items-center" 
                        type="button" 
                        id="profileDropdown" 
                        data-bs-toggle="dropdown" 
                        aria-expanded="false">
                    <i class="bi bi-gear fs-5 me-1"></i>
                    <span class="d-none d-md-inline">Settings</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.index') }}">
                            <i class="bi bi-person me-2"></i>My Profile
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger border-0 bg-transparent text-start w-100">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            
            <!-- Mobile Menu Toggle -->
            <button id="menu-btn" title="Open Menu" @class('sidebar-toggle mobile-toggle ms-2')>
                <i @class('bi bi-list fs-5')></i>
            </button>
        </div>
    </div>
</nav>