<x-app-layout>
    <div class="container py-5 pb-5 pb-md-4">
        @if(!$businessProfile)
        <div class="alert alert-info mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle me-3 fs-4"></i>
                <div>
                    <h5 class="alert-heading mb-1">Complete Your Business Profile</h5>
                    <p class="mb-0">To start accepting payments and managing your sites, please complete your business profile.</p>
                    <a href="{{ route('business-profile.create') }}" class="btn btn-primary mt-3">Create Business Profile</a>
                </div>
            </div>
        </div>
        @endif

        <!-- Stat Cards Row -->
        <div class="row g-3 g-md-4 mb-4">
            <div class="col-6 col-md-3">
                <div class="card text-white h-100" style="background: linear-gradient(135deg, rgb(24, 164, 75), rgb(34, 197, 94)); border-radius: 1rem;">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="fw-semibold small d-none d-md-block">Total Balance</span>
                            <span class="fw-semibold small d-block d-md-none">Balance</span>
                            <span class="fs-5 d-none d-md-block">₦</span>
                        </div>
                        <div class="fs-5" style="font-size: 16px;">₦{{ number_format($totalBalance ?? 0) }}</div>
                        <div class="small d-none d-md-block">All sites combined</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-white h-100" style="background: linear-gradient(135deg, rgb(54, 88, 238), rgb(30, 50, 150)); border-radius: 1rem;">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="fw-semibold small d-none d-md-block">Daily Revenue</span>
                            <span class="fw-semibold small d-block d-md-none">Today</span>
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="fs-5" style="font-size: 16px;">₦{{ number_format($dailyRevenue ?? 0) }}</div>
                        <div class="small d-none d-md-block">Successful transactions only</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-white h-100" style="background: linear-gradient(135deg, rgb(150, 50, 161), rgb(100, 30, 110)); border-radius: 1rem;">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="fw-semibold small d-none d-md-block">Active Sites</span>
                            <span class="fw-semibold small d-block d-md-none">Sites</span>
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="fs-5" style="font-size: 16px;">{{ $activeSites ?? 0 }}</div>
                        <div class="small d-none d-md-block">Out of {{ $totalSites ?? 0 }} total</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-white h-100" style="background: linear-gradient(135deg, rgb(234, 88, 12), rgb(249, 115, 22)); border-radius: 1rem;">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="fw-semibold small d-none d-md-block">Transactions</span>
                            <span class="fw-semibold small d-block d-md-none">Txns</span>
                            <i class="fas fa-wave-square"></i>
                        </div>
                        <div class="fs-5" style="font-size: 16px;">{{ $recentTransactionsCount ?? 0 }}</div>
                        <div class="small d-none d-md-block">Successful transactions</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop Navigation Tabs -->
        <ul class="nav nav-tabs mb-4 d-none d-md-flex" id="dashboardTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">
                    <i class="fas fa-chart-line me-1"></i> Overview
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="sites-tab" data-bs-toggle="tab" data-bs-target="#sites" type="button" role="tab" aria-controls="sites" aria-selected="false">
                    <i class="fas fa-map-marker-alt me-1"></i> Sites
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="transactions-tab" href="{{ route('transactions.index') }}">
                    <i class="far fa-credit-card me-1"></i> Transactions
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="transfers-tab" href="{{ route('withdrawal.dashboard') }}">
                    <i class="fas fa-wave-square me-1"></i> Transfers
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="statistics-tab" href="{{ route('statistics.index') }}">
                    <i class="fas fa-user-friends me-1"></i> Statistics
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="business-profile-tab" data-bs-toggle="tab" data-bs-target="#business-profile" type="button" role="tab" aria-controls="business-profile" aria-selected="false">
                    <i class="fas fa-briefcase me-1"></i> Business Profile
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tickets-tab" data-bs-toggle="tab" data-bs-target="#tickets" type="button" role="tab" aria-controls="tickets" aria-selected="false">
                    <i class="fas fa-ticket-alt me-1"></i> Support Tickets
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab" aria-controls="notifications" aria-selected="false">
                    <i class="fas fa-bell me-1"></i> Notifications
                </button>
            </li>
        </ul>

        <div class="tab-content" id="dashboardTabsContent">
            <!-- Overview Tab -->
            <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                <div class="row g-4" id="overviewAnalyticsRow">
                    <!-- Site Revenue Overview -->
                    <div class="col-12 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title mb-1"><i class="fas fa-map-marker-alt me-2"></i>Site Revenue Overview</h5>
                                <p class="text-muted mb-3">Revenue by location</p>
                                <ul class="list-group list-group-flush">
                                    @foreach($sites as $site)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <span class="me-3" style="width: 12px; height: 12px; border-radius: 50%; display: inline-block; background: {{ $site->is_active ? '#22c55e' : '#e3342f' }};"></span>
                                            <div>
                                                <div class="fw-semibold">{{ $site->name }}</div>
                                                <div class="text-muted small">{{ $site->url }}</div>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold">₦{{ number_format($site->daily_revenue, 2) }}</div>
                                            <div class="text-muted small">Today</div>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- Recent Transactions -->
                    <div class="col-12 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title mb-1"><i class="fas fa-wave-square me-2"></i>Recent Transactions</h5>
                                <p class="text-muted mb-3">Latest payment activities</p>
                                <ul class="list-group list-group-flush">
                                    @foreach($recentTransactions as $txn)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-semibold">{{ $txn->site->name ?? 'Unknown Site' }}</div>
                                            <div class="text-muted small">{{ \Carbon\Carbon::parse($txn->created_at)->format('Y-m-d \a\t H:i') }}</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold {{ $txn->amount > 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $txn->amount > 0 ? '+' : '' }}₦{{ number_format($txn->amount, 2) }}
                                            </div>
                                            <div class="text-muted small">{{ $txn->type ?? $txn->payment_method ?? '' }}</div>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- Weekly Performance (Line Chart) -->
                    <div class="col-12 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-gradient text-white" style="background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%);">Weekly Performance</div>
                            <div class="card-body">
                                <h5 class="card-title mb-3">Weekly Performance</h5>
                                <canvas id="lineChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- Revenue by Month (Bar Chart) -->
                    <div class="col-12 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white">Revenue by Month</div>
                            <div class="card-body">
                                <canvas id="barChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- Transaction Types (Pie Chart) -->
                    <div class="col-12 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-success text-white">Transaction Types</div>
                            <div class="card-body">
                                <canvas id="pieChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- Empty/Reserved for future use -->
                    <div class="col-12 col-lg-4 mb-4"></div>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Bar Chart: Revenue by Month
                    const barCtx = document.getElementById('barChart').getContext('2d');
                    new Chart(barCtx, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode($monthlyData->pluck('month')) !!},
                            datasets: [{
                                label: 'Revenue (₦)',
                                data: {!! json_encode($monthlyData->pluck('amount')) !!},
                                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                borderRadius: 8,
                                borderSkipped: false
                            }]
                        },
                        options: {
                            plugins: {
                                legend: { display: false },
                                title: { display: false }
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

                    // Line Chart: Weekly Performance
                    const lineCtx = document.getElementById('lineChart').getContext('2d');
                    new Chart(lineCtx, {
                        type: 'line',
                        data: {
                            labels: {!! json_encode($weeklyData->pluck('date')) !!},
                            datasets: [{
                                label: 'Daily Revenue (₦)',
                                data: {!! json_encode($weeklyData->pluck('amount')) !!},
                                borderColor: 'rgb(75, 192, 192)',
                                tension: 0.1,
                                fill: false
                            }]
                        },
                        options: {
                            plugins: {
                                legend: { display: false },
                                title: { display: false }
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

                    // Pie Chart: Transaction Types
                    const pieCtx = document.getElementById('pieChart').getContext('2d');
                    new Chart(pieCtx, {
                        type: 'pie',
                        data: {
                            labels: {!! json_encode($transactionTypes->pluck('type')) !!},
                            datasets: [{
                                data: {!! json_encode($transactionTypes->pluck('count')) !!},
                                backgroundColor: [
                                    'rgba(40, 167, 69, 0.8)',
                                    'rgba(0, 123, 255, 0.8)',
                                    'rgba(255, 193, 7, 0.8)',
                                    'rgba(220, 53, 69, 0.8)'
                                ]
                            }]
                        },
                        options: {
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                });
                </script>
            </div>
            <!-- Sites Tab -->
            <div class="tab-pane fade" id="sites" role="tabpanel" aria-labelledby="sites-tab">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
                    <div>
                        <h2 class="h5 fw-bold mb-1">Site Management</h2>
                        <p class="text-secondary mb-0">Manage your business locations and monitor their performance</p>
                    </div>
                    <div class="mt-3 mt-md-0">
                        <!-- Add Site Button (place in the appropriate tab or section) -->
                        <button type="button" class="btn btn-dark d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addSiteModal">
                            <i class="fas fa-plus me-2"></i> Add New Site
                        </button>
                    </div>
                </div>
                <div class="row g-4" id="sites-grid">
                    @foreach($sites as $site)
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="card-subtitle mb-2 text-muted">{{ $site->name }}</h6>
                                        <p class="card-text mb-1">URL: <a href="{{ $site->url }}" target="_blank">{{ $site->url }}</a></p>
                                    </div>
                                    <!-- Bootstrap Dropdown for 3-dot menu -->
                                    <div class="dropdown">
                                        <button class="btn btn-link text-dark p-0" type="button" id="dropdownMenuButton{{ $site->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton{{ $site->id }}">
                                            <li><a class="dropdown-item" href="#" onclick="openEditModal({{ $site->id }})">Edit</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="openViewModal({{ $site->id }})">View</a></li>
                                            @if($site->is_active)
                                                <li><a class="dropdown-item text-warning" href="#" onclick="deactivateSite({{ $site->id }})">Deactivate</a></li>
                                            @else
                                                <li><a class="dropdown-item text-success" href="#" onclick="activateSite({{ $site->id }})">Activate</a></li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-warning" href="#" onclick="deleteSite({{ $site->id }})">Archive</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="mb-2 row g-2">
                                    <div class="col-6">
                                        <div class="bg-light rounded p-2 h-100">
                                            <span class="text-xs text-secondary">Daily Revenue</span><br>
                                            <span class="text-success">₦{{ number_format($site->daily_revenue, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="bg-light rounded p-2 h-100">
                                            <span class="text-xs text-secondary">Monthly Total</span><br>
                                            <span class="text-primary">₦{{ number_format($site->monthly_revenue, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <span class="rounded-circle me-2" style="width: 10px; height: 10px; background: {{ $site->is_active ? '#22c55e' : '#e3342f' }}; display: inline-block;"></span>
                                        <span class="badge {{ $site->is_active ? 'bg-success' : 'bg-secondary' }} text-xs">{{ $site->is_active ? 'Currently Active' : 'Currently Inactive' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Add Site Modal (place at the end of the file) -->
                <div class="modal fade" id="addSiteModal" tabindex="-1" aria-labelledby="addSiteModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addSiteModalLabel">Add New Site</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="addSiteForm" method="POST" action="{{ route('sites.store') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="site_name" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="site_name" name="name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="site_url" class="form-label">URL</label>
                                        <input type="url" class="form-control" id="site_url" name="url" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="site_webhook_url" class="form-label">Webhook URL</label>
                                        <input type="url" class="form-control" id="site_webhook_url" name="webhook_url" required>
                                        <div class="form-text">The URL where this site will send transaction notifications to your system.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="site_allowed_ips" class="form-label">Allowed IPs</label>
                                        <input type="text" class="form-control" id="site_allowed_ips" name="allowed_ips" placeholder="Comma-separated IPs (e.g. 1.2.3.4,5.6.7.8)">
                                        <div class="form-text">Only these IPs can send webhooks to this site.</div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save Site</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    let transferData = {};
                    const transferForm = document.getElementById('transferForm');
                    const pinForm = document.getElementById('pinForm');
                    const pinModal = new bootstrap.Modal(document.getElementById('pinModal'));
                    const transferStatus = document.getElementById('transferStatus');
                    const pinError = document.getElementById('pinError');
                    const beneficiaryForm = document.getElementById('beneficiaryForm');
                    const beneficiaryStatus = document.getElementById('beneficiaryStatus');
                    const beneficiarySelect = document.getElementById('beneficiary_id');

                    // Add Beneficiary
                    beneficiaryForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        beneficiaryStatus.innerHTML = '';
                        fetch('/beneficiaries', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                bank: beneficiaryForm.beneficiary_bank.value,
                                account_number: beneficiaryForm.beneficiary_account_number.value,
                                account_name: beneficiaryForm.beneficiary_account_name.value
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.beneficiary) {
                                beneficiaryStatus.innerHTML = '<div class="alert alert-success mt-3">Business account added.</div>';
                                // Add new option to select
                                const opt = document.createElement('option');
                                opt.value = data.beneficiary.id;
                                opt.text = `${data.beneficiary.account_name} (${data.beneficiary.bank} - ${data.beneficiary.account_number})`;
                                beneficiarySelect.appendChild(opt);
                                beneficiaryForm.reset();
                            } else {
                                beneficiaryStatus.innerHTML = '<div class="alert alert-danger mt-3">An error occurred. Please try again.</div>';
                            }
                        })
                        .catch(() => {
                            beneficiaryStatus.innerHTML = '<div class="alert alert-danger mt-3">An error occurred. Please try again.</div>';
                        });
                    });

                    // Transfer Form
                    transferForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        transferData = {
                            beneficiary_id: transferForm.beneficiary_id.value,
                            amount: transferForm.amount.value
                        };
                        pinForm.reset();
                        pinError.style.display = 'none';
                        pinModal.show();
                    });

                    // PIN Form
                    pinForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const pin = pinForm.pin.value;
                        if (pin === '1234') {
                            pinModal.hide();
                            // Send transfer to backend
                            fetch('{{ route("withdrawals.store") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify(transferData)
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.message) {
                                    transferStatus.innerHTML = '<div class="alert alert-success mt-3">' + data.message + '</div>';
                                    transferForm.reset();
                                    // Add new transfer to the top of the table
                                    if (data.transfer) {
                                        const tbody = document.querySelector('#transfers-history-table tbody');
                                        const tr = document.createElement('tr');
                                        tr.innerHTML = `
                                            <td>${data.transfer.reference}</td>
                                            <td>${data.transfer.recipient_account_name}<br><small>${data.transfer.recipient_account_number}</small></td>
                                            <td>${data.transfer.recipient_bank}</td>
                                            <td>₦${parseFloat(data.transfer.amount).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                                            <td><span class="badge bg-warning text-dark">Pending</span></td>
                                            <td>${(new Date(data.transfer.created_at)).toLocaleString('sv-SE', { hour12: false }).replace('T', ' ').slice(0, 16)}</td>
                                        `;
                                        if (tbody) tbody.prepend(tr);
                                    }
                                } else {
                                    transferStatus.innerHTML = '<div class="alert alert-danger mt-3">An error occurred. Please try again.</div>';
                                }
                            })
                            .catch(() => {
                                transferStatus.innerHTML = '<div class="alert alert-danger mt-3">An error occurred. Please try again.</div>';
                            });
                        } else {
                            pinError.style.display = 'block';
                        }
                    });

                    // Add Site Modal Logic
                    const siteForm = document.getElementById('siteForm');
                    const siteStatus = document.getElementById('siteStatus');
                    const apiCodeInput = document.getElementById('api_code');
                    const generateApiCodeBtn = document.getElementById('generateApiCode');
                    const copyApiCodeBtn = document.getElementById('copyApiCode');
                    const sitesGrid = document.getElementById('sites-grid');

                    function generateApiCode() {
                        // Simple random string generator
                        return 'API-' + Math.random().toString(36).substr(2, 12).toUpperCase();
                    }

                    generateApiCodeBtn.addEventListener('click', function() {
                        apiCodeInput.value = generateApiCode();
                    });

                    copyApiCodeBtn.addEventListener('click', function() {
                        apiCodeInput.select();
                        document.execCommand('copy');
                    });

                    siteForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        siteStatus.innerHTML = '';
                        fetch('/sites', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                name: siteForm.site_name.value,
                                url: siteForm.site_url.value,
                                webhook_url: siteForm.webhook_url.value,
                                api_code: siteForm.api_code.value,
                                allowed_ips: siteForm.site_allowed_ips.value
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.site) {
                                siteStatus.innerHTML = '<div class="alert alert-success mt-3">Site added successfully.</div>';
                                // Add new site card to grid
                                const col = document.createElement('div');
                                col.className = 'col-12 col-sm-6 col-lg-4';
                                col.innerHTML = `
                                    <div class="card h-100">
                                        <div class="card-body d-flex flex-column justify-content-between">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="card-subtitle mb-2 text-muted">${data.site.name}</h6>
                                                    <p class="card-text mb-1'>Webhook: <span class='text-break'>${data.site.webhook_url}</span></p>
                                                    <div class='d-flex align-items-center mb-1'>
                                                        <span class='me-2'>API Code:</span>
                                                        <input type='text' class='form-control form-control-sm w-auto d-inline-block' value='${data.site.api_code}' readonly id='apiCodeInputNew${data.site.id}'>
                                                        <button class='btn btn-outline-secondary btn-sm ms-2' type='button' onclick='navigator.clipboard.writeText(document.getElementById("apiCodeInputNew${data.site.id}").value)'>Copy</button>
                                                    </div>
                                                    <p class='card-text mb-1'>URL: <a href='${data.site.url}' target='_blank'>${data.site.url}</a></p>
                                                </div>
                                            </div>
                                            <div class="mb-2 row g-2">
                                                <div class="col-6">
                                                    <div class="bg-light rounded p-2 h-100">
                                                        <span class="text-xs text-secondary">Daily Revenue</span><br>
                                                        <span class="text-success">₦0.00</span>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="bg-light rounded p-2 h-100">
                                                        <span class="text-xs text-secondary">Monthly Total</span><br>
                                                        <span class="text-primary">₦0.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-auto">
                                                <div class="d-flex justify-content-center align-items-center">
                                                    <span class="rounded-circle me-2" style="width: 10px; height: 10px; background: #e3342f; display: inline-block;"></span>
                                                    <span class="badge bg-secondary text-xs">Currently Inactive</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                sitesGrid.prepend(col);
                                siteForm.reset();
                            } else if (data.errors) {
                                let errorHtml = '<div class="alert alert-danger mt-3"><ul>';
                                for (const key in data.errors) {
                                    errorHtml += `<li>${data.errors[key][0]}</li>`;
                                }
                                errorHtml += '</ul></div>';
                                siteStatus.innerHTML = errorHtml;
                            } else {
                                siteStatus.innerHTML = '<div class="alert alert-danger mt-3">An error occurred. Please try again.</div>';
                            }
                        })
                        .catch(async (err) => {
                            if (err.response) {
                                const data = await err.response.json();
                                if (data.errors) {
                                    let errorHtml = '<div class="alert alert-danger mt-3"><ul>';
                                    for (const key in data.errors) {
                                        errorHtml += `<li>${data.errors[key][0]}</li>`;
                                    }
                                    errorHtml += '</ul></div>';
                                    siteStatus.innerHTML = errorHtml;
                                    return;
                                }
                            }
                            siteStatus.innerHTML = '<div class="alert alert-danger mt-3">An error occurred. Please try again.</div>';
                        });
                    });
                });
                </script>

                <!-- Edit Site Modal -->
                <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Edit Site</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="editSiteForm" action="" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="url" class="form-label">URL</label>
                                        <input type="url" class="form-control" id="url" name="url" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="webhook_url" class="form-label">Webhook URL</label>
                                        <input type="url" class="form-control" id="webhook_url" name="webhook_url" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="api_code" class="form-label">API Code</label>
                                        <input type="text" class="form-control" id="api_code" name="api_code" readonly>
                                        <div class="form-text">This code is auto-generated and cannot be changed.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="allowed_ips" class="form-label">Allowed IPs</label>
                                        <input type="text" class="form-control" id="allowed_ips" name="allowed_ips" placeholder="Comma-separated IPs (e.g. 1.2.3.4,5.6.7.8)">
                                        <div class="form-text">Only these IPs can send webhooks to this site.</div>
                                    </div>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1">
                                        <label class="form-check-label" for="is_active">Active Site</label>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Update Site</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- View Site Modal -->
                <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewModalLabel">View Site</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Site details will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>

                <script>
            </div>
            <!-- Transactions Tab -->
            <div class="tab-pane fade" id="transactions" role="tabpanel" aria-labelledby="transactions-tab">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
                    <div>
                        <h2 class="h5 fw-bold mb-1">Transaction History</h2>
                        <p class="text-secondary mb-0">View and manage all payment transactions across your sites</p>
                    </div>
                    <div class="mt-3 mt-md-0">
                        <button type="button" class="btn btn-dark d-flex align-items-center">
                            <i class="fas fa-download me-2"></i> Export Transactions
                        </button>
                    </div>
                </div>
                <form class="row g-3 align-items-center mb-4">
                    <div class="col-12 col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search"></i></span>
                            <input type="search" class="form-control border-start-0" placeholder="Search transactions or references..." name="search" id="search">
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <select class="form-select" name="type" id="type">
                            <option>All Types</option>
                            <option>Deposit</option>
                            <option>Transfer Out</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <select class="form-select" name="site" id="site">
                            <option>All Sites</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}">{{ $site->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-1 d-grid">
                        <button type="submit" class="btn btn-outline-secondary">Filter</button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Reference</th>
                                <th>Site</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paginatedTransactions ?? [] as $txn)
                            <tr>
                                <td>{{ $txn->reference }}</td>
                                <td>{{ $txn->site->name ?? '-' }}</td>
                                <td class="fw-bold {{ $txn->amount > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $txn->amount > 0 ? '+' : '' }}₦{{ number_format($txn->amount, 2) }}
                                </td>
                                <td>{{ $txn->type ?? $txn->payment_method ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $txn->status === 'success' ? 'bg-success' : ($txn->status === 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ ucfirst($txn->status) }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($txn->created_at)->format('Y-m-d H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No transactions found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if(isset($paginatedTransactions) && $paginatedTransactions->hasPages())
                <div class="mt-4">
                    {{ $paginatedTransactions->links('vendor.pagination.bootstrap-5') }}
                </div>
                @endif
            </div>
            <!-- Transfers Tab -->
            <div class="tab-pane fade" id="transfers" role="tabpanel" aria-labelledby="transfers-tab">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">New Withdrawal</h5>
                            </div>
                            <div class="card-body text-center">
                                <a href="{{ route('withdrawal.dashboard') }}" class="btn btn-primary w-100">
                                    Go to Withdrawals
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Recent Withdrawals</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Reference</th>
                                                <th>Amount</th>
                                                <th>Recipient</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($transfers as $transfer)
                                            <tr>
                                                <td>{{ $transfer->reference }}</td>
                                                <td>₦{{ number_format($transfer->amount, 2) }}</td>
                                                <td>
                                                    <div>{{ $transfer->recipient_account_name }}</div>
                                                    <small class="text-muted">{{ $transfer->recipient_bank }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $transfer->status === 'completed' ? 'success' : ($transfer->status === 'pending' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($transfer->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $transfer->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @push('scripts')
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const transferForm = document.getElementById('transferForm');
                const submitButton = document.getElementById('transferButton');
                
                if (transferForm) {
                    transferForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        // Get form data
                        const formData = new FormData(transferForm);
                        const data = {
                            beneficiary_id: formData.get('beneficiary_id'),
                            amount: formData.get('amount'),
                            narration: formData.get('narration')
                        };
                        
                        // Disable the button and show loading state
                        submitButton.disabled = true;
                        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
                        
                        // Make the API call
                        fetch('{{ route("withdrawals.store") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(data)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Show success modal
                                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                                document.getElementById('successMessage').textContent = data.message;
                                successModal.show();
                                
                                // Clear the form
                                transferForm.reset();
                                
                                // Reload the page after a short delay
                                setTimeout(() => {
                                    window.location.reload();
                                }, 2000);
                            } else {
                                // Show error modal
                                const insufficientFundsModal = new bootstrap.Modal(document.getElementById('insufficientFundsModal'));
                                document.getElementById('insufficientFundsMessage').textContent = data.message;
                                insufficientFundsModal.show();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            // Show error modal
                            const insufficientFundsModal = new bootstrap.Modal(document.getElementById('insufficientFundsModal'));
                            document.getElementById('insufficientFundsMessage').textContent = 'An error occurred while processing the transfer. Please try again.';
                            insufficientFundsModal.show();
                        })
                        .finally(() => {
                            // Re-enable the button and restore original text
                            submitButton.disabled = false;
                            submitButton.innerHTML = 'Initiate Transfer';
                        });
                    });
                }

                // Add event listeners for modal close
                const insufficientFundsModal = document.getElementById('insufficientFundsModal');
                const successModal = document.getElementById('successModal');

                if (insufficientFundsModal) {
                    insufficientFundsModal.addEventListener('hidden.bs.modal', function () {
                        const submitButton = document.getElementById('transferButton');
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.innerHTML = 'Initiate Transfer';
                        }
                    });
                }

                if (successModal) {
                    successModal.addEventListener('hidden.bs.modal', function () {
                        const submitButton = document.getElementById('transferButton');
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.innerHTML = 'Initiate Transfer';
                        }
                    });
                }
            });
            </script>
            @endpush
            <!-- Statistics Tab -->
            <div class="tab-pane fade" id="statistics" role="tabpanel" aria-labelledby="statistics-tab">
                <!-- Overview Stats Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="card text-white bg-primary h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-semibold small">Total Revenue</div>
                                        <div class="fs-4">₦{{ number_format($totalRevenue ?? 0) }}</div>
                                    </div>
                                    <i class="fas fa-chart-line fs-4"></i>
                                </div>
                                <div class="small mt-2">Successful transactions only</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card text-white bg-success h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-semibold small">Monthly Revenue</div>
                                        <div class="fs-4">₦{{ number_format($monthlyRevenue ?? 0) }}</div>
                                    </div>
                                    <i class="fas fa-calendar-alt fs-4"></i>
                                </div>
                                <div class="small mt-2">Successful transactions only</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card text-white bg-info h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-semibold small">Total Withdrawals</div>
                                        <div class="fs-4">₦{{ number_format($totalWithdrawals ?? 0) }}</div>
                                    </div>
                                    <i class="fas fa-money-bill-wave fs-4"></i>
                                </div>
                                <div class="small mt-2">All time</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card text-white bg-warning h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-semibold small">Pending Withdrawals</div>
                                        <div class="fs-4">₦{{ number_format($pendingWithdrawals ?? 0) }}</div>
                                    </div>
                                    <i class="fas fa-clock fs-4"></i>
                                </div>
                                <div class="small mt-2">Awaiting approval</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transaction Status Stats -->
                <div class="row g-4 mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-chart-pie me-2"></i>Transaction Status Overview
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center p-3 bg-success bg-opacity-10 rounded">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-check-circle text-success fs-3"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <div class="fw-bold text-success">{{ $successfulTransactions ?? 0 }}</div>
                                                <div class="small text-muted">Successful</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center p-3 bg-warning bg-opacity-10 rounded">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-clock text-warning fs-3"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <div class="fw-bold text-warning">{{ $pendingTransactions ?? 0 }}</div>
                                                <div class="small text-muted">Pending</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center p-3 bg-danger bg-opacity-10 rounded">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-times-circle text-danger fs-3"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <div class="fw-bold text-danger">{{ $failedTransactions ?? 0 }}</div>
                                                <div class="small text-muted">Failed</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center p-3 bg-info bg-opacity-10 rounded">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-sync-alt text-info fs-3"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <div class="fw-bold text-info">{{ $initiatedTransactions ?? 0 }}</div>
                                                <div class="small text-muted">Initiated</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Site Performance and Revenue Charts -->
                <div class="row g-4 mb-4">
                    <div class="col-12 col-lg-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-chart-bar me-2"></i>Site Performance
                                </h5>
                            </div>
                            <div class="card-body">
                                <canvas id="sitePerformanceChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-chart-line me-2"></i>Monthly Revenue Trend
                                </h5>
                            </div>
                            <div class="card-body">
                                <canvas id="monthlyRevenueChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Site Details Table -->
                <div class="row g-4 mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-table me-2"></i>Site Performance Details
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Site Name</th>
                                                <th>Status</th>
                                                <th>Total Transactions</th>
                                                <th>Successful</th>
                                                <th>Pending</th>
                                                <th>Failed</th>
                                                <th>Daily Revenue</th>
                                                <th>Monthly Revenue</th>
                                                <th>Total Revenue</th>
                                                <th>Success Rate</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($sites as $site)
                                            @php
                                                $siteTransactions = $transactions->where('site_id', $site->id);
                                                $totalSiteTransactions = $siteTransactions->count();
                                                $successfulSiteTransactions = $siteTransactions->where('status', 'success')->count();
                                                $pendingSiteTransactions = $siteTransactions->where('status', 'pending')->count();
                                                $failedSiteTransactions = $siteTransactions->where('status', 'failed')->count();
                                                $successRate = $totalSiteTransactions > 0 ? round(($successfulSiteTransactions / $totalSiteTransactions) * 100, 1) : 0;
                                                $totalSiteRevenue = $siteTransactions->where('status', 'success')->sum('amount');
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2" style="width: 8px; height: 8px; border-radius: 50%; display: inline-block; background: {{ $site->is_active ? '#22c55e' : '#e3342f' }};"></span>
                                                        <div>
                                                            <div class="fw-semibold">{{ $site->name }}</div>
                                                            <div class="text-muted small">{{ $site->url }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $site->is_active ? 'success' : 'danger' }}">
                                                        {{ $site->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td class="fw-bold">{{ $totalSiteTransactions }}</td>
                                                <td class="text-success fw-bold">{{ $successfulSiteTransactions }}</td>
                                                <td class="text-warning fw-bold">{{ $pendingSiteTransactions }}</td>
                                                <td class="text-danger fw-bold">{{ $failedSiteTransactions }}</td>
                                                <td class="fw-bold">₦{{ number_format($site->daily_revenue, 2) }}</td>
                                                <td class="fw-bold">₦{{ number_format($site->monthly_revenue ?? 0, 2) }}</td>
                                                <td class="fw-bold">₦{{ number_format($totalSiteRevenue, 2) }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                            <div class="progress-bar bg-success" style="width: {{ $successRate }}%"></div>
                                                        </div>
                                                        <span class="small fw-bold">{{ $successRate }}%</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Withdrawal Statistics -->
                <div class="row g-4 mb-4">
                    <div class="col-12 col-lg-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-money-bill-wave me-2"></i>Withdrawal Statistics
                                </h5>
                            </div>
                            <div class="card-body">
                                <canvas id="withdrawalChart" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-list me-2"></i>Recent Withdrawals
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Reference</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($transfers->take(5) as $transfer)
                                            <tr>
                                                <td class="small">{{ $transfer->reference }}</td>
                                                <td class="fw-bold">₦{{ number_format($transfer->amount, 2) }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $transfer->status === 'completed' ? 'success' : ($transfer->status === 'pending' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($transfer->status) }}
                                                    </span>
                                                </td>
                                                <td class="small">{{ $transfer->created_at->format('M d, Y') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transaction Volume Over Time -->
                <div class="row g-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-chart-area me-2"></i>Transaction Volume Over Time
                                </h5>
                            </div>
                            <div class="card-body">
                                <canvas id="transactionVolumeChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    // Site Performance Chart (Bar Chart)
                    const sitePerformanceCtx = document.getElementById('sitePerformanceChart').getContext('2d');
                    const sitePerformanceChart = new Chart(sitePerformanceCtx, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode($sites->pluck('name')) !!},
                            datasets: [{
                                label: 'Successful Revenue',
                                data: {!! json_encode($sites->map(function($site) use ($transactions) {
                                    return $transactions->where('site_id', $site->id)->where('status', 'success')->sum('amount');
                                })) !!},
                                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { display: false }
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

                    // Monthly Revenue Chart (Line Chart)
                    const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
                    const monthlyRevenueChart = new Chart(monthlyRevenueCtx, {
                        type: 'line',
                        data: {
                            labels: {!! json_encode($monthlyData->pluck('month')) !!},
                            datasets: [{
                                label: 'Monthly Revenue',
                                data: {!! json_encode($monthlyData->pluck('amount')) !!},
                                fill: false,
                                borderColor: 'rgba(75, 192, 192, 1)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                tension: 0.1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { display: false }
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

                    // Withdrawal Chart (Doughnut Chart)
                    const withdrawalCtx = document.getElementById('withdrawalChart').getContext('2d');
                    const withdrawalChart = new Chart(withdrawalCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Completed', 'Pending', 'Failed'],
                            datasets: [{
                                data: [
                                    {{ $transfers->where('status', 'completed')->count() }},
                                    {{ $transfers->where('status', 'pending')->count() }},
                                    {{ $transfers->where('status', 'failed')->count() }}
                                ],
                                backgroundColor: [
                                    'rgba(34, 197, 94, 0.8)',
                                    'rgba(251, 191, 36, 0.8)',
                                    'rgba(239, 68, 68, 0.8)'
                                ],
                                borderColor: [
                                    'rgba(34, 197, 94, 1)',
                                    'rgba(251, 191, 36, 1)',
                                    'rgba(239, 68, 68, 1)'
                                ],
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });

                    // Transaction Volume Over Time (Line Chart)
                    const transactionVolumeCtx = document.getElementById('transactionVolumeChart').getContext('2d');
                    const transactionData = @json(
                        $transactions->groupBy(function($txn) {
                            return \Carbon\Carbon::parse($txn->created_at)->format('Y-m-d');
                        })->map->count()
                    );
                    const transactionLabels = Object.keys(transactionData);
                    const transactionCounts = Object.values(transactionData);
                    const transactionVolumeChart = new Chart(transactionVolumeCtx, {
                        type: 'line',
                        data: {
                            labels: transactionLabels,
                            datasets: [{
                                label: 'Transactions',
                                data: transactionCounts,
                                fill: false,
                                borderColor: 'rgba(147, 51, 234, 1)',
                                backgroundColor: 'rgba(147, 51, 234, 0.2)',
                                tension: 0.1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                </script>
            </div>
            <!-- Business Profile Tab -->
            <div class="tab-pane fade" id="business-profile" role="tabpanel" aria-labelledby="business-profile-tab">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Business Profile</h5>
                                
                                <!-- Progress Steps -->
                                <div class="progress-steps mb-4">
                                    <div class="d-flex justify-content-between position-relative">
                                        <div class="progress" style="height: 2px; position: absolute; top: 20px; left: 0; right: 0; z-index: 1;">
                                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                        </div>
                                        <div class="step active" data-step="1">
                                            <div class="step-circle">1</div>
                                            <div class="step-label">Basic Info</div>
                                        </div>
                                        <div class="step" data-step="2">
                                            <div class="step-circle">2</div>
                                            <div class="step-label">Contact</div>
                                        </div>
                                        <div class="step" data-step="3">
                                            <div class="step-circle">3</div>
                                            <div class="step-label">Additional</div>
                                        </div>
                                    </div>
                                </div>

                                <form id="businessProfileForm">
                                    <!-- Step 1: Basic Information -->
                                    <div class="step-content" data-step="1">
                                        <h6 class="mb-3">Basic Information</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="business_name" class="form-label">Business Name</label>
                                                <input type="text" class="form-control" id="business_name" name="business_name" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="business_type" class="form-label">Business Type</label>
                                                <input type="text" class="form-control" id="business_type" name="business_type" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="registration_number" class="form-label">Registration Number</label>
                                                <input type="text" class="form-control" id="registration_number" name="registration_number" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="tax_identification_number" class="form-label">Tax ID</label>
                                                <input type="text" class="form-control" id="tax_identification_number" name="tax_identification_number" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="industry" class="form-label">Industry</label>
                                                <input type="text" class="form-control" id="industry" name="industry" required>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step 2: Contact Information -->
                                    <div class="step-content d-none" data-step="2">
                                        <h6 class="mb-3">Contact Information</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="address" class="form-label">Address</label>
                                                <input type="text" class="form-control" id="address" name="address" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="city" class="form-label">City</label>
                                                <input type="text" class="form-control" id="city" name="city" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="state" class="form-label">State</label>
                                                <input type="text" class="form-control" id="state" name="state" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="country" class="form-label">Country</label>
                                                <input type="text" class="form-control" id="country" name="country" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="phone" class="form-label">Phone</label>
                                                <input type="text" class="form-control" id="phone" name="phone" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email" name="email" required>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step 3: Additional Information -->
                                    <div class="step-content d-none" data-step="3">
                                        <h6 class="mb-3">Additional Information & KYC Verification</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="website" class="form-label">Website</label>
                                                <input type="url" class="form-control" id="website" name="website">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="logo" class="form-label">Business Logo</label>
                                                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                            </div>
                                            <div class="col-12">
                                                <hr class="my-4">
                                                <h6 class="mb-3">KYC Verification Documents</h6>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="verification_id" class="form-label">Verification ID</label>
                                                <select class="form-select" id="verification_id_type" name="verification_id_type" required>
                                                    <option value="">Select ID Type</option>
                                                    <option value="passport">Passport</option>
                                                    <option value="national_id">National ID</option>
                                                    <option value="drivers_license">Driver's License</option>
                                                    <option value="voters_card">Voter's Card</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="verification_id_number" class="form-label">ID Number</label>
                                                <input type="text" class="form-control" id="verification_id_number" name="verification_id_number" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="verification_id_file" class="form-label">ID Document</label>
                                                <input type="file" class="form-control" id="verification_id_file" name="verification_id_file" accept="image/*,.pdf" required>
                                                <small class="text-muted">Upload a clear image or PDF of your ID document</small>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="proof_of_address_file" class="form-label">Proof of Address</label>
                                                <input type="file" class="form-control" id="proof_of_address_file" name="proof_of_address_file" accept="image/*,.pdf" required>
                                                <small class="text-muted">Upload a recent utility bill, bank statement, or government document (not older than 3 months)</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Navigation Buttons -->
                                    <div class="d-flex justify-content-between mt-4">
                                        <button type="button" class="btn btn-secondary" id="prevStep" style="display: none;">Previous</button>
                                        <button type="button" class="btn btn-primary" id="nextStep">Next</button>
                                        <button type="submit" class="btn btn-success" id="submitForm" style="display: none;">Save Profile</button>
                                    </div>
                                </form>
                                <div id="businessProfileStatus" class="mt-3"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                .progress-steps {
                    padding: 0 40px;
                }
                .step {
                    position: relative;
                    z-index: 2;
                    text-align: center;
                }
                .step-circle {
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    background: #e9ecef;
                    border: 2px solid #dee2e6;
                    color: #6c757d;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 8px;
                    font-weight: bold;
                }
                .step.active .step-circle {
                    background: #0d6efd;
                    border-color: #0d6efd;
                    color: white;
                }
                .step.completed .step-circle {
                    background: #198754;
                    border-color: #198754;
                    color: white;
                }
                .step-label {
                    font-size: 0.875rem;
                    color: #6c757d;
                }
                .step.active .step-label {
                    color: #0d6efd;
                    font-weight: bold;
                }
                </style>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    let currentStep = 1;
                    const totalSteps = 3;
                    const form = document.getElementById('businessProfileForm');
                    const prevBtn = document.getElementById('prevStep');
                    const nextBtn = document.getElementById('nextStep');
                    const submitBtn = document.getElementById('submitForm');
                    const progressBar = document.querySelector('.progress-bar');

                    // Helper to get CSRF token
                    function getCsrfToken() {
                        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    }

                    // Update step visibility and buttons
                    function updateStepVisibility() {
                        document.querySelectorAll('.step-content').forEach(content => {
                            content.classList.add('d-none');
                        });
                        document.querySelector(`.step-content[data-step="${currentStep}"]`).classList.remove('d-none');

                        // Update progress bar
                        const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
                        progressBar.style.width = `${progress}%`;

                        // Update step indicators
                        document.querySelectorAll('.step').forEach(step => {
                            const stepNum = parseInt(step.dataset.step);
                            step.classList.remove('active', 'completed');
                            if (stepNum === currentStep) {
                                step.classList.add('active');
                            } else if (stepNum < currentStep) {
                                step.classList.add('completed');
                            }
                        });

                        // Show/hide navigation buttons
                        prevBtn.style.display = currentStep > 1 ? 'block' : 'none';
                        nextBtn.style.display = currentStep < totalSteps ? 'block' : 'none';
                        submitBtn.style.display = currentStep === totalSteps ? 'block' : 'none';
                    }

                    // Navigation button handlers
                    prevBtn.addEventListener('click', () => {
                        if (currentStep > 1) {
                            currentStep--;
                            updateStepVisibility();
                        }
                    });

                    nextBtn.addEventListener('click', () => {
                        if (currentStep < totalSteps) {
                            currentStep++;
                            updateStepVisibility();
                        }
                    });

                    // Fetch and pre-fill business profile
                    function fetchBusinessProfile() {
                        fetch('/business-profile/1', {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.profile) {
                                for (const key in data.profile) {
                                    if (form.elements[key] && key !== 'logo') {
                                        form.elements[key].value = data.profile[key] ?? '';
                                    }
                                }
                                form.setAttribute('data-mode', 'edit');
                                form.setAttribute('data-id', data.profile.id);
                            } else {
                                form.reset();
                                form.setAttribute('data-mode', 'create');
                                form.removeAttribute('data-id');
                            }
                        });
                    }

                    // Form submission
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const formData = new FormData(form);
                        const mode = form.getAttribute('data-mode');
                        const id = form.getAttribute('data-id');
                        let url = '/business-profile';
                        let method = 'POST';

                        if (mode === 'edit' && id) {
                            url = `/business-profile/${id}`;
                            formData.append('_method', 'PUT');
                        }

                        // Add validation for KYC documents
                        const idFile = formData.get('verification_id_file');
                        const addressFile = formData.get('proof_of_address_file');
                        
                        if (!idFile || !addressFile) {
                            document.getElementById('businessProfileStatus').innerHTML = 
                                '<div class="alert alert-danger">Please upload both ID document and proof of address.</div>';
                            return;
                        }

                        fetch(url, {
                            method: method,
                            headers: {
                                'X-CSRF-TOKEN': getCsrfToken(),
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        })
                        .then(async res => {
                            const data = await res.json();
                            if (res.ok) {
                                document.getElementById('businessProfileStatus').innerHTML = 
                                    '<div class="alert alert-success">Profile saved successfully. Your KYC documents will be reviewed within 24-48 hours.</div>';
                                fetchBusinessProfile();
                            } else {
                                let msg = '';
                                if (data.errors) {
                                    for (const key in data.errors) {
                                        msg += `<div>${data.errors[key][0]}</div>`;
                                    }
                                } else if (data.error) {
                                    msg = data.error;
                                } else {
                                    msg = 'An error occurred.';
                                }
                                document.getElementById('businessProfileStatus').innerHTML = 
                                    `<div class="alert alert-danger">${msg}</div>`;
                            }
                        })
                        .catch(() => {
                            document.getElementById('businessProfileStatus').innerHTML = 
                                '<div class="alert alert-danger">An error occurred.</div>';
                        });
                    });

                    // Initialize
                    updateStepVisibility();
                    if (document.getElementById('business-profile').classList.contains('show')) {
                        fetchBusinessProfile();
                    }

                    // Tab show event
                    const businessProfileTab = document.getElementById('business-profile-tab');
                    if (businessProfileTab) {
                        businessProfileTab.addEventListener('shown.bs.tab', function () {
                            fetchBusinessProfile();
                        });
                    }
                });
                </script>
            </div>
            <!-- Tickets Tab -->
            <div class="tab-pane fade" id="tickets" role="tabpanel" aria-labelledby="tickets-tab">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="card-title mb-0">Support Tickets</h5>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newTicketModal">
                                        <i class="fas fa-plus me-1"></i> New Ticket
                                    </button>
                                </div>

                                <!-- Ticket Filters -->
                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <select class="form-select" id="ticketStatus">
                                            <option value="">All Status</option>
                                            <option value="open">Open</option>
                                            <option value="in_progress">In Progress</option>
                                            <option value="resolved">Resolved</option>
                                            <option value="closed">Closed</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" id="ticketPriority">
                                            <option value="">All Priority</option>
                                            <option value="low">Low</option>
                                            <option value="medium">Medium</option>
                                            <option value="high">High</option>
                                            <option value="urgent">Urgent</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Search tickets..." id="ticketSearch">
                                            <button class="btn btn-outline-secondary" type="button">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tickets List -->
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Ticket ID</th>
                                                <th>Subject</th>
                                                <th>Status</th>
                                                <th>Priority</th>
                                                <th>Created</th>
                                                <th>Last Update</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="ticketsList">
                                            <!-- Tickets will be loaded here via AJAX -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <div class="text-muted">
                                        Showing <span id="ticketStart">0</span> to <span id="ticketEnd">0</span> of <span id="ticketTotal">0</span> tickets
                                    </div>
                                    <nav aria-label="Ticket pagination">
                                        <ul class="pagination mb-0" id="ticketPagination">
                                            <!-- Pagination will be loaded here via AJAX -->
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- New Ticket Modal -->
                <div class="modal fade" id="newTicketModal" tabindex="-1" aria-labelledby="newTicketModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="newTicketModalLabel">Create New Support Ticket</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="newTicketForm">
                                    <div id="ticketFormErrors" class="alert alert-danger d-none"></div>
                                    <div class="mb-3">
                                        <label for="ticketSubject" class="form-label">Subject</label>
                                        <input type="text" class="form-control" id="ticketSubject" name="subject" required autocomplete="off">
                                    </div>
                                    <div class="mb-3">
                                        <label for="ticketCategory" class="form-label">Category</label>
                                        <select class="form-select" id="ticketCategory" name="category" required autocomplete="off">
                                            <option value="">Select Category</option>
                                            <option value="technical">Technical Support</option>
                                            <option value="billing">Billing & Payments</option>
                                            <option value="account">Account Issues</option>
                                            <option value="kyc">KYC Verification</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="ticketPriority" class="form-label">Priority</label>
                                        <select class="form-select" id="ticketPriority" name="priority" required autocomplete="off">
                                            <option value="low">Low</option>
                                            <option value="medium">Medium</option>
                                            <option value="high">High</option>
                                            <option value="urgent">Urgent</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="ticketMessage" class="form-label">Message</label>
                                        <textarea class="form-control" id="ticketMessage" name="message" rows="5" required autocomplete="off"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="ticketAttachments" class="form-label">Attachments (Optional)</label>
                                        <input type="file" class="form-control" id="ticketAttachments" name="attachments[]" multiple autocomplete="off">
                                        <small class="text-muted">You can attach up to 3 files (max 2MB each)</small>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="submitTicket">
                                    <span id="submitTicketSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    <span id="submitTicketText">Submit Ticket</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- View Ticket Modal -->
                <div class="modal fade" id="viewTicketModal" tabindex="-1" aria-labelledby="viewTicketModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewTicketModalLabel">Ticket Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="ticket-header mb-4">
                                    <h6 id="ticketSubject"></h6>
                                    <div class="d-flex gap-2">
                                        <span class="badge bg-primary" id="ticketStatus"></span>
                                        <span class="badge bg-secondary" id="ticketPriority"></span>
                                        <span class="badge bg-info" id="ticketCategory"></span>
                                    </div>
                                </div>
                                <div class="ticket-messages" id="ticketMessages">
                                    <!-- Messages will be loaded here -->
                                </div>
                                <div class="ticket-reply mt-4">
                                    <form id="ticketReplyForm">
                                        <div class="mb-3">
                                            <label for="replyMessage" class="form-label">Reply</label>
                                            <textarea class="form-control" id="replyMessage" rows="3" required></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="replyAttachments" class="form-label">Attachments (Optional)</label>
                                            <input type="file" class="form-control" id="replyAttachments" name="attachments[]" multiple>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Send Reply</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                .ticket-messages {
                    max-height: 400px;
                    overflow-y: auto;
                }
                .ticket-message {
                    padding: 1rem;
                    margin-bottom: 1rem;
                    border-radius: 0.5rem;
                }
                .ticket-message.user {
                    background-color: #e3f2fd;
                    margin-left: 2rem;
                }
                .ticket-message.support {
                    background-color: #f5f5f5;
                    margin-right: 2rem;
                }
                .ticket-message .message-header {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 0.5rem;
                    font-size: 0.875rem;
                }
                .ticket-message .message-content {
                    margin-bottom: 0.5rem;
                }
                .ticket-message .message-attachments {
                    display: flex;
                    gap: 0.5rem;
                    flex-wrap: wrap;
                }
                .ticket-message .attachment {
                    padding: 0.25rem 0.5rem;
                    background-color: #fff;
                    border-radius: 0.25rem;
                    font-size: 0.875rem;
                }
                </style>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Load tickets when tab is shown
                    const ticketsTab = document.getElementById('tickets-tab');
                    if (ticketsTab) {
                        ticketsTab.addEventListener('shown.bs.tab', function () {
                            loadTickets();
                        });
                    }

                    // Load tickets function
                    function loadTickets(page = 1) {
                        const status = document.getElementById('ticketStatus').value;
                        const priority = document.getElementById('ticketPriority').value;
                        const search = document.getElementById('ticketSearch').value;

                        fetch(`/tickets?page=${page}&status=${status}&priority=${priority}&search=${search}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(res => res.json())
                        .then(data => {
                            const ticketsList = document.getElementById('ticketsList');
                            ticketsList.innerHTML = '';

                            data.tickets.forEach(ticket => {
                                const row = document.createElement('tr');
                                row.innerHTML = `
                                    <td>#${ticket.id}</td>
                                    <td>${ticket.subject}</td>
                                    <td><span class="badge bg-${getStatusColor(ticket.status)}">${ticket.status}</span></td>
                                    <td><span class="badge bg-${getPriorityColor(ticket.priority)}">${ticket.priority}</span></td>
                                    <td>${formatDate(ticket.created_at)}</td>
                                    <td>${formatDate(ticket.updated_at)}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary view-ticket" data-id="${ticket.id}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                `;
                                ticketsList.appendChild(row);
                            });

                            // Update pagination
                            updatePagination(data);
                        });
                    }

                    // Make loadTickets globally accessible
                    window.loadTickets = loadTickets;

                    // Helper functions
                    function getStatusColor(status) {
                        const colors = {
                            'open': 'success',
                            'in_progress': 'primary',
                            'resolved': 'info',
                            'closed': 'secondary'
                        };
                        return colors[status] || 'secondary';
                    }

                    function getPriorityColor(priority) {
                        const colors = {
                            'low': 'success',
                            'medium': 'info',
                            'high': 'warning',
                            'urgent': 'danger'
                        };
                        return colors[priority] || 'secondary';
                    }

                    function formatDate(dateString) {
                        return new Date(dateString).toLocaleDateString();
                    }

                    function updatePagination(data) {
                        const pagination = document.getElementById('ticketPagination');
                        pagination.innerHTML = '';

                        // Previous button
                        const prevLi = document.createElement('li');
                        prevLi.className = `page-item ${data.current_page === 1 ? 'disabled' : ''}`;
                        prevLi.innerHTML = `<a class="page-link" href="#" data-page="${data.current_page - 1}">Previous</a>`;
                        pagination.appendChild(prevLi);

                        // Page numbers
                        for (let i = 1; i <= data.last_page; i++) {
                            const li = document.createElement('li');
                            li.className = `page-item ${data.current_page === i ? 'active' : ''}`;
                            li.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
                            pagination.appendChild(li);
                        }

                        // Next button
                        const nextLi = document.createElement('li');
                        nextLi.className = `page-item ${data.current_page === data.last_page ? 'disabled' : ''}`;
                        nextLi.innerHTML = `<a class="page-link" href="#" data-page="${data.current_page + 1}">Next</a>`;
                        pagination.appendChild(nextLi);

                        // Update pagination info
                        document.getElementById('ticketStart').textContent = data.from || 0;
                        document.getElementById('ticketEnd').textContent = data.to || 0;
                        document.getElementById('ticketTotal').textContent = data.total || 0;
                    }

                    // Event Listeners
                    document.getElementById('ticketStatus').addEventListener('change', () => loadTickets());
                    document.getElementById('ticketPriority').addEventListener('change', () => loadTickets());
                function debounce(func, wait) {
                    let timeout;
                    return function(...args) {
                        clearTimeout(timeout);
                        timeout = setTimeout(() => func.apply(this, args), wait);
                    };
                }

                document.getElementById('ticketSearch').addEventListener('input', debounce(() => loadTickets(), 500));

                document.getElementById('ticketPagination').addEventListener('click', function(e) {
                    e.preventDefault();
                    if (e.target.tagName === 'A') {
                        const page = e.target.dataset.page;
                        if (page) loadTickets(page);
                    }
                });

                    // New Ticket Form Submission
                    const submitTicketBtn = document.getElementById('submitTicket');
                    const submitTicketSpinner = document.getElementById('submitTicketSpinner');
                    const submitTicketText = document.getElementById('submitTicketText');
                    const ticketFormErrors = document.getElementById('ticketFormErrors');
                    let ticketSubmitting = false;

                    submitTicketBtn.addEventListener('click', function() {
                        if (ticketSubmitting) return;
                        const form = document.getElementById('newTicketForm');
                        if (!form.checkValidity()) {
                            form.reportValidity();
                            return;
                        }
                        ticketFormErrors.classList.add('d-none');
                        ticketFormErrors.innerHTML = '';
                        ticketSubmitting = true;
                        submitTicketBtn.disabled = true;
                        submitTicketSpinner.classList.remove('d-none');
                        submitTicketText.textContent = 'Submitting...';
                        const formData = new FormData(form);
                        fetch('/tickets', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        })
                        .then(res => res.json())
                        .then(data => {
                            ticketSubmitting = false;
                            submitTicketBtn.disabled = false;
                            submitTicketSpinner.classList.add('d-none');
                            submitTicketText.textContent = 'Submit Ticket';
                            if (data.success) {
                                bootstrap.Modal.getInstance(document.getElementById('newTicketModal')).hide();
                                form.reset();
                                loadTickets();
                                showAlert('success', 'Ticket created successfully');
                            } else {
                                let errorMsg = data.message || 'Error creating ticket';
                                if (data.errors) {
                                    errorMsg += '<ul class="mb-0">';
                                    for (const key in data.errors) {
                                        errorMsg += `<li>${data.errors[key]}</li>`;
                                    }
                                    errorMsg += '</ul>';
                                }
                                ticketFormErrors.innerHTML = errorMsg;
                                ticketFormErrors.classList.remove('d-none');
                            }
                        })
                        .catch(() => {
                            ticketSubmitting = false;
                            submitTicketBtn.disabled = false;
                            submitTicketSpinner.classList.add('d-none');
                            submitTicketText.textContent = 'Submit Ticket';
                            ticketFormErrors.innerHTML = 'Error creating ticket. Please try again.';
                            ticketFormErrors.classList.remove('d-none');
                        });
                    });

                    // View Ticket
                    document.getElementById('ticketsList').addEventListener('click', function(e) {
                        if (e.target.closest('.view-ticket')) {
                            const ticketId = e.target.closest('.view-ticket').dataset.id;
                            loadTicketDetails(ticketId);
                        }
                    });

                    function loadTicketDetails(ticketId) {
                        fetch(`/tickets/${ticketId}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(res => res.json())
                        .then(data => {
                            const ticket = data.ticket;
                            document.getElementById('ticketSubject').textContent = ticket.subject;
                            document.getElementById('ticketStatus').textContent = ticket.status;
                            document.getElementById('ticketPriority').textContent = ticket.priority;
                            document.getElementById('ticketCategory').textContent = ticket.category;

                            const messagesContainer = document.getElementById('ticketMessages');
                            messagesContainer.innerHTML = '';

                            ticket.messages.forEach(message => {
                                const messageDiv = document.createElement('div');
                                messageDiv.className = `ticket-message ${message.is_support ? 'support' : 'user'}`;
                                messageDiv.innerHTML = `
                                    <div class="message-header">
                                        <span>${message.sender}</span>
                                        <span>${formatDate(message.created_at)}</span>
                                    </div>
                                    <div class="message-content">${message.content}</div>
                                    ${message.attachments ? `
                                        <div class="message-attachments">
                                            ${message.attachments.map(attachment => `
                                                <a href="${attachment.url}" class="attachment" target="_blank">
                                                    <i class="fas fa-paperclip"></i> ${attachment.name}
                                                </a>
                                            `).join('')}
                                        </div>
                                    ` : ''}
                                `;
                                messagesContainer.appendChild(messageDiv);
                            });

                            new bootstrap.Modal(document.getElementById('viewTicketModal')).show();
                        });
                    }

                    // Reply to Ticket
                    document.getElementById('ticketReplyForm').addEventListener('submit', function(e) {
                        e.preventDefault();
                        const ticketId = this.dataset.ticketId;
                        const formData = new FormData(this);

                        fetch(`/tickets/${ticketId}/reply`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                bootstrap.Modal.getInstance(document.getElementById('viewTicketModal')).hide();
                                form.reset();
                                loadTickets();
                                showAlert('success', 'Reply added successfully');
                            } else {
                                showAlert('danger', data.message || 'Error adding reply');
                            }
                        })
                        .catch(() => {
                            showAlert('danger', 'Error adding reply');
                        });
                    });
                });
                </script>
            </div>
            <!-- Notifications Tab -->
            <div class="tab-pane fade" id="notifications" role="tabpanel" aria-labelledby="notifications-tab">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Notification Settings</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Set up your notification preferences to receive alerts for successful transactions.</p>
                        <form action="{{ route('notifications.settings') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="telegram_bot_token" class="block text-sm font-medium text-slate-700">Telegram Bot Token</label>
                                <input type="text" name="telegram_bot_token" id="telegram_bot_token" value="{{ old('telegram_bot_token', $telegram_bot_token ?? '') }}" required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="mt-1 text-sm text-gray-500">Get your bot token from <a href="https://core.telegram.org/bots#botfather" target="_blank">BotFather</a>.</p>
                                @error('telegram_bot_token')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="telegram_chat_id" class="block text-sm font-medium text-slate-700">Telegram Chat ID</label>
                                <input type="text" name="telegram_chat_id" id="telegram_chat_id" value="{{ old('telegram_chat_id', $telegram_chat_id ?? '') }}" required
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="mt-1 text-sm text-gray-500">Find your chat ID by messaging <a href="https://t.me/userinfobot" target="_blank">@userinfobot</a>.</p>
                                @error('telegram_chat_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-700">Notification Preferences</label>
                                <div class="mt-2">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="notify_successful" id="notify_successful" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="notify_successful" class="ml-2 block text-sm text-gray-900">Notify on Successful Transactions</label>
                                    </div>
                                    <div class="flex items-center mt-2">
                                        <input type="checkbox" name="notify_failed" id="notify_failed" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="notify_failed" class="ml-2 block text-sm text-gray-900">Notify on Failed Transactions</label>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Save Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Defensive modal and button lookups
        const withdrawalButton = document.getElementById('withdrawalButton');
        const openPinModalBtn = document.getElementById('openPinModal');
        const verifyPinButton = document.getElementById('verifyPinButton');
        const withdrawalForm = document.getElementById('withdrawalForm');
        const pinInput = document.getElementById('pin');
        const withdrawalModalEl = document.getElementById('withdrawalModal');
        const pinModalEl = document.getElementById('pinVerificationModal');
        const successModalEl = document.getElementById('successModal');
        const errorModalEl = document.getElementById('insufficientFundsModal');

        // Debug log for missing elements
        console.log({
          withdrawalButton, openPinModalBtn, verifyPinButton, withdrawalForm, pinInput, withdrawalModalEl, pinModalEl, successModalEl, errorModalEl
        });

        let withdrawalModal = null;
        let pinModal = null;
        let successModal = null;
        let errorModal = null;
        if (withdrawalModalEl) withdrawalModal = new bootstrap.Modal(withdrawalModalEl);
        if (pinModalEl) pinModal = new bootstrap.Modal(pinModalEl);
        if (successModalEl) successModal = new bootstrap.Modal(successModalEl);
        if (errorModalEl) errorModal = new bootstrap.Modal(errorModalEl);

        // Open withdrawal modal (if not using data-bs-toggle)
        if (withdrawalButton && withdrawalModal) {
            withdrawalButton.addEventListener('click', function() {
                withdrawalModal.show();
            });
        }

        // Continue to PIN modal
        if (openPinModalBtn && withdrawalForm && pinModal && withdrawalModal) {
            openPinModalBtn.addEventListener('click', function() {
                const beneficiaryId = withdrawalForm.querySelector('[name="beneficiary_id"]').value;
                const amount = withdrawalForm.querySelector('[name="amount"]').value;
                if (!beneficiaryId || !amount) {
                    alert('Please fill in all required fields');
                    return;
                }
                withdrawalModal.hide();
                setTimeout(() => {
                    pinModal.show();
                    // Move focus to the PIN input after the modal is shown
                    setTimeout(() => {
                        const pinInput = document.getElementById('pin');
                        if (pinInput) pinInput.focus();
                    }, 300);
                }, 400);
            });
        }

        // After PIN modal is closed, move focus to withdrawal button
        if (pinModalEl) {
            pinModalEl.addEventListener('hidden.bs.modal', function () {
                const withdrawalButton = document.getElementById('withdrawalButton');
                if (withdrawalButton) withdrawalButton.focus();
            });
        }

        // Handle PIN verification and withdrawal
        if (verifyPinButton && withdrawalForm && pinInput && pinModal) {
            verifyPinButton.addEventListener('click', function() {
                const pin = pinInput.value;
                if (!pin || pin.length !== 4) {
                    alert('Please enter a valid 4-digit PIN');
                    return;
                }
                const data = {
                    beneficiary_id: withdrawalForm.querySelector('[name="beneficiary_id"]').value,
                    amount: withdrawalForm.querySelector('[name="amount"]').value,
                    narration: withdrawalForm.querySelector('[name="narration"]').value,
                    pin: pin
                };
                const verifyBtn = this;
                verifyBtn.disabled = true;
                verifyBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
                fetch('{{ route("withdrawals.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    pinModal.hide();
                    pinInput.value = '';
                    if (data.success) {
                        if (successModal) {
                            document.getElementById('successMessage').textContent = data.message;
                            successModal.show();
                        } else {
                            alert(data.message);
                        }
                        withdrawalForm.reset();
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else {
                        if (errorModal) {
                            document.getElementById('insufficientFundsMessage').textContent = data.message;
                            errorModal.show();
                        } else {
                            alert(data.message);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    pinModal.hide();
                    if (errorModal) {
                        document.getElementById('insufficientFundsMessage').textContent = 'An error occurred while processing the withdrawal. Please try again.';
                        errorModal.show();
                    } else {
                        alert('An error occurred while processing the withdrawal. Please try again.');
                    }
                })
                .finally(() => {
                    verifyBtn.disabled = false;
                    verifyBtn.innerHTML = 'Verify & Proceed';
                });
            });
        }
    });
    </script>
    @endpush

@push('scripts')
    <script src="/js/site-management.js"></script>
@endpush

    <!-- Mobile Sticky Bottom Navigation -->
    <div class="d-md-none fixed-bottom bg-white border-top" style="z-index: 1030;">
        <div class="row g-0">
            <div class="col-3">
                <button class="btn btn-link w-100 py-2 text-decoration-none nav-link-mobile active" data-bs-toggle="tab" data-bs-target="#overview" type="button">
                    <i class="fas fa-chart-line fs-6"></i>
                    <div class="small mt-1">Overview</div>
                </button>
            </div>
            <div class="col-3">
                <button class="btn btn-link w-100 py-2 text-decoration-none nav-link-mobile" data-bs-toggle="tab" data-bs-target="#sites" type="button">
                    <i class="fas fa-map-marker-alt fs-6"></i>
                    <div class="small mt-1">Sites</div>
                </button>
            </div>
            <div class="col-3">
                <a class="btn btn-link w-100 py-2 text-decoration-none nav-link-mobile" href="{{ route('transactions.index') }}">
                    <i class="far fa-credit-card fs-6"></i>
                    <div class="small mt-1">Txns</div>
                </a>
            </div>
            <div class="col-3">
                <a class="btn btn-link w-100 py-2 text-decoration-none nav-link-mobile" href="{{ route('withdrawal.dashboard') }}">
                    <i class="fas fa-wave-square fs-6"></i>
                    <div class="small mt-1">Transfer</div>
                </a>
            </div>
        </div>
        <div class="row g-0">
            <div class="col-3">
                <a class="btn btn-link w-100 py-2 text-decoration-none nav-link-mobile" href="{{ route('statistics.index') }}">
                    <i class="fas fa-user-friends fs-6"></i>
                    <div class="small mt-1">Stats</div>
                </a>
            </div>
            <div class="col-3">
                <button class="btn btn-link w-100 py-2 text-decoration-none nav-link-mobile" data-bs-toggle="tab" data-bs-target="#business-profile" type="button" onclick="showBusinessProfileTab()">
                    <i class="fas fa-briefcase fs-6"></i>
                    <div class="small mt-1">Profile</div>
                </button>
            </div>
            <div class="col-3">
                <button class="btn btn-link w-100 py-2 text-decoration-none nav-link-mobile" data-bs-toggle="tab" data-bs-target="#tickets" type="button" onclick="showTicketsTab()">
                    <i class="fas fa-ticket-alt fs-6"></i>
                    <div class="small mt-1">Tickets</div>
                </button>
            </div>
            <div class="col-3">
                <button class="btn btn-link w-100 py-2 text-decoration-none nav-link-mobile" data-bs-toggle="tab" data-bs-target="#notifications" type="button">
                    <i class="fas fa-bell fs-6"></i>
                    <div class="small mt-1">Alerts</div>
                </button>
            </div>
        </div>
    </div>

    <style>
    .nav-link-mobile {
        color: #6c757d !important;
        border: none !important;
        border-radius: 0 !important;
        transition: all 0.2s ease;
        font-size: 0.75rem;
        line-height: 1.2;
    }
    
    .nav-link-mobile:hover,
    .nav-link-mobile.active {
        color: #0d6efd !important;
        background-color: #f8f9fa !important;
    }
    
    .nav-link-mobile.active {
        border-bottom: 3px solid #0d6efd !important;
    }
    
    .nav-link-mobile .small {
        font-size: 0.7rem !important;
        line-height: 1;
        margin-top: 2px !important;
    }
    
    /* Ensure content doesn't get hidden behind mobile nav */
    @media (max-width: 767.98px) {
        body {
            padding-bottom: 140px;
        }
    }
    </style>

    <script>
    // Mobile navigation functionality
    document.addEventListener('DOMContentLoaded', function() {
        const mobileNavLinks = document.querySelectorAll('.nav-link-mobile');
        
        // Function to show tab content
        function showTab(tabId) {
            // Hide all tab panes
            const tabPanes = document.querySelectorAll('.tab-pane');
            tabPanes.forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            
            // Show the target tab pane
            const targetPane = document.getElementById(tabId);
            if (targetPane) {
                targetPane.classList.add('show', 'active');
            }
            
            // Update mobile nav active state
            mobileNavLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('data-bs-target') === '#' + tabId) {
                    link.classList.add('active');
                }
            });
        }
        
        mobileNavLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Remove active class from all mobile nav links
                mobileNavLinks.forEach(l => l.classList.remove('active'));
                // Add active class to clicked link
                this.classList.add('active');
                
                // Handle tab switching for buttons (not links)
                if (this.tagName === 'BUTTON') {
                    e.preventDefault();
                    const target = this.getAttribute('data-bs-target');
                    if (target) {
                        const tabId = target.replace('#', '');
                        showTab(tabId);
                    }
                }
            });
        });
        
        // Initialize with overview tab active
        showTab('overview');
        
        // Function to show tickets tab specifically
        window.showTicketsTab = function() {
            showTab('tickets');
            // Trigger ticket loading after a short delay to ensure DOM is ready
            setTimeout(() => {
                if (typeof loadTickets === 'function') {
                    loadTickets();
                } else {
                    // If loadTickets function doesn't exist, try to trigger the desktop tab event
                    const desktopTicketsTab = document.getElementById('tickets-tab');
                    if (desktopTicketsTab) {
                        const event = new Event('shown.bs.tab');
                        desktopTicketsTab.dispatchEvent(event);
                    }
                }
            }, 100);
        };
        
        // Function to show business profile tab specifically
        window.showBusinessProfileTab = function() {
            showTab('business-profile');
            // Trigger business profile loading after a short delay to ensure DOM is ready
            setTimeout(() => {
                // Try to trigger the desktop tab event to load business profile
                const desktopBusinessProfileTab = document.getElementById('business-profile-tab');
                if (desktopBusinessProfileTab) {
                    const event = new Event('shown.bs.tab');
                    desktopBusinessProfileTab.dispatchEvent(event);
                }
            }, 100);
        };
        
        // Listen for desktop tab changes and update mobile nav
        const tabElements = document.querySelectorAll('[data-bs-toggle="tab"]');
        tabElements.forEach(tab => {
            tab.addEventListener('shown.bs.tab', function(e) {
                const target = e.target.getAttribute('data-bs-target');
                if (target) {
                    const tabId = target.replace('#', '');
                    showTab(tabId);
                }
            });
        });
    });
    </script>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="successMessage">Operation completed successfully!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>