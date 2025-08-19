<?php
/**
 * User Logout API Endpoint
 * Location-Based Marketplace Platform
 */

// Define secure access constant
define('SECURE_ACCESS', true);

// Load configuration
require_once __DIR__ . '/../../../src/config/config.php';

// Set content type to JSON
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST for logout.'
    ]);
    exit;
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid CSRF token. Please refresh the page and try again.'
    ]);
    exit;
}

try {
    // Get username for logging before destroying session
    $username = $_SESSION['username'] ?? 'Unknown';
    
    // Clear remember me cookie if it exists
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/', '', false, true);
    }
    
    // Destroy session
    session_destroy();
    
    // Log successful logout
    if (LOG_ERRORS) {
        error_log("User logged out successfully: $username");
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Logged out successfully!',
        'redirect_url' => '/'
    ]);
    
} catch (Exception $e) {
    // Log error
    if (LOG_ERRORS) {
        error_log("Logout error: " . $e->getMessage());
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred during logout. Please try again.'
    ]);
}

/**
 * Validate CSRF token
 */
function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}
?>