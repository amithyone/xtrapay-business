<x-app-layout>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Enter Admin Code</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.code.verify') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="code" class="form-label">Enter 5-digit Code</label>
                                <input type="text" class="form-control" id="code" name="code" required 
                                    pattern="[0-9]{5}" maxlength="5" placeholder="Enter 5-digit code">
                                @error('code')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 