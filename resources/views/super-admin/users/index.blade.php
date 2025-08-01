<x-app-layout>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">User Management</h1>
                <p class="text-muted mb-0">Manage all users and their permissions</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('super-admin.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
                <a href="{{ route('super-admin.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create User
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('super-admin.users.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Search by name or email">
                    </div>
                    <div class="col-md-3">
                        <label for="admin_status" class="form-label">Admin Status</label>
                        <select class="form-select" id="admin_status" name="admin_status">
                            <option value="">All Users</option>
                            <option value="admin" {{ request('admin_status') === 'admin' ? 'selected' : '' }}>Admins Only</option>
                            <option value="user" {{ request('admin_status') === 'user' ? 'selected' : '' }}>Regular Users</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                        <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Users ({{ $users->total() }})</h5>
            </div>
            <div class="card-body">
                @if($users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Business Profile</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                @if($user->profile_photo)
                                                    <img src="{{ $user->profile_photo_url }}" class="rounded-circle" width="40" height="40">
                                                @else
                                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $user->name }}</div>
                                                @if($user->businessProfile)
                                                    <small class="text-muted">{{ $user->businessProfile->business_name }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->businessProfile)
                                            <span class="badge bg-success">Complete</span>
                                        @else
                                            <span class="badge bg-warning">Incomplete</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->isSuperAdmin())
                                            <span class="badge bg-danger">Super Admin</span>
                                        @elseif($user->is_admin)
                                            <span class="badge bg-primary">Admin</span>
                                        @else
                                            <span class="badge bg-secondary">User</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->email_verified_at)
                                            <span class="badge bg-success">Verified</span>
                                        @else
                                            <span class="badge bg-warning">Unverified</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('super-admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($user->businessProfile)
                                                <a href="{{ route('super-admin.businesses.show', $user->businessProfile) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-building"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $users->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Users Found</h4>
                        <p class="text-muted">No users match your search criteria.</p>
                        <a href="{{ route('super-admin.users.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create First User
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout> 