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
                <h1 class="h2 fw-bold mb-1">Site Management</h1>
                <p class="text-secondary mb-0">Manage your business locations and monitor their performance</p>
            </div>
            <div class="mt-3 mt-md-0 d-flex gap-2">
                <a href="{{ route('documentation.index') }}" class="btn btn-outline-info" target="_blank">
                    <i class="fas fa-book me-2"></i>API Documentation
                </a>
                <a href="{{ route('sites.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Site
                </a>
            </div>
        </div>

        <!-- Sites Grid -->
        <div class="row">
            @foreach($sites as $site)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title mb-1">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>{{ $site->name }}
                            </h5>
                            <p class="text-muted small mb-0">{{ $site->url }}</p>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('sites.edit', $site) }}">
                                    <i class="fas fa-edit me-2"></i>Edit
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('sites.show', $site) }}">
                                    <i class="fas fa-eye me-2"></i>View
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                @if($site->is_active)
                                    <li>
                                        <form action="{{ route('sites.deactivate', $site) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-pause me-2"></i>Deactivate
                                            </button>
                                        </form>
                                    </li>
                                @else
                                    <li>
                                        <form action="{{ route('sites.activate', $site) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-success">
                                                <i class="fas fa-play me-2"></i>Activate
                                            </button>
                                        </form>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="bg-success bg-opacity-10 rounded p-3">
                                    <p class="text-muted small mb-1">Daily Revenue</p>
                                    <p class="text-success fw-bold mb-0">₦{{ number_format($site->daily_revenue, 2) }}</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-primary bg-opacity-10 rounded p-3">
                                    <p class="text-muted small mb-1">Monthly Total</p>
                                    <p class="text-primary fw-bold mb-0">₦{{ number_format($site->monthly_revenue, 2) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <span class="badge {{ $site->is_active ? 'bg-success' : 'bg-secondary' }} rounded-pill">
                                <i class="fas fa-circle me-1"></i>
                                {{ $site->is_active ? 'Currently Active' : 'Currently Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($sites->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No Sites Found</h4>
            <p class="text-muted">Get started by creating your first site.</p>
            <a href="{{ route('sites.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create Your First Site
            </a>
        </div>
        @endif
    </div>
</x-app-layout> 