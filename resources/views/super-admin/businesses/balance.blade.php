<x-app-layout>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">Update Business Balance</h1>
                <p class="text-muted mb-0">{{ $business->business_name }} - Balance Accounting</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('super-admin.businesses.show', $business) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Business
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Balance Accounting</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Balance Accounting:</strong> Update the business balance accounting. Money in Bank is manually managed, withdrawable balance is what users can withdraw.
                        </div>
                        
                        <form id="updateBalanceForm">
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
                                               value="{{ ($business->balance ?? 0) - ($business->actual_balance ?? 0) }}" step="0.01" readonly>
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
                                          placeholder="Add notes about this balance update...">{{ $business->balance_notes }}</textarea>
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Balance
                                </button>
                                <a href="{{ route('super-admin.businesses.show', $business) }}" class="btn btn-outline-secondary ms-2">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Current Balance Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                    <div>
                                        <h6 class="mb-1">Money with Us</h6>
                                        <small class="text-muted">Business Profile Balance</small>
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
                                        <small class="text-muted">Actual Balance</small>
                                    </div>
                                    <div class="text-end">
                                        <h4 class="mb-0 text-success">₦{{ number_format($business->actual_balance ?? 0, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                    <div>
                                        <h6 class="mb-1">Withdrawable</h6>
                                        <small class="text-muted">Available for withdrawal</small>
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
                                        <small class="text-muted">What we owe the business</small>
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
                        </div>
                        
                        @if($business->last_balance_update)
                        <div class="mt-3">
                            <small class="text-muted">Last updated: {{ $business->last_balance_update->format('M d, Y H:i') }}</small>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('super-admin.businesses.show', $business) }}" class="btn btn-outline-info">
                                <i class="fas fa-eye me-2"></i>View Business Details
                            </a>
                            <a href="{{ route('super-admin.businesses.edit', $business) }}" class="btn btn-outline-primary">
                                <i class="fas fa-edit me-2"></i>Edit Business
                            </a>
                            <a href="{{ route('super-admin.savings.show', $business) }}" class="btn btn-outline-success">
                                <i class="fas fa-piggy-bank me-2"></i>Manage Savings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-calculate ledger balance and withdrawable balance
        function calculateBalances() {
            const moneyWithUs = parseFloat(document.getElementById('balance').value) || 0;
            const moneyInBank = parseFloat(document.getElementById('actual_balance').value) || 0;
            const withdrawableBalance = moneyInBank; // Withdrawable = Money in Bank
            const ledgerBalance = moneyWithUs - moneyInBank; // Ledger = Money with Us - Money in Bank
            
            document.getElementById('withdrawable_balance').value = withdrawableBalance.toFixed(2);
            document.getElementById('ledger_balance').value = ledgerBalance.toFixed(2);
        }

        // Add event listeners for auto-calculation
        document.getElementById('balance').addEventListener('input', calculateBalances);
        document.getElementById('actual_balance').addEventListener('input', calculateBalances);

        // Form submission
        document.getElementById('updateBalanceForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(`{{ route('super-admin.businesses.balance', $business) }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Balance updated successfully!');
                    window.location.href = '{{ route("super-admin.businesses.show", $business) }}';
                } else {
                    alert('Error updating balance: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating balance. Please try again.');
            });
        });
    </script>
</x-app-layout> 