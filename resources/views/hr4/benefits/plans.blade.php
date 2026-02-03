@extends('layouts.app')

@section('page-title', 'Benefit Plans')
@section('page-subtitle', 'HMO/Benefits configuration')
@section('breadcrumbs', 'Benefits / Plans')

@php
    $oldEmployee = $employees->firstWhere('id', old('employee_id'));
@endphp

@section('content')
<div class="container-xxl">
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-center mb-4">
        <div class="input-group shadow-sm">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="planSearch" class="form-control border-start-0" placeholder="Search assigned plans...">
        </div>
        <button class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#createPlanModal">
            <i class="bi bi-plus-circle me-2"></i>Assign Benefit Plan
        </button>
    </div>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <div class="fw-semibold mb-1">Please fix the errors below:</div>
            <ul class="mb-0 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle" id="plansTable">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Benefit Types</th>
                            <th>Rate Type</th>
                            <th>Date Assigned</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($plans as $plan)
                            <tr data-search="{{ strtolower(($plan->employee->last_name ?? '') . ' ' . ($plan->employee->first_name ?? '') . ' ' . $plan->type . ' ' . $plan->rate_type) }}">
                                <td class="fw-semibold">
                                    {{ $plan->employee ? $plan->employee->last_name . ', ' . $plan->employee->first_name : $plan->name }}
                                    @if($plan->employee && $plan->employee->position)
                                        <div class="text-muted small">{{ $plan->employee->position }}</div>
                                    @endif
                                </td>
                                <td>
                                    @foreach(explode(',', $plan->type) as $label)
                                        @php $label = trim($label); @endphp
                                        @if($label !== '')
                                            <span class="badge bg-light text-dark border me-1 mb-1">{{ $label }}</span>
                                        @endif
                                    @endforeach
                                </td>
                                <td>{{ ucfirst($plan->rate_type) }}</td>
                                <td>{{ $plan->assigned_date ? \Carbon\Carbon::parse($plan->assigned_date)->format('M d, Y') : '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No benefit plans assigned yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Assign Benefit Modal --}}
    <div class="modal fade" id="createPlanModal" tabindex="-1" aria-labelledby="createPlanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createPlanModalLabel">Assign Benefit Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('benefits.plans.store') }}" id="planForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label">Employee<span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <input type="text" id="employeeSearch" class="form-control" placeholder="Search employee name" autocomplete="off" value="{{ $oldEmployee ? $oldEmployee->first_name . ' ' . $oldEmployee->last_name : '' }}" required>
                                    <input type="hidden" name="employee_id" id="employeeId" value="{{ old('employee_id') }}" required>
                                    <input type="hidden" name="name" id="planName">
                                    <div class="list-group position-absolute w-100 shadow-sm d-none" id="employeeResults" style="z-index:1056; max-height:240px; overflow:auto;"></div>
                                </div>
                                <div class="form-text">Start typing to search employees.</div>
                                @error('employee_id')
                                    <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Assignment Date</label>
                                <input type="date" class="form-control" name="assigned_date" value="{{ old('assigned_date', now()->toDateString()) }}">
                                @error('assigned_date')
                                    <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Benefit Types<span class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap gap-2 mb-2" id="typePresetButtons">
                                    @foreach(['SSS', 'Pag-IBIG', 'PhilHealth', 'HMO'] as $preset)
                                        <button type="button" class="btn btn-outline-primary btn-sm" data-type-option="{{ $preset }}">{{ $preset }}</button>
                                    @endforeach
                                </div>
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" id="customTypeInput" placeholder="Add custom type (letters only)">
                                    <button class="btn btn-outline-secondary" type="button" id="addCustomType">Add</button>
                                </div>
                                <div id="selectedTypes" class="d-flex flex-wrap gap-2"></div>
                                <div id="typeHiddenInputs"></div>
                                <div class="form-text">Click the presets or add your own labels (letters and spaces only).</div>
                                @error('type')
                                    <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Rate Type</label>
                                <select name="rate_type" class="form-select">
                                    <option value="monthly" {{ old('rate_type', 'monthly') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="fixed" {{ old('rate_type') === 'fixed' ? 'selected' : '' }}>Fixed</option>
                                </select>
                                @error('rate_type')
                                    <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 d-flex align-items-center">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="active" id="activePlan" {{ old('active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activePlan">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Assignment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const initialState = {
        employeeId: @json(old('employee_id')),
        employeeLabel: @json($oldEmployee ? $oldEmployee->first_name . ' ' . $oldEmployee->last_name : null),
        types: @json(old('type', [])),
        autoOpen: @json($errors->any())
    };

    const employees = @json($employees);
    const planSearchInput = document.getElementById('planSearch');
    const plansTable = document.querySelectorAll('#plansTable tbody tr');
    const employeeSearch = document.getElementById('employeeSearch');
    const employeeResults = document.getElementById('employeeResults');
    const employeeIdInput = document.getElementById('employeeId');
    const planNameInput = document.getElementById('planName');
    const selectedTypesEl = document.getElementById('selectedTypes');
    const typeHiddenInputs = document.getElementById('typeHiddenInputs');
    const typeButtons = document.querySelectorAll('[data-type-option]');
    const customTypeInput = document.getElementById('customTypeInput');
    const addCustomTypeBtn = document.getElementById('addCustomType');
    const selectedTypes = new Set();
    const planModalEl = document.getElementById('createPlanModal');

    if (initialState.autoOpen && planModalEl && window.bootstrap?.Modal) {
        const modalInstance = window.bootstrap.Modal.getOrCreateInstance(planModalEl);
        modalInstance.show();
    }

    if (initialState.employeeId && initialState.employeeLabel) {
        employeeIdInput.value = initialState.employeeId;
        employeeSearch.value = initialState.employeeLabel;
        planNameInput.value = initialState.employeeLabel;
    }

    if (Array.isArray(initialState.types)) {
        initialState.types.forEach(label => addType(label));
    }

    // Table search
    if (planSearchInput) {
        planSearchInput.addEventListener('input', () => {
            const query = planSearchInput.value.toLowerCase();
            plansTable.forEach(row => {
                const haystack = row.getAttribute('data-search') || '';
                row.style.display = haystack.includes(query) ? '' : 'none';
            });
        });
    }

    // Employee search
    function renderEmployeeResults(matches, term = '') {
        employeeResults.innerHTML = '';
        if (!matches.length) {
            const empty = document.createElement('div');
            empty.className = 'list-group-item text-muted';
            empty.textContent = term ? 'No employees found.' : 'Start typing to search employees.';
            employeeResults.appendChild(empty);
            employeeResults.classList.remove('d-none');
            return;
        }
        matches.forEach(emp => {
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'list-group-item list-group-item-action';
            item.innerHTML = `<div class="fw-semibold">${emp.last_name}, ${emp.first_name}</div><div class="small text-muted">${emp.position ?? ''}</div>`;
            item.addEventListener('click', () => {
                employeeIdInput.value = emp.id;
                employeeSearch.value = `${emp.first_name} ${emp.last_name}`;
                planNameInput.value = `${emp.first_name} ${emp.last_name}`;
                employeeResults.classList.add('d-none');
            });
            employeeResults.appendChild(item);
        });
        employeeResults.classList.remove('d-none');
    }

    function getEmployeeMatches(term) {
        if (!term) return [];
        const parts = term.split(' ').filter(Boolean);
        return employees.filter(emp => {
            const first = (emp.first_name || '').toLowerCase();
            const last = (emp.last_name || '').toLowerCase();
            return parts.every(part => first.includes(part) || last.includes(part));
        });
    }

    employeeSearch?.addEventListener('input', () => {
        const term = employeeSearch.value.toLowerCase();
        employeeIdInput.value = '';
        planNameInput.value = '';
        const matches = getEmployeeMatches(term);
        const exact = matches.find(emp => `${emp.first_name} ${emp.last_name}`.toLowerCase() === term);
        if (exact) {
            employeeIdInput.value = exact.id;
            planNameInput.value = `${exact.first_name} ${exact.last_name}`;
        }
        renderEmployeeResults(matches.slice(0, 6), term);
    });

    employeeSearch?.addEventListener('focus', () => {
        const term = employeeSearch.value.toLowerCase();
        renderEmployeeResults(getEmployeeMatches(term).slice(0, 6), term);
    });

    employeeSearch?.addEventListener('blur', () => {
        if (employeeIdInput.value) return;
        const term = employeeSearch.value.toLowerCase();
        if (!term) return;
        const matches = getEmployeeMatches(term);
        const exact = matches.find(emp => `${emp.first_name} ${emp.last_name}`.toLowerCase() === term);
        const match = exact || matches[0];
        if (match) {
            employeeIdInput.value = match.id;
            employeeSearch.value = `${match.first_name} ${match.last_name}`;
            planNameInput.value = `${match.first_name} ${match.last_name}`;
            employeeResults.classList.add('d-none');
        }
    });

    employeeSearch?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            const term = employeeSearch.value.toLowerCase();
            const matches = getEmployeeMatches(term);
            if (matches.length) {
                const match = matches[0];
                employeeIdInput.value = match.id;
                employeeSearch.value = `${match.first_name} ${match.last_name}`;
                planNameInput.value = `${match.first_name} ${match.last_name}`;
                employeeResults.classList.add('d-none');
            } else {
                renderEmployeeResults([], term);
            }
        }
    });

    document.addEventListener('click', (evt) => {
        if (!employeeResults.contains(evt.target) && evt.target !== employeeSearch) {
            employeeResults.classList.add('d-none');
        }
    });

    function addType(label) {
        const normalized = label.trim();
        if (!normalized || selectedTypes.has(normalized)) return;
        selectedTypes.add(normalized);

        const badge = document.createElement('span');
        badge.className = 'badge bg-primary-subtle text-primary border d-flex align-items-center gap-2';
        badge.innerHTML = `${normalized} <button type="button" class="btn-close btn-close-sm" aria-label="Remove"></button>`;
        badge.querySelector('button').addEventListener('click', () => {
            selectedTypes.delete(normalized);
            badge.remove();
            [...typeHiddenInputs.querySelectorAll('input')].forEach(input => {
                if (input.value === normalized) input.remove();
            });
        });
        selectedTypesEl.appendChild(badge);

        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'type[]';
        hidden.value = normalized;
        typeHiddenInputs.appendChild(hidden);
    }

    typeButtons.forEach(btn => btn.addEventListener('click', () => addType(btn.dataset.typeOption)));
    addCustomTypeBtn?.addEventListener('click', () => {
        const value = customTypeInput.value.trim();
        if (/^[A-Za-z ]+$/.test(value)) {
            addType(value);
            customTypeInput.value = '';
        } else if (value) {
            customTypeInput.classList.add('is-invalid');
            setTimeout(() => customTypeInput.classList.remove('is-invalid'), 1500);
        }
    });

    customTypeInput?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            addCustomTypeBtn?.click();
        }
    });
});
</script>
@endpush
@endsection
