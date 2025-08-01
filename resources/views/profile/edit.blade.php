<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    @endpush

    <div class="container py-5">
        <!-- Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
            <div>
                <h1 class="h2 fw-bold mb-1">Profile Management</h1>
                <p class="text-secondary mb-0">Manage your personal information and account settings</p>
            </div>
            <div class="mt-3 mt-md-0 d-flex gap-2">
                <a href="{{ route('dashboard') }}" class="btn btn-primary d-flex align-items-center">
                    <i class="fas fa-arrow-left me-2"></i>Return to Dashboard
                </a>
                <button type="button" class="btn btn-outline-secondary d-flex align-items-center">
                    <i class="far fa-calendar-alt me-2"></i>
                    Last updated: {{ $user->updated_at ? $user->updated_at->format('n/j/Y') : 'Never' }}
                </button>
            </div>
        </div>

        <!-- Success Messages -->
        @if (session('status') === 'profile-updated')
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Success!</strong> Profile updated successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('status') === 'pin-updated')
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Success!</strong> PIN updated successfully for both user and business profile.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('status') === 'pin-created')
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Success!</strong> PIN created successfully for both user and business profile.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('status') === 'password-updated')
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Success!</strong> Password updated successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Profile Information Card -->
        <div class="card mb-4 form-section">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user me-2"></i>Profile Information
                </h5>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('patch')

                    <!-- Profile Photo Section -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="text-center profile-photo-container">
                                <img class="rounded-circle mb-3" 
                                     src="{{ $user->profile_photo_url }}" 
                                     alt="{{ $user->name }}'s profile photo"
                                     style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #e9ecef;">
                                <div class="mb-3">
                                    <label for="profile_photo" class="form-label">Profile Photo</label>
                                    <input type="file" id="profile_photo" name="profile_photo" 
                                           accept="image/*" class="form-control form-control-sm">
                                    <div class="form-text">PNG, JPG, GIF up to 2MB</div>
                                </div>
                                @error('profile_photo')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Full Name *</label>
                                    <input type="text" id="name" name="name" class="form-control" 
                                           value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" id="email" name="email" class="form-control" 
                                           value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="phone_number" class="form-label">Phone Number</label>
                                    <input type="tel" id="phone_number" name="phone_number" class="form-control" 
                                           value="{{ old('phone_number', $user->phone_number) }}">
                                    @error('phone_number')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select id="gender" name="gender" class="form-select">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                                    <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" 
                                           value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}">
                                    @error('date_of_birth')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="bvn" class="form-label">BVN</label>
                                    <input type="text" id="bvn" name="bvn" class="form-control" 
                                           value="{{ old('bvn', $user->bvn) }}" maxlength="11" pattern="[0-9]{11}">
                                    <div class="form-text">11-digit Bank Verification Number</div>
                                    @error('bvn')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div class="border-top pt-4">
                        <h6 class="mb-3"><i class="fas fa-map-marker-alt me-2"></i>Address Information</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" id="address" name="address" class="form-control" 
                                       value="{{ old('address', $user->address) }}">
                                @error('address')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="city" class="form-label">City</label>
                                <input type="text" id="city" name="city" class="form-control" 
                                       value="{{ old('city', $user->city) }}">
                                @error('city')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="state" class="form-label">State</label>
                                <input type="text" id="state" name="state" class="form-control" 
                                       value="{{ old('state', $user->state) }}">
                                @error('state')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="country" class="form-label">Country</label>
                                <input type="text" id="country" name="country" class="form-control" 
                                       value="{{ old('country', $user->country) }}">
                                @error('country')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="postal_code" class="form-label">Postal Code</label>
                                <input type="text" id="postal_code" name="postal_code" class="form-control" 
                                       value="{{ old('postal_code', $user->postal_code) }}">
                                @error('postal_code')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Security Settings Row -->
        <div class="row g-4">
            <!-- PIN Management Card -->
            <div class="col-md-6">
                <div class="card h-100 form-section">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-lock me-2"></i>PIN Management
                        </h5>
                        <small class="text-dark">Changes affect both user and business profile</small>
                    </div>
                    <div class="card-body">
                        @if ($user->pin)
                            <!-- Change PIN Form -->
                            <form method="post" action="{{ route('profile.pin.update') }}">
                                @csrf
                                @method('patch')
                                <div class="mb-3">
                                    <label for="current_pin" class="form-label">Current PIN</label>
                                    <input type="password" id="current_pin" name="current_pin" 
                                           class="form-control" maxlength="4" pattern="[0-9]{4}" required>
                                    <div class="form-text">4-digit PIN</div>
                                    @error('current_pin')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="new_pin" class="form-label">New PIN</label>
                                    <input type="password" id="new_pin" name="new_pin" 
                                           class="form-control" maxlength="4" pattern="[0-9]{4}" required>
                                    @error('new_pin')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_pin" class="form-label">Confirm New PIN</label>
                                    <input type="password" id="confirm_pin" name="confirm_pin" 
                                           class="form-control" maxlength="4" pattern="[0-9]{4}" required>
                                    @error('confirm_pin')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-key me-2"></i>Change PIN
                                </button>
                            </form>
                        @else
                            <!-- Create PIN Form -->
                            <form method="post" action="{{ route('profile.pin.create') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="new_pin" class="form-label">Create PIN</label>
                                    <input type="password" id="new_pin" name="new_pin" 
                                           class="form-control" maxlength="4" pattern="[0-9]{4}" required>
                                    <div class="form-text">4-digit PIN for transactions</div>
                                    @error('new_pin')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_pin" class="form-label">Confirm PIN</label>
                                    <input type="password" id="confirm_pin" name="confirm_pin" 
                                           class="form-control" maxlength="4" pattern="[0-9]{4}" required>
                                    @error('confirm_pin')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-plus me-2"></i>Create PIN
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Password Update Card -->
            <div class="col-md-6">
                <div class="card h-100 form-section">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-shield-alt me-2"></i>Update Password
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('profile.password.update') }}">
                            @csrf
                            @method('patch')
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" id="current_password" name="current_password" 
                                       class="form-control" required>
                                @error('current_password')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" id="password" name="password" 
                                       class="form-control" required>
                                @error('password')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" 
                                       class="form-control" required>
                                @error('password_confirmation')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-info text-white">
                                <i class="fas fa-save me-2"></i>Update Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Account Card -->
        <div class="card mt-4 border-danger form-section">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>Delete Account
                </h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h6 class="text-danger">Permanent Account Deletion</h6>
                        <p class="text-muted mb-0">
                            Once your account is deleted, all of its resources and data will be permanently deleted. 
                            Before deleting your account, please download any data or information that you wish to retain.
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-outline-danger" id="delete-account-btn">
                            <i class="fas fa-trash me-2"></i>Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div class="modal fade" id="confirm-user-deletion" tabindex="-1" aria-labelledby="confirm-user-deletion-label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirm-user-deletion-label">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirm Account Deletion
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> This action cannot be undone.
                        </div>
                        <p class="text-muted">
                            Once your account is deleted, all of its resources and data will be permanently deleted. 
                            Please enter your password to confirm you would like to permanently delete your account.
                        </p>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Enter your password" required>
                            @if($errors->userDeletion->has('password'))
                                <div class="text-danger small mt-1">{{ $errors->userDeletion->first('password') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Delete Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Initialize delete account modal
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButton = document.getElementById('delete-account-btn');
            if (deleteButton) {
                deleteButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    const modal = new bootstrap.Modal(document.getElementById('confirm-user-deletion'));
                    modal.show();
                });
            }
        });
    </script>
</x-app-layout> 