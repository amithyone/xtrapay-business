<x-app-layout>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">Edit User</h1>
                <p class="text-muted mb-0">Update user information and permissions</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Users
                </a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">User Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('super-admin.users.update', $user) }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" placeholder="Leave blank to keep current password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Leave blank to keep the current password</small>
                                </div>

                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation" placeholder="Confirm new password">
                                </div>
                            </div>

                            <hr class="my-4">

                            <h6 class="mb-3">Permissions</h6>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_admin" name="is_admin" value="1" 
                                               {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_admin">
                                            Admin User
                                        </label>
                                        <small class="form-text text-muted">Gives basic admin privileges</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_super_admin" name="is_super_admin" value="1" 
                                               {{ old('is_super_admin', $user->superAdmin && $user->superAdmin->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_super_admin">
                                            Super Admin
                                        </label>
                                        <small class="form-text text-muted">Gives full system access</small>
                                    </div>
                                </div>
                            </div>

                            <div id="superAdminOptions" class="mt-3" style="display: none;">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="super_admin_role" class="form-label">Super Admin Role</label>
                                        <select class="form-select @error('super_admin_role') is-invalid @enderror" 
                                                id="super_admin_role" name="super_admin_role">
                                            <option value="">Select Role</option>
                                            <option value="super_admin" {{ old('super_admin_role', $user->superAdmin->role ?? '') === 'super_admin' ? 'selected' : '' }}>Super Admin (Full Access)</option>
                                            <option value="admin" {{ old('super_admin_role', $user->superAdmin->role ?? '') === 'admin' ? 'selected' : '' }}>Admin (Limited Access)</option>
                                        </select>
                                        @error('super_admin_role')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Specific Permissions</label>
                                        @php
                                            $currentPermissions = old('super_admin_permissions', $user->superAdmin->permissions ?? []);
                                        @endphp
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="manage_users" name="super_admin_permissions[]" value="manage_users" 
                                                   {{ in_array('manage_users', $currentPermissions) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="manage_users">Manage Users</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="manage_businesses" name="super_admin_permissions[]" value="manage_businesses" 
                                                   {{ in_array('manage_businesses', $currentPermissions) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="manage_businesses">Manage Businesses</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="manage_withdrawals" name="super_admin_permissions[]" value="manage_withdrawals" 
                                                   {{ in_array('manage_withdrawals', $currentPermissions) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="manage_withdrawals">Manage Withdrawals</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="manage_tickets" name="super_admin_permissions[]" value="manage_tickets" 
                                                   {{ in_array('manage_tickets', $currentPermissions) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="manage_tickets">Manage Support Tickets</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="manage_balance" name="super_admin_permissions[]" value="manage_balance" 
                                                   {{ in_array('manage_balance', $currentPermissions) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="manage_balance">Manage Business Balances</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="view_reports" name="super_admin_permissions[]" value="view_reports" 
                                                   {{ in_array('view_reports', $currentPermissions) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="view_reports">View Reports</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('super-admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('is_super_admin').addEventListener('change', function() {
            const superAdminOptions = document.getElementById('superAdminOptions');
            const superAdminRole = document.getElementById('super_admin_role');
            
            if (this.checked) {
                superAdminOptions.style.display = 'block';
                superAdminRole.required = true;
            } else {
                superAdminOptions.style.display = 'none';
                superAdminRole.required = false;
            }
        });

        // Trigger on page load if already checked
        if (document.getElementById('is_super_admin').checked) {
            document.getElementById('superAdminOptions').style.display = 'block';
            document.getElementById('super_admin_role').required = true;
        }
    </script>
</x-app-layout> 