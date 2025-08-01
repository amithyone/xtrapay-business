<x-app-layout>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Support Tickets</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                <i class="fas fa-plus me-2"></i>Create New Ticket
            </button>
        </div>

        @if(!Auth::user()->businessProfile)
        <div class="alert alert-info" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle me-3 fs-4"></i>
                <div>
                    <h5 class="alert-heading mb-1">Complete Your Business Profile</h5>
                    <p class="mb-0">Please complete your business profile before creating support tickets.</p>
                    <a href="{{ route('business-profile.create') }}" class="btn btn-primary mt-3">Create Business Profile</a>
                </div>
            </div>
        </div>
        @endif

        <!-- Rest of your existing tickets view code -->
    </div>
</x-app-layout> 