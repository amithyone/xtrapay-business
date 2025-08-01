<x-app-layout>
    <div class="container py-5">
        <!-- Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
            <div>
                <h1 class="h2 fw-bold mb-1">Transaction Management</h1>
                <p class="text-secondary mb-0">Monitor and manage all your payment transactions</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary d-flex align-items-center">
                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-6 col-md-3">
                <div class="card text-white h-100" style="background: linear-gradient(135deg, rgb(24, 164, 75), rgb(34, 197, 94)); border-radius: 1rem;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="fw-semibold small">Total Transactions</span>
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="fs-5">{{ number_format($stats['total_transactions']) }}</div>
                        <div class="small">All time</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-white h-100" style="background: linear-gradient(135deg, rgb(54, 88, 238), rgb(30, 50, 150)); border-radius: 1rem;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="fw-semibold small">Successful Amount</span>
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="fs-5">₦{{ number_format($stats['successful_amount'], 2) }}</div>
                        <div class="small">All time</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-white h-100" style="background: linear-gradient(135deg, rgb(150, 50, 161), rgb(100, 30, 110)); border-radius: 1rem;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="fw-semibold small">This Month</span>
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="fs-5">₦{{ number_format($stats['monthly_amount'], 2) }}</div>
                        <div class="small">{{ number_format($stats['monthly_transactions']) }} transactions</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-white h-100" style="background: linear-gradient(135deg, rgb(234, 88, 12), rgb(249, 115, 22)); border-radius: 1rem;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="fw-semibold small">Today</span>
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="fs-5">₦{{ number_format($stats['daily_amount'], 2) }}</div>
                        <div class="small">{{ number_format($stats['daily_transactions']) }} transactions</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Summary Cards -->
        <div class="row g-4 mb-4">
            <div class="col-4">
                <div class="card border-success h-100">
                    <div class="card-body text-center">
                        <div class="text-success mb-2">
                            <i class="fas fa-check-circle fs-1"></i>
                        </div>
                        <h5 class="card-title text-success">{{ number_format($stats['successful_transactions']) }}</h5>
                        <p class="card-text text-muted">Successful</p>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card border-warning h-100">
                    <div class="card-body text-center">
                        <div class="text-warning mb-2">
                            <i class="fas fa-clock fs-1"></i>
                        </div>
                        <h5 class="card-title text-warning">{{ number_format($stats['pending_transactions']) }}</h5>
                        <p class="card-text text-muted">Pending</p>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card border-danger h-100">
                    <div class="card-body text-center">
                        <div class="text-danger mb-2">
                            <i class="fas fa-times-circle fs-1"></i>
                        </div>
                        <h5 class="card-title text-danger">{{ number_format($stats['failed_transactions']) }}</h5>
                        <p class="card-text text-muted">Failed</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Site Distribution Chart -->
        @if(!empty($stats['site_distribution']))
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Payment Gateway Distribution
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <canvas id="siteDistributionChart" width="400" height="200"></canvas>
                            </div>
                            <div class="col-md-4">
                                <div class="site-legend">
                                    @foreach($stats['site_distribution'] as $index => $site)
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="legend-color me-2" style="width: 20px; height: 20px; background-color: {{ $colors[$index] ?? '#'.substr(md5($site['site_name']), 0, 6) }}; border-radius: 4px;"></div>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold">{{ $site['site_name'] }}</div>
                                            <div class="text-muted small">
                                                ₦{{ number_format($site['total_amount'], 2) }} 
                                                ({{ $site['transaction_count'] }} transactions)
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="fas fa-filter me-2"></i>Filters
                </h5>
                <form method="GET" action="{{ route('transactions.index') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Reference, email, name...">
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Success</option>
                                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="site_id" class="form-label">Site</label>
                            <select class="form-select" id="site_id" name="site_id">
                                <option value="">All Sites</option>
                                @foreach($sites as $site)
                                    <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>
                                        {{ $site->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>Transactions
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportTransactions()">
                        <i class="fas fa-download me-1"></i>Export
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="refreshTable()">
                        <i class="fas fa-sync-alt me-1"></i>Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="d-none d-md-table-cell">Reference</th>
                                <th>Site</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th class="d-none d-md-table-cell">Payment Method</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td class="d-none d-md-table-cell">
                                        <div class="fw-semibold">{{ $transaction->reference }}</div>
                                        @if($transaction->external_id)
                                            <small class="text-muted">ID: {{ $transaction->external_id }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-primary me-2">{{ $transaction->site->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-success">₦{{ number_format($transaction->amount, 2) }}</div>
                                        <small class="text-muted d-none d-md-inline">{{ strtoupper($transaction->currency ?? 'NGN') }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'success' => 'bg-success',
                                                'pending' => 'bg-warning',
                                                'failed' => 'bg-danger',
                                                'abandoned' => 'bg-secondary'
                                            ];
                                            $statusColor = $statusColors[$transaction->status] ?? 'bg-secondary';
                                        @endphp
                                        <span class="badge {{ $statusColor }}">{{ ucfirst($transaction->status) }}</span>
                                        @if($transaction->status === 'pending')
                                            <br><small class="text-muted">{{ $transaction->age_for_humans }}</small>
                                            @if($transaction->should_be_abandoned)
                                                <br><small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Should be abandoned</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <div class="text-capitalize">{{ $transaction->payment_method }}</div>
                                    </td>
                                    <td>
                                        @if($transaction->customer_email)
                                            <div class="d-none d-md-block">{{ $transaction->customer_email }}</div>
                                            <div class="d-block d-md-none">
                                                @if($transaction->customer_name)
                                                    <div class="fw-semibold">{{ $transaction->customer_name }}</div>
                                                    <small class="text-muted">{{ $transaction->customer_email }}</small>
                                                @else
                                                    <div>{{ $transaction->customer_email }}</div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-none d-md-block">{{ $transaction->created_at->format('M d, Y') }}</div>
                                        <div class="d-block d-md-none">
                                            <div class="fw-semibold">{{ $transaction->created_at->format('M d') }}</div>
                                            <small class="text-muted">{{ $transaction->created_at->format('H:i') }}</small>
                                        </div>
                                        <small class="text-muted d-none d-md-inline">{{ $transaction->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary" 
                                                    onclick="viewTransaction({{ $transaction->id }})" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 d-md-none">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fs-1 mb-3"></i>
                                            <p>No transactions found</p>
                                            <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                                                Create Your First Transaction
                                            </a>
                                        </div>
                                    </td>
                                    <td colspan="8" class="text-center py-4 d-none d-md-table-cell">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fs-1 mb-3"></i>
                                            <p>No transactions found</p>
                                            <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                                                Create Your First Transaction
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} 
                        of {{ $transactions->total() }} results
                    </div>
                    <div>
                        {{ $transactions->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Details Modal -->
    <div class="modal fade" id="transactionDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-receipt me-2"></i>
                        Transaction Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="transactionDetailsContent">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading transaction details...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function viewTransaction(transactionId) {
            // Show the modal with loading state
            const modal = new bootstrap.Modal(document.getElementById('transactionDetailsModal'));
            modal.show();
            
            // Fetch transaction details
            fetch(`/transactions/${transactionId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Transaction data:', data);
                    if (data.transaction) {
                        const transaction = data.transaction;
                        const content = document.getElementById('transactionDetailsContent');
                        
                        content.innerHTML = `
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3">Basic Information</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-semibold">Reference:</td>
                                            <td>${transaction.reference || 'N/A'}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">External ID:</td>
                                            <td>${transaction.external_id || 'N/A'}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Site:</td>
                                            <td>${transaction.site ? transaction.site.name : 'N/A'}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Amount:</td>
                                            <td class="fw-bold text-success">₦${parseFloat(transaction.amount || 0).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Currency:</td>
                                            <td>${transaction.currency || 'NGN'}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Status:</td>
                                            <td><span class="badge ${getStatusBadgeClass(transaction.status)}">${(transaction.status || 'unknown').toUpperCase()}</span></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3">Payment Details</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-semibold">Payment Method:</td>
                                            <td>${transaction.payment_method || 'N/A'}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Customer Email:</td>
                                            <td>${transaction.customer_email || 'N/A'}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Customer Name:</td>
                                            <td>${transaction.customer_name || 'N/A'}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Created:</td>
                                            <td>${transaction.created_at ? new Date(transaction.created_at).toLocaleString() : 'N/A'}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Updated:</td>
                                            <td>${transaction.updated_at ? new Date(transaction.updated_at).toLocaleString() : 'N/A'}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            ${transaction.metadata ? `
                            <div class="mt-4">
                                <h6 class="fw-bold mb-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Transaction Details
                                </h6>
                                <div class="bg-light p-3 rounded">
                                    ${formatTransactionDetails(transaction.metadata)}
                                </div>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('${escapeForClipboard(formatJSON(transaction.metadata))}')">
                                        <i class="fas fa-copy me-1"></i> Copy Full JSON
                                    </button>
                                </div>
                            </div>
                            ` : `
                            <div class="mt-4">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    No transaction details available.
                                </div>
                            </div>
                            `}
                        `;
                    } else {
                        document.getElementById('transactionDetailsContent').innerHTML = `
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                No transaction data found. Response: ${JSON.stringify(data)}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error fetching transaction details:', error);
                    document.getElementById('transactionDetailsContent').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error loading transaction details: ${error.message}
                            <br><small>Please check the browser console for more details.</small>
                        </div>
                    `;
                });
        }

        function getStatusBadgeClass(status) {
            const statusClasses = {
                'success': 'bg-success',
                'pending': 'bg-warning',
                'failed': 'bg-danger',
                'abandoned': 'bg-secondary'
            };
            return statusClasses[status] || 'bg-secondary';
        }

        function formatJSON(data) {
            try {
                if (typeof data === 'string') {
                    return JSON.stringify(JSON.parse(data), null, 2);
                } else {
                    return JSON.stringify(data, null, 2);
                }
            } catch (error) {
                return JSON.stringify(data, null, 2);
            }
        }

        function formatTransactionDetails(metadata) {
            try {
                let data = metadata;
                if (typeof metadata === 'string') {
                    data = JSON.parse(metadata);
                }
                
                const fields = [
                    { key: 'reference', label: 'Reference', icon: 'fas fa-hashtag' },
                    { key: 'virtual_account', label: 'Virtual Account', icon: 'fas fa-credit-card' },
                    { key: 'bank_name', label: 'Bank Name', icon: 'fas fa-university' },
                    { key: 'account_name', label: 'Account Name', icon: 'fas fa-user' }
                ];
                
                const extraDataFields = [
                    { key: 'sourceAccountNumber', label: 'Source Account Number', icon: 'fas fa-account-number' },
                    { key: 'sourceAccountName', label: 'Source Account Name', icon: 'fas fa-user-tie' },
                    { key: 'sessionId', label: 'Session ID', icon: 'fas fa-id-badge' }
                ];
                
                let html = '<div class="row">';
                
                // Main fields
                html += '<div class="col-md-6"><h6 class="fw-bold mb-3">Basic Information</h6>';
                fields.forEach(field => {
                    const value = data[field.key];
                    if (value) {
                        html += `
                            <div class="mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="${field.icon} me-2 text-muted" style="width: 16px;"></i>
                                    <span class="fw-semibold me-2">${field.label}:</span>
                                </div>
                                <div class="ms-4 text-break">${value}</div>
                            </div>
                        `;
                    }
                });
                html += '</div>';
                
                // Extra data fields
                html += '<div class="col-md-6"><h6 class="fw-bold mb-3">Payment Details</h6>';
                if (data.extra_data) {
                    extraDataFields.forEach(field => {
                        const value = data.extra_data[field.key];
                        if (value) {
                            html += `
                                <div class="mb-2">
                                    <div class="d-flex align-items-center">
                                        <i class="${field.icon} me-2 text-muted" style="width: 16px;"></i>
                                        <span class="fw-semibold me-2">${field.label}:</span>
                                    </div>
                                    <div class="ms-4 text-break">${value}</div>
                                </div>
                            `;
                        }
                    });
                } else {
                    html += '<div class="text-muted">No additional payment details available</div>';
                }
                html += '</div>';
                
                html += '</div>';
                return html;
                
            } catch (error) {
                return '<div class="text-danger">Error parsing transaction details</div>';
            }
        }

        function escapeForClipboard(text) {
            return text.replace(/'/g, "\\'").replace(/\n/g, '\\n').replace(/\r/g, '\\r');
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show a temporary success message
                const button = event.target.closest('button');
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check me-1"></i> Copied!';
                button.classList.remove('btn-outline-secondary');
                button.classList.add('btn-success');
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-secondary');
                }, 2000);
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
                alert('Failed to copy to clipboard');
            });
        }

        function exportTransactions() {
            const form = document.getElementById('filterForm');
            const originalAction = form.action;
            form.action = '{{ route("transactions.index") }}?export=1';
            form.submit();
            form.action = originalAction;
        }

        function refreshTable() {
            location.reload();
        }

        // Auto-submit form when filters change
        document.addEventListener('DOMContentLoaded', function() {
            const filterInputs = document.querySelectorAll('#filterForm select, #filterForm input[type="date"]');
            filterInputs.forEach(input => {
                input.addEventListener('change', function() {
                    document.getElementById('filterForm').submit();
                });
            });

            // Initialize pie chart if data exists
            @if(!empty($stats['site_distribution']))
            initializeSiteDistributionChart();
            @endif
        });

        // Initialize site distribution pie chart
        function initializeSiteDistributionChart() {
            const ctx = document.getElementById('siteDistributionChart').getContext('2d');
            
            const chartData = @json($stats['site_distribution']);
            const colors = [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
                '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
            ];

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: chartData.map(site => site.site_name),
                    datasets: [{
                        data: chartData.map(site => site.total_amount),
                        backgroundColor: colors.slice(0, chartData.length),
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // We have custom legend
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const site = chartData[context.dataIndex];
                                    return [
                                        site.site_name,
                                        '₦' + Number(site.total_amount).toLocaleString('en-NG', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        }),
                                        site.transaction_count + ' transactions'
                                    ];
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
    @endpush

    <style>
    /* Mobile-specific table styles */
    @media (max-width: 767.98px) {
        .table-responsive {
            border: none;
        }
        
        .table {
            font-size: 0.875rem;
        }
        
        .table th,
        .table td {
            padding: 0.5rem 0.25rem;
            vertical-align: middle;
        }
        
        .table th {
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge {
            font-size: 0.7rem;
        }
        
        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        
        /* Ensure customer info fits better on mobile */
        .table td:nth-child(5) {
            max-width: 120px;
            word-wrap: break-word;
        }
        
        /* Ensure date fits better on mobile */
        .table td:nth-child(6) {
            min-width: 60px;
        }
    }
    </style>
</x-app-layout> 