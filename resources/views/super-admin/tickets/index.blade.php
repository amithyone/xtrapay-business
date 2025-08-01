<x-app-layout>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">Support Ticket Management</h1>
                <p class="text-muted mb-0">Manage and respond to support tickets</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('super-admin.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Statistics -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Open Tickets</h6>
                                <h2 class="mb-0">{{ $tickets->where('status', 'open')->count() }}</h2>
                            </div>
                            <i class="fas fa-exclamation-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">In Progress</h6>
                                <h2 class="mb-0">{{ $tickets->where('status', 'in_progress')->count() }}</h2>
                            </div>
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Resolved</h6>
                                <h2 class="mb-0">{{ $tickets->where('status', 'resolved')->count() }}</h2>
                            </div>
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-secondary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Total Tickets</h6>
                                <h2 class="mb-0">{{ $tickets->total() }}</h2>
                            </div>
                            <i class="fas fa-ticket-alt fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('super-admin.tickets.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Search by subject or user">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="priority" class="form-label">Priority</label>
                        <select class="form-select" id="priority" name="priority">
                            <option value="">All Priority</option>
                            <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                        <a href="{{ route('super-admin.tickets.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tickets Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Support Tickets ({{ $tickets->total() }})</h5>
            </div>
            <div class="card-body">
                @if($tickets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Subject</th>
                                    <th>User</th>
                                    <th>Business</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Assigned To</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tickets as $ticket)
                                <tr>
                                    <td>
                                        <div class="fw-bold">#{{ $ticket->id }}</div>
                                        <small class="text-muted">{{ $ticket->category }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ Str::limit($ticket->subject, 50) }}</div>
                                        <small class="text-muted">{{ Str::limit($ticket->message, 80) }}</small>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-bold">{{ $ticket->user->name }}</div>
                                            <small class="text-muted">{{ $ticket->user->email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($ticket->businessProfile)
                                            <div class="fw-bold">{{ $ticket->businessProfile->business_name }}</div>
                                        @else
                                            <span class="text-muted">No business profile</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ticket->status === 'open')
                                            <span class="badge bg-danger">Open</span>
                                        @elseif($ticket->status === 'in_progress')
                                            <span class="badge bg-warning">In Progress</span>
                                        @elseif($ticket->status === 'resolved')
                                            <span class="badge bg-success">Resolved</span>
                                        @else
                                            <span class="badge bg-secondary">Closed</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ticket->priority === 'urgent')
                                            <span class="badge bg-danger">Urgent</span>
                                        @elseif($ticket->priority === 'high')
                                            <span class="badge bg-warning">High</span>
                                        @elseif($ticket->priority === 'medium')
                                            <span class="badge bg-info">Medium</span>
                                        @else
                                            <span class="badge bg-secondary">Low</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ticket->assignedTo)
                                            <div class="fw-bold">{{ $ticket->assignedTo->name }}</div>
                                            @if($ticket->assigned_at)
                                                <small class="text-muted">{{ $ticket->assigned_at->format('M d, H:i') }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">Unassigned</span>
                                        @endif
                                    </td>
                                    <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('super-admin.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($ticket->status !== 'resolved' && $ticket->status !== 'closed')
                                                <button type="button" class="btn btn-sm btn-outline-success" 
                                                        onclick="updateStatus({{ $ticket->id }}, 'resolved')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                            @if($ticket->status === 'open')
                                                <button type="button" class="btn btn-sm btn-outline-warning" 
                                                        onclick="updateStatus({{ $ticket->id }}, 'in_progress')">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $tickets->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Tickets Found</h4>
                        <p class="text-muted">No support tickets match your search criteria.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Ticket Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="updateStatusForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="resolved">Resolved</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="resolution_notes" class="form-label">Resolution Notes (Optional)</label>
                            <textarea class="form-control" id="resolution_notes" name="resolution_notes" rows="3" 
                                      placeholder="Add notes about the resolution"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentTicketId = null;

        function updateStatus(ticketId, status) {
            currentTicketId = ticketId;
            document.getElementById('status').value = status;
            document.getElementById('resolution_notes').value = '';
            new bootstrap.Modal(document.getElementById('updateStatusModal')).show();
        }

        document.getElementById('updateStatusForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(`/super-admin/tickets/${currentTicketId}/status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('updateStatusModal')).hide();
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the ticket status.');
            });
        });
    </script>
</x-app-layout> 