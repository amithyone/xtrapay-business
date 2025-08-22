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
                                <h6 class="card-title mb-1">Collection Amount</h6>
                                <h2 class="mb-0">₦{{ number_format($configValues['min_collection_amount'], 0) }}-{{ number_format($configValues['max_collection_amount'], 0) }}</h2>
                            </div>
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                        <small class="opacity-75">{{ ucfirst(str_replace('_', ' ', $configValues['collection_frequency'])) }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manual Trigger Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>Manual Savings Collection
                </h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <p class="mb-2">
                            <strong>How it works:</strong> The system automatically deducts ₦{{ number_format($configValues['min_collection_amount'], 0) }}-{{ number_format($configValues['max_collection_amount'], 0) }} from business balance {{ $configValues['collection_frequency'] === 'twice_daily' ? 'twice a day' : ($configValues['collection_frequency'] === 'once_daily' ? 'once a day' : 'hourly') }} (every {{ $configValues['collection_interval_hours'] }} hours) 
                            and adds it to the hidden savings account. You can manually trigger a collection if needed.
                        </p>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Only works for Business ID 1. Collection will only happen if sufficient balance is available (minimum ₦{{ number_format($configValues['min_balance_required'], 0) }}).
                        </small>
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-primary" onclick="triggerManualCollection()">
                            <i class="fas fa-bolt me-2"></i>Trigger Collection Now
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Savings Configuration Management -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cog me-2"></i>Savings Configuration
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <p class="mb-2">
                            <strong>Dynamic Configuration:</strong> All savings parameters can be adjusted here. Changes take effect immediately for new collections.
                        </p>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            These settings control how the automatic savings collection works across all businesses.
                        </small>
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-outline-primary" onclick="openConfigModal()">
                            <i class="fas fa-edit me-2"></i>Edit Configuration
                        </button>
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
                                    <th>Last Collection</th>
                                    <th>Next Collection</th>
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
                                        @if($business->savings && $business->savings->last_collection_date)
                                            <div class="text-center">
                                                <div class="fw-bold">{{ $business->savings->last_collection_date->format('M d, H:i') }}</div>
                                                <small class="text-muted">{{ $business->savings->last_collection_date->diffForHumans() }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">Never</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($business->savings && $business->savings->last_collection_date)
                                            <div class="text-center">
                                                <div class="fw-bold">{{ $business->savings->next_collection_date_time ? $business->savings->next_collection_date_time->format('M d, H:i') : 'N/A' }}</div>
                                                <small class="text-muted">{{ $business->savings->hours_until_next_collection }}h remaining</small>
                                            </div>
                                        @else
                                            <span class="text-success">Ready Now</span>
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
                                                                                @if($business->id == 1)
                                <button type="button" class="btn btn-sm btn-outline-warning" 
                                        onclick="triggerManualCollection()">
                                    <i class="fas fa-bolt"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="resetCollectionTime({{ $business->id }})">
                                    <i class="fas fa-clock"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-success" 
                                        onclick="triggerNextCollection({{ $business->id }})">
                                    <i class="fas fa-play"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info" 
                                        onclick="triggerCronCollection()" title="Trigger Cron Collection">
                                    <i class="fas fa-cog"></i>
                                </button>
                                @endif
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

    <!-- Configuration Modal -->
    <div class="modal fade" id="configModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Savings Configuration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="configForm">
                    <div class="modal-body">
                        <div class="row">
                            @foreach($savingsConfigs as $config)
                            <div class="col-md-6 mb-3">
                                <label for="config_{{ $config->key }}" class="form-label">
                                    {{ ucwords(str_replace('_', ' ', $config->key)) }}
                                    @if($config->type === 'float')
                                        <span class="text-muted">(₦)</span>
                                    @elseif($config->type === 'integer')
                                        <span class="text-muted">(Number)</span>
                                    @endif
                                </label>
                                @if($config->type === 'boolean')
                                    <select class="form-control" id="config_{{ $config->key }}" name="configs[{{ $config->key }}][value]">
                                        <option value="1" {{ $config->value == '1' ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ $config->value == '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                @elseif($config->key === 'collection_frequency')
                                    <select class="form-control" id="config_{{ $config->key }}" name="configs[{{ $config->key }}][value]">
                                        <option value="once_daily" {{ $config->value === 'once_daily' ? 'selected' : '' }}>Once Daily</option>
                                        <option value="twice_daily" {{ $config->value === 'twice_daily' ? 'selected' : '' }}>Twice Daily</option>
                                        <option value="hourly" {{ $config->value === 'hourly' ? 'selected' : '' }}>Hourly</option>
                                    </select>
                                @else
                                    <input type="{{ $config->type === 'float' ? 'number' : ($config->type === 'integer' ? 'number' : 'text') }}" 
                                           class="form-control" 
                                           id="config_{{ $config->key }}" 
                                           name="configs[{{ $config->key }}][value]"
                                           value="{{ $config->value }}"
                                           @if($config->type === 'float') step="0.01" @endif
                                           @if($config->type === 'integer') step="1" @endif>
                                @endif
                                <input type="hidden" name="configs[{{ $config->key }}][key]" value="{{ $config->key }}">
                                <input type="hidden" name="configs[{{ $config->key }}][type]" value="{{ $config->type }}">
                                <div class="form-text">{{ $config->description }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Configuration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function initializeSavings(businessId, businessName) {
            console.log('initializeSavings called with:', businessId, businessName);
            document.getElementById('business_id').value = businessId;
            document.getElementById('initializeSavingsModal').querySelector('.modal-title').textContent = `Initialize Savings - ${businessName}`;
            new bootstrap.Modal(document.getElementById('initializeSavingsModal')).show();
        }

        function editSavings(businessId, businessName) {
            // Load current savings data via AJAX
            fetch(`/super-admin/savings/${businessId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.savings) {
                        document.getElementById('edit_business_id').value = businessId;
                        document.getElementById('edit_monthly_goal').value = data.savings.monthly_goal;
                        document.getElementById('edit_current_savings').value = data.savings.current_savings;
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

        function triggerManualCollection() {
            if (confirm('Are you sure you want to manually trigger a savings collection for Business ID 1?')) {
                fetch('/super-admin/savings/trigger-manual-collection', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Manual savings collection triggered successfully!');
                        location.reload();
                    } else {
                        alert('Error triggering manual collection: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error triggering manual collection: ' + error);
                });
            }
        }

        function resetCollectionTime(businessId) {
            if (confirm('Are you sure you want to reset the collection time for this business? This will allow immediate collection.')) {
                fetch(`/super-admin/savings/reset-collection-time/${businessId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Collection time reset successfully!');
                        location.reload();
                    } else {
                        alert('Error resetting collection time: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error resetting collection time: ' + error);
                });
            }
        }

        function triggerNextCollection(businessId) {
            if (confirm('Are you sure you want to trigger the next collection for this business?')) {
                fetch(`/super-admin/savings/trigger-next-collection/${businessId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Next collection triggered successfully!');
                        location.reload();
                    } else {
                        alert('Error triggering next collection: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error triggering next collection: ' + error);
                });
            }
        }

        function triggerCronCollection() {
            if (confirm('Are you sure you want to trigger the cron collection? This will run the savings collection command.')) {
                fetch('/super-admin/cron/savings-collection', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Cron collection triggered successfully!\n\nOutput: ' + data.output);
                        location.reload();
                    } else {
                        alert('Error triggering cron collection: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error triggering cron collection: ' + error);
                });
            }
        }

        // Form submissions
        console.log('Setting up form event listeners...');
        const initializeForm = document.getElementById('initializeSavingsForm');
        console.log('Initialize form found:', initializeForm);
        
        if (initializeForm) {
            initializeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submitted!');
            
            const businessId = document.getElementById('business_id').value;
            const formData = new FormData(this);
            
            console.log('Business ID:', businessId);
            console.log('Form data:', Object.fromEntries(formData));
            
            fetch(`/super-admin/savings/initialize`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        console.log('Non-JSON response:', text);
                        throw new Error('Server returned non-JSON response. Check server logs.');
                    });
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    alert('Savings initialized successfully!');
                    location.reload();
                } else {
                    alert('Error initializing savings: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Error initializing savings: ' + error.message);
            });
        });
        } else {
            console.error('Initialize form not found!');
        }

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

        // Configuration form submission
        document.getElementById('configForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('/super-admin/savings/update-config', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Configuration updated successfully!');
                    location.reload();
                } else {
                    alert('Error updating configuration: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error updating configuration: ' + error);
            });
        });
    </script>
</x-app-layout> 