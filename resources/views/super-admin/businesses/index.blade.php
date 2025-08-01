<x-app-layout>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">Business Management</h1>
                <p class="text-muted mb-0">Manage business profiles and balance accounting</p>
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
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Total Businesses</h6>
                                <h2 class="mb-0">{{ number_format($businesses->total()) }}</h2>
                            </div>
                            <i class="fas fa-building fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Total Current Balance</h6>
                                <h2 class="mb-0">₦{{ number_format($businesses->sum('balance'), 2) }}</h2>
                            </div>
                            <i class="fas fa-credit-card fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Total Actual Balance</h6>
                                <h2 class="mb-0">₦{{ number_format($businesses->sum('actual_balance'), 2) }}</h2>
                            </div>
                            <i class="fas fa-wallet fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Total Withdrawable</h6>
                                <h2 class="mb-0">₦{{ number_format($businesses->sum('withdrawable_balance'), 2) }}</h2>
                            </div>
                            <i class="fas fa-money-bill-wave fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Additional Statistics Row -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card bg-secondary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Total Revenue</h6>
                                <h2 class="mb-0">₦{{ number_format($businesses->sum('total_revenue'), 2) }}</h2>
                            </div>
                            <i class="fas fa-chart-line fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Total Ledger Balance</h6>
                                <h2 class="mb-0">₦{{ number_format($businesses->sum('balance') - $businesses->sum('actual_balance'), 2) }}</h2>
                            </div>
                            <i class="fas fa-calculator fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Active Sites</h6>
                                <h2 class="mb-0">{{ $businesses->sum(function($business) { return $business->sites->where('is_active', true)->count(); }) }}</h2>
                            </div>
                            <i class="fas fa-globe fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('super-admin.businesses.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Search by business name or user">
                    </div>
                    <div class="col-md-3">
                        <label for="verification_status" class="form-label">Verification Status</label>
                        <select class="form-select" id="verification_status" name="verification_status">
                            <option value="">All Status</option>
                            <option value="verified" {{ request('verification_status') === 'verified' ? 'selected' : '' }}>Verified</option>
                            <option value="unverified" {{ request('verification_status') === 'unverified' ? 'selected' : '' }}>Unverified</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                        <a href="{{ route('super-admin.businesses.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Businesses Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Business Profiles ({{ $businesses->total() }})</h5>
            </div>
            <div class="card-body">
                @if($businesses->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Business</th>
                                    <th>Owner</th>
                                    <th>Balance Accounting</th>
                                    <th>Status</th>
                                    <th>Sites</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($businesses as $business)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($business->logo)
                                                <img src="{{ asset('storage/' . $business->logo) }}" class="rounded me-3" width="40" height="40">
                                            @else
                                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-building text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ $business->business_name }}</div>
                                                <small class="text-muted">{{ $business->business_type }} - {{ $business->industry }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-bold">{{ $business->user->name }}</div>
                                            <small class="text-muted">{{ $business->user->email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between">
                                                    <small class="text-muted">Current:</small>
                                                    <span class="fw-bold text-primary">₦{{ number_format($business->balance, 2) }}</span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between">
                                                    <small class="text-muted">Actual:</small>
                                                    <span class="fw-bold">₦{{ number_format($business->actual_balance, 2) }}</span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between">
                                                    <small class="text-muted">Withdrawable:</small>
                                                    <span class="fw-bold {{ $business->withdrawable_balance < $business->actual_balance ? 'text-warning' : 'text-success' }}">
                                                        ₦{{ number_format($business->withdrawable_balance, 2) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between">
                                                    <small class="text-muted">Revenue:</small>
                                                    <span class="fw-bold text-info">₦{{ number_format($business->total_revenue, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($business->is_verified)
                                            <span class="badge bg-success">Verified</span>
                                        @else
                                            <span class="badge bg-warning">Unverified</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <div class="fw-bold">{{ $business->sites->count() }}</div>
                                            <small class="text-muted">{{ $business->sites->where('is_active', true)->count() }} active</small>
                                        </div>
                                    </td>
                                    <td>{{ $business->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('super-admin.businesses.show', $business) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('super-admin.businesses.edit', $business) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="updateBalance({{ $business->id }}, '{{ $business->business_name }}', {{ $business->actual_balance }}, {{ $business->withdrawable_balance }})">
                                                <i class="fas fa-wallet"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm {{ $business->is_verified ? 'btn-outline-warning' : 'btn-outline-success' }}" 
                                                    onclick="toggleVerification({{ $business->id }}, '{{ $business->business_name }}', {{ $business->is_verified ? 'true' : 'false' }})">
                                                <i class="fas {{ $business->is_verified ? 'fa-times' : 'fa-check' }}"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $businesses->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-building fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Businesses Found</h4>
                        <p class="text-muted">No business profiles match your search criteria.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Update Balance Modal -->
    <div class="modal fade" id="updateBalanceModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Business Balance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="updateBalanceForm">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Balance Accounting:</strong> Actual balance is manually managed, withdrawable balance is what users can withdraw.
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="actual_balance" class="form-label">Actual Balance</label>
                                <div class="input-group">
                                    <span class="input-group-text">₦</span>
                                    <input type="number" class="form-control" id="actual_balance" name="actual_balance" 
                                           step="0.01" min="0" required>
                                </div>
                                <small class="text-muted">Manually managed business balance</small>
                            </div>
                            <div class="col-md-6">
                                <label for="withdrawable_balance" class="form-label">Withdrawable Balance</label>
                                <div class="input-group">
                                    <span class="input-group-text">₦</span>
                                    <input type="number" class="form-control" id="withdrawable_balance" name="withdrawable_balance" 
                                           step="0.01" min="0" required>
                                </div>
                                <small class="text-muted">Amount available for withdrawal</small>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <label for="balance_notes" class="form-label">Balance Notes</label>
                            <textarea class="form-control" id="balance_notes" name="balance_notes" rows="3" 
                                      placeholder="Add notes about this balance update (optional)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Balance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentBusinessId = null;

        function updateBalance(businessId, businessName, actualBalance, withdrawableBalance) {
            currentBusinessId = businessId;
            
            // Update modal title
            document.querySelector('#updateBalanceModal .modal-title').textContent = `Update Balance - ${businessName}`;
            
            // Set current values
            document.getElementById('actual_balance').value = actualBalance;
            document.getElementById('withdrawable_balance').value = withdrawableBalance;
            document.getElementById('balance_notes').value = '';
            
            new bootstrap.Modal(document.getElementById('updateBalanceModal')).show();
        }

        function resetDailySavings(businessId) {
            if (confirm('Are you sure you want to reset daily savings collection for this business?')) {
                fetch(`/super-admin/businesses/${businessId}/savings/reset`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error resetting daily savings: ' + data.message);
                    }
                });
            }
        }

        function toggleVerification(businessId, businessName, isVerified) {
            const action = isVerified ? 'unverify' : 'verify';
            if (confirm(`Are you sure you want to ${action} the business "${businessName}"?`)) {
                fetch(`/super-admin/businesses/${businessId}/toggle-verification`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
        }

        document.getElementById('updateBalanceForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(`/super-admin/businesses/${currentBusinessId}/balance`, {
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
                    bootstrap.Modal.getInstance(document.getElementById('updateBalanceModal')).hide();
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the balance.');
            });
        });
    </script>
</x-app-layout> 