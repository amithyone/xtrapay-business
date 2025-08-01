<x-app-layout>
    <div class="container py-5">
        <!-- Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
            <div>
                <h1 class="h2 fw-bold mb-1">Analytics & Statistics</h1>
                <p class="text-secondary mb-0">Comprehensive insights into your business performance</p>
            </div>
            <div class="mt-3 mt-md-0 d-flex gap-2">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary d-flex align-items-center">
                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                </a>
                <button type="button" class="btn btn-outline-primary d-flex align-items-center" onclick="exportReport()">
                    <i class="fas fa-download me-2"></i> Export Report
                </button>
            </div>
        </div>

        <!-- Date Range Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('statistics.index') }}" id="dateRangeForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ $dateFrom }}" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="{{ $dateTo }}" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange(7)">Last 7 Days</button>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange(30)">Last 30 Days</button>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange(90)">Last 90 Days</button>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange(365)">Last Year</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="row g-4 mb-4">
            <div class="col-6 col-md-3">
                <div class="card text-white h-100" style="background: linear-gradient(135deg, rgb(24, 164, 75), rgb(34, 197, 94)); border-radius: 1rem;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="fw-semibold small">Successful Revenue</span>
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="fs-5">₦{{ number_format($stats['successful_amount'], 2) }}</div>
                        <div class="small">{{ $stats['date_range']['from'] }} - {{ $stats['date_range']['to'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-white h-100" style="background: linear-gradient(135deg, rgb(54, 88, 238), rgb(30, 50, 150)); border-radius: 1rem;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="fw-semibold small">Total Transactions</span>
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="fs-5">{{ number_format($stats['total_transactions']) }}</div>
                        <div class="small">{{ number_format($stats['daily_average_transactions'], 1) }} avg/day</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-white h-100" style="background: linear-gradient(135deg, rgb(150, 50, 161), rgb(100, 30, 110)); border-radius: 1rem;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="fw-semibold small">Success Rate</span>
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div class="fs-5">{{ number_format($stats['success_rate'], 1) }}%</div>
                        <div class="small">{{ number_format($stats['successful_transactions']) }} successful</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-white h-100" style="background: linear-gradient(135deg, rgb(234, 88, 12), rgb(249, 115, 22)); border-radius: 1rem;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="fw-semibold small">Pending Amount</span>
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="fs-5">₦{{ number_format($stats['pending_amount'] ?? 0, 2) }}</div>
                        <div class="small">{{ number_format($stats['pending_transactions']) }} pending</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Information Note -->
        <div class="alert alert-info mb-4" role="alert">
            <div class="d-flex align-items-start">
                <i class="fas fa-info-circle me-2 mt-1"></i>
                <div>
                    <strong>Revenue Calculation:</strong> The "Successful Revenue" shows only completed transactions, while "Pending Amount" shows transactions that are still being processed. This provides a more accurate picture of your actual earnings.
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-4 mb-4">
            <!-- Daily Revenue Chart -->
            <div class="col-12 col-lg-8">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-area me-2"></i>Daily Revenue Trend
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="dailyRevenueChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Payment Methods Chart -->
            <div class="col-12 col-lg-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Payment Methods
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="paymentMethodsChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Charts Row -->
        <div class="row g-4 mb-4">
            <!-- Status Distribution -->
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-doughnut me-2"></i>Transaction Status
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <!-- Hourly Distribution -->
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clock me-2"></i>Hourly Activity
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="hourlyChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Site Performance Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-trophy me-2"></i>Site Performance
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Site</th>
                                <th>Status</th>
                                <th>Transactions</th>
                                <th>Successful Revenue</th>
                                <th>Success Rate</th>
                                <th>Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sitePerformance as $site)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <i class="fas fa-globe text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $site['name'] }}</div>
                                                <small class="text-muted">{{ $site['url'] }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $site['is_active'] ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $site['is_active'] ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ number_format($site['total_transactions']) }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-success">₦{{ number_format($site['successful_amount'], 2) }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress me-2" style="width: 60px; height: 6px;">
                                                <div class="progress-bar bg-success" style="width: {{ $site['success_rate'] }}%"></div>
                                            </div>
                                            <span class="small">{{ number_format($site['success_rate'], 1) }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $performance = $site['successful_amount'] > 0 ? 'High' : 'Low';
                                            $performanceColor = $site['successful_amount'] > 100000 ? 'success' : ($site['successful_amount'] > 10000 ? 'warning' : 'danger');
                                        @endphp
                                        <span class="badge bg-{{ $performanceColor }}">{{ $performance }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-chart-bar fs-1 mb-3"></i>
                                            <p>No site performance data available</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart colors
        const colors = {
            primary: '#3b82f6',
            success: '#10b981',
            warning: '#f59e0b',
            danger: '#ef4444',
            info: '#06b6d4',
            purple: '#8b5cf6',
            pink: '#ec4899'
        };

        // Initialize charts when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
        });

        function initializeCharts() {
            // Daily Revenue Chart
            const dailyRevenueCtx = document.getElementById('dailyRevenueChart').getContext('2d');
            new Chart(dailyRevenueCtx, {
                type: 'line',
                data: {
                    labels: @json($chartData['daily_revenue']->pluck('date')),
                    datasets: [{
                        label: 'Revenue (₦)',
                        data: @json($chartData['daily_revenue']->pluck('amount')),
                        borderColor: colors.primary,
                        backgroundColor: colors.primary + '20',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₦' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            // Payment Methods Chart
            const paymentMethodsCtx = document.getElementById('paymentMethodsChart').getContext('2d');
            new Chart(paymentMethodsCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($chartData['payment_methods']->pluck('method')),
                    datasets: [{
                        data: @json($chartData['payment_methods']->pluck('count')),
                        backgroundColor: [colors.primary, colors.success, colors.warning, colors.danger, colors.info]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Status Distribution Chart
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($chartData['status_distribution']->pluck('status')),
                    datasets: [{
                        data: @json($chartData['status_distribution']->pluck('count')),
                        backgroundColor: [colors.success, colors.warning, colors.danger]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Hourly Distribution Chart
            const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
            new Chart(hourlyCtx, {
                type: 'bar',
                data: {
                    labels: @json($chartData['hourly_distribution']->pluck('hour')),
                    datasets: [{
                        label: 'Transactions',
                        data: @json($chartData['hourly_distribution']->pluck('count')),
                        backgroundColor: colors.info,
                        borderColor: colors.info,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        function setDateRange(days) {
            const today = new Date();
            const fromDate = new Date(today.getTime() - (days * 24 * 60 * 60 * 1000));
            
            document.getElementById('date_from').value = fromDate.toISOString().split('T')[0];
            document.getElementById('date_to').value = today.toISOString().split('T')[0];
            
            document.getElementById('dateRangeForm').submit();
        }

        function exportReport() {
            const dateFrom = document.getElementById('date_from').value;
            const dateTo = document.getElementById('date_to').value;
            const url = `{{ route('statistics.index') }}?date_from=${dateFrom}&date_to=${dateTo}&export=1`;
            window.open(url, '_blank');
        }
    </script>
    @endpush
</x-app-layout> 