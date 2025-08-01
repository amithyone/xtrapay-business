@if(!request()->is('pwa-test*'))
<div id="pwa-install-prompt" class="alert alert-info alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 1050; max-width: 350px; display: none;">
    <div class="d-flex align-items-start">
        <div class="flex-shrink-0">
            <i class="fas fa-mobile-alt fa-2x text-primary me-3"></i>
        </div>
        <div class="flex-grow-1">
            <h6 class="alert-heading mb-2">Install XtraPay App</h6>
            <p class="mb-2 small">Get the best experience by installing our app on your device.</p>
            <div class="d-flex gap-2">
                <button id="pwa-install-btn-inline" class="btn btn-primary btn-sm">
                    <i class="fas fa-download me-1"></i>Install
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="dismissPwaPrompt()">
                    Later
                </button>
            </div>
        </div>
        <button type="button" class="btn-close" onclick="dismissPwaPrompt()"></button>
    </div>
</div>

<script>
let deferredPrompt;
const pwaInstallPrompt = document.getElementById('pwa-install-prompt');
const pwaInstallBtn = document.getElementById('pwa-install-btn-inline');

// Check if user has already dismissed the prompt
if (!localStorage.getItem('pwa-prompt-dismissed')) {
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        
        // Show prompt after a delay
        setTimeout(() => {
            if (pwaInstallPrompt) {
                pwaInstallPrompt.style.display = 'block';
            }
        }, 3000);
    });
}

// Handle install button click
if (pwaInstallBtn) {
    pwaInstallBtn.addEventListener('click', async () => {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            
            if (outcome === 'accepted') {
                pwaInstallPrompt.style.display = 'none';
                showToast('App installed successfully!', 'success');
            }
            
            deferredPrompt = null;
        }
    });
}

function dismissPwaPrompt() {
    if (pwaInstallPrompt) {
        pwaInstallPrompt.style.display = 'none';
        localStorage.setItem('pwa-prompt-dismissed', 'true');
    }
}

function showToast(message, type = 'info') {
    // Create a simple toast notification
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed`;
    toast.style.cssText = 'top: 20px; left: 50%; transform: translateX(-50%); z-index: 1060; min-width: 300px;';
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 3000);
}

// Check if app is already installed
if (window.matchMedia('(display-mode: standalone)').matches) {
    // App is already installed, don't show prompt
    if (pwaInstallPrompt) {
        pwaInstallPrompt.style.display = 'none';
    }
}
</script>
@endif 