<x-app-layout>
    <div class="container py-5 pb-5 pb-md-4">
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
                <h1 class="h2 fw-bold mb-1">
                    <i class="fas fa-bell me-2 text-primary"></i>Notification Settings
                </h1>
                <p class="text-secondary mb-0">Configure your notification preferences for real-time alerts</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Settings Form -->
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-bell me-2"></i>Telegram Notifications
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Debug info -->
                        @if(config('app.debug'))
                        <div class="alert alert-info">
                            <strong>Debug Info:</strong><br>
                            Current telegram_bot_token: {{ $telegram_bot_token ?? 'NULL' }}<br>
                            Current telegram_chat_id: {{ $telegram_chat_id ?? 'NULL' }}<br>
                            Old telegram_bot_token: {{ old('telegram_bot_token') ?? 'NULL' }}<br>
                            Old telegram_chat_id: {{ old('telegram_chat_id') ?? 'NULL' }}
                        </div>
                        @endif
                        
                        <form action="{{ route('notifications.settings') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="telegram_bot_token" class="form-label fw-semibold">
                                        <i class="fas fa-robot me-2 text-primary"></i>Telegram Bot Token
                                    </label>
                                    <input type="text" class="form-control @error('telegram_bot_token') is-invalid @enderror" 
                                           id="telegram_bot_token" name="telegram_bot_token" 
                                           value="{{ old('telegram_bot_token', $telegram_bot_token ?? '') }}" 
                                           placeholder="Enter your Telegram bot token" required>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Get your bot token from <a href="https://core.telegram.org/bots#botfather" target="_blank" class="text-decoration-none">BotFather</a>
                                    </div>
                                    @error('telegram_bot_token')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="telegram_chat_id" class="form-label fw-semibold">
                                        <i class="fas fa-comments me-2 text-primary"></i>Telegram Chat ID
                                    </label>
                                    <input type="text" class="form-control @error('telegram_chat_id') is-invalid @enderror" 
                                           id="telegram_chat_id" name="telegram_chat_id" 
                                           value="{{ old('telegram_chat_id', $telegram_chat_id ?? '') }}" 
                                           placeholder="Enter your Telegram chat ID" required>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Find your chat ID by messaging <a href="https://t.me/userinfobot" target="_blank" class="text-decoration-none">@userinfobot</a>
                                    </div>
                                    @error('telegram_chat_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <h6 class="alert-heading">
                                            <i class="fas fa-lightbulb me-2"></i>How to set up Telegram notifications:
                                        </h6>
                                        <ol class="mb-0">
                                            <li>Create a bot using <a href="https://t.me/botfather" target="_blank" class="text-decoration-none">@BotFather</a> on Telegram</li>
                                            <li>Get your bot token from BotFather</li>
                                            <li>Start a chat with your bot or add it to a group</li>
                                            <li>Get your chat ID by messaging <a href="https://t.me/userinfobot" target="_blank" class="text-decoration-none">@userinfobot</a></li>
                                            <li>Enter both values above and save</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-outline-info" onclick="testTelegram()">
                                    <i class="fas fa-paper-plane me-2"></i>Test Notification
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Settings
                                </button>
                            </div>
                        </form>
                        
                        <script>
                            function testTelegram() {
                                const token = document.getElementById('telegram_bot_token').value;
                                const chatId = document.getElementById('telegram_chat_id').value;
                                
                                if (!token || !chatId) {
                                    alert('Please enter both bot token and chat ID first');
                                    return;
                                }
                                
                                // Show loading state
                                const btn = event.target;
                                const originalText = btn.innerHTML;
                                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Testing...';
                                btn.disabled = true;
                                
                                // Send test notification
                                fetch('/test-business-telegram', {
                                    method: 'GET',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.ok) {
                                        alert('✅ Test notification sent successfully! Check your Telegram.');
                                    } else {
                                        alert('❌ Test failed: ' + (data.description || 'Unknown error'));
                                    }
                                })
                                .catch(error => {
                                    alert('❌ Test failed: ' + error.message);
                                })
                                .finally(() => {
                                    btn.innerHTML = originalText;
                                    btn.disabled = false;
                                });
                            }
                            
                            @if(config('app.debug'))
                            document.querySelector('form').addEventListener('submit', function(e) {
                                const formData = new FormData(this);
                                console.log('Form data being submitted:');
                                for (let [key, value] of formData.entries()) {
                                    console.log(key + ': ' + value);
                                }
                            });
                            @endif
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 