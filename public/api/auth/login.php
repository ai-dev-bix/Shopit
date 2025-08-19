<?php
/**
 * User Login API Endpoint
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
        'message' => 'Method not allowed. Use POST for login.'
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
    $requiredFields = ['username'];
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
    
    // Sanitize input
    $username = trim($_POST['username']);
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']) && $_POST['remember'] === 'on';
    
    // Validate username format
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid username format.'
        ]);
        exit;
    }
    
    // Authenticate user
    $user = new User();
    if ($user->authenticate($username)) {
        // Get user data
        $userData = $user->getProfile();
        
        // Create session
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['username'] = $user->getUsername();
        $_SESSION['user_type'] = $user->getType();
        $_SESSION['login_time'] = time();
        
        // Set remember me cookie if requested
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
            
            // Store remember token in database (simplified for prototype)
            // In production, this should be stored securely
        }
        
        // Log successful login
        if (LOG_ERRORS) {
            error_log("User logged in successfully: $username (ID: " . $user->getId() . ")");
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Login successful!',
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'type' => $user->getType(),
                'email' => $user->getEmail(),
                'location' => $user->getLocation()
            ],
            'redirect_url' => '/dashboard'
        ]);
        
    } else {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid username or account not found.'
        ]);
    }
    
} catch (Exception $e) {
    // Log error
    if (LOG_ERRORS) {
        error_log("Login error: " . $e->getMessage());
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred during login. Please try again.'
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