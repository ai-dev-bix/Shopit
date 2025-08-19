<?php
/**
 * User Registration API Endpoint
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

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST for registration.'
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
    // Validate required fields
    $requiredFields = ['username', 'type'];
    $missingFields = [];
    
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            $missingFields[] = $field;
        }
    }
    
    if (!empty($missingFields)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Missing required fields: ' . implode(', ', $missingFields)
        ]);
        exit;
    }
    
    // Sanitize and validate input
    $username = trim($_POST['username']);
    $type = trim($_POST['type']);
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $latitude = isset($_POST['latitude']) ? floatval($_POST['latitude']) : DEFAULT_LATITUDE;
    $longitude = isset($_POST['longitude']) ? floatval($_POST['longitude']) : DEFAULT_LONGITUDE;
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    
    // Validate username format
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Username must be 3-20 characters, letters, numbers, and underscores only.'
        ]);
        exit;
    }
    
    // Validate user type
    $validTypes = [USER_TYPE_BUYER, USER_TYPE_SELLER, USER_TYPE_BOTH];
    if (!in_array($type, $validTypes)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid user type selected.'
        ]);
        exit;
    }
    
    // Validate email if provided
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email format.'
        ]);
        exit;
    }
    
    // Validate coordinates
    if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid coordinates provided.'
        ]);
        exit;
    }
    
    // Check if username already exists
    $user = new User();
    if ($user->isUsernameTaken($username)) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => 'Username already taken. Please choose a different username.'
        ]);
        exit;
    }
    
    // Prepare user data
    $userData = [
        'username' => $username,
        'type' => $type,
        'email' => $email,
        'phone' => $phone,
        'location' => [
            'lat' => $latitude,
            'lng' => $longitude,
            'address' => $address
        ]
    ];
    
    // Create user
    $userId = $user->create($userData);
    
    if ($userId) {
        // Log successful registration
        if (LOG_ERRORS) {
            error_log("User registered successfully: $username (ID: $userId)");
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Account created successfully!',
            'user_id' => $userId,
            'username' => $username,
            'redirect_url' => '/login'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create account. Please try again.'
        ]);
    }
    
} catch (Exception $e) {
    // Log error
    if (LOG_ERRORS) {
        error_log("Registration error: " . $e->getMessage());
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred during registration. Please try again.'
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