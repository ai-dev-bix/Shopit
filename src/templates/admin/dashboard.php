<!-- Admin Dashboard -->
<section class="section">
    <div class="container">
        <!-- Admin Header -->
        <div class="columns is-centered mb-6">
            <div class="column is-10">
                <div class="notification is-danger is-light">
                    <h1 class="title is-3">
                        <i class="fas fa-cog mr-2"></i>
                        Admin Dashboard
                    </h1>
                    <p class="subtitle is-6">
                        System administration and monitoring for <?php echo PLATFORM_NAME; ?>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- System Overview -->
        <div class="columns is-multiline mb-6">
            <div class="column is-3">
                <div class="card has-text-centered">
                    <div class="card-content">
                        <div class="has-text-info">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <p class="title is-4" id="totalUsers">-</p>
                        <p class="subtitle is-6">Total Users</p>
                    </div>
                </div>
            </div>
            
            <div class="column is-3">
                <div class="card has-text-centered">
                    <div class="card-content">
                        <div class="has-text-success">
                            <i class="fas fa-list fa-2x"></i>
                        </div>
                        <p class="title is-4" id="totalListings">-</p>
                        <p class="subtitle is-6">Total Listings</p>
                    </div>
                </div>
            </div>
            
            <div class="column is-3">
                <div class="card has-text-centered">
                    <div class="card-content">
                        <div class="has-text-warning">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                        <p class="title is-4" id="totalOrders">-</p>
                        <p class="subtitle is-6">Total Orders</p>
                    </div>
                </div>
            </div>
            
            <div class="column is-3">
                <div class="card has-text-centered">
                    <div class="card-content">
                        <div class="has-text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                        <p class="title is-4" id="pendingApprovals">-</p>
                        <p class="subtitle is-6">Pending Approvals</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Admin Actions -->
        <div class="columns is-centered mb-6">
            <div class="column is-10">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            <i class="fas fa-bolt mr-2"></i>
                            Quick Admin Actions
                        </p>
                    </header>
                    
                    <div class="card-content">
                        <div class="columns is-multiline">
                            <div class="column is-3">
                                <a href="/admin/users" class="button is-info is-fullwidth">
                                    <span class="icon">
                                        <i class="fas fa-users"></i>
                                    </span>
                                    <span>Manage Users</span>
                                </a>
                            </div>
                            
                            <div class="column is-3">
                                <a href="/admin/listings" class="button is-success is-fullwidth">
                                    <span class="icon">
                                        <i class="fas fa-list"></i>
                                    </span>
                                    <span>Manage Listings</span>
                                </a>
                            </div>
                            
                            <div class="column is-3">
                                <a href="/admin/orders" class="button is-warning is-fullwidth">
                                    <span class="icon">
                                        <i class="fas fa-shopping-cart"></i>
                                    </span>
                                    <span>Manage Orders</span>
                                </a>
                            </div>
                            
                            <div class="column is-3">
                                <a href="/admin/system" class="button is-danger is-fullwidth">
                                    <span class="icon">
                                        <i class="fas fa-cog"></i>
                                    </span>
                                    <span>System Settings</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- System Status -->
        <div class="columns is-centered mb-6">
            <div class="column is-6">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            <i class="fas fa-server mr-2"></i>
                            System Status
                        </p>
                    </header>
                    
                    <div class="card-content">
                        <div class="field">
                            <label class="label">Platform Version</label>
                            <div class="control">
                                <input class="input" type="text" value="<?php echo PLATFORM_VERSION; ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="field">
                            <label class="label">Environment</label>
                            <div class="control">
                                <input class="input" type="text" value="<?php echo DEVELOPMENT_MODE ? 'Development' : 'Production'; ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="field">
                            <label class="label">Maintenance Mode</label>
                            <div class="control">
                                <label class="checkbox">
                                    <input type="checkbox" id="maintenanceMode" 
                                           <?php echo MAINTENANCE_MODE ? 'checked' : ''; ?>>
                                    Enable maintenance mode
                                </label>
                            </div>
                        </div>
                        
                        <div class="field">
                            <div class="control">
                                <button class="button is-primary" onclick="updateSystemSettings()">
                                    <span class="icon">
                                        <i class="fas fa-save"></i>
                                    </span>
                                    <span>Update Settings</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="column is-6">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            <i class="fas fa-chart-line mr-2"></i>
                            Recent Activity
                        </p>
                    </header>
                    
                    <div class="card-content">
                        <div id="recentActivity">
                            <p class="has-text-grey">Loading recent activity...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- User Management Preview -->
        <div class="columns is-centered mb-6">
            <div class="column is-10">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            <i class="fas fa-users mr-2"></i>
                            Recent User Registrations
                        </p>
                        <div class="card-header-icon">
                            <a href="/admin/users" class="button is-small is-info">
                                View All Users
                            </a>
                        </div>
                    </header>
                    
                    <div class="card-content">
                        <div id="recentUsers">
                            <p class="has-text-grey">Loading recent users...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Listing Management Preview -->
        <div class="columns is-centered mb-6">
            <div class="column is-10">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            <i class="fas fa-list mr-2"></i>
                            Recent Listings
                        </p>
                        <div class="card-header-icon">
                            <a href="/admin/listings" class="button is-small is-info">
                                View All Listings
                            </a>
                        </div>
                    </header>
                    
                    <div class="card-content">
                        <div id="recentListings">
                            <p class="has-text-grey">Loading recent listings...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- System Logs -->
        <div class="columns is-centered">
            <div class="column is-10">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            <i class="fas fa-file-alt mr-2"></i>
                            System Logs
                        </p>
                        <div class="card-header-icon">
                            <button class="button is-small is-info" onclick="refreshLogs()">
                                <span class="icon">
                                    <i class="fas fa-sync-alt"></i>
                                </span>
                                <span>Refresh</span>
                            </button>
                        </div>
                    </header>
                    
                    <div class="card-content">
                        <div class="field">
                            <div class="control">
                                <div class="select">
                                    <select id="logLevel">
                                        <option value="all">All Levels</option>
                                        <option value="error">Errors Only</option>
                                        <option value="warning">Warnings & Errors</option>
                                        <option value="info">Info, Warnings & Errors</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div id="systemLogs">
                            <p class="has-text-grey">Loading system logs...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load initial data
    loadSystemOverview();
    loadRecentActivity();
    loadRecentUsers();
    loadRecentListings();
    loadSystemLogs();
    
    // Auto-refresh every 2 minutes
    setInterval(function() {
        loadSystemOverview();
        loadRecentActivity();
    }, 120000);
});

function loadSystemOverview() {
    fetch('/api/admin/overview')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('totalUsers').textContent = data.overview.total_users || 0;
            document.getElementById('totalListings').textContent = data.overview.total_listings || 0;
            document.getElementById('totalOrders').textContent = data.overview.total_orders || 0;
            document.getElementById('pendingApprovals').textContent = data.overview.pending_approvals || 0;
        }
    })
    .catch(error => {
        console.error('Failed to load system overview:', error);
    });
}

function loadRecentActivity() {
    fetch('/api/admin/recent-activity')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const container = document.getElementById('recentActivity');
            if (data.activities && data.activities.length > 0) {
                let html = '<div class="table-container">';
                html += '<table class="table is-fullwidth is-striped">';
                html += '<thead><tr><th>Time</th><th>Action</th><th>User</th><th>Details</th></tr></thead><tbody>';
                
                data.activities.forEach(activity => {
                    html += `<tr>
                        <td>${formatDateTime(activity.timestamp)}</td>
                        <td>${activity.action}</td>
                        <td>${activity.user || 'System'}</td>
                        <td>${activity.details}</td>
                    </tr>`;
                });
                
                html += '</tbody></table></div>';
                container.innerHTML = html;
            } else {
                container.innerHTML = '<p class="has-text-grey">No recent activity</p>';
            }
        }
    })
    .catch(error => {
        console.error('Failed to load recent activity:', error);
    });
}

function loadRecentUsers() {
    fetch('/api/admin/recent-users')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const container = document.getElementById('recentUsers');
            if (data.users && data.users.length > 0) {
                let html = '<div class="table-container">';
                html += '<table class="table is-fullwidth is-striped">';
                html += '<thead><tr><th>Username</th><th>Type</th><th>Status</th><th>Joined</th><th>Actions</th></tr></thead><tbody>';
                
                data.users.forEach(user => {
                    html += `<tr>
                        <td>${user.username}</td>
                        <td><span class="tag is-${getUserTypeColor(user.type)}">${user.type}</span></td>
                        <td><span class="tag is-${getUserStatusColor(user.status)}">${user.status}</span></td>
                        <td>${formatDateTime(user.created_at)}</td>
                        <td>
                            <a href="/admin/users/edit?id=${user.id}" class="button is-small is-info">
                                <span class="icon"><i class="fas fa-edit"></i></span>
                            </a>
                        </td>
                    </tr>`;
                });
                
                html += '</tbody></table></div>';
                container.innerHTML = html;
            } else {
                container.innerHTML = '<p class="has-text-grey">No recent users</p>';
            }
        }
    })
    .catch(error => {
        console.error('Failed to load recent users:', error);
    });
}

function loadRecentListings() {
    fetch('/api/admin/recent-listings')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const container = document.getElementById('recentListings');
            if (data.listings && data.listings.length > 0) {
                let html = '<div class="table-container">';
                html += '<table class="table is-fullwidth is-striped">';
                html += '<thead><tr><th>Title</th><th>Type</th><th>User</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead><tbody>';
                
                data.listings.forEach(listing => {
                    html += `<tr>
                        <td>${listing.title}</td>
                        <td><span class="tag is-${listing.type === 'product' ? 'info' : 'success'}">${listing.type}</span></td>
                        <td>${listing.username}</td>
                        <td><span class="tag is-${getListingStatusColor(listing.status)}">${listing.status}</span></td>
                        <td>${formatDateTime(listing.created_at)}</td>
                        <td>
                            <a href="/admin/listings/edit?id=${listing.id}" class="button is-small is-info">
                                <span class="icon"><i class="fas fa-edit"></i></span>
                            </a>
                        </td>
                    </tr>`;
                });
                
                html += '</tbody></table></div>';
                container.innerHTML = html;
            } else {
                container.innerHTML = '<p class="has-text-grey">No recent listings</p>';
            }
        }
    })
    .catch(error => {
        console.error('Failed to load recent listings:', error);
    });
}

function loadSystemLogs() {
    const logLevel = document.getElementById('logLevel').value;
    
    fetch(`/api/admin/logs?level=${logLevel}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const container = document.getElementById('systemLogs');
            if (data.logs && data.logs.length > 0) {
                let html = '<div class="table-container">';
                html += '<table class="table is-fullwidth is-striped">';
                html += '<thead><tr><th>Time</th><th>Level</th><th>Component</th><th>Message</th></tr></thead><tbody>';
                
                data.logs.forEach(log => {
                    html += `<tr>
                        <td>${formatDateTime(log.timestamp)}</td>
                        <td><span class="tag is-${getLogLevelColor(log.level)}">${log.level}</span></td>
                        <td>${log.component}</td>
                        <td>${log.message}</td>
                    </tr>`;
                });
                
                html += '</tbody></table></div>';
                container.innerHTML = html;
            } else {
                container.innerHTML = '<p class="has-text-grey">No logs found</p>';
            }
        }
    })
    .catch(error => {
        console.error('Failed to load system logs:', error);
    });
}

function refreshLogs() {
    loadSystemLogs();
}

function updateSystemSettings() {
    const maintenanceMode = document.getElementById('maintenanceMode').checked;
    
    fetch('/api/admin/system-settings', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            maintenance_mode: maintenanceMode
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('System settings updated successfully!', 'success');
        } else {
            showNotification('Failed to update system settings.', 'danger');
        }
    })
    .catch(error => {
        console.error('Failed to update system settings:', error);
        showNotification('An error occurred while updating settings.', 'danger');
    });
}

// Helper functions
function formatDateTime(timestamp) {
    if (!timestamp) return '-';
    const date = new Date(timestamp);
    return date.toLocaleString();
}

function getUserTypeColor(type) {
    switch (type) {
        case 'admin': return 'danger';
        case 'seller': return 'success';
        case 'buyer': return 'info';
        default: return 'light';
    }
}

function getUserStatusColor(status) {
    switch (status) {
        case 'active': return 'success';
        case 'suspended': return 'danger';
        case 'pending': return 'warning';
        default: return 'light';
    }
}

function getListingStatusColor(status) {
    switch (status) {
        case 'active': return 'success';
        case 'pending': return 'warning';
        case 'rejected': return 'danger';
        default: return 'light';
    }
}

function getLogLevelColor(level) {
    switch (level.toLowerCase()) {
        case 'error': return 'danger';
        case 'warning': return 'warning';
        case 'info': return 'info';
        default: return 'light';
    }
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification is-${type} is-light`;
    notification.innerHTML = `
        <button class="delete" onclick="this.parentElement.remove();"></button>
        ${message}
    `;
    
    // Insert at top of page
    const container = document.querySelector('.container');
    container.insertBefore(notification, container.firstChild);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Log level change handler
document.getElementById('logLevel').addEventListener('change', function() {
    loadSystemLogs();
});
</script>