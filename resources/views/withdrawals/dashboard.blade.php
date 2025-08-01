<x-app-layout>
    <div class="container py-5">
        <!-- Page Alert Area -->
        <div id="pageAlert" style="display: none;" class="alert alert-dismissible fade show mb-4">
            <span id="pageAlertIcon"></span>
            <span id="pageAlertMessage"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
            <div>
                <h1 class="h2 fw-bold mb-1">Withdrawal Dashboard</h1>
                <p class="text-secondary mb-0">Manage your withdrawals and transfers</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#withdrawalModal">
                    <i class="fas fa-plus me-2"></i>New Withdrawal
                </button>
            </div>
        </div>

        <!-- Stat Cards Row -->
        <div class="row g-4 mb-4">
            <div class="col-6 col-md-3">
                <div class="card text-white h-100" style="background: linear-gradient(135deg, rgb(24, 164, 75), rgb(34, 197, 94)); border-radius: 1rem;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="fw-semibold small">Available Balance</span>
                            <span class="fs-5">₦</span>
                        </div>
                        <div class="fs-5" style="font-size: 16px;">₦{{ number_format($businessProfile->balance ?? 0) }}</div>
                        <div class="small">Ready for withdrawal</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-white h-100" style="background: linear-gradient(135deg, rgb(54, 88, 238), rgb(30, 50, 150)); border-radius: 1rem;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="fw-semibold small">Total Withdrawals</span>
                            <i class="fas fa-wave-square"></i>
                        </div>
                        <div class="fs-5" style="font-size: 16px;">₦{{ number_format($totalWithdrawals ?? 0) }}</div>
                        <div class="small">All time</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-white h-100" style="background: linear-gradient(135deg, rgb(150, 50, 161), rgb(100, 30, 110)); border-radius: 1rem;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="fw-semibold small">Pending</span>
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="fs-5" style="font-size: 16px;">{{ $pendingWithdrawals ?? 0 }}</div>
                        <div class="small">Awaiting approval</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-white h-100" style="background: linear-gradient(135deg, rgb(234, 88, 12), rgb(249, 115, 22)); border-radius: 1rem;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="fw-semibold small">Beneficiaries</span>
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="fs-5" style="font-size: 16px;">{{ $beneficiaries->count() ?? 0 }}</div>
                        <div class="small">Saved accounts</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row g-4">
            <!-- Withdrawals List -->
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Withdrawals</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Beneficiary</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($withdrawals as $withdrawal)
                                    <tr>
                                        <td>{{ $withdrawal->reference }}</td>
                                        <td>
                                            <div>{{ $withdrawal->recipient_account_name }}</div>
                                            <small class="text-muted">{{ $withdrawal->recipient_bank }} - {{ $withdrawal->recipient_account_number }}</small>
                                        </td>
                                        <td>₦{{ number_format($withdrawal->amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $withdrawal->status === 'completed' ? 'success' : ($withdrawal->status === 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($withdrawal->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $withdrawal->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-2x mb-3"></i>
                                                <p>No withdrawals found</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $withdrawals->links() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions & Beneficiaries -->
            <div class="col-12 col-lg-4">
                <!-- Quick Withdrawal -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Quick Withdrawal</h5>
                    </div>
                    <div class="card-body text-center">
                        <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#withdrawalModal">
                            <i class="fas fa-plus me-2"></i>New Withdrawal
                        </button>
                    </div>
                </div>

                <!-- Beneficiaries -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Saved Beneficiaries</h5>
                        @if($beneficiaries->count() < 2)
                        <a href="{{ route('beneficiaries.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-plus"></i>
                        </a>
                        @endif
                    </div>
                    <div class="card-body">
                        @forelse($beneficiaries as $beneficiary)
                        <div class="d-flex align-items-center mb-3 p-2 border rounded">
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $beneficiary->account_name }}</div>
                                <div class="text-muted small">{{ $beneficiary->bank }}</div>
                                <div class="text-muted small">{{ $beneficiary->account_number }}</div>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link text-dark p-0" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#" onclick="editBeneficiary({{ $beneficiary->id }})">
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </a></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteBeneficiary({{ $beneficiary->id }})">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-user-plus fa-2x mb-2"></i>
                            <p class="mb-0">No beneficiaries saved</p>
                            <small>Add up to 2 bank accounts for withdrawals</small>
                        </div>
                        @endforelse
                        
                        @if($beneficiaries->count() >= 2)
                        <div class="alert alert-info mt-3 mb-0">
                            <small><i class="fas fa-info-circle me-1"></i>Maximum of 2 beneficiaries allowed</small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Withdrawal Modal -->
    <div class="modal fade" id="withdrawalModal" tabindex="-1" aria-labelledby="withdrawalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="withdrawalModalLabel">New Withdrawal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('withdrawals.store') }}" method="POST" id="withdrawalForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="beneficiary_id" class="form-label">Select Beneficiary</label>
                            <select class="form-select @error('beneficiary_id') is-invalid @enderror" id="beneficiary_id" name="beneficiary_id" required>
                                <option value="">Select a beneficiary</option>
                                @foreach($beneficiaries as $beneficiary)
                                <option value="{{ $beneficiary->id }}" {{ old('beneficiary_id') == $beneficiary->id ? 'selected' : '' }}>
                                    {{ $beneficiary->account_name }} - {{ $beneficiary->bank }} ({{ $beneficiary->account_number }})
                                </option>
                                @endforeach
                            </select>
                            @error('beneficiary_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount (₦)</label>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" min="1" step="0.01" value="{{ old('amount') }}" required>
                            <div class="form-text">Available balance: ₦{{ number_format($businessProfile->balance ?? 0) }}</div>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="narration" class="form-label">Narration (Optional)</label>
                            <input type="text" class="form-control @error('narration') is-invalid @enderror" id="narration" name="narration" value="{{ old('narration') }}">
                            @error('narration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="pin" class="form-label">PIN</label>
                            <input type="password" class="form-control @error('pin') is-invalid @enderror" id="pin" name="pin" maxlength="4" placeholder="Enter 4-digit PIN" required autocomplete="off">
                            @error('pin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submitWithdrawalBtn">
                            <span class="btn-text">Initiate Withdrawal</span>
                            <span class="btn-spinner" style="display: none;">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Processing...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Beneficiary Modal -->
    <div class="modal fade" id="addBeneficiaryModal" tabindex="-1" aria-labelledby="addBeneficiaryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBeneficiaryModalLabel">Add Beneficiary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addBeneficiaryForm">
                        <div class="mb-3">
                            <label for="bank" class="form-label">Bank Name</label>
                            <input type="text" class="form-control" id="bank" name="bank" required>
                        </div>
                        <div class="mb-3">
                            <label for="account_number" class="form-label">Account Number</label>
                            <input type="text" class="form-control" id="account_number" name="account_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="account_name" class="form-label">Account Name</label>
                            <input type="text" class="form-control" id="account_name" name="account_name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveBeneficiaryBtn">Save Beneficiary</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Beneficiary Modal -->
    <div class="modal fade" id="editBeneficiaryModal" tabindex="-1" aria-labelledby="editBeneficiaryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBeneficiaryModalLabel">Edit Beneficiary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editBeneficiaryForm">
                        <input type="hidden" id="edit_beneficiary_id" name="beneficiary_id">
                        <div class="mb-3">
                            <label for="edit_bank" class="form-label">Bank Name</label>
                            <input type="text" class="form-control" id="edit_bank" name="bank" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_account_number" class="form-label">Account Number</label>
                            <input type="text" class="form-control" id="edit_account_number" name="account_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_account_name" class="form-label">Account Name</label>
                            <input type="text" class="form-control" id="edit_account_name" name="account_name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="updateBeneficiaryBtn">Update Beneficiary</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteBeneficiaryModal" tabindex="-1" aria-labelledby="deleteBeneficiaryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="mb-3">Delete Beneficiary</h5>
                    <p class="mb-4">Are you sure you want to delete this beneficiary? This action cannot be undone.</p>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger flex-fill" id="confirmDeleteBeneficiaryBtn">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Withdrawal Dashboard loaded');
        
        // Initialize Bootstrap modals
        const withdrawalModal = new bootstrap.Modal(document.getElementById('withdrawalModal'));
        const addBeneficiaryModal = new bootstrap.Modal(document.getElementById('addBeneficiaryModal'));
        const editBeneficiaryModal = new bootstrap.Modal(document.getElementById('editBeneficiaryModal'));
        const deleteBeneficiaryModal = new bootstrap.Modal(document.getElementById('deleteBeneficiaryModal'));

        // Test JavaScript functionality
        const initiateWithdrawalBtn = document.querySelector('[data-bs-target="#withdrawalModal"]');
        if (initiateWithdrawalBtn) {
            console.log('Initiate withdrawal button found');
            initiateWithdrawalBtn.addEventListener('click', function() {
                console.log('Initiate withdrawal button clicked');
            });
        } else {
            console.error('Initiate withdrawal button not found');
        }

        // Beneficiary management variables
        let beneficiaryToDelete = null;

        // ===== BENEFICIARY MANAGEMENT =====
        
        // Add Beneficiary functionality
        const saveBeneficiaryBtn = document.getElementById('saveBeneficiaryBtn');
        
        if (saveBeneficiaryBtn) {
            saveBeneficiaryBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const form = document.getElementById('addBeneficiaryForm');
                if (!form) {
                    console.error('Add beneficiary form not found');
                    return;
                }
                
                const formData = new FormData(form);
                
                // Check if we already have 2 beneficiaries
                const currentCount = {{ $beneficiaries->count() }};
                
                if (currentCount >= 2) {
                    alert('Maximum of 2 beneficiaries allowed');
                    return;
                }
                
                const data = {
                    bank: formData.get('bank'),
                    account_number: formData.get('account_number'),
                    account_name: formData.get('account_name')
                };
                
                // Disable button and show loading
                saveBeneficiaryBtn.disabled = true;
                saveBeneficiaryBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
                
                fetch('/beneficiaries', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        addBeneficiaryModal.hide();
                        window.location.reload();
                    } else {
                        alert(data.message || 'Failed to add beneficiary');
                    }
                })
                .catch(error => {
                    console.error('Error saving beneficiary:', error);
                    alert('An error occurred while adding the beneficiary');
                })
                .finally(() => {
                    // Re-enable button
                    saveBeneficiaryBtn.disabled = false;
                    saveBeneficiaryBtn.innerHTML = 'Save Beneficiary';
                });
            });
        }

        // Edit Beneficiary functionality
        window.editBeneficiary = function(beneficiaryId) {
            // Find the beneficiary data
            const beneficiaries = {!! $beneficiaries->toJson() !!};
            const beneficiary = beneficiaries.find(b => b.id === beneficiaryId);
            
            if (beneficiary) {
                document.getElementById('edit_beneficiary_id').value = beneficiary.id;
                document.getElementById('edit_bank').value = beneficiary.bank;
                document.getElementById('edit_account_number').value = beneficiary.account_number;
                document.getElementById('edit_account_name').value = beneficiary.account_name;
                editBeneficiaryModal.show();
            }
        };

        // Update Beneficiary functionality
        const updateBeneficiaryBtn = document.getElementById('updateBeneficiaryBtn');
        if (updateBeneficiaryBtn) {
            updateBeneficiaryBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const form = document.getElementById('editBeneficiaryForm');
                const formData = new FormData(form);
                
                const data = {
                    bank: formData.get('bank'),
                    account_number: formData.get('account_number'),
                    account_name: formData.get('account_name')
                };
                
                const beneficiaryId = formData.get('beneficiary_id');
                
                // Disable button and show loading
                updateBeneficiaryBtn.disabled = true;
                updateBeneficiaryBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...';
                
                fetch(`/beneficiaries/${beneficiaryId}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        editBeneficiaryModal.hide();
                        window.location.reload();
                    } else {
                        alert(data.message || 'Failed to update beneficiary');
                    }
                })
                .catch(error => {
                    console.error('Error updating beneficiary:', error);
                    alert('An error occurred while updating the beneficiary');
                })
                .finally(() => {
                    // Re-enable button
                    updateBeneficiaryBtn.disabled = false;
                    updateBeneficiaryBtn.innerHTML = 'Update Beneficiary';
                });
            });
        }

        // Delete Beneficiary functionality
        window.deleteBeneficiary = function(beneficiaryId) {
            beneficiaryToDelete = beneficiaryId;
            deleteBeneficiaryModal.show();
        };

        // Confirm Delete Beneficiary
        const confirmDeleteBeneficiaryBtn = document.getElementById('confirmDeleteBeneficiaryBtn');
        if (confirmDeleteBeneficiaryBtn) {
            confirmDeleteBeneficiaryBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (!beneficiaryToDelete) return;
                
                // Disable button and show loading
                confirmDeleteBeneficiaryBtn.disabled = true;
                confirmDeleteBeneficiaryBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...';
                
                // Create form data with method spoofing
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('_method', 'DELETE');
                
                fetch(`/beneficiaries/${beneficiaryToDelete}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => {
                    if (response.ok) {
                        deleteBeneficiaryModal.hide();
                        window.location.reload();
                    } else {
                        throw new Error('Delete failed');
                    }
                })
                .catch(error => {
                    console.error('Error deleting beneficiary:', error);
                    alert('An error occurred while deleting the beneficiary. Please try again.');
                })
                .finally(() => {
                    // Re-enable button
                    confirmDeleteBeneficiaryBtn.disabled = false;
                    confirmDeleteBeneficiaryBtn.innerHTML = 'Delete';
                    beneficiaryToDelete = null;
                });
            });
        }

        // Handle withdrawal form submission with spinner
        const withdrawalForm = document.getElementById('withdrawalForm');
        const submitWithdrawalBtn = document.getElementById('submitWithdrawalBtn');
        
        console.log('Form found:', withdrawalForm);
        console.log('Button found:', submitWithdrawalBtn);
        
        if (withdrawalForm && submitWithdrawalBtn) {
            console.log('Adding event listener to form');
            withdrawalForm.addEventListener('submit', function(e) {
                console.log('Form submit triggered');
                e.preventDefault(); // Prevent normal form submission
                console.log('Form submission prevented');
                
                // Show spinner
                const btnText = submitWithdrawalBtn.querySelector('.btn-text');
                const btnSpinner = submitWithdrawalBtn.querySelector('.btn-spinner');
                
                console.log('Button elements:', { btnText, btnSpinner });
                
                if (btnText && btnSpinner) {
                    btnText.style.display = 'none';
                    btnSpinner.style.display = 'inline-block';
                    console.log('Spinner shown');
                }
                
                // Disable button to prevent double submission
                submitWithdrawalBtn.disabled = true;
                
                // Get form data
                const formData = new FormData(withdrawalForm);
                console.log('Form data:', {
                    beneficiary_id: formData.get('beneficiary_id'),
                    amount: formData.get('amount'),
                    narration: formData.get('narration'),
                    pin: formData.get('pin')
                });
                
                // Submit via fetch
                fetch('{{ route("withdrawals.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        beneficiary_id: formData.get('beneficiary_id'),
                        amount: formData.get('amount'),
                        narration: formData.get('narration'),
                        pin: formData.get('pin')
                    })
                })
                .then(response => {
                    console.log('Response received:', response);
                    return response.json();
                })
                .then(data => {
                    console.log('Data received:', data);
                    if (data.success) {
                        // Success - show page alert and reload
                        showPageAlert(data.message, 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);
                    } else {
                        // Error - show page alert, open modal, and reset button
                        showPageAlert(data.message, 'danger');
                        var withdrawalModal = new bootstrap.Modal(document.getElementById('withdrawalModal'));
                        withdrawalModal.show();
                        resetButton();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showPageAlert('An error occurred. Please try again.', 'danger');
                    resetButton();
                });
                
                function resetButton() {
                    console.log('Resetting button');
                    // Reset button state
                    if (btnText && btnSpinner) {
                        btnText.style.display = 'inline-block';
                        btnSpinner.style.display = 'none';
                    }
                    submitWithdrawalBtn.disabled = false;
                }
            });
            console.log('Event listener added successfully');
        } else {
            console.error('Form or button not found');
        }

        // Show page alerts for any errors (for page load errors)
        @if($errors->any())
            var withdrawalModal = new bootstrap.Modal(document.getElementById('withdrawalModal'));
            withdrawalModal.show();
            setTimeout(function() {
                showPageAlert('{{ $errors->first("pin") ?? $errors->first("amount") ?? $errors->first("general") ?? "Withdrawal failed" }}', 'danger');
            }, 100);
        @endif

        // Success feedback (on page flash message)
        @if(session('success'))
            showPageAlert('{{ session("success") }}', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 3000);
        @endif

        console.log('Withdrawal Dashboard initialization complete');
    });
    
    // Simple CSS animations
    const style = document.createElement('style');
    style.innerHTML = `
    @keyframes shake {
        0% { transform: translateX(0); }
        20% { transform: translateX(-10px); }
        40% { transform: translateX(10px); }
        60% { transform: translateX(-10px); }
        80% { transform: translateX(10px); }
        100% { transform: translateX(0); }
    }
    @keyframes popIn {
        0% { transform: scale(0.5); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
    `;
    document.head.appendChild(style);

    // Function to show page alerts
    function showPageAlert(message, type) {
        const pageAlert = document.getElementById('pageAlert');
        const pageAlertIcon = document.getElementById('pageAlertIcon');
        const pageAlertMessage = document.getElementById('pageAlertMessage');
        
        if (pageAlert && pageAlertIcon && pageAlertMessage) {
            // Set alert type
            pageAlert.className = `alert alert-${type} alert-dismissible fade show mb-4`;
            
            // Set icon based on type
            if (type === 'success') {
                pageAlertIcon.innerHTML = '<i class="fas fa-check-circle me-2"></i>';
            } else {
                pageAlertIcon.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>';
            }
            
            // Set message
            pageAlertMessage.textContent = message;
            
            // Show alert
            pageAlert.style.display = 'block';
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                pageAlert.style.display = 'none';
            }, 5000);
        }
    }
    </script>
    @endpush
</x-app-layout> 