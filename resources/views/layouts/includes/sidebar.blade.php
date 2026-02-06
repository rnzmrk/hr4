{{-- Sidebar --}}
<aside id="sidebar" @class('bg-white border-end p-3 shadow-sm')>

    {{-- Profile Section --}}
    <div @class('profile-section text-center')>
        <div class="text-center">
        @php
            $loggedIn = Auth::user();
            $sidebarName = $loggedIn->name ?? 'John Doe';
            $sidebarPosition = 'Administrator';
            $sidebarAvatar = null;

            try {
                if ($loggedIn && $loggedIn->email) {
                    $response = \Illuminate\Support\Facades\Http::withoutVerifying()->get('https://hr4.jetlougetravels-ph.com/api/accounts');

                    if ($response->successful()) {
                        $payload = $response->json();

                        $systemAccounts = \Illuminate\Support\Arr::get($payload, 'system_accounts');
                        if (!is_array($systemAccounts)) {
                            $systemAccounts = \Illuminate\Support\Arr::get($payload, 'data.system_accounts', []);
                        }

                        $matched = collect($systemAccounts)->first(function ($account) use ($loggedIn) {
                            $employee = $account['employee'] ?? null;
                            $apiEmail = isset($employee['email']) ? trim($employee['email']) : '';

                            return ($account['account_type'] ?? null) === 'system'
                                && !($account['blocked'] ?? false)
                                && $employee
                                && strcasecmp($apiEmail, trim($loggedIn->email)) === 0;
                        });

                        if ($matched) {
                            $employee = $matched['employee'] ?? [];
                            $sidebarName = trim(($employee['first_name'] ?? '') . ' ' . ($employee['middle_name'] ?? '') . ' ' . ($employee['last_name'] ?? '') . ' ' . ($employee['suffix_name'] ?? '')) ?: $sidebarName;
                            $sidebarPosition = $employee['position'] ?? $sidebarPosition;

                            // Check for profile picture - ensure it's not empty and not null
                            if (!empty($matched['profile_picture']) && $matched['profile_picture'] !== '') {
                                $sidebarAvatar = 'https://hr4.jetlougetravels-ph.com/storage/profile_pictures/' . $matched['profile_picture'];
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Fallbacks already set above
            }

            $firstLetter = strtoupper(substr(trim($sidebarName), 0, 1));
        @endphp
        <a href="{{ route('profile.index') }}" class="text-decoration-none">
            @if($sidebarAvatar)
                <img src="{{ $sidebarAvatar }}"
                    alt="{{ $sidebarName }}" class="profile-img mb-2 rounded-circle">
            @else
                <div class="profile-img mb-2 rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                     style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-size: 24px; font-weight: bold;">
                    {{ $firstLetter }}
                </div>
            @endif
            <h6 @class('fw-semibold mb-1 text-dark')">{{ $sidebarName }}</h6>
            <small @class('text-muted')>{{ $sidebarPosition }}</small>
        </a>
        </div>
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
            <a href="#coreHumanMenu"
               role="button"
               aria-expanded="{{ (request()->is('departments') || request()->is('employees') || request()->is('accounts')) ? 'true' : 'false' }}"
               aria-controls="coreHumanMenu"
               data-bs-toggle="collapse"
               @class('nav-link text-dark d-flex justify-content-between align-items-center')>
               <span><i @class('bi bi-people-fill me-2')></i> Core Human</span>
               <i @class('bi bi-chevron-down small')></i>
            </a>

            <div id="coreHumanMenu" @class('collapse ps-4 ' . ((request()->is('departments') || request()->is('employees') || request()->is('accounts')) ? 'show' : ''))>
                <ul @class('nav flex-column')>
                    <li @class('nav-item')>
                        <a href="{{ route('departments.index') }}"
                           @class('nav-link text-dark ' . (request()->is('departments') ? 'active' : ''))">
                           <i @class('bi bi-file-earmark-plus me-2')></i> Departments
                        </a>
                    </li>
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
                    <li @class('nav-item')>
                        <a href="{{ route('benefits.rewards') }}"
                           @class('nav-link text-dark ' . (request()->is('benefits/rewards') ? 'active' : ''))>
                           <i @class('bi bi-award me-2')></i> Rewards
                        </a>
                    </li>
                </ul>
            </div>
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
                <ul @class('nav flex-column')">
                                        <li @class('nav-item')>
                        <a href="{{ route('compensation.leaves') }}"
                           @class('nav-link text-dark ' . (request()->is('compensation/leaves') ? 'active' : ''))">
                           <i @class('bi bi-calendar-x me-2')"></i> Leaves
                        </a>
                    </li>
                    <li @class('nav-item')>
                        <a href="{{ route('compensation.potential') }}"
                           @class('nav-link text-dark ' . (request()->is('compensation/potential') ? 'active' : ''))">
                           <i @class('bi bi-graph-up-arrow me-2')"></i> Potential
                        </a>
                    </li>
                    <li @class('nav-item')>
                        <a href="{{ route('salary.adjustment.index') }}"
                           @class('nav-link text-dark ' . (request()->is('salary-adjustment') ? 'active' : ''))">
                           <i @class('bi bi-cash-stack me-2')></i> Salary Adjustment
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
               @class('nav-link text-dark d-flex justify-content-between align-items-center')>
               <span><i @class('bi bi-cash-stack me-2')></i> Payroll</span>
               <i @class('bi bi-chevron-down small')></i>
            </a>

            <div id="payrollMenuTop" @class('collapse ps-4 ' . (request()->is('payroll*') ? 'show' : ''))>
                <ul @class('nav flex-column')>
                    <li @class('nav-item')>
                        <a href="{{ route('payroll.employee-details') }}"
                           @class('nav-link text-dark ' . (request()->is('payroll/employee-details') ? 'active' : ''))>
                           <i @class('bi bi-person-badge me-2')></i> Employee Details
                        </a>
                    </li>
                    <li @class('nav-item')>
                        <a href="{{ route('payroll.attendance-record') }}"
                           @class('nav-link text-dark ' . (request()->is('payroll/attendance-record') ? 'active' : ''))>
                           <i @class('bi bi-calendar-check me-2')"></i> Attendance Record
                        </a>
                    </li>
                    <li @class('nav-item')>
                        <a href="{{ route('payroll.salary-computation') }}"
                           @class('nav-link text-dark ' . (request()->is('payroll/salary-computation') ? 'active' : ''))>
                           <i @class('bi bi-calculator me-2')"></i> Salary Computation
                        </a>
                    </li>
                    <li @class('nav-item')>
                        <a href="{{ route('payroll.approval') }}"
                           @class('nav-link text-dark ' . (request()->is('payroll/approval') ? 'active' : ''))>
                           <i @class('bi bi-check-circle me-2')"></i> Budget Request
                        </a>
                    </li>
                    <li @class('nav-item')>
                        <a href="{{ route('payroll.payment-requests.index') }}"
                           @class('nav-link text-dark ' . (request()->is('payroll/payment-requests*') ? 'active' : ''))">
                           <i @class('bi bi-cash-stack me-2')"></i> Payment Request
                        </a>
                    </li>
                    <li @class('nav-item')>
                        <a href="{{ route('net-payout.index') }}"
                           @class('nav-link text-dark ' . (request()->is('net-payout*') ? 'active' : ''))">
                           <i @class('bi bi-wallet2 me-2')"></i> Net Payout
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

        <li @class('nav-item mt-2')>
            <a href="{{ route('vehicle.reservation') }}"
                @class('nav-link text-dark ' . (request()->is('vehicle-reservation') ? 'active' : ''))">
                <i @class('bi bi-truck me-2')></i> Vehicle Reservation
            </a>
        </li>

    </ul>

</aside>
