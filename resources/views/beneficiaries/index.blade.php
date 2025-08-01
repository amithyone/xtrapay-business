<x-app-layout>
    <div class="container py-5">
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
                <h1 class="h2 fw-bold mb-1">Beneficiary Management</h1>
                <p class="text-secondary mb-0">Manage your bank accounts for withdrawals</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('withdrawal.dashboard') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-2"></i>Back to Withdrawals
                </a>
            </div>
        </div>

        <!-- Stats Card -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white" style="background: linear-gradient(135deg, rgb(24, 164, 75), rgb(34, 197, 94)); border-radius: 1rem;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold small">Saved Accounts</div>
                                <div class="fs-4">{{ $beneficiaries->count() }}/2</div>
                            </div>
                            <i class="fas fa-users fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Beneficiary Form -->
        @if($beneficiaries->count() < 2)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    @if($beneficiaries->count() == 0)
                        Add Your First Beneficiary
                    @else
                        Add Another Beneficiary
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('beneficiaries.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="bank" class="form-label">Bank Name</label>
                            <input type="text" class="form-control @error('bank') is-invalid @enderror" id="bank" name="bank" value="{{ old('bank') }}" required>
                            @error('bank')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="account_number" class="form-label">Account Number</label>
                            <input type="text" class="form-control @error('account_number') is-invalid @enderror" id="account_number" name="account_number" value="{{ old('account_number') }}" required>
                            @error('account_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="account_name" class="form-label">Account Name</label>
                            <input type="text" class="form-control @error('account_name') is-invalid @enderror" id="account_name" name="account_name" value="{{ old('account_name') }}" required>
                            @error('account_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Save Beneficiary
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- Beneficiaries List -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Your Beneficiaries</h5>
            </div>
            <div class="card-body">
                @if($beneficiaries->count() > 0)
                    <div class="row">
                        @foreach($beneficiaries as $beneficiary)
                        <div class="col-md-6 mb-4">
                            <div class="card border">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="flex-grow-1">
                                            <h6 class="card-title mb-1">{{ $beneficiary->account_name }}</h6>
                                            <p class="text-muted mb-1">{{ $beneficiary->bank }}</p>
                                            <p class="text-muted small mb-0">Account: {{ $beneficiary->account_number }}</p>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item edit-beneficiary-btn"
                                                       href="#"
                                                       data-id="{{ $beneficiary->id }}"
                                                       data-bank="{{ $beneficiary->bank }}"
                                                       data-account_number="{{ $beneficiary->account_number }}"
                                                       data-account_name="{{ $beneficiary->account_name }}">
                                                        <i class="fas fa-edit me-2"></i>Edit
                                                    </a>
                                                </li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="showDeleteForm({{ $beneficiary->id }})">
                                                    <i class="fas fa-trash me-2"></i>Delete
                                                </a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    
                                    <!-- Delete Form (Hidden by default) -->
                                    <div id="deleteForm{{ $beneficiary->id }}" class="delete-form" style="display: none;">
                                        <div class="alert alert-warning">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <strong>Delete Beneficiary</strong>
                                            </div>
                                            <p class="mb-2 small">Are you sure you want to delete this beneficiary? This action cannot be undone.</p>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteBeneficiary({{ $beneficiary->id }})">Delete</button>
                                                <button type="button" class="btn btn-sm btn-secondary" onclick="hideDeleteForm({{ $beneficiary->id }})">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-user-plus fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Beneficiaries Added</h5>
                        <p class="text-muted">Add up to 2 bank accounts to start making withdrawals</p>
                    </div>
                @endif

                @if($beneficiaries->count() >= 2)
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    You have reached the maximum of 2 beneficiaries. To add a new one, please delete an existing beneficiary first.
                </div>
                @endif
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
                <form id="editBeneficiaryForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Beneficiary</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delegated event for edit buttons
        document.body.addEventListener('click', function(e) {
            if (e.target.closest('.edit-beneficiary-btn')) {
                e.preventDefault();
                const btn = e.target.closest('.edit-beneficiary-btn');
                const id = btn.getAttribute('data-id');
                const bank = btn.getAttribute('data-bank');
                const accountNumber = btn.getAttribute('data-account_number');
                const accountName = btn.getAttribute('data-account_name');
                // Set form action
                const form = document.getElementById('editBeneficiaryForm');
                form.action = `/beneficiaries/${id}`;
                // Set values
                document.getElementById('edit_bank').value = bank;
                document.getElementById('edit_account_number').value = accountNumber;
                document.getElementById('edit_account_name').value = accountName;
                // Show modal
                var modal = new bootstrap.Modal(document.getElementById('editBeneficiaryModal'));
                modal.show();
            }
        });
    });
    
    function showDeleteForm(beneficiaryId) {
        const deleteForm = document.getElementById('deleteForm' + beneficiaryId);
        deleteForm.style.display = 'block';
    }
    
    function hideDeleteForm(beneficiaryId) {
        const deleteForm = document.getElementById('deleteForm' + beneficiaryId);
        deleteForm.style.display = 'none';
    }

    function deleteBeneficiary(beneficiaryId) {
        if (!confirm('Are you sure you want to delete this beneficiary? This action cannot be undone.')) {
            return;
        }

        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('_method', 'DELETE');

        fetch(`/beneficiaries/${beneficiaryId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.ok) {
                window.location.reload();
            } else {
                throw new Error('Delete failed');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the beneficiary. Please try again.');
        });
    }
    </script>
    @endpush
</x-app-layout> 