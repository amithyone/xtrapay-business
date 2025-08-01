<x-app-layout>
    <div class="container py-5">
        <!-- Toast Notifications Container -->
        <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1055;">
            <!-- Toast notifications will be dynamically added here -->
        </div>

        <!-- Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
            <div>
                <h1 class="h2 fw-bold mb-1">Withdrawals</h1>
                <p class="text-secondary mb-0">Manage your withdrawals and transfers</p>
            </div>
            <div class="mt-3 mt-md-0">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#withdrawalModal">
                    <i class="fas fa-plus me-2"></i>New Withdrawal
                </button>
            </div>
        </div>

        <!-- Withdrawals List -->
        <div class="card">
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
                                <th>Actions</th>
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
                                    <span class="badge bg-{{ $withdrawal->status === 'success' ? 'success' : ($withdrawal->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($withdrawal->status) }}
                                    </span>
                                </td>
                                <td>{{ $withdrawal->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewWithdrawal({{ $withdrawal->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
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

    <!-- Withdrawal Modal -->
    <div class="modal fade" id="withdrawalModal" tabindex="-1" aria-labelledby="withdrawalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="withdrawalModalLabel">New Withdrawal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Status Notification -->
                    <div id="statusNotification" class="alert mb-3" style="display: none;">
                        <div class="d-flex align-items-center">
                            <i id="statusIcon" class="me-2"></i>
                            <div>
                                <div id="statusTitle" class="fw-bold"></div>
                                <div id="statusMessage" class="small"></div>
                            </div>
                        </div>
                    </div>

                    <form id="withdrawalForm">
                        <div class="mb-3">
                            <label for="beneficiary_id" class="form-label">Select Beneficiary</label>
                            <select class="form-select" id="beneficiary_id" name="beneficiary_id" required>
                                <option value="">Select a beneficiary</option>
                                @foreach($beneficiaries as $beneficiary)
                                <option value="{{ $beneficiary->id }}">
                                    {{ $beneficiary->account_name }} - {{ $beneficiary->bank }} ({{ $beneficiary->account_number }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount (₦)</label>
                            <input type="number" class="form-control" id="amount" name="amount" min="1" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="narration" class="form-label">Narration (Optional)</label>
                            <input type="text" class="form-control" id="narration" name="narration">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="initiateWithdrawal">
                        <i class="fas fa-plus me-2"></i>Initiate Withdrawal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- PIN Verification Modal -->
    <div class="modal fade" id="pinVerificationModal" tabindex="-1" aria-labelledby="pinVerificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pinVerificationModalLabel">Enter PIN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <!-- Notification Area in PIN Modal -->
                    <div id="pinModalNotification" class="mb-3" style="display: none;">
                        <div id="pinModalAlert" class="alert" role="alert">
                            <div class="d-flex align-items-center">
                                <i id="pinModalAlertIcon" class="me-2"></i>
                                <div>
                                    <div id="pinModalAlertTitle" class="fw-bold"></div>
                                    <div id="pinModalAlertMessage" class="small"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <i class="fas fa-lock text-primary" style="font-size: 4rem;"></i>
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control form-control-lg text-center" id="pin" maxlength="4" placeholder="Enter 4-digit PIN">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="verifyPinButton">Verify & Proceed</button>
                </div>
            </div>
        </div>
    </div>

    <!-- PIN Error Modal -->
    <div class="modal fade" id="pinErrorModal" tabindex="-1" aria-labelledby="pinErrorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <i class="fas fa-lock text-warning" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="mb-3 text-warning">Invalid PIN</h5>
                    <p id="pinErrorMessage" class="mb-4">Please ensure you enter the correct 4-digit PIN associated with your business profile.</p>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary flex-fill" onclick="retryWithdrawal()">Try Again</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="mb-3 text-success">Withdrawal Successful!</h5>
                    <p id="successMessage" class="mb-3"></p>
                    <div id="successDetails" class="text-start bg-light p-3 rounded mb-3" style="display: none;">
                        <small class="text-muted">
                            <div><strong>Reference:</strong> <span id="successReference"></span></div>
                            <div><strong>Amount:</strong> <span id="successAmount"></span></div>
                            <div><strong>Beneficiary:</strong> <span id="successBeneficiary"></span></div>
                        </small>
                    </div>
                    <button type="button" class="btn btn-success w-100" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <i id="errorIcon" class="fas fa-exclamation-circle text-danger" style="font-size: 4rem;"></i>
                    </div>
                    <h5 id="errorTitle" class="mb-3 text-danger">Error</h5>
                    <p id="errorMessage" class="mb-3"></p>
                    <div id="errorDetails" class="text-start bg-light p-3 rounded mb-3" style="display: none;">
                        <small class="text-muted">
                            <div id="errorDetailText"></div>
                        </small>
                    </div>
                    <button type="button" class="btn btn-danger w-100" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Content Loaded');

        // Initialize Bootstrap modals
        const withdrawalModal = new bootstrap.Modal('#withdrawalModal');
        const pinModal = new bootstrap.Modal('#pinVerificationModal');
        const successModal = new bootstrap.Modal('#successModal');
        const errorModal = new bootstrap.Modal('#errorModal');
        const pinErrorModal = new bootstrap.Modal('#pinErrorModal');

        console.log('Modals initialized');

        // Function to show status notification in withdrawal modal
        function showStatusNotification(type, title, message) {
            const notification = document.getElementById('statusNotification');
            const icon = document.getElementById('statusIcon');
            const statusTitle = document.getElementById('statusTitle');
            const statusMessage = document.getElementById('statusMessage');

            // Set alert classes based on type
            notification.className = 'alert mb-3';
            if (type === 'success') {
                notification.classList.add('alert-success');
                icon.className = 'fas fa-check-circle me-2 text-success';
            } else if (type === 'warning') {
                notification.classList.add('alert-warning');
                icon.className = 'fas fa-exclamation-triangle me-2 text-warning';
            } else {
                notification.classList.add('alert-danger');
                icon.className = 'fas fa-exclamation-circle me-2 text-danger';
            }

            statusTitle.textContent = title;
            statusMessage.textContent = message;
            notification.style.display = 'block';
        }

        // Function to show notification in withdrawal modal
        function showModalNotification(type, title, message) {
            const notification = document.getElementById('modalNotification');
            const alert = document.getElementById('modalAlert');
            const icon = document.getElementById('modalAlertIcon');
            const alertTitle = document.getElementById('modalAlertTitle');
            const alertMessage = document.getElementById('modalAlertMessage');

            // Set alert classes based on type
            alert.className = 'alert';
            if (type === 'success') {
                alert.classList.add('alert-success');
                icon.className = 'fas fa-check-circle me-2 text-success';
            } else if (type === 'warning') {
                alert.classList.add('alert-warning');
                icon.className = 'fas fa-exclamation-triangle me-2 text-warning';
            } else {
                alert.classList.add('alert-danger');
                icon.className = 'fas fa-exclamation-circle me-2 text-danger';
            }

            alertTitle.textContent = title;
            alertMessage.textContent = message;
            notification.style.display = 'block';

            // Auto-hide after 5 seconds
            setTimeout(() => {
                notification.style.display = 'none';
            }, 5000);
        }

        // Function to show notification in PIN modal
        function showPinModalNotification(type, title, message) {
            const notification = document.getElementById('pinModalNotification');
            const alert = document.getElementById('pinModalAlert');
            const icon = document.getElementById('pinModalAlertIcon');
            const alertTitle = document.getElementById('pinModalAlertTitle');
            const alertMessage = document.getElementById('pinModalAlertMessage');

            // Set alert classes based on type
            alert.className = 'alert';
            if (type === 'success') {
                alert.classList.add('alert-success');
                icon.className = 'fas fa-check-circle me-2 text-success';
            } else if (type === 'warning') {
                alert.classList.add('alert-warning');
                icon.className = 'fas fa-exclamation-triangle me-2 text-warning';
            } else {
                alert.classList.add('alert-danger');
                icon.className = 'fas fa-exclamation-circle me-2 text-danger';
            }

            alertTitle.textContent = title;
            alertMessage.textContent = message;
            notification.style.display = 'block';

            // Auto-hide after 5 seconds
            setTimeout(() => {
                notification.style.display = 'none';
            }, 5000);
        }

        // Add click event listener to initiate withdrawal button
        const initiateButton = document.getElementById('initiateWithdrawal');
        console.log('Initiate button found:', initiateButton);

        if (initiateButton) {
            console.log('Adding click listener to initiate button');
            initiateButton.onclick = function(e) {
                console.log('Initiate button clicked');
                e.preventDefault();
                
                const form = document.getElementById('withdrawalForm');
                console.log('Form found:', form);
                
                const beneficiaryId = form.querySelector('[name="beneficiary_id"]').value;
                const amount = form.querySelector('[name="amount"]').value;
                
                console.log('Form values:', { beneficiaryId, amount });
                
                if (!beneficiaryId || !amount) {
                    showModalNotification('warning', 'Validation Error', 'Please fill in all required fields');
                    return;
                }
                
                // Hide withdrawal modal and show PIN modal
                withdrawalModal.hide();
                setTimeout(() => {
                    console.log('Showing PIN modal');
                    pinModal.show();
                }, 500);
            };
        }

        // Add click event listener to verify PIN button
        const verifyButton = document.getElementById('verifyPinButton');
        console.log('Verify button found:', verifyButton);

        if (verifyButton) {
            console.log('Adding click listener to verify button');
            verifyButton.onclick = function(e) {
                console.log('Verify button clicked');
                e.preventDefault();
                
                const form = document.getElementById('withdrawalForm');
                const pin = document.getElementById('pin').value;
                
                if (!pin || pin.length !== 4) {
                    showPinModalNotification('warning', 'Invalid PIN', 'Please enter a valid 4-digit PIN');
                    return;
                }
                
                const data = {
                    beneficiary_id: form.querySelector('[name="beneficiary_id"]').value,
                    amount: form.querySelector('[name="amount"]').value,
                    narration: form.querySelector('[name="narration"]').value,
                    pin: pin
                };
                
                console.log('Sending data:', data);
                
                verifyButton.disabled = true;
                verifyButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
                
                fetch('{{ route("withdrawals.store") }}', {
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
                    console.log('Response:', data);
                    pinModal.hide();
                    document.getElementById('pin').value = '';
                    
                    // Show withdrawal modal again to display the result
                    withdrawalModal.show();
                    
                    if (data.success) {
                        // Success
                        showStatusNotification('success', 'Withdrawal Successful!', data.message || 'Your withdrawal has been initiated successfully!');
                        
                        // Disable the button
                        const initiateButton = document.getElementById('initiateWithdrawal');
                        initiateButton.disabled = true;
                        initiateButton.innerHTML = '<i class="fas fa-check me-2"></i>Success!';
                        
                        // Show success modal with details
                        document.getElementById('successMessage').textContent = data.message || 'Your withdrawal has been initiated successfully!';
                        
                        // Show additional details if available
                        if (data.withdrawal) {
                            document.getElementById('successReference').textContent = data.withdrawal.reference || 'N/A';
                            document.getElementById('successAmount').textContent = '₦' + parseFloat(data.withdrawal.amount || 0).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                            document.getElementById('successBeneficiary').textContent = data.withdrawal.recipient_account_name || 'N/A';
                            document.getElementById('successDetails').style.display = 'block';
                        } else {
                            document.getElementById('successDetails').style.display = 'none';
                        }
                        
                        setTimeout(() => {
                            successModal.show();
                        }, 1000);
                        
                        form.reset();
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);
                    } else {
                        // Error
                        let statusType = 'danger';
                        let statusTitle = 'Error';
                        let statusMessage = data.message || 'An error occurred while processing your withdrawal.';
                        
                        if (data.message && data.message.toLowerCase().includes('pin')) {
                            statusType = 'warning';
                            statusTitle = 'Invalid PIN';
                            statusMessage = 'Please ensure you enter the correct 4-digit PIN. Click "Try Again" to retry.';
                            
                            // Make button retryable
                            const initiateButton = document.getElementById('initiateWithdrawal');
                            initiateButton.disabled = false;
                            initiateButton.innerHTML = '<i class="fas fa-redo me-2"></i>Try Again';
                            initiateButton.onclick = function(e) {
                                e.preventDefault();
                                // Reset form and show PIN modal
                                document.getElementById('statusNotification').style.display = 'none';
                                initiateButton.innerHTML = '<i class="fas fa-plus me-2"></i>Initiate Withdrawal';
                                initiateButton.onclick = null;
                                pinModal.show();
                            };
                        } else if (data.message && data.message.toLowerCase().includes('insufficient') || data.message.toLowerCase().includes('balance')) {
                            statusType = 'danger';
                            statusTitle = 'Insufficient Balance';
                            statusMessage = 'Your account balance is not sufficient to complete this withdrawal.';
                            
                            // Disable button
                            const initiateButton = document.getElementById('initiateWithdrawal');
                            initiateButton.disabled = true;
                            initiateButton.innerHTML = '<i class="fas fa-times me-2"></i>Insufficient Balance';
                        } else {
                            // Generic error
                            const initiateButton = document.getElementById('initiateWithdrawal');
                            initiateButton.disabled = false;
                            initiateButton.innerHTML = '<i class="fas fa-redo me-2"></i>Try Again';
                            initiateButton.onclick = function(e) {
                                e.preventDefault();
                                // Reset form and show PIN modal
                                document.getElementById('statusNotification').style.display = 'none';
                                initiateButton.innerHTML = '<i class="fas fa-plus me-2"></i>Initiate Withdrawal';
                                initiateButton.onclick = null;
                                pinModal.show();
                            };
                        }
                        
                        showStatusNotification(statusType, statusTitle, statusMessage);
                        
                        // Also show the error modal with details
                        let errorTitle = 'Error';
                        let errorIcon = 'fas fa-exclamation-circle text-danger';
                        let errorDetailText = '';
                        
                        // Handle specific error types for modal
                        if (data.message && data.message.toLowerCase().includes('pin')) {
                            errorTitle = 'Invalid PIN';
                            errorIcon = 'fas fa-lock text-warning';
                            errorDetailText = 'Please ensure you enter the correct 4-digit PIN associated with your business profile.';
                        } else if (data.message && data.message.toLowerCase().includes('insufficient') || data.message.toLowerCase().includes('balance')) {
                            errorTitle = 'Insufficient Balance';
                            errorIcon = 'fas fa-wallet text-warning';
                            errorDetailText = 'Your account balance is not sufficient to complete this withdrawal. Please check your balance and try a smaller amount.';
                        } else if (data.message && data.message.toLowerCase().includes('beneficiary')) {
                            errorTitle = 'Beneficiary Error';
                            errorIcon = 'fas fa-user-times text-warning';
                            errorDetailText = 'There was an issue with the selected beneficiary. Please verify the beneficiary details.';
                        } else if (data.message && data.message.toLowerCase().includes('amount')) {
                            errorTitle = 'Invalid Amount';
                            errorIcon = 'fas fa-money-bill-wave text-warning';
                            errorDetailText = 'Please enter a valid amount greater than zero.';
                        }
                        
                        // Update error modal content
                        document.getElementById('errorTitle').textContent = errorTitle;
                        document.getElementById('errorTitle').className = errorTitle === 'Error' ? 'mb-3 text-danger' : 'mb-3 text-warning';
                        document.getElementById('errorIcon').className = errorIcon;
                        document.getElementById('errorMessage').textContent = data.message || 'An error occurred while processing your withdrawal.';
                        
                        // Show error details if available
                        if (errorDetailText) {
                            document.getElementById('errorDetailText').innerHTML = errorDetailText;
                            document.getElementById('errorDetails').style.display = 'block';
                        } else {
                            document.getElementById('errorDetails').style.display = 'none';
                        }
                        
                        setTimeout(() => {
                            errorModal.show();
                        }, 1000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    pinModal.hide();
                    showModalNotification('danger', 'Network Error', 'An error occurred while processing the withdrawal. Please try again.');
                    document.getElementById('errorMessage').textContent = 'An error occurred while processing the withdrawal. Please try again.';
                    errorModal.show();
                })
                .finally(() => {
                    verifyButton.disabled = false;
                    verifyButton.innerHTML = 'Verify & Proceed';
                });
            };
        }

        // Add event listeners for modal close
        document.getElementById('withdrawalModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('withdrawalForm').reset();
            // Hide notification when modal closes
            document.getElementById('modalNotification').style.display = 'none';
            document.getElementById('statusNotification').style.display = 'none';
            
            // Reset button to original state
            const initiateButton = document.getElementById('initiateWithdrawal');
            initiateButton.className = 'btn btn-primary';
            initiateButton.innerHTML = '<i class="fas fa-plus me-2"></i>Initiate Withdrawal';
            initiateButton.disabled = false;
            initiateButton.onclick = null; // Remove any custom onclick handlers
        });

        document.getElementById('pinVerificationModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('pin').value = '';
            // Hide notification when modal closes
            document.getElementById('pinModalNotification').style.display = 'none';
        });
    });

    // Global function to retry withdrawal
    function retryWithdrawal() {
        console.log('Retry withdrawal function called');
        // Close PIN error modal
        const pinErrorModalElement = document.getElementById('pinErrorModal');
        const pinErrorModalInstance = bootstrap.Modal.getInstance(pinErrorModalElement);
        if (pinErrorModalInstance) {
            console.log('Closing PIN error modal');
            pinErrorModalInstance.hide();
        } else {
            console.log('PIN error modal instance not found');
        }
        
        // Show withdrawal modal again
        setTimeout(() => {
            console.log('Showing withdrawal modal');
            const withdrawalModalElement = document.getElementById('withdrawalModal');
            const withdrawalModalInstance = new bootstrap.Modal(withdrawalModalElement);
            withdrawalModalInstance.show();
        }, 300);
    }

    function viewWithdrawal(id) {
        // Implement view withdrawal details functionality
        alert('View withdrawal details functionality to be implemented');
    }
    </script>
    @endpush
</x-app-layout> 