<x-app-layout>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">Withdrawal Management</h1>
                <p class="text-muted mb-0">Review and approve withdrawal requests</p>
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
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Pending</h6>
                                <h2 class="mb-0">{{ $withdrawals->where('status', 'pending')->count() }}</h2>
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
                                <h6 class="card-title mb-1">Completed</h6>
                                <h2 class="mb-0">{{ $withdrawals->where('status', 'completed')->count() }}</h2>
                            </div>
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Failed</h6>
                                <h2 class="mb-0">{{ $withdrawals->where('status', 'failed')->count() }}</h2>
                            </div>
                            <i class="fas fa-times-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Pending Amount</h6>
                                <h2 class="mb-0">₦{{ number_format($withdrawals->where('status', 'pending')->sum('amount'), 2) }}</h2>
                            </div>
                            <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('super-admin.withdrawals.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Search by reference or business name">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                        <a href="{{ route('super-admin.withdrawals.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Withdrawals Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Withdrawal Requests ({{ $withdrawals->total() }})</h5>
            </div>
            <div class="card-body">
                @if($withdrawals->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Business</th>
                                    <th>Beneficiary</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($withdrawals as $withdrawal)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $withdrawal->reference }}</div>
                                        <small class="text-muted">{{ $withdrawal->created_at->format('M d, Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-bold">{{ $withdrawal->businessProfile->business_name }}</div>
                                            <small class="text-muted">{{ $withdrawal->businessProfile->user->name }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($withdrawal->beneficiary)
                                            <div>
                                                <div class="fw-bold">{{ $withdrawal->beneficiary->account_name }}</div>
                                                <small class="text-muted">{{ $withdrawal->beneficiary->bank_name }} - {{ $withdrawal->beneficiary->account_number }}</small>
                                            </div>
                                        @else
                                            <div>
                                                <div class="fw-bold">{{ $withdrawal->recipient_account_name }}</div>
                                                <small class="text-muted">{{ $withdrawal->recipient_bank }} - {{ $withdrawal->recipient_account_number }}</small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold">₦{{ number_format($withdrawal->amount, 2) }}</div>
                                        <small class="text-muted">{{ $withdrawal->currency }}</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $withdrawal->status_badge_class }}">{{ $withdrawal->status_text }}</span>
                                        @if($withdrawal->processed_at)
                                            <div class="small text-muted">{{ $withdrawal->processed_at->format('M d, H:i') }}</div>
                                        @endif
                                        @if($withdrawal->processed_by)
                                            <div class="small text-muted">by {{ $withdrawal->processed_by }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $withdrawal->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('super-admin.withdrawals.show', $withdrawal) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($withdrawal->status === 'pending')
                                                <button type="button" class="btn btn-sm btn-outline-success" 
                                                        onclick="approveWithdrawal({{ $withdrawal->id }})">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="rejectWithdrawal({{ $withdrawal->id }})">
                                                    <i class="fas fa-times"></i>
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
                        {{ $withdrawals->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Withdrawals Found</h4>
                        <p class="text-muted">No withdrawal requests match your search criteria.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Approve Withdrawal Modal -->
    <div class="modal fade" id="approveWithdrawalModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approve Withdrawal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="approveWithdrawalForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="processing_method" class="form-label">Processing Method</label>
                            <select class="form-select" id="processing_method" name="processing_method" required>
                                <option value="">Select Method</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="manual_transfer">Manual Transfer</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="admin_notes" class="form-label">Admin Notes (Optional)</label>
                            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" 
                                      placeholder="Add any notes about this approval"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>Approve Withdrawal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Withdrawal Modal -->
    <div class="modal fade" id="rejectWithdrawalModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Withdrawal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="rejectWithdrawalForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="reject_reason" class="form-label">Rejection Reason</label>
                            <textarea class="form-control" id="reject_reason" name="admin_notes" rows="3" 
                                      placeholder="Please provide a reason for rejection" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times me-2"></i>Reject Withdrawal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentWithdrawalId = null;

        function approveWithdrawal(withdrawalId) {
            currentWithdrawalId = withdrawalId;
            new bootstrap.Modal(document.getElementById('approveWithdrawalModal')).show();
        }

        function rejectWithdrawal(withdrawalId) {
            currentWithdrawalId = withdrawalId;
            new bootstrap.Modal(document.getElementById('rejectWithdrawalModal')).show();
        }

        document.getElementById('approveWithdrawalForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(`/super-admin/withdrawals/${currentWithdrawalId}/approve`, {
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
                    bootstrap.Modal.getInstance(document.getElementById('approveWithdrawalModal')).hide();
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while approving the withdrawal.');
            });
        });

        document.getElementById('rejectWithdrawalForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(`/super-admin/withdrawals/${currentWithdrawalId}/reject`, {
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
                    bootstrap.Modal.getInstance(document.getElementById('rejectWithdrawalModal')).hide();
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while rejecting the withdrawal.');
            });
        });
    </script>
</x-app-layout> 