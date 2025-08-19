<?php
/**
 * Main Entry Point
 * Location-Based Marketplace Platform
 */

// Define secure access constant
define('SECURE_ACCESS', true);

// Load configuration
require_once __DIR__ . '/../src/config/config.php';

// Load required classes
require_once __DIR__ . '/../src/classes/Database.php';
require_once __DIR__ . '/../src/classes/User.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set security headers
if (!headers_sent()) {
    foreach (SECURITY_HEADERS as $header => $value) {
        header("$header: $value");
    }
}

/**
 * Router class to handle URL routing
 */
class Router {
    private $routes = [];
    private $defaultRoute = 'home';
    
    public function __construct() {
        $this->initializeRoutes();
    }
    
    /**
     * Initialize application routes
     */
    private function initializeRoutes() {
        $this->routes = [
            // Public routes
            '' => 'home',
            'home' => 'home',
            'register' => 'register',
            'login' => 'login',
            'search' => 'search',
            'browse' => 'browse',
            'listing' => 'listing_detail',
            'map' => 'map_view',
            
            // User routes (require authentication)
            'dashboard' => 'dashboard',
            'profile' => 'profile',
            'create-listing' => 'create_listing',
            'edit-listing' => 'edit_listing',
            'manage-listings' => 'manage_listings',
            'orders' => 'orders',
            'purchase' => 'purchase',
            'seller-approvals' => 'seller_approvals',
            
            // Admin routes
            'admin' => 'admin_dashboard',
            'admin/users' => 'admin_users',
            'admin/listings' => 'admin_listings',
            'admin/orders' => 'admin_orders',
            'admin/system' => 'admin_system',
            
            // API routes
            'api/auth' => 'api_auth',
            'api/users' => 'api_users',
            'api/listings' => 'api_listings',
            'api/search' => 'api_search',
            'api/orders' => 'api_orders',
            'api/upload' => 'api_upload',
            'api/location' => 'api_location',
            'api/admin' => 'api_admin'
        ];
    }
    
    /**
     * Route the current request
     */
    public function route() {
        $route = $this->getCurrentRoute();
        $action = $this->routes[$route] ?? $this->defaultRoute;
        
        // Check authentication requirements
        if ($this->requiresAuthentication($action)) {
            if (!$this->isUserAuthenticated()) {
                $this->redirectToLogin();
                return;
            }
        }
        
        // Check admin requirements
        if ($this->requiresAdmin($action)) {
            if (!$this->isUserAdmin()) {
                $this->showError('Access Denied', 'You do not have permission to access this page.', 403);
                return;
            }
        }
        
        // Execute the action
        $this->executeAction($action);
    }
    
    /**
     * Get current route from URL
     */
    private function getCurrentRoute() {
        $path = $_GET['route'] ?? '';
        $path = trim($path, '/');
        
        // Remove query parameters
        if (strpos($path, '?') !== false) {
            $path = substr($path, 0, strpos($path, '?'));
        }
        
        return $path;
    }
    
    /**
     * Check if action requires authentication
     */
    private function requiresAuthentication($action) {
        $protectedActions = [
            'dashboard', 'profile', 'create_listing', 'edit_listing', 
            'manage_listings', 'orders', 'purchase', 'seller_approvals',
            'admin_dashboard', 'admin_users', 'admin_listings', 
            'admin_orders', 'admin_system'
        ];
        
        return in_array($action, $protectedActions);
    }
    
    /**
     * Check if action requires admin privileges
     */
    private function requiresAdmin($action) {
        $adminActions = [
            'admin_dashboard', 'admin_users', 'admin_listings', 
            'admin_orders', 'admin_system', 'api_admin'
        ];
        
        return in_array($action, $adminActions);
    }
    
    /**
     * Check if user is authenticated
     */
    private function isUserAuthenticated() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Check if user is admin
     */
    private function isUserAdmin() {
        if (!$this->isUserAuthenticated()) {
            return false;
        }
        
        $user = new User();
        $user->loadById($_SESSION['user_id']);
        return $user->isAdmin();
    }
    
    /**
     * Redirect to login page
     */
    private function redirectToLogin() {
        header('Location: /login');
        exit;
    }
    
    /**
     * Execute the specified action
     */
    private function executeAction($action) {
        switch ($action) {
            case 'home':
                $this->showHomePage();
                break;
                
            case 'register':
                $this->showRegisterPage();
                break;
                
            case 'login':
                $this->showLoginPage();
                break;
                
            case 'dashboard':
                $this->showDashboard();
                break;
                
            case 'search':
                $this->showSearchPage();
                break;
                
            case 'browse':
                $this->showBrowsePage();
                break;
                
            case 'listing_detail':
                $this->showListingDetail();
                break;
                
            case 'create_listing':
                $this->showCreateListing();
                break;
                
            case 'admin_dashboard':
                $this->showAdminDashboard();
                break;
                
            default:
                $this->showError('Page Not Found', 'The requested page could not be found.', 404);
                break;
        }
    }
    
    /**
     * Show home page
     */
    private function showHomePage() {
        $pageTitle = 'Welcome to ' . PLATFORM_NAME;
        $pageDescription = PLATFORM_DESCRIPTION;
        
        include __DIR__ . '/../src/templates/header.php';
        include __DIR__ . '/../src/templates/home.php';
        include __DIR__ . '/../src/templates/footer.php';
    }
    
    /**
     * Show registration page
     */
    private function showRegisterPage() {
        $pageTitle = 'Register - ' . PLATFORM_NAME;
        $pageDescription = 'Create your account to start buying and selling';
        
        include __DIR__ . '/../src/templates/header.php';
        include __DIR__ . '/../src/templates/register.php';
        include __DIR__ . '/../src/templates/footer.php';
    }
    
    /**
     * Show login page
     */
    private function showLoginPage() {
        $pageTitle = 'Login - ' . PLATFORM_NAME;
        $pageDescription = 'Sign in to your account';
        
        include __DIR__ . '/../src/templates/header.php';
        include __DIR__ . '/../src/templates/login.php';
        include __DIR__ . '/../src/templates/footer.php';
    }
    
    /**
     * Show user dashboard
     */
    private function showDashboard() {
        $pageTitle = 'Dashboard - ' . PLATFORM_NAME;
        $pageDescription = 'Manage your account and listings';
        
        $user = new User();
        $user->loadById($_SESSION['user_id']);
        
        include __DIR__ . '/../src/templates/header.php';
        include __DIR__ . '/../src/templates/dashboard.php';
        include __DIR__ . '/../src/templates/footer.php';
    }
    
    /**
     * Show search page
     */
    private function showSearchPage() {
        $pageTitle = 'Search - ' . PLATFORM_NAME;
        $pageDescription = 'Find products and services near you';
        
        include __DIR__ . '/../src/templates/header.php';
        include __DIR__ . '/../src/templates/search.php';
        include __DIR__ . '/../src/templates/footer.php';
    }
    
    /**
     * Show browse page
     */
    private function showBrowsePage() {
        $pageTitle = 'Browse - ' . PLATFORM_NAME;
        $pageDescription = 'Explore all available listings';
        
        include __DIR__ . '/../src/templates/header.php';
        include __DIR__ . '/../src/templates/browse.php';
        include __DIR__ . '/../src/templates/footer.php';
    }
    
    /**
     * Show listing detail page
     */
    private function showListingDetail() {
        $pageTitle = 'Listing Details - ' . PLATFORM_NAME;
        $pageDescription = 'View listing information and purchase options';
        
        include __DIR__ . '/../src/templates/header.php';
        include __DIR__ . '/../src/templates/listing-detail.php';
        include __DIR__ . '/../src/templates/footer.php';
    }
    
    /**
     * Show create listing page
     */
    private function showCreateListing() {
        $pageTitle = 'Create Listing - ' . PLATFORM_NAME;
        $pageDescription = 'Add your product or service to the marketplace';
        
        include __DIR__ . '/../src/templates/header.php';
        include __DIR__ . '/../src/templates/create-listing.php';
        include __DIR__ . '/../src/templates/footer.php';
    }
    
    /**
     * Show admin dashboard
     */
    private function showAdminDashboard() {
        $pageTitle = 'Admin Dashboard - ' . PLATFORM_NAME;
        $pageDescription = 'System administration and monitoring';
        
        include __DIR__ . '/../src/templates/header.php';
        include __DIR__ . '/../src/templates/admin/dashboard.php';
        include __DIR__ . '/../src/templates/footer.php';
    }
    
    /**
     * Show error page
     */
    private function showError($title, $message, $code = 500) {
        http_response_code($code);
        $pageTitle = $title . ' - ' . PLATFORM_NAME;
        
        include __DIR__ . '/../src/templates/header.php';
        include __DIR__ . '/../src/templates/error.php';
        include __DIR__ . '/../src/templates/footer.php';
    }
}

/**
 * Authentication helper functions
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $user = new User();
    $user->loadById($_SESSION['user_id']);
    return $user;
}

function isAdmin() {
    $user = getCurrentUser();
    return $user && $user->isAdmin();
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login');
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    
    if (!isAdmin()) {
        http_response_code(403);
        include __DIR__ . '/../src/templates/error.php';
        exit;
    }
}

/**
 * Utility functions
 */
function sanitizeOutput($data) {
    if (is_array($data)) {
        return array_map('sanitizeOutput', $data);
    }
    
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function formatCurrency($amount, $currency = DEFAULT_CURRENCY) {
    return CURRENCY_SYMBOL . number_format($amount, 2);
}

function formatDate($date, $format = DATE_FORMAT) {
    if (empty($date)) {
        return '';
    }
    
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return $date;
    }
    
    return date($format, $timestamp);
}

function getDistance($lat1, $lng1, $lat2, $lng2) {
    $earthRadius = 6371; // Earth's radius in kilometers
    
    $latDelta = deg2rad($lat2 - $lat1);
    $lngDelta = deg2rad($lng2 - $lng1);
    
    $a = sin($latDelta / 2) * sin($latDelta / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($lngDelta / 2) * sin($lngDelta / 2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
    return $earthRadius * $c;
}

/**
 * CSRF protection
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    
    return true;
}

// Initialize and run the router
$router = new Router();
$router->route();