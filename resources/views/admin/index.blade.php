<x-app-layout>
    <div class="container-fluid py-4">
        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6 class="card-title">Total Users</h6>
                        <h2 class="mb-0">{{ \App\Models\User::count() }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6 class="card-title">Total Revenue</h6>
                        <h2 class="mb-0">₦{{ number_format(\App\Models\Transaction::where('status', 'success')->sum('amount'), 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6 class="card-title">Total Sites</h6>
                        <h2 class="mb-0">{{ \App\Models\Site::count() }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h6 class="card-title">Pending Withdrawals</h6>
                        <h2 class="mb-0">₦{{ number_format(\App\Models\Transfer::where('is_approved', false)->sum('amount'), 2) }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <!-- Users Management -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Users Management</h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            Add New User
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Balance</th>
                                        <th>Status</th>
                                        <th>Joined Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(\App\Models\User::latest()->take(10)->get() as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="badge bg-{{ $user->is_admin ? 'danger' : 'info' }}">
                                                {{ $user->is_admin ? 'Admin' : 'User' }}
                                            </span>
                                        </td>
                                        <td>₦{{ number_format($user->balance, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="viewUser({{ $user->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-primary" onclick="editUser({{ $user->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-{{ $user->is_active ? 'danger' : 'success' }}" 
                                                    onclick="toggleUserStatus({{ $user->id }})">
                                                <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sites Management -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Sites Management</h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSiteModal">
                            Add New Site
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Site Name</th>
                                        <th>API Key</th>
                                        <th>Database</th>
                                        <th>Webhook URL</th>
                                        <th>Status</th>
                                        <th>Revenue</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(\App\Models\Site::latest()->get() as $site)
                                    <tr>
                                        <td>{{ $site->name }}</td>
                                        <td>
                                            <code>{{ Str::limit($site->api_key, 20) }}</code>
                                            <button class="btn btn-sm btn-link" onclick="copyApiKey('{{ $site->api_key }}')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </td>
                                        <td>{{ $site->db_connection }}</td>
                                        <td>{{ Str::limit($site->webhook_url, 30) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $site->is_active ? 'success' : 'danger' }}">
                                                {{ $site->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>₦{{ number_format($site->transactions()->sum('amount'), 2) }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="viewSite({{ $site->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-primary" onclick="editSite({{ $site->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-{{ $site->is_active ? 'danger' : 'success' }}" 
                                                    onclick="toggleSiteStatus({{ $site->id }})">
                                                <i class="fas fa-{{ $site->is_active ? 'ban' : 'check' }}"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Database Connections -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Database Connections</h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addConnectionModal">
                            Add New Connection
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Site</th>
                                        <th>Database Type</th>
                                        <th>Host</th>
                                        <th>Database</th>
                                        <th>Status</th>
                                        <th>Last Sync</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(\App\Models\Site::whereNotNull('db_connection')->get() as $site)
                                    <tr>
                                        <td>{{ $site->name }}</td>
                                        <td>{{ $site->db_connection }}</td>
                                        <td>{{ $site->db_host }}</td>
                                        <td>{{ $site->db_name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $site->db_status ? 'success' : 'danger' }}">
                                                {{ $site->db_status ? 'Connected' : 'Disconnected' }}
                                            </span>
                                        </td>
                                        <td>{{ $site->last_sync ? $site->last_sync->format('M d, Y H:i') : 'Never' }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="testConnection({{ $site->id }})">
                                                <i class="fas fa-plug"></i> Test
                                            </button>
                                            <button class="btn btn-sm btn-primary" onclick="editConnection({{ $site->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-success" onclick="syncData({{ $site->id }})">
                                                <i class="fas fa-sync"></i> Sync
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Successful Transactions -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Successful Transactions</h5>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary" onclick="exportTransactions()">
                                <i class="fas fa-download"></i> Export
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="refreshTransactions()">
                                <i class="fas fa-sync"></i> Refresh
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="transactionsTable">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Site</th>
                                        <th>Amount</th>
                                        <th>Customer</th>
                                        <th>Payment Method</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(\App\Models\Transaction::where('status', 'success')->latest()->take(10)->get() as $transaction)
                                    <tr>
                                        <td>{{ $transaction->reference }}</td>
                                        <td>{{ $transaction->site->name }}</td>
                                        <td>₦{{ number_format($transaction->amount, 2) }}</td>
                                        <td>{{ $transaction->customer_name ?? 'N/A' }}</td>
                                        <td>{{ $transaction->payment_method }}</td>
                                        <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <span class="badge bg-success">Success</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="viewTransaction({{ $transaction->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-primary" onclick="editTransaction({{ $transaction->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Withdrawals -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Pending Withdrawal Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Amount</th>
                                        <th>Bank Details</th>
                                        <th>Request Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(\App\Models\Transfer::where('is_approved', false)->latest()->get() as $transfer)
                                    <tr>
                                        <td>
                                            <div>{{ $transfer->user->name }}</div>
                                            <small class="text-muted">{{ $transfer->user->email }}</small>
                                        </td>
                                        <td>₦{{ number_format($transfer->amount, 2) }}</td>
                                        <td>
                                            <div>{{ $transfer->bank_name }}</div>
                                            <div>{{ $transfer->account_number }}</div>
                                            <div>{{ $transfer->account_name }}</div>
                                        </td>
                                        <td>{{ $transfer->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-success" onclick="approveWithdrawal({{ $transfer->id }})">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="rejectWithdrawal({{ $transfer->id }})">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
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
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-control" name="is_admin">
                                <option value="0">User</option>
                                <option value="1">Admin</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveUser()">Save User</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Site Modal -->
    <div class="modal fade" id="addSiteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Site</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addSiteForm">
                        <div class="mb-3">
                            <label class="form-label">Site Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API Key</label>
                            <input type="text" class="form-control" name="api_key" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Webhook URL</label>
                            <input type="url" class="form-control" name="webhook_url">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Database Connection</label>
                            <select class="form-control" name="db_connection">
                                <option value="mysql">MySQL</option>
                                <option value="pgsql">PostgreSQL</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveSite()">Save Site</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Connection Modal -->
    <div class="modal fade" id="addConnectionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Database Connection</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addConnectionForm">
                        <div class="mb-3">
                            <label class="form-label">Site</label>
                            <select class="form-control" name="site_id" required>
                                @foreach(\App\Models\Site::all() as $site)
                                    <option value="{{ $site->id }}">{{ $site->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Database Type</label>
                            <select class="form-control" name="db_connection" required>
                                <option value="mysql">MySQL</option>
                                <option value="pgsql">PostgreSQL</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Host</label>
                            <input type="text" class="form-control" name="db_host" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Database Name</label>
                            <input type="text" class="form-control" name="db_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="db_username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="db_password" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveConnection()">Save Connection</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction View Modal -->
    <div class="modal fade" id="transactionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Transaction Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Basic Information</h6>
                            <table class="table">
                                <tr>
                                    <th>Reference</th>
                                    <td id="modal-reference"></td>
                                </tr>
                                <tr>
                                    <th>Amount</th>
                                    <td id="modal-amount"></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td id="modal-status"></td>
                                </tr>
                                <tr>
                                    <th>Payment Method</th>
                                    <td id="modal-payment-method"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Customer Information</h6>
                            <table class="table">
                                <tr>
                                    <th>Name</th>
                                    <td id="modal-customer-name"></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td id="modal-customer-email"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Metadata</h6>
                            <pre id="modal-metadata" class="bg-light p-3"></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function viewUser(id) {
            // Implement user view logic
        }

        function editUser(id) {
            // Implement user edit logic
        }

        function toggleUserStatus(id) {
            if (confirm('Are you sure you want to change this user\'s status?')) {
                fetch(`/admin/users/${id}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert('User status updated successfully');
                    location.reload();
                });
            }
        }

        function saveUser() {
            const form = document.getElementById('addUserForm');
            const formData = new FormData(form);

            fetch('/admin/users', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert('User created successfully');
                location.reload();
            });
        }

        function viewSite(id) {
            // Implement site view logic
        }

        function editSite(id) {
            // Implement site edit logic
        }

        function toggleSiteStatus(id) {
            if (confirm('Are you sure you want to change this site\'s status?')) {
                fetch(`/admin/sites/${id}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert('Site status updated successfully');
                    location.reload();
                });
            }
        }

        function saveSite() {
            const form = document.getElementById('addSiteForm');
            const formData = new FormData(form);

            fetch('/admin/sites', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert('Site created successfully');
                location.reload();
            });
        }

        function testConnection(id) {
            fetch(`/admin/connections/${id}/test`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
            });
        }

        function editConnection(id) {
            // Implement connection edit logic
        }

        function syncData(id) {
            if (confirm('Are you sure you want to sync data for this connection?')) {
                fetch(`/admin/connections/${id}/sync`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert('Data sync completed successfully');
                    location.reload();
                });
            }
        }

        function saveConnection() {
            const form = document.getElementById('addConnectionForm');
            const formData = new FormData(form);

            fetch('/admin/connections', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert('Connection created successfully');
                location.reload();
            });
        }

        function copyApiKey(key) {
            navigator.clipboard.writeText(key).then(() => {
                alert('API Key copied to clipboard');
            });
        }

        function viewTransaction(id) {
            fetch(`/admin/transactions/${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modal-reference').textContent = data.reference;
                    document.getElementById('modal-amount').textContent = `₦${parseFloat(data.amount).toLocaleString()}`;
                    document.getElementById('modal-status').textContent = data.status;
                    document.getElementById('modal-payment-method').textContent = data.payment_method;
                    document.getElementById('modal-customer-name').textContent = data.customer_name || 'N/A';
                    document.getElementById('modal-customer-email').textContent = data.customer_email || 'N/A';
                    document.getElementById('modal-metadata').textContent = JSON.stringify(data.metadata, null, 2);
                    
                    new bootstrap.Modal(document.getElementById('transactionModal')).show();
                });
        }

        function approveWithdrawal(id) {
            if (confirm('Are you sure you want to approve this withdrawal?')) {
                fetch(`/admin/withdrawals/${id}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert('Withdrawal approved successfully');
                    location.reload();
                });
            }
        }

        function rejectWithdrawal(id) {
            if (confirm('Are you sure you want to reject this withdrawal?')) {
                fetch(`/admin/withdrawals/${id}/reject`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert('Withdrawal rejected successfully');
                    location.reload();
                });
            }
        }

        function exportTransactions() {
            window.location.href = '/admin/transactions/export';
        }

        function refreshTransactions() {
            location.reload();
        }
    </script>
    @endpush
</x-app-layout> 