{{-- Sidebar --}}
<aside id="sidebar" @class('bg-white border-end p-3 shadow-sm')>

    {{-- Profile Section --}}
    <div @class('profile-section text-center')>
    <img src=""
        alt="Admin Profile" class="profile-img mb-2">
        <h6 @class('fw-semibold mb-1')>John Doe</h6>
        <small @class('text-muted')>Travel Administrator</small>
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

            <div id="compMenuTop" @class('collapse ps-4 ' . (request()->is('compensation*') ? 'show' : ''))>
                <ul @class('nav flex-column')>
                    <li @class('nav-item')>
                        <a href="{{ route('compensation.index') }}"
                           @class('nav-link text-dark ' . (request()->is('compensation') ? 'active' : ''))>
                           <i @class('bi bi-sliders me-2')></i> Adjustments
                        </a>
                    </li>
                    <li @class('nav-item')>
                        <a href="{{ route('compensation.leaves') }}"
                           @class('nav-link text-dark ' . (request()->is('compensation/leaves') ? 'active' : ''))>
                           <i @class('bi bi-calendar2-check me-2')></i> Leaves
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


    </ul>

</aside>
