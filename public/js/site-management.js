// Site management functions
function openEditModal(siteId) {
    console.log('Opening edit modal for site:', siteId);
    
    // Fetch site data and populate the edit modal
    fetch(`/sites/${siteId}/edit`, {
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Response text:', text);
                    throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Site data received:', data);
            if (data.site) {
                const site = data.site;
                
                // Check if form fields exist before setting values
                const nameField = document.getElementById('name');
                const urlField = document.getElementById('url');
                const webhookUrlField = document.getElementById('webhook_url');
                const apiCodeField = document.getElementById('api_code');
                const allowedIpsField = document.getElementById('allowed_ips');
                const isActiveField = document.getElementById('is_active');
                const editSiteForm = document.getElementById('editSiteForm');
                
                if (!nameField || !urlField || !webhookUrlField || !apiCodeField || !allowedIpsField || !isActiveField || !editSiteForm) {
                    console.error('Missing form fields:', {
                        nameField: !!nameField,
                        urlField: !!urlField,
                        webhookUrlField: !!webhookUrlField,
                        apiCodeField: !!apiCodeField,
                        allowedIpsField: !!allowedIpsField,
                        isActiveField: !!isActiveField,
                        editSiteForm: !!editSiteForm
                    });
                    throw new Error('Required form fields not found');
                }
                
                console.log('Setting form values:', {
                    name: site.name,
                    url: site.url,
                    webhook_url: site.webhook_url,
                    api_code: site.api_code,
                    allowed_ips: site.allowed_ips,
                    is_active: site.is_active
                });
                
                nameField.value = site.name || '';
                urlField.value = site.url || '';
                webhookUrlField.value = site.webhook_url || '';
                apiCodeField.value = site.api_code || '';
                allowedIpsField.value = site.allowed_ips || '';
                isActiveField.checked = site.is_active || false;
                
                // Update form action
                editSiteForm.action = `/sites/${siteId}`;
                
                // Show the modal
                const editModal = new bootstrap.Modal(document.getElementById('editModal'));
                editModal.show();
            } else {
                throw new Error('No site data received');
            }
        })
        .catch(error => {
            console.error('Error fetching site data:', error);
            console.error('Error stack:', error.stack);
            alert('Error loading site data. Please try again. Error: ' + error.message);
        });
}

function openViewModal(siteId) {
    console.log('Opening view modal for site:', siteId);
    
    // Fetch site data and populate the view modal
    fetch(`/sites/${siteId}`, {
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Response text:', text);
                    throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Site data received:', data);
            if (data.site) {
                const site = data.site;
                const modalBody = document.querySelector('#viewModal .modal-body');
                
                if (!modalBody) {
                    throw new Error('View modal body not found');
                }
                
                modalBody.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> ${site.name || 'N/A'}</p>
                            <p><strong>URL:</strong> <a href="${site.url || '#'}" target="_blank">${site.url || 'N/A'}</a></p>
                            <p><strong>Webhook URL:</strong> ${site.webhook_url || 'N/A'}</p>
                            <p><strong>API Code:</strong> ${site.api_code || 'N/A'}</p>
                            <p><strong>API Key:</strong> <code>${site.api_key || 'Not set'}</code></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong> <span class="badge ${site.is_active ? 'bg-success' : 'bg-secondary'}">${site.is_active ? 'Active' : 'Inactive'}</span></p>
                            <p><strong>Allowed IPs:</strong> ${site.allowed_ips || 'Any IP'}</p>
                            <p><strong>Created:</strong> ${site.created_at ? new Date(site.created_at).toLocaleDateString() : 'N/A'}</p>
                            <p><strong>Last Updated:</strong> ${site.updated_at ? new Date(site.updated_at).toLocaleDateString() : 'N/A'}</p>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <button type="button" class="btn btn-outline-primary me-2" onclick="openEditModal(${siteId})">Edit Site</button>
                        <button type="button" class="btn btn-outline-warning me-2" onclick="toggleSiteStatus(${siteId}, ${site.is_active})">${site.is_active ? 'Deactivate' : 'Activate'}</button>
                        <button type="button" class="btn btn-outline-warning" onclick="deleteSite(${siteId})">Archive Site</button>
                    </div>
                `;
                
                // Show the modal
                const viewModal = new bootstrap.Modal(document.getElementById('viewModal'));
                viewModal.show();
            } else {
                throw new Error('No site data received');
            }
        })
        .catch(error => {
            console.error('Error fetching site data:', error);
            console.error('Error stack:', error.stack);
            alert('Error loading site data. Please try again. Error: ' + error.message);
        });
}

function activateSite(siteId) {
    if (confirm('Are you sure you want to activate this site?')) {
        fetch(`/sites/${siteId}/activate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message
                showNotification('Site activated successfully!', 'success');
                // Reload after a short delay
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Error activating site: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Error activating site:', error);
            showNotification('Error activating site. Please try again.', 'error');
        });
    }
}

function deactivateSite(siteId) {
    if (confirm('Are you sure you want to deactivate this site?')) {
        fetch(`/sites/${siteId}/deactivate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            console.log('Deactivate response status:', response.status);
            console.log('Deactivate response headers:', response.headers);
            
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Deactivate response text:', text);
                    throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Deactivate response data:', data);
            if (data.success) {
                // Show success message
                showNotification('Site deactivated successfully!', 'success');
                // Reload after a short delay
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Error deactivating site: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Error deactivating site:', error);
            showNotification('Error deactivating site: ' + error.message, 'error');
        });
    }
}

function deleteSite(siteId) {
    if (confirm('Are you sure you want to archive this site? This will deactivate the site but preserve all transaction history. You can reactivate it later if needed.')) {
        fetch(`/sites/${siteId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            console.log('Archive response status:', response.status);
            console.log('Archive response headers:', response.headers);
            
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Archive response text:', text);
                    throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Archive response data:', data);
            if (data.success) {
                // Show success message
                showNotification(data.message || 'Site archived successfully!', 'success');
                // Reload after a short delay
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Error archiving site: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Error archiving site:', error);
            showNotification('Error archiving site: ' + error.message, 'error');
        });
    }
}

// Helper function to show notifications
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Toggle site status (activate/deactivate)
function toggleSiteStatus(siteId, currentStatus) {
    const action = currentStatus ? 'deactivate' : 'activate';
    const actionText = currentStatus ? 'deactivate' : 'activate';
    
    if (confirm(`Are you sure you want to ${actionText} this site?`)) {
        fetch(`/sites/${siteId}/${action}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message
                showNotification(`Site ${actionText}d successfully!`, 'success');
                // Reload after a short delay
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification(`Error ${actionText}ing site: ` + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error(`Error ${actionText}ing site:`, error);
            showNotification(`Error ${actionText}ing site. Please try again.`, 'error');
        });
    }
}

// Handle edit form submission
document.addEventListener('DOMContentLoaded', function() {
    const editSiteForm = document.getElementById('editSiteForm');
    if (editSiteForm) {
        editSiteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const siteId = this.action.split('/').pop();
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide the modal
                    const editModal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
                    if (editModal) {
                        editModal.hide();
                    }
                    
                    // Show success message
                    const successMessageEl = document.getElementById('successMessage');
                    const successModalEl = document.getElementById('successModal');
                    
                    if (successMessageEl && successModalEl) {
                        successMessageEl.textContent = 'Site updated successfully!';
                        const successModal = new bootstrap.Modal(successModalEl);
                        successModal.show();
                        
                        // Reload the page after a short delay
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        // Fallback to alert if modal not found
                        alert('Site updated successfully!');
                        location.reload();
                    }
                } else {
                    alert('Error updating site: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error updating site:', error);
                alert('Error updating site. Please try again.');
            });
        });
    }
}); 