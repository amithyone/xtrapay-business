<x-app-layout>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">Hidden Savings Management</h1>
                <p class="text-muted mb-0">Manage automatic savings collection from transactions</p>
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
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Total Savings</h6>
                                <h2 class="mb-0">₦{{ number_format($businesses->where('savings')->sum('savings.current_savings'), 2) }}</h2>
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
                                <h6 class="card-title mb-1">Active Savings</h6>
                                <h2 class="mb-0">{{ $businesses->where('savings.is_active', true)->count() }}</h2>
                            </div>
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Monthly Goal</h6>
                                <h2 class="mb-0">₦{{ number_format($businesses->where('savings')->sum('savings.monthly_goal'), 2) }}</h2>
                            </div>
                            <i class="fas fa-target fa-2x opacity-75"></i>
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
                                <h2 class="mb-0">{{ $businesses->where('savings')->sum('savings.transactions_today') }}</h2>
                            </div>
                            <i class="fas fa-calendar-day fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Businesses with Savings -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Business Savings ({{ $businesses->count() }})</h5>
            </div>
            <div class="card-body">
                @if($businesses->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Business</th>
                                    <th>Savings Status</th>
                                    <th>Monthly Goal</th>
                                    <th>Current Savings</th>
                                    <th>Progress</th>
                                    <th>Today's Collections</th>
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
                                                <small class="text-muted">{{ $business->user->name }} (ID: {{ $business->id }})</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($business->savings)
                                            @if($business->savings->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        @else
                                            <span class="badge bg-warning">Not Set</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($business->savings)
                                            ₦{{ number_format($business->savings->monthly_goal, 2) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($business->savings)
                                            <span class="fw-bold">₦{{ number_format($business->savings->current_savings, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($business->savings)
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-success" role="progressbar" 
                                                     style="width: {{ $business->savings->progress_percentage }}%"
                                                     aria-valuenow="{{ $business->savings->progress_percentage }}" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                    {{ number_format($business->savings->progress_percentage, 1) }}%
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($business->savings)
                                            <div class="text-center">
                                                <div class="fw-bold">{{ $business->savings->transactions_today }}</div>
                                                <small class="text-muted">/ {{ $business->savings->daily_transaction_limit }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @if($business->savings)
                                                <a href="{{ route('super-admin.savings.show', $business) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        onclick="editSavings({{ $business->id }}, '{{ $business->business_name }}')">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-warning" 
                                                        onclick="resetDailySavings({{ $business->id }})">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-success" 
                                                        onclick="initializeSavings({{ $business->id }}, '{{ $business->business_name }}')">
                                                    <i class="fas fa-plus"></i> Initialize
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-building fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No businesses found</h5>
                        <p class="text-muted">No business profiles are available for savings management.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Initialize Savings Modal -->
    <div class="modal fade" id="initializeSavingsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Initialize Savings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="initializeSavingsForm">
                    <div class="modal-body">
                        <input type="hidden" id="business_id" name="business_id">
                        <div class="mb-3">
                            <label for="monthly_goal" class="form-label">Monthly Goal (₦)</label>
                            <input type="number" class="form-control" id="monthly_goal" name="monthly_goal" 
                                   value="1600000" step="0.01" required>
                            <div class="form-text">Default: ₦1,600,000</div>
                        </div>
                        <div class="mb-3">
                            <label for="daily_transaction_limit" class="form-label">Daily Transaction Limit</label>
                            <input type="number" class="form-control" id="daily_transaction_limit" name="daily_transaction_limit" 
                                   value="5" min="1" max="10" required>
                            <div class="form-text">Maximum transactions per day for savings collection</div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                <label class="form-check-label" for="is_active">
                                    Active Savings Collection
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Initialize Savings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Savings Modal -->
    <div class="modal fade" id="editSavingsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Savings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editSavingsForm">
                    <div class="modal-body">
                        <input type="hidden" id="edit_business_id" name="business_id">
                        <div class="mb-3">
                            <label for="edit_monthly_goal" class="form-label">Monthly Goal (₦)</label>
                            <input type="number" class="form-control" id="edit_monthly_goal" name="monthly_goal" 
                                   step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_current_savings" class="form-label">Current Savings (₦)</label>
                            <input type="number" class="form-control" id="edit_current_savings" name="current_savings" 
                                   step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_daily_transaction_limit" class="form-label">Daily Transaction Limit</label>
                            <input type="number" class="form-control" id="edit_daily_transaction_limit" name="daily_transaction_limit" 
                                   min="1" max="10" required>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                                <label class="form-check-label" for="edit_is_active">
                                    Active Savings Collection
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="edit_notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Savings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function initializeSavings(businessId, businessName) {
            document.getElementById('business_id').value = businessId;
            document.getElementById('initializeSavingsModal').querySelector('.modal-title').textContent = `Initialize Savings - ${businessName}`;
            new bootstrap.Modal(document.getElementById('initializeSavingsModal')).show();
        }

        function editSavings(businessId, businessName) {
            // Load current savings data via AJAX
            fetch(`/super-admin/businesses/${businessId}/savings`)
                .then(response => response.json())
                .then(data => {
                    if (data.savings) {
                        document.getElementById('edit_business_id').value = businessId;
                        document.getElementById('edit_monthly_goal').value = data.savings.monthly_goal;
                        document.getElementById('edit_current_savings').value = data.savings.current_savings;
                        document.getElementById('edit_daily_transaction_limit').value = data.savings.daily_transaction_limit;
                        document.getElementById('edit_is_active').checked = data.savings.is_active;
                        document.getElementById('edit_notes').value = data.savings.notes || '';
                        
                        document.getElementById('editSavingsModal').querySelector('.modal-title').textContent = `Edit Savings - ${businessName}`;
                        new bootstrap.Modal(document.getElementById('editSavingsModal')).show();
                    }
                });
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

        // Form submissions
        document.getElementById('initializeSavingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const businessId = document.getElementById('business_id').value;
            const formData = new FormData(this);
            
            fetch(`/super-admin/businesses/${businessId}/savings/initialize`, {
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
                    alert('Error initializing savings: ' + data.message);
                }
            });
        });

        document.getElementById('editSavingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const businessId = document.getElementById('edit_business_id').value;
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