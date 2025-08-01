<x-app-layout>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">Super Admin Dashboard</h1>
                <p class="text-muted mb-0">Manage users, businesses, withdrawals, and support tickets</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('super-admin.users.index') }}" class="btn btn-primary">
                    <i class="fas fa-users me-2"></i>Manage Users
                </a>
                <a href="{{ route('super-admin.businesses.index') }}" class="btn btn-success">
                    <i class="fas fa-building me-2"></i>Manage Businesses
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Total Users</h6>
                                <h2 class="mb-0">{{ number_format($stats['total_users']) }}</h2>
                            </div>
                            <i class="fas fa-users fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Total Businesses</h6>
                                <h2 class="mb-0">{{ number_format($stats['total_businesses']) }}</h2>
                            </div>
                            <i class="fas fa-building fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Total Revenue</h6>
                                <h2 class="mb-0">₦{{ number_format($stats['total_revenue'], 2) }}</h2>
                            </div>
                            <i class="fas fa-chart-line fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Pending Withdrawals</h6>
                                <h2 class="mb-0">₦{{ number_format($stats['pending_withdrawals'], 2) }}</h2>
                            </div>
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Stats Row -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Open Tickets</h6>
                                <h2 class="mb-0">{{ number_format($stats['open_tickets']) }}</h2>
                            </div>
                            <i class="fas fa-ticket-alt fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-secondary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Total Sites</h6>
                                <h2 class="mb-0">{{ number_format($stats['total_sites']) }}</h2>
                            </div>
                            <i class="fas fa-map-marker-alt fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Active Sites</h6>
                                <h2 class="mb-0">{{ number_format($stats['active_sites']) }}</h2>
                            </div>
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <a href="{{ route('super-admin.withdrawals.index') }}" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-money-bill-wave me-2"></i>
                                    Review Withdrawals
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('super-admin.tickets.index') }}" class="btn btn-outline-warning w-100">
                                    <i class="fas fa-ticket-alt me-2"></i>
                                    Support Tickets
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('super-admin.reports.index') }}" class="btn btn-outline-info w-100">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    View Reports
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('super-admin.users.create') }}" class="btn btn-outline-success w-100">
                                    <i class="fas fa-user-plus me-2"></i>
                                    Create User
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="row g-4">
            <!-- Recent Withdrawals -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Recent Withdrawals</h5>
                        <a href="{{ route('super-admin.withdrawals.index') }}" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        @if($recentWithdrawals->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Business</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentWithdrawals as $withdrawal)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <div class="fw-bold">{{ $withdrawal->businessProfile->business_name }}</div>
                                                        <small class="text-muted">{{ $withdrawal->businessProfile->user->name }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>₦{{ number_format($withdrawal->amount, 2) }}</td>
                                            <td>
                                                @if($withdrawal->is_approved)
                                                    <span class="badge bg-success">Approved</span>
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td>{{ $withdrawal->created_at->format('M d, H:i') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center mb-0">No recent withdrawals</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Tickets -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Recent Support Tickets</h5>
                        <a href="{{ route('super-admin.tickets.index') }}" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        @if($recentTickets->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Subject</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentTickets as $ticket)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <div class="fw-bold">{{ $ticket->user->name }}</div>
                                                        <small class="text-muted">{{ $ticket->businessProfile->business_name }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 150px;" title="{{ $ticket->subject }}">
                                                    {{ $ticket->subject }}
                                                </div>
                                            </td>
                                            <td>
                                                @if($ticket->status === 'open')
                                                    <span class="badge bg-danger">Open</span>
                                                @elseif($ticket->status === 'in_progress')
                                                    <span class="badge bg-warning">In Progress</span>
                                                @elseif($ticket->status === 'resolved')
                                                    <span class="badge bg-success">Resolved</span>
                                                @else
                                                    <span class="badge bg-secondary">Closed</span>
                                                @endif
                                            </td>
                                            <td>{{ $ticket->created_at->format('M d, H:i') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center mb-0">No recent tickets</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="row g-4 mt-2">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Transactions</h5>
                    </div>
                    <div class="card-body">
                        @if($recentTransactions->count() > 0)
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Business</th>
                                            <th>Site</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentTransactions as $transaction)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <div class="fw-bold">
                                                            {{ $transaction->businessProfile ? $transaction->businessProfile->business_name : 'N/A' }}
                                                        </div>
                                                        <small class="text-muted">
                                                            {{ $transaction->businessProfile && $transaction->businessProfile->user ? $transaction->businessProfile->user->name : 'N/A' }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $transaction->site ? $transaction->site->name : 'N/A' }}</td>
                                            <td>₦{{ number_format($transaction->amount, 2) }}</td>
                                            <td>
                                                @if($transaction->status === 'success')
                                                    <span class="badge bg-success">Success</span>
                                                @elseif($transaction->status === 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @else
                                                    <span class="badge bg-danger">Failed</span>
                                                @endif
                                            </td>
                                            <td>{{ $transaction->created_at->format('M d, H:i') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center mb-0">No recent transactions</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 