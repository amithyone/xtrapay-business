// PWA Registration and Installation
class PWAInstaller {
    constructor() {
        this.deferredPrompt = null;
        this.installButton = null;
        this.init();
    }

    init() {
        // Register service worker
        this.registerServiceWorker();
        
        // Listen for install prompt
        this.listenForInstallPrompt();
        
        // Check if app is already installed
        this.checkIfInstalled();
        
        // Create install button
        this.createInstallButton();
    }

    async registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.register('/sw.js');
                console.log('Service Worker registered successfully:', registration);
            } catch (error) {
                console.error('Service Worker registration failed:', error);
            }
        }
    }

    listenForInstallPrompt() {
        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent the mini-infobar from appearing on mobile
            e.preventDefault();
            
            // Stash the event so it can be triggered later
            this.deferredPrompt = e;
            
            // Show install button
            this.showInstallButton();
        });
    }

    checkIfInstalled() {
        // Check if app is already installed
        if (window.matchMedia('(display-mode: standalone)').matches) {
            console.log('App is already installed');
            this.hideInstallButton();
        }
    }

    createInstallButton() {
        // Create install button if it doesn't exist
        if (!document.getElementById('pwa-install-btn')) {
            const button = document.createElement('button');
            button.id = 'pwa-install-btn';
            button.className = 'btn btn-primary position-fixed';
            button.style.cssText = 'bottom: 20px; right: 20px; z-index: 1000; display: none; border-radius: 50px; padding: 12px 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
            button.innerHTML = '<i class="fas fa-download me-2"></i>Install App';
            button.addEventListener('click', () => this.installApp());
            
            document.body.appendChild(button);
            this.installButton = button;
        }
    }

    showInstallButton() {
        if (this.installButton) {
            this.installButton.style.display = 'block';
        }
    }

    hideInstallButton() {
        if (this.installButton) {
            this.installButton.style.display = 'none';
        }
    }

    async installApp() {
        if (this.deferredPrompt) {
            // Show the install prompt
            this.deferredPrompt.prompt();
            
            // Wait for the user to respond to the prompt
            const { outcome } = await this.deferredPrompt.userChoice;
            
            if (outcome === 'accepted') {
                console.log('User accepted the install prompt');
                this.hideInstallButton();
            } else {
                console.log('User dismissed the install prompt');
            }
            
            // Clear the deferredPrompt
            this.deferredPrompt = null;
        }
    }

    // Check for updates
    checkForUpdates() {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistration().then(registration => {
                if (registration) {
                    registration.update();
                }
            });
        }
    }
}

// Initialize PWA when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new PWAInstaller();
});

// Handle app updates
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.addEventListener('controllerchange', () => {
        // New service worker activated, reload the page
        window.location.reload();
    });
} 