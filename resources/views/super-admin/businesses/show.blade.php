<x-app-layout>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">{{ $business->business_name }}</h1>
                <p class="text-muted mb-0">Business Profile Details</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('super-admin.businesses.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Businesses
                </a>
                <button type="button" class="btn btn-primary" onclick="updateBalance()">
                    <i class="fas fa-edit me-2"></i>Update Balance
                </button>
            </div>
        </div>

        <div class="row g-4">
            <!-- Business Information -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Business Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-3">
                                    @if($business->logo)
                                        <img src="{{ asset('storage/' . $business->logo) }}" class="rounded me-3" width="60" height="60">
                                    @else
                                        <div class="bg-secondary rounded d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                            <i class="fas fa-building text-white fa-2x"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h4 class="mb-1">{{ $business->business_name }}</h4>
                                        <p class="text-muted mb-0">{{ $business->business_type }} - {{ $business->industry }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Registration Number</label>
                                <p class="mb-0">{{ $business->registration_number }}</p>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tax ID</label>
                                <p class="mb-0">{{ $business->tax_identification_number }}</p>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Address</label>
                                <p class="mb-0">{{ $business->address }}, {{ $business->city }}, {{ $business->state }}, {{ $business->country }}</p>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Phone</label>
                                <p class="mb-0">{{ $business->phone }}</p>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email</label>
                                <p class="mb-0">{{ $business->email }}</p>
                            </div>

                            @if($business->website)
                            <div class="col-12">
                                <label class="form-label fw-bold">Website</label>
                                <p class="mb-0"><a href="{{ $business->website }}" target="_blank">{{ $business->website }}</a></p>
                            </div>
                            @endif

                            <div class="col-12">
                                <label class="form-label fw-bold">Verification Status</label>
                                <p class="mb-0">
                                    @if($business->is_verified)
                                        <span class="badge bg-success">Verified</span>
                                    @else
                                        <span class="badge bg-warning">Unverified</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Balance Accounting -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Balance Accounting</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                    <div>
                                        <h6 class="mb-1">Money with Us (Business Profile Balance)</h6>
                                        <small class="text-muted">What the business has with your platform</small>
                                    </div>
                                    <div class="text-end">
                                        <h4 class="mb-0 text-primary">₦{{ number_format($business->balance ?? 0, 2) }}</h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                    <div>
                                        <h6 class="mb-1">Money in Bank</h6>
                                        <small class="text-muted">What the business has in their bank account</small>
                                    </div>
                                    <div class="text-end">
                                        <h4 class="mb-0 text-success">₦{{ number_format($business->actual_balance ?? 0, 2) }}</h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                    <div>
                                        <h6 class="mb-1">Withdrawable Balance</h6>
                                        <small class="text-muted">Available for withdrawal (same as Money in Bank)</small>
                                    </div>
                                    <div class="text-end">
                                        <h4 class="mb-0 text-info">₦{{ number_format($business->withdrawable_balance ?? 0, 2) }}</h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                    <div>
                                        <h6 class="mb-1">Ledger Balance</h6>
                                        <small class="text-muted">What we owe the business (Money with Us - Money in Bank)</small>
                                    </div>
                                    <div class="text-end">
                                        @php
                                            $ledgerBalance = ($business->balance ?? 0) - ($business->actual_balance ?? 0);
                                        @endphp
                                        <h4 class="mb-0 {{ $ledgerBalance >= 0 ? 'text-success' : 'text-danger' }}">
                                            ₦{{ number_format($ledgerBalance, 2) }}
                                        </h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                    <div>
                                        <h6 class="mb-1">Total Revenue</h6>
                                        <small class="text-muted">All time revenue</small>
                                    </div>
                                    <div class="text-end">
                                        <h4 class="mb-0 text-info">₦{{ number_format($stats['total_revenue'] ?? 0, 2) }}</h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                    <div>
                                        <h6 class="mb-1">Total Withdrawals</h6>
                                        <small class="text-muted">All time withdrawals</small>
                                    </div>
                                    <div class="text-end">
                                        <h4 class="mb-0 text-secondary">₦{{ number_format($stats['total_withdrawals'] ?? 0, 2) }}</h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                    <div>
                                        <h6 class="mb-1">Pending Withdrawals</h6>
                                        <small class="text-muted">Awaiting approval</small>
                                    </div>
                                    <div class="text-end">
                                        <h4 class="mb-0 text-warning">₦{{ number_format($stats['pending_withdrawals'] ?? 0, 2) }}</h4>
                                    </div>
                                </div>
                            </div>

                            @if($business->last_balance_update)
                            <div class="col-12">
                                <small class="text-muted">Last updated: {{ $business->last_balance_update->format('M d, Y H:i') }}</small>
                            </div>
                            @endif

                            @if($business->balance_notes)
                            <div class="col-12">
                                <label class="form-label fw-bold">Balance Notes</label>
                                <p class="mb-0">{{ $business->balance_notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="row g-4 mt-2">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-1">{{ $stats['total_sites'] }}</h3>
                        <p class="mb-0">Total Sites</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-1">{{ $stats['active_sites'] }}</h3>
                        <p class="mb-0">Active Sites</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-1">{{ $stats['total_transactions'] }}</h3>
                        <p class="mb-0">Total Transactions</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-1">₦{{ number_format($stats['total_revenue'], 2) }}</h3>
                        <p class="mb-0">Total Revenue</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Owner Information -->
        <div class="row g-4 mt-2">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Owner Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            @if($business->user->profile_photo)
                                <img src="{{ $business->user->profile_photo_url }}" class="rounded-circle me-3" width="50" height="50">
                            @else
                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            @endif
                            <div>
                                <h5 class="mb-1">{{ $business->user->name }}</h5>
                                <p class="text-muted mb-0">{{ $business->user->email }}</p>
                            </div>
                        </div>
                        <p class="mb-0"><strong>Member since:</strong> {{ $business->user->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('super-admin.businesses.edit', $business) }}" class="btn btn-outline-primary">
                                <i class="fas fa-edit me-2"></i>Edit Business
                            </a>
                            <a href="{{ route('super-admin.withdrawals.index') }}?search={{ $business->business_name }}" class="btn btn-outline-warning">
                                <i class="fas fa-money-bill-wave me-2"></i>View Withdrawals
                            </a>
                            <a href="{{ route('super-admin.tickets.index') }}?search={{ $business->user->name }}" class="btn btn-outline-info">
                                <i class="fas fa-ticket-alt me-2"></i>View Support Tickets
                            </a>
                            <button type="button" class="btn btn-outline-primary" onclick="updateBalance()">
                                <i class="fas fa-edit me-2"></i>Update Balance
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Balance Modal -->
    <div class="modal fade" id="updateBalanceModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Balance - {{ $business->business_name }}</h5>
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
                                <label for="actual_balance" class="form-label">Money in Bank (Super Admin Sets)</label>
                                <div class="input-group">
                                    <span class="input-group-text">₦</span>
                                    <input type="number" class="form-control" id="actual_balance" name="actual_balance" 
                                           value="{{ $business->actual_balance ?? 0 }}" step="0.01" min="0" required>
                                </div>
                                <small class="text-muted">What the business actually has in their bank account.</small>
                            </div>
                            <div class="col-md-6">
                                <label for="ledger_balance" class="form-label">Ledger Balance (Auto-calculated)</label>
                                <div class="input-group">
                                    <span class="input-group-text">₦</span>
                                    <input type="number" class="form-control" id="ledger_balance" name="ledger_balance" 
                                           value="{{ $business->ledger_balance ?? 0 }}" step="0.01" readonly>
                                </div>
                                <small class="text-muted">Money with Us - Money in Bank (what we owe the business).</small>
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label for="balance" class="form-label">Money with Us (Business Profile Balance)</label>
                                <div class="input-group">
                                    <span class="input-group-text">₦</span>
                                    <input type="number" class="form-control" id="balance" name="balance" 
                                           value="{{ $business->balance ?? 0 }}" step="0.01" min="0" required>
                                </div>
                                <small class="text-muted">The business profile's main balance (what they have with your platform).</small>
                            </div>
                            <div class="col-md-6">
                                <label for="withdrawable_balance" class="form-label">Withdrawable Balance (Same as Money in Bank)</label>
                                <div class="input-group">
                                    <span class="input-group-text">₦</span>
                                    <input type="number" class="form-control" id="withdrawable_balance" name="withdrawable_balance" 
                                           value="{{ $business->actual_balance ?? 0 }}" step="0.01" min="0" readonly>
                                </div>
                                <small class="text-muted">Same as Money in Bank (what they can withdraw).</small>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <label for="balance_notes" class="form-label">Balance Notes</label>
                            <textarea class="form-control" id="balance_notes" name="balance_notes" rows="3" 
                                      placeholder="Add notes about this balance update (optional)">{{ $business->balance_notes }}</textarea>
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
        function updateBalance() {
            new bootstrap.Modal(document.getElementById('updateBalanceModal')).show();
        }

        // Auto-calculate balances based on the new logic
        function calculateBalances() {
            const moneyWithUs = parseFloat(document.getElementById('balance').value) || 0;
            const moneyInBank = parseFloat(document.getElementById('actual_balance').value) || 0;
            
            // Withdrawable balance = Money in bank (same as money in bank)
            const withdrawableBalance = moneyInBank;
            
            // Ledger balance = Money with us - Money in bank (what we owe the business)
            const ledgerBalance = moneyWithUs - moneyInBank;
            
            document.getElementById('withdrawable_balance').value = withdrawableBalance.toFixed(2);
            document.getElementById('ledger_balance').value = ledgerBalance.toFixed(2);
        }

        // Add event listeners for auto-calculation
        document.addEventListener('DOMContentLoaded', function() {
            const actualBalanceField = document.getElementById('actual_balance');
            const ledgerBalanceField = document.getElementById('ledger_balance');
            const balanceField = document.getElementById('balance');
            
            if (actualBalanceField) {
                actualBalanceField.addEventListener('input', calculateBalances);
            }
            if (ledgerBalanceField) {
                ledgerBalanceField.addEventListener('input', calculateBalances);
            }
            if (balanceField) {
                balanceField.addEventListener('input', calculateBalances);
            }
        });

        document.getElementById('updateBalanceForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(`{{ route('super-admin.businesses.balance', $business) }}`, {
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