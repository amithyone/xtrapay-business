<x-app-layout>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Create Business Profile</h5>
                    </div>
                    <div class="card-body">
                        <form id="businessProfileForm" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <h6 class="mb-3">Basic Information</h6>
                                    <div class="mb-3">
                                        <label class="form-label">Business Name</label>
                                        <input type="text" class="form-control" name="business_name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Registration Number</label>
                                        <input type="text" class="form-control" name="registration_number" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Tax Identification Number</label>
                                        <input type="text" class="form-control" name="tax_identification_number" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Business Type</label>
                                        <select class="form-control" name="business_type" required>
                                            <option value="">Select Business Type</option>
                                            <option value="sole_proprietorship">Sole Proprietorship</option>
                                            <option value="partnership">Partnership</option>
                                            <option value="corporation">Corporation</option>
                                            <option value="llc">Limited Liability Company (LLC)</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Industry</label>
                                        <input type="text" class="form-control" name="industry" required>
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="col-md-6">
                                    <h6 class="mb-3">Contact Information</h6>
                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <input type="text" class="form-control" name="address" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">City</label>
                                        <input type="text" class="form-control" name="city" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">State</label>
                                        <input type="text" class="form-control" name="state" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Country</label>
                                        <input type="text" class="form-control" name="country" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Phone</label>
                                        <input type="tel" class="form-control" name="phone" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Website (Optional)</label>
                                        <input type="url" class="form-control" name="website">
                                    </div>
                                </div>

                                <!-- Verification Documents -->
                                <div class="col-12 mt-4">
                                    <h6 class="mb-3">Verification Documents</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Verification ID Type</label>
                                                <select class="form-control" name="verification_id_type" required>
                                                    <option value="">Select ID Type</option>
                                                    <option value="passport">Passport</option>
                                                    <option value="national_id">National ID</option>
                                                    <option value="drivers_license">Driver's License</option>
                                                    <option value="voters_card">Voter's Card</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Verification ID Number</label>
                                                <input type="text" class="form-control" name="verification_id_number" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Verification ID Document</label>
                                                <input type="file" class="form-control" name="verification_id_file" accept=".jpeg,.png,.jpg,.pdf" required>
                                                <small class="text-muted">Upload a clear image or PDF of your ID (max 2MB)</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Proof of Address</label>
                                                <input type="file" class="form-control" name="proof_of_address_file" accept=".jpeg,.png,.jpg,.pdf" required>
                                                <small class="text-muted">Upload a recent utility bill or bank statement (max 2MB)</small>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Business Logo (Optional)</label>
                                                <input type="file" class="form-control" name="logo" accept=".jpeg,.png,.jpg">
                                                <small class="text-muted">Upload your business logo (max 2MB)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Create Business Profile</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('businessProfileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Creating...';
            
            fetch('{{ route("business-profile.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.error || 'Failed to create business profile');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }
                if (data.errors) {
                    const errorMessages = Object.values(data.errors).flat();
                    throw new Error(errorMessages.join('\n'));
                }
                
                // Success
                alert('Business profile created successfully!');
                window.location.href = '/business-profile/' + data.profile.id;
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'An error occurred while creating the business profile.');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = 'Create Business Profile';
            });
        });
    </script>
    @endpush
</x-app-layout> 