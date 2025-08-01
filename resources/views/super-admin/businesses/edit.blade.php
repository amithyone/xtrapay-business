<x-app-layout>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">Edit Business</h1>
                <p class="text-muted mb-0">{{ $business->business_name }} - Update business details</p>
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
                        <h5 class="card-title mb-0">Business Information</h5>
                    </div>
                    <div class="card-body">
                        <form id="editBusinessForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="business_name" class="form-label">Business Name *</label>
                                    <input type="text" class="form-control" id="business_name" name="business_name" 
                                           value="{{ $business->business_name }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="business_type" class="form-label">Business Type *</label>
                                    <select class="form-select" id="business_type" name="business_type" required>
                                        <option value="">Select Business Type</option>
                                        <option value="Sole Proprietorship" {{ $business->business_type === 'Sole Proprietorship' ? 'selected' : '' }}>Sole Proprietorship</option>
                                        <option value="Partnership" {{ $business->business_type === 'Partnership' ? 'selected' : '' }}>Partnership</option>
                                        <option value="Limited Liability Company" {{ $business->business_type === 'Limited Liability Company' ? 'selected' : '' }}>Limited Liability Company</option>
                                        <option value="Corporation" {{ $business->business_type === 'Corporation' ? 'selected' : '' }}>Corporation</option>
                                        <option value="Non-Profit" {{ $business->business_type === 'Non-Profit' ? 'selected' : '' }}>Non-Profit</option>
                                        <option value="Other" {{ $business->business_type === 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="industry" class="form-label">Industry *</label>
                                    <select class="form-select" id="industry" name="industry" required>
                                        <option value="">Select Industry</option>
                                        <option value="Technology" {{ $business->industry === 'Technology' ? 'selected' : '' }}>Technology</option>
                                        <option value="Finance" {{ $business->industry === 'Finance' ? 'selected' : '' }}>Finance</option>
                                        <option value="Healthcare" {{ $business->industry === 'Healthcare' ? 'selected' : '' }}>Healthcare</option>
                                        <option value="Education" {{ $business->industry === 'Education' ? 'selected' : '' }}>Education</option>
                                        <option value="Retail" {{ $business->industry === 'Retail' ? 'selected' : '' }}>Retail</option>
                                        <option value="Manufacturing" {{ $business->industry === 'Manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                                        <option value="Real Estate" {{ $business->industry === 'Real Estate' ? 'selected' : '' }}>Real Estate</option>
                                        <option value="Entertainment" {{ $business->industry === 'Entertainment' ? 'selected' : '' }}>Entertainment</option>
                                        <option value="Food & Beverage" {{ $business->industry === 'Food & Beverage' ? 'selected' : '' }}>Food & Beverage</option>
                                        <option value="Transportation" {{ $business->industry === 'Transportation' ? 'selected' : '' }}>Transportation</option>
                                        <option value="Other" {{ $business->industry === 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="registration_number" class="form-label">Registration Number</label>
                                    <input type="text" class="form-control" id="registration_number" name="registration_number" 
                                           value="{{ $business->registration_number }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="tax_identification_number" class="form-label">Tax ID Number</label>
                                    <input type="text" class="form-control" id="tax_identification_number" name="tax_identification_number" 
                                           value="{{ $business->tax_identification_number }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="{{ $business->phone }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Business Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="{{ $business->email }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="website" class="form-label">Website</label>
                                    <input type="url" class="form-control" id="website" name="website" 
                                           value="{{ $business->website }}" placeholder="https://example.com">
                                </div>
                                <div class="col-12">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="2">{{ $business->address }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city" 
                                           value="{{ $business->city }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="state" class="form-label">State</label>
                                    <input type="text" class="form-control" id="state" name="state" 
                                           value="{{ $business->state }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="country" class="form-label">Country</label>
                                    <input type="text" class="form-control" id="country" name="country" 
                                           value="{{ $business->country }}">
                                </div>
                                <div class="col-12">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ $business->balance_notes }}</textarea>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_verified" name="is_verified" 
                                               value="1" {{ $business->is_verified ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_verified">
                                            <strong>Verified Business</strong>
                                        </label>
                                        <small class="form-text text-muted d-block">Check this to verify the business</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Business
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
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-success" onclick="toggleVerification()">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ $business->is_verified ? 'Unverify Business' : 'Verify Business' }}
                            </button>
                            <a href="{{ route('super-admin.businesses.show', $business) }}" class="btn btn-outline-info">
                                <i class="fas fa-eye me-2"></i>View Business Details
                            </a>
                            <a href="{{ route('super-admin.businesses.balance', $business) }}" class="btn btn-outline-warning">
                                <i class="fas fa-wallet me-2"></i>Update Balance
                            </a>
                            <a href="{{ route('super-admin.savings.show', $business) }}" class="btn btn-outline-primary">
                                <i class="fas fa-piggy-bank me-2"></i>Manage Savings
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Business Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-6">
                                <small class="text-muted">Verification Status</small>
                                <div>
                                    @if($business->is_verified)
                                        <span class="badge bg-success">Verified</span>
                                    @else
                                        <span class="badge bg-warning">Unverified</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Created</small>
                                <div>{{ $business->created_at->format('M d, Y') }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Last Updated</small>
                                <div>{{ $business->updated_at->format('M d, Y') }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Owner</small>
                                <div>{{ $business->user->name }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('editBusinessForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(`{{ route('super-admin.businesses.update', $business) }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Business updated successfully!');
                    window.location.href = '{{ route("super-admin.businesses.show", $business) }}';
                } else {
                    alert('Error updating business: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating business. Please try again.');
            });
        });

        function toggleVerification() {
            if (confirm('Are you sure you want to {{ $business->is_verified ? "unverify" : "verify" }} this business?')) {
                fetch(`{{ route('super-admin.businesses.toggle-verification', $business) }}`, {
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
    </script>
</x-app-layout> 