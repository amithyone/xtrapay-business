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
                <h1 class="h2 fw-bold mb-1">Add New Site</h1>
                <p class="text-secondary mb-0">Create a new site for your business</p>
            </div>
            <div class="mt-3 mt-md-0 d-flex gap-2">
                <a href="{{ route('documentation.index') }}" class="btn btn-outline-info" target="_blank">
                    <i class="fas fa-book me-2"></i>API Documentation
                </a>
                <a href="{{ route('sites.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Sites
                </a>
            </div>
        </div>

        <!-- Create Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-plus me-2"></i>Site Information
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('sites.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Site Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="Enter site name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="url" class="form-label">Site URL</label>
                            <input type="url" class="form-control @error('url') is-invalid @enderror" 
                                   id="url" name="url" 
                                   value="{{ old('url') }}" 
                                   placeholder="https://example.com" required>
                            @error('url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="webhook_url" class="form-label">
                                Webhook URL
                                <a href="{{ route('documentation.integration-guide') }}#setting-up-webhook-notifications" target="_blank" class="text-info ms-2" title="Learn about webhook setup">
                                    <i class="fas fa-question-circle"></i>
                                </a>
                            </label>
                            <input type="url" class="form-control @error('webhook_url') is-invalid @enderror" 
                                   id="webhook_url" name="webhook_url" 
                                   value="{{ old('webhook_url') }}" 
                                   placeholder="https://example.com/webhooks/xtrapay" required>
                            <div class="form-text">
                                Where to receive payment notifications. 
                                <a href="{{ route('documentation.integration-guide') }}#setting-up-webhook-notifications" target="_blank">See documentation</a>
                            </div>
                            @error('webhook_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="api_code" class="form-label">API Code</label>
                            <input type="text" class="form-control @error('api_code') is-invalid @enderror" 
                                   id="api_code" name="api_code" 
                                   value="{{ old('api_code') }}" 
                                   placeholder="Enter API code" required>
                            @error('api_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="allowed_ips" class="form-label">Allowed IPs</label>
                            <input type="text" class="form-control @error('allowed_ips') is-invalid @enderror" 
                                   id="allowed_ips" name="allowed_ips" 
                                   value="{{ old('allowed_ips') }}" 
                                   placeholder="Comma-separated IPs (e.g. 1.2.3.4,5.6.7.8)">
                            <div class="form-text">Only these IPs can send webhooks to this site.</div>
                            @error('allowed_ips')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input @error('is_active') is-invalid @enderror" 
                                       id="is_active" name="is_active" value="1" 
                                       {{ old('is_active') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Site
                                </label>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('sites.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Site
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- API Integration Info Card -->
        <div class="card mt-4 border-info">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>Need Help with Integration?
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-3">After creating your site, you'll receive API credentials. Use them to integrate XtraPay Virtual Accounts:</p>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('documentation.quick-start') }}" class="btn btn-sm btn-outline-primary" target="_blank">
                        <i class="fas fa-rocket me-2"></i>Quick Start Guide
                    </a>
                    <a href="{{ route('documentation.integration-guide') }}" class="btn btn-sm btn-outline-success" target="_blank">
                        <i class="fas fa-code me-2"></i>Integration Guide
                    </a>
                    <a href="{{ route('documentation.api-docs') }}" class="btn btn-sm btn-outline-info" target="_blank">
                        <i class="fas fa-server me-2"></i>API Reference
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 