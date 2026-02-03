{{-- Sidebar --}}
<aside id="sidebar" @class('bg-white border-end p-3 shadow-sm')>

    {{-- Profile Section --}}
    <div @class('profile-section text-center')>
        <a href="{{ route('profile.index') }}" class="text-decoration-none">
            <img src="{{ asset('images/default-avatar.png') }}"
                alt="Admin Profile" class="profile-img mb-2">
            <h6 @class('fw-semibold mb-1 text-dark')>{{ Auth::user()->name ?? 'John Doe' }}</h6>
            <small @class('text-muted')>{{ Auth::user()->position ?? 'Administrator' }}</small>
        </a>
    </div>

    {{-- Navigation Menu --}}
    <ul @class('nav flex-column')>
        <li @class('nav-item')>
            <a href="{{ route('dashboard') }}"
                @class('nav-link text-dark ' . (request()->is('dashboard') ? 'active' : ''))>
                <i @class('bi bi-grid me-2')></i> Dashboard
            </a>
        </li>

        <li @class('nav-item mt-2')>
            <a href="#compMenuTop"
               role="button"
               aria-expanded="{{ request()->is('compensation*') ? 'true' : 'false' }}"
               aria-controls="compMenuTop"
               data-bs-toggle="collapse"
               @class('nav-link text-dark d-flex justify-content-between align-items-center')>
               <span><i @class('bi bi-currency-exchange me-2')></i> Compensation</span>
               <i @class('bi bi-chevron-down small')></i>
            </a>

            <div id="compMenuTop" @class('collapse ps-4 ' . (request()->is('compensation*') ? 'show' : ''))">
                <ul @class('nav flex-column')>
                    <li @class('nav-item')>
                        <a href="{{ route('compensation.index') }}"
                           @class('nav-link text-dark ' . (request()->is('compensation') ? 'active' : ''))">
                           <i @class('bi bi-sliders me-2')></i> Adjustments
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <li @class('nav-item mt-2')>
            <a href="#payrollMenuTop"
               role="button"
               aria-expanded="{{ request()->is('payroll*') ? 'true' : 'false' }}"
               aria-controls="payrollMenuTop"
               data-bs-toggle="collapse"
               @class('nav-link text-dark d-flex justify-content-between align-items-center')">
               <span><i @class('bi bi-cash-stack me-2')></i> Payroll</span>
               <i @class('bi bi-chevron-down small')></i>
            </a>

            <div id="payrollMenuTop" @class('collapse ps-4 ' . (request()->is('payroll*') ? 'show' : ''))">
                <ul @class('nav flex-column')>
                    <li @class('nav-item')>
                        <a href="{{ route('payroll.employee-details') }}"
                           @class('nav-link text-dark ' . (request()->is('payroll/employee-details') ? 'active' : ''))">
                           <i @class('bi bi-person-badge me-2')"></i> Employee Details
                        </a>
                    </li>
                    <li @class('nav-item')>
                        <a href="{{ route('payroll.attendance-record') }}"
                           @class('nav-link text-dark ' . (request()->is('payroll/attendance-record') ? 'active' : ''))">
                           <i @class('bi bi-calendar-check me-2')"></i> Attendance Record
                        </a>
                    </li>
                    <li @class('nav-item')>
                        <a href="{{ route('payroll.salary-computation') }}"
                           @class('nav-link text-dark ' . (request()->is('payroll/salary-computation') ? 'active' : ''))">
                           <i @class('bi bi-calculator me-2')"></i> Salary Computation
                        </a>
                    </li>
                    <li @class('nav-item')>
                        <a href="{{ route('payroll.approval') }}"
                           @class('nav-link text-dark ' . (request()->is('payroll/approval') ? 'active' : ''))">
                           <i @class('bi bi-check-circle me-2')"></i> Payroll Approval
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <li @class('nav-item mt-2')>
            <a href="#benefitsMenuTop"
               role="button"
               aria-expanded="{{ request()->is('benefits/*') ? 'true' : 'false' }}"
               aria-controls="benefitsMenuTop"
               data-bs-toggle="collapse"
               @class('nav-link text-dark d-flex justify-content-between align-items-center')>
               <span><i @class('bi bi-heart-pulse me-2')></i> Benefits</span>
               <i @class('bi bi-chevron-down small')></i>
            </a>

            <div id="benefitsMenuTop" @class('collapse ps-4 ' . (request()->is('benefits/*') ? 'show' : ''))>
                <ul @class('nav flex-column')>
                    <li @class('nav-item')>
                        <a href="{{ route('benefits.plans') }}"
                           @class('nav-link text-dark ' . (request()->is('benefits/plans') ? 'active' : ''))>
                           <i @class('bi bi-gear me-2')></i> Plans
                        </a>
                    </li>
                    <li @class('nav-item')>
                        <a href="{{ route('benefits.employee') }}"
                           @class('nav-link text-dark ' . (request()->is('benefits/employees') ? 'active' : ''))>
                           <i @class('bi bi-people me-2')></i> Employee Benefits
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <li @class('nav-item')>
            <a href="#coreHumanMenu"
            role="button"
            aria-expanded="{{ (request()->is('departments') || request()->is('employees') || request()->is('accounts')) ? 'true' : 'false' }}"
            aria-controls="coreHumanMenu"
            data-bs-toggle="collapse"
            @class('nav-link text-dark d-flex justify-content-between align-items-center')>
            <span><i @class('bi bi-people-fill me-2')></i> Core Human</span>
            <i @class('bi bi-chevron-down small')></i>
            </a>

            <div id="coreHumanMenu" @class('collapse ps-4 ' . ((request()->is('departments') || request()->is('employees') || request()->is('accounts') || request()->is('benefits/*')) ? 'show' : ''))>
                <ul @class('nav flex-column')>
                <li @class('nav-item')>
                <a href="{{ route('departments.index') }}"
                    @class('nav-link text-dark ' . (request()->is('departments') ? 'active' : ''))>
                    <i @class('bi bi-file-earmark-plus me-2')></i> Departments
                </a>
                <li @class('nav-item')>
                <a href="{{ route('employees.index') }}"
                    @class('nav-link text-dark ' . (request()->is('employees') ? 'active' : ''))>
                    <i @class('bi bi-person-badge me-2')></i> Employees
                </a>
                </li>
                
                <li @class('nav-item')>
                <a href="{{ route('accounts.index') }}"
                    @class('nav-link text-dark ' . (request()->is('accounts') ? 'active' : ''))>
                    <i @class('bi bi-people-fill me-2')></i> Accounts
                </a>
                </li>
                
            </ul>
            </div>
        </li>

        <li @class('nav-item mt-2')>
            <a href="{{ route('request.index') }}"
                @class('nav-link text-dark ' . (request()->is('request*') ? 'active' : ''))">
                <i @class('bi bi-box-seam me-2')></i> Item Request
            </a>
        </li>

    </ul>

</aside>
