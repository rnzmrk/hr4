@extends('layouts.app')

@section('page-title', 'Item Requests')
@section('page-subtitle', 'Track, review, and manage item requests for smooth inventory and delivery processing')
@section('breadcrumbs', 'Item Request')

@section('content')
<!-- CSRF Token Meta Tag -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div></div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createItemRequestModal">
                    <i class="bi bi-plus-circle me-2"></i>Create New Request
                </button>
            </div>

            <!-- Item Requests Table -->
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('request.index') }}" class="input-group" id="requestSearchForm">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" name="search" placeholder="Search requests..." value="{{ request('search') }}">
                            </form>
                        </div>
                        <div class="col-md-6 text-end">
                            <button class="btn btn-primary me-2" type="submit" form="requestSearchForm">
                                <i class="bi bi-search me-1"></i>Search
                            </button>
                            <a class="btn btn-outline-secondary" href="{{ route('request.index') }}">
                                <i class="bi bi-x-circle me-1"></i>Clear
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Request ID</th>
                                    <th>Item Name</th>
                                    <th>Requested Quantity</th>
                                    <th>Delivery Location</th>
                                    <th>Department</th>
                                    <th>Priority Level</th>
                                    <th>Status</th>
                                    <th>Request Date</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($requests->count() > 0)
                                    @foreach($requests as $request)
                                        <tr>
                                            <td>{{ $request['id'] ?? 'N/A' }}</td>
                                            <td>{{ $request['item_name'] ?? 'N/A' }}</td>
                                            <td>{{ $request['requested_quantity'] ?? 'N/A' }}</td>
                                            <td>{{ $request['storage_location'] ?? $request['delivery_location'] ?? 'N/A' }}</td>
                                            <td>{{ $request['department'] ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ 
                                                    $request['priority'] == 'HIGH' ? 'danger' : 
                                                    ($request['priority'] == 'MEDIUM' ? 'warning' : 'info') 
                                                }}">
                                                    {{ $request['priority'] ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ 
                                                    $request['status'] == 'approved' ? 'success' : 
                                                    ($request['status'] == 'pending' ? 'warning' : 'danger') 
                                                }}">
                                                    {{ $request['status'] ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>{{ 
                                                isset($request['created_at']) ? 
                                                \Carbon\Carbon::parse($request['created_at'])->format('M d, Y') : 
                                                (isset($request['request_date']) ? $request['request_date'] : 'N/A') 
                                            }}</td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="viewRequest({{ $request['id'] ?? 0 }})" title="View">
                                                    <i class="bi bi-eye"></i> View
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            No item requests found. Click "Create New Request" to add one.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $requests->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Item Request Modal -->
<div class="modal fade" id="createItemRequestModal" tabindex="-1" aria-labelledby="createItemRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createItemRequestModalLabel">Create Item Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="itemRequestForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="selectItem" class="form-label">Select Item</label>
                            <select class="form-select" id="selectItem" name="item_id" required onchange="updateAvailableQuantity()">
                                <option value="">Choose an item...</option>
                                @if(is_array($items) && count($items) > 0)
                                    @foreach($items as $item)
                                        <option value="{{ $item['id'] ?? '' }}" data-quantity="{{ $item['current_stock'] ?? 0 }}" data-name="{{ $item['name'] ?? 'Unknown Item' }}">
                                            {{ $item['name'] ?? 'Unknown Item' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="availableQuantity" class="form-label">Available Quantity</label>
                            <input type="number" class="form-control" id="availableQuantity" readonly value="">
                            <small class="text-muted">Existing stock for selected item</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="requestedQuantity" class="form-label">Requested Quantity</label>
                            <input type="number" class="form-control" id="requestedQuantity" name="requested_quantity" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="priorityLevel" class="form-label">Priority Level</label>
                            <select class="form-select" id="priorityLevel" name="priority_level" required>
                                <option value="">Select priority...</option>
                                <option value="low">LOW</option>
                                <option value="medium" selected>MEDIUM</option>
                                <option value="high">HIGH</option>
                                <option value="urgent">URGENT</option>
                            </select>
                        </div>
                    </div>

                    <h6 class="mb-3">Delivery Information</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="deliveryLocation" class="form-label">Delivery Location</label>
                            <input type="text" class="form-control" id="deliveryLocation" name="delivery_location" placeholder="Enter delivery location..." required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="departmentUnit" class="form-label">Department/Unit</label>
                            <input type="text" class="form-control" id="departmentUnit" name="department_unit" placeholder="e.g., Accounting, Operations, Project Team Alpha" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="deliveryInstructions" class="form-label">Delivery Instructions</label>
                        <textarea class="form-control" id="deliveryInstructions" name="delivery_instructions" rows="3" placeholder="Special delivery instructions..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="additionalNotes" class="form-label">Additional Notes</label>
                        <textarea class="form-control" id="additionalNotes" name="additional_notes" rows="3" placeholder="Any additional information..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitItemRequest()">Create Request</button>
            </div>
        </div>
    </div>
</div>

<!-- View Item Request Modal -->
<div class="modal fade" id="viewItemRequestModal" tabindex="-1" aria-labelledby="viewItemRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewItemRequestModalLabel">View Item Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="viewRequestContent">
                    <!-- Request details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<script>
// Items data passed from PHP controller
const itemsData = @json($items);

function showNotification(message, type = 'success') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 5000);
}

function viewRequest(requestId) {
    // Find the request data from the requests array
    const requests = @json($requests->items());
    const request = requests.find(r => r.id == requestId);
    
    if (request) {
        // Build the HTML content for the view modal
        const content = `
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary mb-3">Request Information</h6>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Request ID:</strong></td>
                            <td>${request.id || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td><strong>Request Number:</strong></td>
                            <td>${request.request_number || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td><strong>Item Name:</strong></td>
                            <td>${request.item_name || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td><strong>Requested Quantity:</strong></td>
                            <td>${request.requested_quantity || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td><strong>Priority Level:</strong></td>
                            <td>
                                <span class="badge bg-${request.priority === 'HIGH' ? 'danger' : (request.priority === 'MEDIUM' ? 'warning' : 'info')}">
                                    ${request.priority || 'N/A'}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <span class="badge bg-${request.status === 'approved' ? 'success' : (request.status === 'pending' ? 'warning' : 'danger')}">
                                    ${request.status || 'N/A'}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-primary mb-3">Delivery Information</h6>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Storage Location:</strong></td>
                            <td>${request.storage_location || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td><strong>Department:</strong></td>
                            <td>${request.department || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td><strong>Approved By ID:</strong></td>
                            <td>${request.approved_by_id || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td><strong>Approved At:</strong></td>
                            <td>${request.approved_at ? new Date(request.approved_at).toLocaleString() : 'N/A'}</td>
                        </tr>
                        <tr>
                            <td><strong>Request Date:</strong></td>
                            <td>${request.created_at ? new Date(request.created_at).toLocaleDateString() : 'N/A'}</td>
                        </tr>
                        <tr>
                            <td><strong>Last Updated:</strong></td>
                            <td>${request.updated_at ? new Date(request.updated_at).toLocaleString() : 'N/A'}</td>
                        </tr>
                        <tr>
                            <td><strong>Notes:</strong></td>
                            <td>${request.notes || 'N/A'}</td>
                        </tr>
                    </table>
                </div>
            </div>
        `;
        
        // Set the content and show the modal
        document.getElementById('viewRequestContent').innerHTML = content;
        
        // Show the modal using vanilla JavaScript
        const modal = document.getElementById('viewItemRequestModal');
        modal.classList.add('show');
        modal.style.display = 'block';
        document.body.classList.add('modal-open');
        document.body.style.overflow = 'hidden';
        
        // Create backdrop
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'viewModalBackdrop';
        document.body.appendChild(backdrop);
    } else {
        showNotification('Request not found', 'danger');
    }
}

function closeTabAfterView() {
    // Close the modal first
    closeViewModal();
    
    // Close the tab after a short delay
    setTimeout(() => {
        window.close();
        
        // If window.close() doesn't work (tab wasn't opened programmatically), redirect back
        setTimeout(() => {
            window.location.href = '/request';
        }, 1000);
    }, 500);
}

function closeViewModal() {
    const modal = document.getElementById('viewItemRequestModal');
    modal.classList.remove('show');
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    
    // Remove backdrop
    const backdrop = document.getElementById('viewModalBackdrop');
    if (backdrop) {
        backdrop.remove();
    }
}

// Close modal when clicking outside or on close button
document.addEventListener('click', function(event) {
    const modal = document.getElementById('viewItemRequestModal');
    const backdrop = document.getElementById('viewModalBackdrop');
    
    if (event.target === modal || event.target === backdrop || event.target.classList.contains('btn-close')) {
        closeViewModal();
    }
});

function updateAvailableQuantity() {
    const selectItem = document.getElementById('selectItem');
    const availableQuantity = document.getElementById('availableQuantity');
    
    if (selectItem.value) {
        const selectedOption = selectItem.options[selectItem.selectedIndex];
        const quantity = selectedOption.getAttribute('data-quantity') || 0;
        availableQuantity.value = quantity;
    } else {
        availableQuantity.value = '';
    }
}

function submitItemRequest() {
    const form = document.getElementById('itemRequestForm');
    const submitBtn = document.querySelector('button[onclick="submitItemRequest()"]');
    const csrfToken = document.querySelector('meta[name="csrf-token"]');

    if (form.checkValidity()) {
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
        }
        
        const formData = new FormData(form);
        const rawData = Object.fromEntries(formData.entries());

        // --- DATA TRANSFORMATION ---
        const selectedItem = itemsData.find(item => item.id == rawData.item_id);
        const itemName = selectedItem ? selectedItem.name : 'Unknown Item';

        const data = {
            item_name: itemName,
            requested_quantity: rawData.requested_quantity,
            priority: rawData.priority_level ? rawData.priority_level.toUpperCase() : 'MEDIUM',
            status: 'pending',
            // --- NEW FIELDS ADDED ---
            storage_location: rawData.delivery_location, // Map form field to API field
            notes: rawData.additional_notes, // Map form field to API field
            department: rawData.department_unit, // Add department field
        };
        
        console.log('Corrected data being sent:', data);
        
        fetch('/api/item-request', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : ''
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => { throw new Error(`HTTP ${response.status}: ${text}`); });
            }
            return response.json();
        })
        .then(result => {
            console.log('✅ DATA INSERTED SUCCESSFULLY!');
            console.log('Response from API:', result);
            
            // Close modal using vanilla JavaScript
            const modal = document.getElementById('createItemRequestModal');
            modal.classList.remove('show');
            modal.style.display = 'none';
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            
            form.reset();
            showNotification(`Item request #${result.id} created successfully!`, 'success');
        })
        .catch(error => {
            console.error('Error submitting request:', error);
            showNotification(`Error: ${error.message}`, 'danger');
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Create Request';
            }
        });
    } else {
        form.reportValidity();
    }
}
</script>
@endsection