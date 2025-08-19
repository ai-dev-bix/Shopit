<?php
/**
 * Username Availability Check API Endpoint
 * Location-Based Marketplace Platform
 */

// Define secure access constant
define('SECURE_ACCESS', true);

// Load configuration and classes
require_once __DIR__ . '/../../../src/config/config.php';
require_once __DIR__ . '/../../../src/classes/Database.php';
require_once __DIR__ . '/../../../src/classes/User.php';

// Set content type to JSON
header('Content-Type: application/json');

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use GET for username check.'
    ]);
    exit;
}

try {
    // Check if username parameter is provided
    if (!isset($_GET['username']) || empty(trim($_GET['username']))) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Username parameter is required.'
        ]);
        exit;
    }
    
    // Sanitize username
    $username = trim($_GET['username']);
    
    // Validate username format
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        echo json_encode([
            'success' => false,
            'available' => false,
            'message' => 'Username format is invalid.'
        ]);
        exit;
    }
    
    // Check username availability
    $user = new User();
    $isAvailable = !$user->isUsernameTaken($username);
    
    echo json_encode([
        'success' => true,
        'available' => $isAvailable,
        'username' => $username,
        'message' => $isAvailable ? 'Username is available!' : 'Username is already taken.'
    ]);
    
} catch (Exception $e) {
    // Log error
    if (LOG_ERRORS) {
        error_log("Username check error: " . $e->getMessage());
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'available' => false,
        'message' => 'An error occurred while checking username availability.'
    ]);
}
?>