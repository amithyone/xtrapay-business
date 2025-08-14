<x-app-layout>
    <div class="container py-5">
        <!-- Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
            <div>
                <h1 class="h2 fw-bold mb-1">Transaction Statistics</h1>
                <p class="text-secondary mb-0">Daily transaction insights and payment method analysis</p>
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
                            <span class="fw-semibold small">Daily Successful Transactions</span>
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="fs-5">{{ number_format($stats['daily_successful_transactions']) }}</div>
                        <div class="small">Today's successful count</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-white h-100" style="background: linear-gradient(135deg, rgb(234, 88, 12), rgb(249, 115, 22)); border-radius: 1rem;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="fw-semibold small">Daily Pending Transactions</span>
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="fs-5">{{ number_format($stats['daily_pending_transactions']) }}</div>
                        <div class="small">Today's pending count</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-white h-100" style="background: linear-gradient(135deg, rgb(54, 88, 238), rgb(30, 50, 150)); border-radius: 1rem;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="fw-semibold small">Success Rate</span>
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div class="fs-5">{{ number_format($stats['daily_success_rate'], 1) }}%</div>
                        <div class="small">Today's success rate</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-white h-100" style="background: linear-gradient(135deg, rgb(150, 50, 161), rgb(100, 30, 110)); border-radius: 1rem;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="fw-semibold small">Total Processed</span>
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="fs-5">₦{{ number_format($stats['daily_total_processed'], 2) }}</div>
                        <div class="small">Today's total amount</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Statistics -->
        <div class="row g-4 mb-4">
            <div class="col-6 col-md-6">
                <div class="card text-white h-100" style="background: linear-gradient(135deg, rgb(16, 185, 129), rgb(5, 150, 105)); border-radius: 1rem;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="fw-semibold small">Monthly Successful Transactions</span>
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="fs-5">{{ number_format($stats['monthly_successful_transactions']) }}</div>
                        <div class="small">This month's successful count</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-6">
                <div class="card text-white h-100" style="background: linear-gradient(135deg, rgb(59, 130, 246), rgb(37, 99, 235)); border-radius: 1rem;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="fw-semibold small">Monthly Total Processed</span>
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="fs-5">₦{{ number_format($stats['monthly_total_processed'], 2) }}</div>
                        <div class="small">This month's total amount</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-4 mb-4">
            <!-- Transaction Distribution Pie Chart -->
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Transaction Distribution (Today)
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="transactionDistributionChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Pending vs Successful Daily Comparison -->
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Pending vs Successful (Daily)
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="pendingVsSuccessfulChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Method Processing -->
        <div class="row g-4 mb-4">
            <!-- Daily Payment Method Processing -->
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-credit-card me-2"></i>Payment Method Processing (Today)
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="dailyPaymentMethodChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Past 7 Days Payment Method Processing -->
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i>Payment Method Processing (Past 7 Days)
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="weeklyPaymentMethodChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Method Summary Table -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-table me-2"></i>Payment Method Summary
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Payment Method</th>
                                <th>Today's Transactions</th>
                                <th>Today's Amount</th>
                                <th>Past 7 Days Transactions</th>
                                <th>Past 7 Days Amount</th>
                                <th>Success Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paymentMethodSummary as $method)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <i class="fas {{ $method['icon'] }} text-primary"></i>
                                            </div>
                                            <div class="fw-semibold">{{ $method['name'] }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ number_format($method['today_transactions']) }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-success">₦{{ number_format($method['today_amount'], 2) }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ number_format($method['week_transactions']) }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-success">₦{{ number_format($method['week_amount'], 2) }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress me-2" style="width: 60px; height: 6px;">
                                                <div class="progress-bar bg-success" style="width: {{ $method['success_rate'] }}%"></div>
                                            </div>
                                            <span class="small">{{ number_format($method['success_rate'], 1) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-chart-bar fs-1 mb-3"></i>
                                            <p>No payment method data available</p>
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Color scheme
        const colors = {
            primary: '#3b82f6',
            success: '#10b981',
            warning: '#f59e0b',
            danger: '#ef4444',
            info: '#06b6d4',
            secondary: '#6b7280'
        };

        // Transaction Distribution Pie Chart
        const transactionDistributionCtx = document.getElementById('transactionDistributionChart').getContext('2d');
        const transactionDistributionData = @json($chartData['transaction_distribution'] ?? []);

        if (transactionDistributionData && transactionDistributionData.length > 0) {
            new Chart(transactionDistributionCtx, {
                type: 'doughnut',
                data: {
                    labels: transactionDistributionData.map(item => item.status),
                    datasets: [{
                        data: transactionDistributionData.map(item => item.count),
                        backgroundColor: [colors.success, colors.warning, colors.danger]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} transactions (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Pending vs Successful Daily Chart
        const pendingVsSuccessfulCtx = document.getElementById('pendingVsSuccessfulChart').getContext('2d');
        const pendingVsSuccessfulData = @json($chartData['pending_vs_successful'] ?? []);

        if (pendingVsSuccessfulData && pendingVsSuccessfulData.length > 0) {
            new Chart(pendingVsSuccessfulCtx, {
                type: 'bar',
                data: {
                    labels: pendingVsSuccessfulData.map(item => item.date),
                    datasets: [
                        {
                            label: 'Successful',
                            data: pendingVsSuccessfulData.map(item => item.successful),
                            backgroundColor: colors.success,
                            borderColor: colors.success,
                            borderWidth: 1
                        },
                        {
                            label: 'Pending',
                            data: pendingVsSuccessfulData.map(item => item.pending),
                            backgroundColor: colors.warning,
                            borderColor: colors.warning,
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    }
                }
            });
        }

        // Daily Payment Method Processing Chart
        const dailyPaymentMethodCtx = document.getElementById('dailyPaymentMethodChart').getContext('2d');
        const dailyPaymentMethodData = @json($chartData['daily_payment_methods'] ?? []);

        if (dailyPaymentMethodData && dailyPaymentMethodData.length > 0) {
            new Chart(dailyPaymentMethodCtx, {
                type: 'bar',
                data: {
                    labels: dailyPaymentMethodData.map(item => item.method),
                    datasets: [{
                        label: 'Amount Processed (₦)',
                        data: dailyPaymentMethodData.map(item => item.amount),
                        backgroundColor: [colors.primary, colors.success, colors.warning, colors.danger],
                        borderColor: [colors.primary, colors.success, colors.warning, colors.danger],
                        borderWidth: 1
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
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return '₦' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        // Weekly Payment Method Processing Chart
        const weeklyPaymentMethodCtx = document.getElementById('weeklyPaymentMethodChart').getContext('2d');
        const weeklyPaymentMethodData = @json($chartData['weekly_payment_methods'] ?? []);

        if (weeklyPaymentMethodData && weeklyPaymentMethodData.length > 0) {
            new Chart(weeklyPaymentMethodCtx, {
                type: 'line',
                data: {
                    labels: weeklyPaymentMethodData.labels,
                    datasets: weeklyPaymentMethodData.datasets
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
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ₦' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        // Date range functions
        function setDateRange(days) {
            const today = new Date();
            const fromDate = new Date(today);
            fromDate.setDate(today.getDate() - days);
            
            document.getElementById('date_from').value = fromDate.toISOString().split('T')[0];
            document.getElementById('date_to').value = today.toISOString().split('T')[0];
            document.getElementById('dateRangeForm').submit();
        }

        function exportReport() {
            // Implement export functionality
            alert('Export functionality will be implemented here');
        }
    </script>
</x-app-layout> 