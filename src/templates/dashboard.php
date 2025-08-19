<!-- User Dashboard -->
<section class="section">
    <div class="container">
        <!-- Welcome Header -->
        <div class="columns is-centered mb-6">
            <div class="column is-8">
                <div class="notification is-primary is-light">
                    <h1 class="title is-3">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Welcome back, <?php echo sanitizeOutput($user->getUsername()); ?>!
                    </h1>
                    <p class="subtitle is-6">
                        Manage your account, listings, and orders from your personal dashboard.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="columns is-multiline mb-6">
            <div class="column is-3">
                <div class="card has-text-centered">
                    <div class="card-content">
                        <div class="has-text-primary">
                            <i class="fas fa-list fa-2x"></i>
                        </div>
                        <p class="title is-4"><?php echo $user->getStats()['total_listings'] ?? 0; ?></p>
                        <p class="subtitle is-6">Total Listings</p>
                    </div>
                </div>
            </div>
            
            <div class="column is-3">
                <div class="card has-text-centered">
                    <div class="card-content">
                        <div class="has-text-success">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                        <p class="title is-4"><?php echo $user->getStats()['total_buyer_orders'] ?? 0; ?></p>
                        <p class="subtitle is-6">Orders as Buyer</p>
                    </div>
                </div>
            </div>
            
            <div class="column is-3">
                <div class="card has-text-centered">
                    <div class="card-content">
                        <div class="has-text-info">
                            <i class="fas fa-store fa-2x"></i>
                        </div>
                        <p class="title is-4"><?php echo $user->getStats()['total_seller_orders'] ?? 0; ?></p>
                        <p class="subtitle is-6">Orders as Seller</p>
                    </div>
                </div>
            </div>
            
            <div class="column is-3">
                <div class="card has-text-centered">
                    <div class="card-content">
                        <div class="has-text-warning">
                            <i class="fas fa-star fa-2x"></i>
                        </div>
                        <p class="title is-4"><?php echo $user->getStats()['rating'] ?? 0; ?></p>
                        <p class="subtitle is-6">Rating</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="columns is-centered mb-6">
            <div class="column is-8">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            <i class="fas fa-bolt mr-2"></i>
                            Quick Actions
                        </p>
                    </header>
                    
                    <div class="card-content">
                        <div class="columns is-multiline">
                            <div class="column is-6">
                                <a href="/create-listing" class="button is-primary is-fullwidth is-medium">
                                    <span class="icon">
                                        <i class="fas fa-plus"></i>
                                    </span>
                                    <span>Create New Listing</span>
                                </a>
                            </div>
                            
                            <div class="column is-6">
                                <a href="/manage-listings" class="button is-info is-fullwidth is-medium">
                                    <span class="icon">
                                        <i class="fas fa-list"></i>
                                    </span>
                                    <span>Manage My Listings</span>
                                </a>
                            </div>
                            
                            <div class="column is-6">
                                <a href="/orders" class="button is-success is-fullwidth is-medium">
                                    <span class="icon">
                                        <i class="fas fa-shopping-cart"></i>
                                    </span>
                                    <span>View My Orders</span>
                                </a>
                            </div>
                            
                            <div class="column is-6">
                                <a href="/profile" class="button is-warning is-fullwidth is-medium">
                                    <span class="icon">
                                        <i class="fas fa-user-edit"></i>
                                    </span>
                                    <span>Edit Profile</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="columns is-centered">
            <div class="column is-8">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            <i class="fas fa-history mr-2"></i>
                            Recent Activity
                        </p>
                    </header>
                    
                    <div class="card-content">
                        <?php 
                        $recentListings = array_slice($user->getListings(), 0, 5);
                        $recentOrders = array_slice($user->getOrders('buyer'), 0, 5);
                        ?>
                        
                        <?php if (!empty($recentListings) || !empty($recentOrders)): ?>
                            <!-- Recent Listings -->
                            <?php if (!empty($recentListings)): ?>
                                <div class="mb-4">
                                    <h4 class="title is-5">
                                        <i class="fas fa-list mr-2"></i>
                                        Recent Listings
                                    </h4>
                                    <div class="table-container">
                                        <table class="table is-fullwidth is-striped">
                                            <thead>
                                                <tr>
                                                    <th>Title</th>
                                                    <th>Type</th>
                                                    <th>Status</th>
                                                    <th>Created</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recentListings as $listing): ?>
                                                    <tr>
                                                        <td><?php echo sanitizeOutput($listing['title'] ?? 'Untitled'); ?></td>
                                                        <td>
                                                            <span class="tag is-<?php echo ($listing['type'] ?? 'product') === 'product' ? 'info' : 'success'; ?>">
                                                                <?php echo ucfirst($listing['type'] ?? 'product'); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="tag is-<?php echo ($listing['status'] ?? 'active') === 'active' ? 'success' : 'warning'; ?>">
                                                                <?php echo ucfirst($listing['status'] ?? 'active'); ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo formatDate($listing['created_at'] ?? ''); ?></td>
                                                        <td>
                                                            <a href="/edit-listing?id=<?php echo $listing['id']; ?>" 
                                                               class="button is-small is-info">
                                                                <span class="icon">
                                                                    <i class="fas fa-edit"></i>
                                                                </span>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Recent Orders -->
                            <?php if (!empty($recentOrders)): ?>
                                <div class="mb-4">
                                    <h4 class="title is-5">
                                        <i class="fas fa-shopping-cart mr-2"></i>
                                        Recent Orders
                                    </h4>
                                    <div class="table-container">
                                        <table class="table is-fullwidth is-striped">
                                            <thead>
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>Item</th>
                                                    <th>Status</th>
                                                    <th>Total</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recentOrders as $order): ?>
                                                    <tr>
                                                        <td>#<?php echo $order['id']; ?></td>
                                                        <td><?php echo sanitizeOutput($order['item_title'] ?? 'Unknown Item'); ?></td>
                                                        <td>
                                                            <span class="tag is-<?php echo getOrderStatusColor($order['status'] ?? 'pending'); ?>">
                                                                <?php echo ucfirst(str_replace('_', ' ', $order['status'] ?? 'pending')); ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo formatCurrency($order['total'] ?? 0); ?></td>
                                                        <td><?php echo formatDate($order['created_at'] ?? ''); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="has-text-centered py-6">
                                <div class="has-text-grey mb-3">
                                    <i class="fas fa-inbox fa-3x"></i>
                                </div>
                                <p class="title is-5">No Activity Yet</p>
                                <p class="subtitle is-6">Start by creating your first listing or browsing available items.</p>
                                <div class="mt-4">
                                    <a href="/create-listing" class="button is-primary">
                                        <span class="icon">
                                            <i class="fas fa-plus"></i>
                                        </span>
                                        <span>Create Your First Listing</span>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Account Information -->
        <div class="columns is-centered mt-6">
            <div class="column is-8">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            <i class="fas fa-user mr-2"></i>
                            Account Information
                        </p>
                    </header>
                    
                    <div class="card-content">
                        <div class="columns is-multiline">
                            <div class="column is-6">
                                <div class="field">
                                    <label class="label">Username</label>
                                    <div class="control">
                                        <input class="input" type="text" value="<?php echo sanitizeOutput($user->getUsername()); ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="column is-6">
                                <div class="field">
                                    <label class="label">Account Type</label>
                                    <div class="control">
                                        <input class="input" type="text" value="<?php echo ucfirst($user->getType()); ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="column is-6">
                                <div class="field">
                                    <label class="label">Email</label>
                                    <div class="control">
                                        <input class="input" type="email" value="<?php echo sanitizeOutput($user->getEmail()); ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="column is-6">
                                <div class="field">
                                    <label class="label">Phone</label>
                                    <div class="control">
                                        <input class="input" type="tel" value="<?php echo sanitizeOutput($user->getPhone()); ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="column is-6">
                                <div class="field">
                                    <label class="label">Member Since</label>
                                    <div class="control">
                                        <input class="input" type="text" value="<?php echo formatDate($user->getCreatedAt()); ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="column is-6">
                                <div class="field">
                                    <label class="label">Last Active</label>
                                    <div class="control">
                                        <input class="input" type="text" value="<?php echo formatDate($user->getLastActive()); ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="field">
                            <div class="control">
                                <a href="/profile" class="button is-primary">
                                    <span class="icon">
                                        <i class="fas fa-edit"></i>
                                    </span>
                                    <span>Edit Profile</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh dashboard data every 5 minutes
    setInterval(function() {
        refreshDashboardStats();
    }, 300000);
    
    function refreshDashboardStats() {
        fetch('/api/users/stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update stats without page refresh
                updateStatsDisplay(data.stats);
            }
        })
        .catch(error => {
            console.error('Failed to refresh stats:', error);
        });
    }
    
    function updateStatsDisplay(stats) {
        // Update the stats display elements
        const statsElements = document.querySelectorAll('.title.is-4');
        if (statsElements.length >= 4) {
            statsElements[0].textContent = stats.total_listings || 0;
            statsElements[1].textContent = stats.total_buyer_orders || 0;
            statsElements[2].textContent = stats.total_seller_orders || 0;
            statsElements[3].textContent = stats.rating || 0;
        }
    }
});
</script>

<?php
// Helper function for order status colors
function getOrderStatusColor($status) {
    switch ($status) {
        case 'completed':
            return 'success';
        case 'in_progress':
            return 'info';
        case 'approved':
            return 'warning';
        case 'cancelled':
            return 'danger';
        default:
            return 'light';
    }
}
?>