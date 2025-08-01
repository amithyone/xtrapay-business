<x-app-layout>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">Savings Details</h1>
                <p class="text-muted mb-0">{{ $business->business_name }} - Hidden Savings Management</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('super-admin.savings.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Savings
                </a>
            </div>
        </div>

        @if($savingsStats)
            <!-- Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title mb-1">Current Savings</h6>
                                    <h2 class="mb-0">₦{{ number_format($savingsStats['current_savings'], 2) }}</h2>
                                </div>
                                <i class="fas fa-piggy-bank fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title mb-1">Monthly Goal</h6>
                                    <h2 class="mb-0">₦{{ number_format($savingsStats['monthly_goal'], 2) }}</h2>
                                </div>
                                <i class="fas fa-target fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title mb-1">Daily Target</h6>
                                    <h2 class="mb-0">₦{{ number_format($savingsStats['daily_target'], 2) }}</h2>
                                </div>
                                <i class="fas fa-calendar-day fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title mb-1">Today's Collections</h6>
                                    <h2 class="mb-0">{{ $savingsStats['transactions_today'] }}/{{ $savingsStats['daily_limit'] }}</h2>
                                </div>
                                <i class="fas fa-chart-line fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Section -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Monthly Progress</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Progress: {{ number_format($savingsStats['progress_percentage'], 1) }}%</span>
                                    <span>₦{{ number_format($savingsStats['current_savings'], 2) }} / ₦{{ number_format($savingsStats['monthly_goal'], 2) }}</span>
                                </div>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $savingsStats['progress_percentage'] }}%"
                                         aria-valuenow="{{ $savingsStats['progress_percentage'] }}" 
                                         aria-valuemin="0" aria-valuemax="100">
                                        {{ number_format($savingsStats['progress_percentage'], 1) }}%
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-info">
                                <strong>Remaining:</strong> ₦{{ number_format($savingsStats['remaining_amount'], 2) }} to reach monthly goal
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Daily Progress</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Daily Progress: {{ number_format($savingsStats['daily_progress'], 1) }}%</span>
                                    <span>{{ $savingsStats['transactions_today'] }}/{{ $savingsStats['daily_limit'] }} transactions</span>
                                </div>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-info" role="progressbar" 
                                         style="width: {{ $savingsStats['daily_progress'] }}%"
                                         aria-valuenow="{{ $savingsStats['daily_progress'] }}" 
                                         aria-valuemin="0" aria-valuemax="100">
                                        {{ number_format($savingsStats['daily_progress'], 1) }}%
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-warning">
                                <strong>Daily Target:</strong> ₦{{ number_format($savingsStats['daily_target'], 2) }} per day
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Section -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Savings Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Current Settings</h6>
                            <ul class="list-unstyled">
                                <li><strong>Status:</strong> 
                                    @if($savingsStats['is_active'])
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </li>
                                <li><strong>Monthly Goal:</strong> ₦{{ number_format($savingsStats['monthly_goal'], 2) }}</li>
                                <li><strong>Daily Transaction Limit:</strong> {{ $savingsStats['daily_limit'] }}</li>
                                <li><strong>Last Collection Date:</strong> 
                                    {{ $savingsStats['last_collection_date'] ? \Carbon\Carbon::parse($savingsStats['last_collection_date'])->format('M d, Y') : 'Never' }}
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Actions</h6>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary" onclick="editSavings({{ $business->id }}, '{{ $business->business_name }}')">
                                    <i class="fas fa-edit me-2"></i>Edit Settings
                                </button>
                                <button type="button" class="btn btn-warning" onclick="resetDailySavings({{ $business->id }})">
                                    <i class="fas fa-redo me-2"></i>Reset Daily
                                </button>
                                @if($savingsStats['is_active'])
                                    <button type="button" class="btn btn-danger" onclick="toggleSavings({{ $business->id }}, false)">
                                        <i class="fas fa-pause me-2"></i>Pause
                                    </button>
                                @else
                                    <button type="button" class="btn btn-success" onclick="toggleSavings({{ $business->id }}, true)">
                                        <i class="fas fa-play me-2"></i>Activate
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Collection History -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Collection Information</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>How Savings Collection Works</h6>
                        <ul class="mb-0">
                            <li>Automatically collects 5-15% from successful transactions</li>
                            <li>Maximum {{ $savingsStats['daily_limit'] }} transactions per day</li>
                            <li>Daily target: ₦{{ number_format($savingsStats['daily_target'], 2) }}</li>
                            <li>Collection is random and hidden from users</li>
                            <li>Only applies to business ID 1</li>
                        </ul>
                    </div>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-piggy-bank fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Savings Configured</h5>
                    <p class="text-muted">This business doesn't have savings configured yet.</p>
                    <button type="button" class="btn btn-primary" onclick="initializeSavings({{ $business->id }}, '{{ $business->business_name }}')">
                        <i class="fas fa-plus me-2"></i>Initialize Savings
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Edit Savings Modal -->
    <div class="modal fade" id="editSavingsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Savings Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editSavingsForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="monthly_goal" class="form-label">Monthly Goal (₦)</label>
                            <input type="number" class="form-control" id="monthly_goal" name="monthly_goal" 
                                   value="{{ $savingsStats['monthly_goal'] ?? 1600000 }}" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="current_savings" class="form-label">Current Savings (₦)</label>
                            <input type="number" class="form-control" id="current_savings" name="current_savings" 
                                   value="{{ $savingsStats['current_savings'] ?? 0 }}" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="daily_transaction_limit" class="form-label">Daily Transaction Limit</label>
                            <input type="number" class="form-control" id="daily_transaction_limit" name="daily_transaction_limit" 
                                   value="{{ $savingsStats['daily_limit'] ?? 5 }}" min="1" max="10" required>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       {{ ($savingsStats['is_active'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Savings Collection
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editSavings(businessId, businessName) {
            new bootstrap.Modal(document.getElementById('editSavingsModal')).show();
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

        function toggleSavings(businessId, activate) {
            const action = activate ? 'activate' : 'deactivate';
            if (confirm(`Are you sure you want to ${action} savings collection for this business?`)) {
                // This would need a new endpoint to toggle savings status
                alert('Toggle functionality to be implemented');
            }
        }

        // Form submission
        document.getElementById('editSavingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const businessId = {{ $business->id }};
            const formData = new FormData(this);
            
            fetch(`/super-admin/businesses/${businessId}/savings/update`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error updating savings: ' + data.message);
                }
            });
        });
    </script>
</x-app-layout> 