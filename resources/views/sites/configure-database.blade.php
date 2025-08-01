<x-app-layout>
    <div class="container py-5">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
            <div>
                <h1 class="h2 fw-bold mb-1">Configure Database Structure</h1>
                <p class="text-secondary mb-0">Set up external database connection for {{ $site->name }}</p>
            </div>
            <div class="mt-3 mt-md-0">
               
                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                    <i class="fas fa-home me-2"></i>Dashboard
                </a>
            </div>
        </div>

        <!-- Site Info Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="card-title">Site Information</h6>
                        <p><strong>Name:</strong> {{ $site->name }}</p>
                        <p><strong>URL:</strong> <a href="{{ $site->url }}" target="_blank">{{ $site->url }}</a></p>
                        <p><strong>Status:</strong> 
                            <span class="badge {{ $site->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $site->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="card-title">Current Configuration</h6>
                        @if($site->field_mapping)
                            <p class="text-success"><i class="fas fa-check-circle me-2"></i>Database configured</p>
                            <p><strong>Database:</strong> {{ $site->field_mapping['db_name'] ?? 'N/A' }}</p>
                            <p><strong>Transaction Table:</strong> {{ $site->field_mapping['transaction_table'] ?? 'N/A' }}</p>
                        @else
                            <p class="text-warning"><i class="fas fa-exclamation-triangle me-2"></i>No database configuration</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Database Configuration Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-database me-2"></i>External Database Configuration
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('sites.save-database-config', $site) }}" method="POST">
                    @csrf
                    
                    <!-- Database Connection -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">Database Connection</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="db_host" class="form-label">Database Host</label>
                            <input type="text" class="form-control @error('field_mapping.db_host') is-invalid @enderror" 
                                   id="db_host" name="field_mapping[db_host]" 
                                   value="{{ old('field_mapping.db_host', $site->field_mapping['db_host'] ?? '') }}" 
                                   placeholder="localhost" required>
                            @error('field_mapping.db_host')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="db_name" class="form-label">Database Name</label>
                            <input type="text" class="form-control @error('field_mapping.db_name') is-invalid @enderror" 
                                   id="db_name" name="field_mapping[db_name]" 
                                   value="{{ old('field_mapping.db_name', $site->field_mapping['db_name'] ?? '') }}" 
                                   placeholder="your_database" required>
                            @error('field_mapping.db_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="db_username" class="form-label">Database Username</label>
                            <input type="text" class="form-control @error('field_mapping.db_username') is-invalid @enderror" 
                                   id="db_username" name="field_mapping[db_username]" 
                                   value="{{ old('field_mapping.db_username', $site->field_mapping['db_username'] ?? '') }}" 
                                   placeholder="db_user" required>
                            @error('field_mapping.db_username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="db_password" class="form-label">Database Password</label>
                            <input type="password" class="form-control @error('field_mapping.db_password') is-invalid @enderror" 
                                   id="db_password" name="field_mapping[db_password]" 
                                   value="{{ old('field_mapping.db_password', $site->field_mapping['db_password'] ?? '') }}" 
                                   placeholder="db_password" required>
                            @error('field_mapping.db_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="db_port" class="form-label">Database Port</label>
                            <input type="number" class="form-control @error('field_mapping.db_port') is-invalid @enderror" 
                                   id="db_port" name="field_mapping[db_port]" 
                                   value="{{ old('field_mapping.db_port', $site->field_mapping['db_port'] ?? '3306') }}" 
                                   placeholder="3306" min="1" max="65535">
                            <div class="form-text">Default: 3306, MAMP: 8889, XAMPP: 3306</div>
                            @error('field_mapping.db_port')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <button type="button" id="testConnection" class="btn btn-outline-primary">
                                <i class="fas fa-plug me-2"></i>Test Database Connection
                            </button>
                            <div id="connectionResult" class="mt-2" style="display: none;"></div>
                        </div>
                    </div>

                    <!-- Database Connection Test -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> Test your database connection before proceeding with table configuration. This will verify that your credentials are correct and the database is accessible.
                    </div>

                    <!-- Transaction Table Configuration -->
                    <div class="row mb-4" id="tableConfiguration" style="opacity: 0.6; pointer-events: none;">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                Transaction Table Configuration
                                <span id="connectionStatus" class="badge bg-secondary ms-2">
                                    @if($site->field_mapping && isset($site->field_mapping['db_host']))
                                        Configuration exists
                                    @else
                                        Test connection first
                                    @endif
                                </span>
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="transaction_table" class="form-label">Transaction Table Name</label>
                            <input type="text" class="form-control @error('field_mapping.transaction_table') is-invalid @enderror" 
                                   id="transaction_table" name="field_mapping[transaction_table]" 
                                   value="{{ old('field_mapping.transaction_table', $site->field_mapping['transaction_table'] ?? '') }}" 
                                   placeholder="transactions" required>
                            @error('field_mapping.transaction_table')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="json_column" class="form-label">JSON Data Column</label>
                            <input type="text" class="form-control @error('field_mapping.json_column') is-invalid @enderror" 
                                   id="json_column" name="field_mapping[json_column]" 
                                   value="{{ old('field_mapping.json_column', $site->field_mapping['json_column'] ?? '') }}" 
                                   placeholder="payment_data" required>
                            <div class="form-text">Column containing payment transaction data in JSON format</div>
                            @error('field_mapping.json_column')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="reference_key" class="form-label">Reference Key (in JSON)</label>
                            <input type="text" class="form-control @error('field_mapping.reference_key') is-invalid @enderror" 
                                   id="reference_key" name="field_mapping[reference_key]" 
                                   value="{{ old('field_mapping.reference_key', $site->field_mapping['reference_key'] ?? 'reference') }}" 
                                   placeholder="reference" required>
                            <div class="form-text">Key in the JSON data that contains the transaction reference (e.g. <code>reference</code>, <code>reference_number</code>)</div>
                            @error('field_mapping.reference_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="amount_column" class="form-label">Amount Column</label>
                            <input type="text" class="form-control @error('field_mapping.amount_column') is-invalid @enderror" 
                                   id="amount_column" name="field_mapping[amount_column]" 
                                   value="{{ old('field_mapping.amount_column', $site->field_mapping['amount_column'] ?? 'amount') }}" 
                                   placeholder="amount" required>
                            <div class="form-text">Column containing the transaction amount (e.g. <code>amount</code>, <code>total</code>, <code>value</code>)</div>
                            @error('field_mapping.amount_column')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="user_id_column" class="form-label">User ID Column</label>
                            <input type="text" class="form-control @error('field_mapping.user_id_column') is-invalid @enderror" 
                                   id="user_id_column" name="field_mapping[user_id_column]" 
                                   value="{{ old('field_mapping.user_id_column', $site->field_mapping['user_id_column'] ?? '') }}" 
                                   placeholder="user_id" required>
                            <div class="form-text">Column containing the user ID in transaction table</div>
                            @error('field_mapping.user_id_column')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- User Table Configuration -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">User Table Configuration</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="user_table" class="form-label">User Table Name</label>
                            <input type="text" class="form-control @error('field_mapping.user_table') is-invalid @enderror" 
                                   id="user_table" name="field_mapping[user_table]" 
                                   value="{{ old('field_mapping.user_table', $site->field_mapping['user_table'] ?? '') }}" 
                                   placeholder="users" required>
                            @error('field_mapping.user_table')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="user_id_column_in_user_table" class="form-label">User ID Column (in User Table)</label>
                            <input type="text" class="form-control @error('field_mapping.user_id_column_in_user_table') is-invalid @enderror" 
                                   id="user_id_column_in_user_table" name="field_mapping[user_id_column_in_user_table]" 
                                   value="{{ old('field_mapping.user_id_column_in_user_table', $site->field_mapping['user_id_column_in_user_table'] ?? '') }}" 
                                   placeholder="id" required>
                            @error('field_mapping.user_id_column_in_user_table')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="user_name_column" class="form-label">User Name Column</label>
                            <input type="text" class="form-control @error('field_mapping.user_name_column') is-invalid @enderror" 
                                   id="user_name_column" name="field_mapping[user_name_column]" 
                                   value="{{ old('field_mapping.user_name_column', $site->field_mapping['user_name_column'] ?? '') }}" 
                                   placeholder="name" required>
                            @error('field_mapping.user_name_column')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="user_email_column" class="form-label">User Email Column</label>
                            <input type="text" class="form-control @error('field_mapping.user_email_column') is-invalid @enderror" 
                                   id="user_email_column" name="field_mapping[user_email_column]" 
                                   value="{{ old('field_mapping.user_email_column', $site->field_mapping['user_email_column'] ?? '') }}" 
                                   placeholder="email" required>
                            @error('field_mapping.user_email_column')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Status Configuration -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">Transaction Status Configuration</h6>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Transaction Status:</strong> Your transaction table has a <code>status</code> column that contains status codes. Configure which status codes represent successful transactions.
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status_column" class="form-label">Status Column Name</label>
                            <input type="text" class="form-control @error('field_mapping.status_column') is-invalid @enderror" 
                                   id="status_column" name="field_mapping[status_column]" 
                                   value="{{ old('field_mapping.status_column', $site->field_mapping['status_column'] ?? 'status') }}" 
                                   placeholder="status" required>
                            <div class="form-text">Name of the column in transaction table that contains status codes</div>
                            @error('field_mapping.status_column')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="success_status_values" class="form-label">Success Status Values</label>
                            <textarea class="form-control @error('field_mapping.success_status_values') is-invalid @enderror" 
                                      id="success_status_values" name="field_mapping[success_status_values]" 
                                      rows="3" placeholder="1" required>{{ old('field_mapping.success_status_values', $site->field_mapping['success_status_values'] ?? '') }}</textarea>
                            <div class="form-text">One value per line. These status codes indicate successful transactions</div>
                            @error('field_mapping.success_status_values')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="pending_status_values" class="form-label">Pending Status Values</label>
                            <textarea class="form-control @error('field_mapping.pending_status_values') is-invalid @enderror" 
                                      id="pending_status_values" name="field_mapping[pending_status_values]" 
                                      rows="3" placeholder="0">{{ old('field_mapping.pending_status_values', $site->field_mapping['pending_status_values'] ?? '') }}</textarea>
                            <div class="form-text">One value per line. These status codes indicate pending transactions</div>
                            @error('field_mapping.pending_status_values')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="rejected_status_values" class="form-label">Rejected Status Values</label>
                            <textarea class="form-control @error('field_mapping.rejected_status_values') is-invalid @enderror" 
                                      id="rejected_status_values" name="field_mapping[rejected_status_values]" 
                                      rows="3" placeholder="2">{{ old('field_mapping.rejected_status_values', $site->field_mapping['rejected_status_values'] ?? '') }}</textarea>
                            <div class="form-text">One value per line. These status codes indicate rejected/failed transactions</div>
                            @error('field_mapping.rejected_status_values')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Common Status Codes</h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong class="text-success">Success:</strong>
                                            <ul class="small mb-0">
                                                <li>success</li>
                                                <li>completed</li>
                                                <li>approved</li>
                                                <li>confirmed</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-3">
                                            <strong class="text-warning">Pending:</strong>
                                            <ul class="small mb-0">
                                                <li>pending</li>
                                                <li>processing</li>
                                                <li>waiting</li>
                                                <li>in_progress</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-3">
                                            <strong class="text-danger">Failed:</strong>
                                            <ul class="small mb-0">
                                                <li>failed</li>
                                                <li>error</li>
                                                <li>declined</li>
                                                <li>cancelled</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-3">
                                            <strong class="text-secondary">Rejected:</strong>
                                            <ul class="small mb-0">
                                                <li>rejected</li>
                                                <li>denied</li>
                                                <li>invalid</li>
                                                <li>expired</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Date Filter Configuration -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">Date Filter Configuration</h6>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Import Efficiency:</strong> Configure how far back to fetch transactions. This helps avoid processing old data repeatedly and improves import performance.
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date_column" class="form-label">Date Column Name</label>
                            <input type="text" class="form-control @error('field_mapping.date_column') is-invalid @enderror" 
                                   id="date_column" name="field_mapping[date_column]" 
                                   value="{{ old('field_mapping.date_column', $site->field_mapping['date_column'] ?? 'created_at') }}" 
                                   placeholder="created_at" required>
                            <div class="form-text">Name of the column in transaction table that contains the transaction date</div>
                            @error('field_mapping.date_column')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="import_lookback_days" class="form-label">Import Lookback Period (Days)</label>
                            <input type="number" class="form-control @error('field_mapping.import_lookback_days') is-invalid @enderror" 
                                   id="import_lookback_days" name="field_mapping[import_lookback_days]" 
                                   value="{{ old('field_mapping.import_lookback_days', $site->field_mapping['import_lookback_days'] ?? '7') }}" 
                                   placeholder="7" min="1" max="365" required>
                            <div class="form-text">How many days back to fetch transactions (1-365 days)</div>
                            @error('field_mapping.import_lookback_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Payment Method Configuration -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">Payment Method Configuration</h6>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Important:</strong> Only instant payment methods will be imported. Configure the fields below to identify instant payments.
                            </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                                <strong>How it works:</strong>
                                <ul class="mb-0 mt-2">
                                    <li><strong>Method Type:</strong> Choose how payment methods are stored in your database</li>
                                    <li><strong>Method Column:</strong> We use the <code>method</code> column directly from your transaction table</li>
                                    <li><strong>Method Code:</strong> Numeric codes that map to method types (e.g., 1=instant, 2=manual, 3=crypto)</li>
                                    <li><strong>Instant Method Values:</strong> List the values that indicate instant payments</li>
                                    <li><strong>Method Code Mapping:</strong> Map codes to types (only needed for method codes)</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="method_type" class="form-label">Method Type</label>
                            <select class="form-select @error('field_mapping.method_type') is-invalid @enderror" 
                                    id="method_type" name="field_mapping[method_type]" required>
                                <option value="">Select method type</option>
                                <option value="method" {{ old('field_mapping.method_type', $site->field_mapping['method_type'] ?? '') == 'method' ? 'selected' : '' }}>Method (direct method name)</option>
                                <option value="method_code" {{ old('field_mapping.method_type', $site->field_mapping['method_type'] ?? '') == 'method_code' ? 'selected' : '' }}>Method Code (coded method identifier)</option>
                            </select>
                            <div class="form-text">How payment methods are stored in your database</div>
                            @error('field_mapping.method_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="instant_method_values" class="form-label">Instant Method Values</label>
                            <textarea class="form-control @error('field_mapping.instant_method_values') is-invalid @enderror" 
                                      id="instant_method_values" name="field_mapping[instant_method_values]" 
                                      rows="3" placeholder="instant&#10;card&#10;bank_transfer" required>{{ old('field_mapping.instant_method_values', $site->field_mapping['instant_method_values'] ?? '') }}</textarea>
                            <div class="form-text">One value per line. These values indicate instant payment methods</div>
                            @error('field_mapping.instant_method_values')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="method_code_mapping" class="form-label">Method Code Mapping (Optional)</label>
                            <textarea class="form-control @error('field_mapping.method_code_mapping') is-invalid @enderror" 
                                      id="method_code_mapping" name="field_mapping[method_code_mapping]" 
                                      rows="3" placeholder="1=instant&#10;2=manual&#10;3=crypto">{{ old('field_mapping.method_code_mapping', $site->field_mapping['method_code_mapping'] ?? '') }}</textarea>
                            <div class="form-text">Format: code=type (one per line). Only needed if using method codes</div>
                            @error('field_mapping.method_code_mapping')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between">
                        <div>
                        <a href="{{ route('sites.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                            @if($site->last_import_at)
                                <button type="button" id="resetImportBtn" class="btn btn-warning ms-2">
                                    <i class="fas fa-redo me-2"></i>Reset Import History
                                </button>
                            @endif
                        </div>
                        <div>
                            @if($site->field_mapping && isset($site->field_mapping['db_host']))
                                <button type="button" id="fetchTransactionsBtn" class="btn btn-success me-2">
                                    <i class="fas fa-download me-2"></i>Fetch Transactions
                                </button>
                            @endif
                            <button type="submit" id="saveConfigBtn" class="btn btn-primary" 
                                    @if(!$site->field_mapping || !isset($site->field_mapping['db_host'])) disabled @endif>
                            <i class="fas fa-save me-2"></i>Save Configuration
                        </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Help Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-question-circle me-2"></i>Configuration Help
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Database Connection</h6>
                        <ul class="small text-muted">
                            <li><strong>Host:</strong> Usually 'localhost' or your database server IP</li>
                            <li><strong>Database:</strong> The name of your external database</li>
                            <li><strong>Username/Password:</strong> Database credentials with read access</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Table Structure</h6>
                        <ul class="small text-muted">
                            <li><strong>Transaction Table:</strong> Table containing payment transactions</li>
                            <li><strong>User Table:</strong> Table containing user information</li>
                            <li><strong>JSON Column:</strong> Column storing transaction data as JSON</li>
                            <li><strong>Status Column:</strong> Column containing transaction status codes</li>
                        </ul>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6>Status Configuration</h6>
                        <ul class="small text-muted">
                            <li><strong>Status Column:</strong> Name of the column containing status codes</li>
                            <li><strong>Success Values:</strong> List of status codes that indicate successful transactions</li>
                            <li><strong>Common Codes:</strong> success, completed, approved, confirmed</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Payment Methods</h6>
                        <ul class="small text-muted">
                            <li><strong>Method Type:</strong> How payment methods are stored (direct names or codes)</li>
                            <li><strong>Method Column:</strong> We use the <code>method</code> column directly from your transaction table</li>
                            <li><strong>Instant Values:</strong> Values that indicate instant payment methods</li>
                            <li><strong>Code Mapping:</strong> Maps numeric codes to method types (if using codes)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const testConnectionBtn = document.getElementById('testConnection');
            const connectionResult = document.getElementById('connectionResult');
            const tableConfiguration = document.getElementById('tableConfiguration');
            const connectionStatus = document.getElementById('connectionStatus');
            const saveConfigBtn = document.getElementById('saveConfigBtn');
            const resetImportBtn = document.getElementById('resetImportBtn');
            const fetchTransactionsBtn = document.getElementById('fetchTransactionsBtn');
            
            // Check if there's already a configuration
            @if($site->field_mapping && isset($site->field_mapping['db_host']))
                // Enable form if configuration exists
                tableConfiguration.style.opacity = '1';
                tableConfiguration.style.pointerEvents = 'auto';
                connectionStatus.textContent = 'Configuration exists';
                connectionStatus.classList.remove('bg-secondary');
                connectionStatus.classList.add('bg-info');
                saveConfigBtn.disabled = false;
            @endif
            
            // Handle reset import history button
            if (resetImportBtn) {
                resetImportBtn.addEventListener('click', function() {
                    if (confirm('Are you sure you want to reset the import history? This will cause the next import to fetch all historical data again.')) {
                        // Show loading state
                        resetImportBtn.disabled = true;
                        resetImportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Resetting...';
                        
                        // Make AJAX request
                        fetch('{{ route("sites.reset-import-history", $site) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showConnectionResult(data.message, 'success');
                                // Hide the reset button after successful reset
                                resetImportBtn.style.display = 'none';
                            } else {
                                showConnectionResult(data.message || 'Failed to reset import history', 'danger');
                            }
                        })
                        .catch(error => {
                            showConnectionResult('Failed to reset import history: ' + error.message, 'danger');
                        })
                        .finally(() => {
                            // Reset button state
                            resetImportBtn.disabled = false;
                            resetImportBtn.innerHTML = '<i class="fas fa-redo me-2"></i>Reset Import History';
                        });
                    }
                });
            }
            
            // Handle fetch transactions button
            if (fetchTransactionsBtn) {
                fetchTransactionsBtn.addEventListener('click', function() {
                    if (confirm('Are you sure you want to fetch transactions from the external database? This will import all new transactions since the last import.')) {
                        // Show loading state
                        fetchTransactionsBtn.disabled = true;
                        fetchTransactionsBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Fetching...';
                        
                        // Make AJAX request
                        fetch('{{ route("sites.fetch-transactions", $site) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showConnectionResult(data.message, 'success');
                            } else {
                                showConnectionResult(data.message || 'Failed to fetch transactions', 'danger');
                            }
                        })
                        .catch(error => {
                            showConnectionResult('Failed to fetch transactions: ' + error.message, 'danger');
                        })
                        .finally(() => {
                            // Reset button state
                            fetchTransactionsBtn.disabled = false;
                            fetchTransactionsBtn.innerHTML = '<i class="fas fa-download me-2"></i>Fetch Transactions';
                        });
                    }
                });
            }
            
            testConnectionBtn.addEventListener('click', function() {
                // Get form data
                const formData = {
                    db_host: document.getElementById('db_host').value,
                    db_name: document.getElementById('db_name').value,
                    db_username: document.getElementById('db_username').value,
                    db_password: document.getElementById('db_password').value,
                    db_port: document.getElementById('db_port').value,
                    _token: '{{ csrf_token() }}'
                };
                
                // Validate required fields
                if (!formData.db_host || !formData.db_name || !formData.db_username || !formData.db_password || !formData.db_port) {
                    showConnectionResult('Please fill in all database connection fields.', 'danger');
                    return;
                }
                
                // Show loading state
                testConnectionBtn.disabled = true;
                testConnectionBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Testing Connection...';
                
                // Make AJAX request
                fetch('{{ route("sites.test-database-connection", $site) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showConnectionResult(data.message, 'success');
                        connectionStatus.textContent = 'Connection successful';
                        connectionStatus.className = 'badge bg-success ms-2';
                        tableConfiguration.style.opacity = '1';
                        tableConfiguration.style.pointerEvents = 'auto';
                        saveConfigBtn.disabled = false;
                    } else {
                        showConnectionResult(data.message, 'danger');
                        connectionStatus.textContent = 'Connection failed';
                        connectionStatus.className = 'badge bg-danger ms-2';
                        tableConfiguration.style.opacity = '0.6';
                        tableConfiguration.style.pointerEvents = 'none';
                        saveConfigBtn.disabled = true;
                    }
                })
                .catch(error => {
                    showConnectionResult('Connection test failed: ' + error.message, 'danger');
                    connectionStatus.textContent = 'Connection failed';
                    connectionStatus.className = 'badge bg-danger ms-2';
                    tableConfiguration.style.opacity = '0.6';
                    tableConfiguration.style.pointerEvents = 'none';
                    saveConfigBtn.disabled = true;
                })
                .finally(() => {
                    // Reset button state
                    testConnectionBtn.disabled = false;
                    testConnectionBtn.innerHTML = '<i class="fas fa-plug me-2"></i>Test Database Connection';
                });
            });
            
            function showConnectionResult(message, type) {
                connectionResult.innerHTML = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                connectionResult.style.display = 'block';
            }
        });
    </script>
</x-app-layout>
