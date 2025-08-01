<x-app-layout>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">Reports & Analytics</h1>
                <p class="text-muted mb-0">Comprehensive system reports and analytics</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('super-admin.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Overall Statistics -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Total Users</h6>
                                <h2 class="mb-0">{{ number_format($overallStats['total_users']) }}</h2>
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
                                <h2 class="mb-0">{{ number_format($overallStats['total_businesses']) }}</h2>
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
                                <h2 class="mb-0">₦{{ number_format($overallStats['total_revenue'], 2) }}</h2>
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
                                <h2 class="mb-0">₦{{ number_format($overallStats['pending_withdrawals'], 2) }}</h2>
                            </div>
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Stats -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">Open Tickets</h6>
                                <h2 class="mb-0">{{ number_format($overallStats['open_tickets']) }}</h2>
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
                                <h6 class="card-title mb-1">Total Withdrawals</h6>
                                <h2 class="mb-0">₦{{ number_format($overallStats['total_withdrawals'], 2) }}</h2>
                            </div>
                            <i class="fas fa-money-bill-wave fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">System Health</h6>
                                <h2 class="mb-0">Good</h2>
                            </div>
                            <i class="fas fa-heartbeat fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-4 mb-4">
            <!-- Monthly Revenue Chart -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Monthly Revenue ({{ date('Y') }})</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyRevenueChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- System Activity -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent System Activity</h5>
                    </div>
                    <div class="card-body">
                        <div class="activity-timeline">
                            @foreach($recentActivities->take(10) as $activity)
                            <div class="activity-item d-flex align-items-start mb-3">
                                <div class="activity-icon me-3">
                                    @if($activity['type'] === 'transaction')
                                        <i class="fas fa-exchange-alt text-success"></i>
                                    @elseif($activity['type'] === 'withdrawal')
                                        <i class="fas fa-money-bill-wave text-warning"></i>
                                    @elseif($activity['type'] === 'ticket')
                                        <i class="fas fa-ticket-alt text-danger"></i>
                                    @endif
                                </div>
                                <div class="activity-content flex-grow-1">
                                    <div class="fw-bold">{{ $activity['title'] }}</div>
                                    <div class="text-muted small">{{ $activity['description'] }}</div>
                                    <div class="text-muted small">{{ $activity['time']->format('M d, H:i') }}</div>
                                </div>
                                <div class="activity-status">
                                    @if($activity['status'] === 'success' || $activity['status'] === 'approved')
                                        <span class="badge bg-success">Success</span>
                                    @elseif($activity['status'] === 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($activity['status'] === 'open')
                                        <span class="badge bg-danger">Open</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($activity['status']) }}</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Reports -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-users me-2"></i>User Report
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('super-admin.businesses.index') }}" class="btn btn-outline-success w-100">
                                    <i class="fas fa-building me-2"></i>Business Report
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('super-admin.withdrawals.index') }}" class="btn btn-outline-warning w-100">
                                    <i class="fas fa-money-bill-wave me-2"></i>Withdrawal Report
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('super-admin.tickets.index') }}" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-ticket-alt me-2"></i>Ticket Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">System Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="fw-bold text-primary">{{ number_format($overallStats['total_users']) }}</div>
                                    <small class="text-muted">Total Users</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="fw-bold text-success">{{ number_format($overallStats['total_businesses']) }}</div>
                                    <small class="text-muted">Active Businesses</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="fw-bold text-info">₦{{ number_format($overallStats['total_revenue'], 2) }}</div>
                                    <small class="text-muted">Total Revenue</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="fw-bold text-warning">₦{{ number_format($overallStats['pending_withdrawals'], 2) }}</div>
                                    <small class="text-muted">Pending Withdrawals</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Monthly Revenue Chart
        const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
        const monthlyRevenueChart = new Chart(monthlyRevenueCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_keys($monthlyRevenue->toArray())) !!},
                datasets: [{
                    label: 'Monthly Revenue (₦)',
                    data: {!! json_encode(array_values($monthlyRevenue->toArray())) !!},
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₦' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: ₦' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout> 