<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo sanitizeOutput($pageDescription ?? PLATFORM_DESCRIPTION); ?>">
    <meta name="keywords" content="marketplace, location-based, buy, sell, local, products, services">
    <meta name="author" content="<?php echo PLATFORM_NAME; ?>">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo sanitizeOutput($pageTitle ?? PLATFORM_NAME); ?>">
    <meta property="og:description" content="<?php echo sanitizeOutput($pageDescription ?? PLATFORM_DESCRIPTION); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:site_name" content="<?php echo PLATFORM_NAME; ?>">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo sanitizeOutput($pageTitle ?? PLATFORM_NAME); ?>">
    <meta name="twitter:description" content="<?php echo sanitizeOutput($pageDescription ?? PLATFORM_DESCRIPTION); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon-16x16.png">
    
    <title><?php echo sanitizeOutput($pageTitle ?? PLATFORM_NAME); ?></title>
    
    <!-- Bulma CSS Framework -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/main.css">
    
    <!-- Google Maps API -->
    <?php if (FEATURE_GOOGLE_MAPS && !empty(GOOGLE_MAPS_API_KEY)): ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY; ?>&libraries=places"></script>
    <?php endif; ?>
    
    <!-- CSRF Token for forms -->
    <meta name="csrf-token" content="<?php echo generateCSRFToken(); ?>">
    
    <style>
        /* Custom CSS Variables */
        :root {
            --primary-color: #3273dc;
            --primary-light: #4a9eff;
            --primary-dark: #1e4a8f;
            --secondary-color: #f14668;
            --success-color: #48c774;
            --warning-color: #ffdd57;
            --danger-color: #f14668;
            --info-color: #3298dc;
            --light-color: #f5f5f5;
            --dark-color: #363636;
            --text-color: #4a4a4a;
            --border-color: #dbdbdb;
            --shadow: 0 2px 4px rgba(0,0,0,0.1);
            --border-radius: 6px;
            --transition: all 0.3s ease;
        }
        
        /* Custom Font */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--text-color);
            line-height: 1.6;
        }
        
        /* Custom Button Styles */
        .button.is-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            transition: var(--transition);
        }
        
        .button.is-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }
        
        /* Custom Card Styles */
        .card {
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            transition: var(--transition);
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        /* Custom Navigation */
        .navbar {
            box-shadow: var(--shadow);
        }
        
        .navbar-brand .navbar-item {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color);
        }
        
        /* Custom Hero Section */
        .hero.is-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        }
        
        /* Custom Form Styles */
        .input, .textarea, .select select {
            border-radius: var(--border-radius);
            border-color: var(--border-color);
            transition: var(--transition);
        }
        
        .input:focus, .textarea:focus, .select select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.125em rgba(50, 115, 220, 0.25);
        }
        
        /* Custom Alert Styles */
        .notification {
            border-radius: var(--border-radius);
            border-left: 4px solid var(--primary-color);
        }
        
        .notification.is-success {
            border-left-color: var(--success-color);
        }
        
        .notification.is-warning {
            border-left-color: var(--warning-color);
        }
        
        .notification.is-danger {
            border-left-color: var(--danger-color);
        }
        
        /* Custom Loading Spinner */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Custom Mobile Responsiveness */
        @media screen and (max-width: 768px) {
            .navbar-menu {
                box-shadow: none;
            }
            
            .navbar-menu.is-active {
                box-shadow: var(--shadow);
            }
        }
        
        /* Custom Utility Classes */
        .text-gradient {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .shadow-sm { box-shadow: var(--shadow); }
        .shadow-md { box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .shadow-lg { box-shadow: 0 10px 15px rgba(0,0,0,0.1); }
        
        .rounded { border-radius: var(--border-radius); }
        .rounded-lg { border-radius: calc(var(--border-radius) * 2); }
        
        .transition { transition: var(--transition); }
        .hover-lift:hover { transform: translateY(-2px); }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar is-primary" role="navigation" aria-label="main navigation">
        <div class="container">
            <div class="navbar-brand">
                <a class="navbar-item" href="/">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    <strong><?php echo PLATFORM_NAME; ?></strong>
                </a>
                
                <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarBasic">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </a>
            </div>
            
            <div id="navbarBasic" class="navbar-menu">
                <div class="navbar-start">
                    <a class="navbar-item" href="/">
                        <i class="fas fa-home mr-1"></i>
                        Home
                    </a>
                    
                    <a class="navbar-item" href="/browse">
                        <i class="fas fa-th-large mr-1"></i>
                        Browse
                    </a>
                    
                    <a class="navbar-item" href="/search">
                        <i class="fas fa-search mr-1"></i>
                        Search
                    </a>
                    
                    <a class="navbar-item" href="/map">
                        <i class="fas fa-map mr-1"></i>
                        Map
                    </a>
                </div>
                
                <div class="navbar-end">
                    <?php if (isLoggedIn()): ?>
                        <?php $currentUser = getCurrentUser(); ?>
                        
                        <div class="navbar-item has-dropdown is-hoverable">
                            <a class="navbar-link">
                                <i class="fas fa-user mr-1"></i>
                                <?php echo sanitizeOutput($currentUser->getUsername()); ?>
                            </a>
                            
                            <div class="navbar-dropdown is-right">
                                <a class="navbar-item" href="/dashboard">
                                    <i class="fas fa-tachometer-alt mr-2"></i>
                                    Dashboard
                                </a>
                                
                                <a class="navbar-item" href="/profile">
                                    <i class="fas fa-user-edit mr-2"></i>
                                    Profile
                                </a>
                                
                                <a class="navbar-item" href="/create-listing">
                                    <i class="fas fa-plus mr-2"></i>
                                    Create Listing
                                </a>
                                
                                <a class="navbar-item" href="/manage-listings">
                                    <i class="fas fa-list mr-2"></i>
                                    My Listings
                                </a>
                                
                                <a class="navbar-item" href="/orders">
                                    <i class="fas fa-shopping-cart mr-2"></i>
                                    Orders
                                </a>
                                
                                <?php if ($currentUser->isSeller()): ?>
                                <a class="navbar-item" href="/seller-approvals">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Approvals
                                </a>
                                <?php endif; ?>
                                
                                <?php if ($currentUser->isAdmin()): ?>
                                <hr class="navbar-divider">
                                <a class="navbar-item" href="/admin">
                                    <i class="fas fa-cog mr-2"></i>
                                    Admin Panel
                                </a>
                                <?php endif; ?>
                                
                                <hr class="navbar-divider">
                                <a class="navbar-item" href="/logout">
                                    <i class="fas fa-sign-out-alt mr-2"></i>
                                    Logout
                                </a>
                            </div>
                        </div>
                        
                    <?php else: ?>
                        <div class="navbar-item">
                            <div class="buttons">
                                <a class="button is-light" href="/login">
                                    <i class="fas fa-sign-in-alt mr-1"></i>
                                    Login
                                </a>
                                <a class="button is-primary" href="/register">
                                    <i class="fas fa-user-plus mr-1"></i>
                                    Register
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="main-content">
        <!-- Flash Messages -->
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="container mt-4">
                <div class="notification is-<?php echo $_SESSION['flash_type'] ?? 'info'; ?> is-light">
                    <button class="delete" onclick="this.parentElement.remove();"></button>
                    <?php echo sanitizeOutput($_SESSION['flash_message']); ?>
                </div>
            </div>
            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
        <?php endif; ?>
        
        <!-- Error Messages -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="container mt-4">
                <div class="notification is-danger is-light">
                    <button class="delete" onclick="this.parentElement.remove();"></button>
                    <?php echo sanitizeOutput($_SESSION['error_message']); ?>
                </div>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
        
        <!-- Success Messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="container mt-4">
                <div class="notification is-success is-light">
                    <button class="delete" onclick="this.parentElement.remove();"></button>
                    <?php echo sanitizeOutput($_SESSION['success_message']); ?>
                </div>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <!-- Page Content -->
        <div class="page-content">