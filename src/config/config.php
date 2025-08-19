<?php
/**
 * Main Configuration File
 * Location-Based Marketplace Platform
 */

// Prevent direct access
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

// Load constants first
require_once __DIR__ . '/constants.php';

// Error reporting configuration
error_reporting(ERROR_REPORTING_LEVEL);
ini_set('display_errors', DISPLAY_ERRORS ? '1' : '0');
ini_set('log_errors', LOG_ERRORS ? '1' : '0');
ini_set('error_log', ERROR_LOG_FILE);

// Session configuration
ini_set('session.cookie_lifetime', SESSION_COOKIE_LIFETIME);
ini_set('session.cookie_path', SESSION_COOKIE_PATH);
ini_set('session.cookie_domain', SESSION_COOKIE_DOMAIN);
ini_set('session.cookie_secure', SESSION_COOKIE_SECURE);
ini_set('session.cookie_httponly', SESSION_COOKIE_HTTPONLY);

// File upload configuration
ini_set('upload_max_filesize', UPLOAD_MAX_FILESIZE);
ini_set('max_file_uploads', UPLOAD_MAX_FILES);
ini_set('post_max_size', UPLOAD_MAX_FILESIZE + 1024); // Add 1KB buffer

// Time zone
date_default_timezone_set('UTC');

// Character encoding
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

// Set security headers
if (!headers_sent()) {
    foreach (SECURITY_HEADERS as $header => $value) {
        header("$header: $value");
    }
}

// Development mode settings
if (DEVELOPMENT_MODE) {
    // Enable error display in development
    ini_set('display_errors', '1');
    
    // Set development-specific headers
    if (!headers_sent()) {
        header('X-Development-Mode: true');
    }
}

// Production mode settings
if (!DEVELOPMENT_MODE) {
    // Disable error display in production
    ini_set('display_errors', '0');
    
    // Enable error logging in production
    ini_set('log_errors', '1');
}

// Maintenance mode check
if (MAINTENANCE_MODE && !in_array($_SERVER['REMOTE_ADDR'] ?? '', MAINTENANCE_ALLOWED_IPS)) {
    http_response_code(503);
    include __DIR__ . '/../templates/maintenance.php';
    exit;
}

// Create necessary directories if they don't exist
$required_directories = [
    DATA_PATH,
    DATA_PATH . '/users',
    DATA_PATH . '/listings',
    DATA_PATH . '/orders',
    DATA_PATH . '/images',
    DATA_PATH . '/images/products',
    DATA_PATH . '/images/services',
    DATA_PATH . '/images/users',
    DATA_PATH . '/system',
    UPLOADS_PATH,
    PRODUCT_IMAGES_PATH,
    SERVICE_IMAGES_PATH,
    USER_IMAGES_PATH,
    dirname(ERROR_LOG_FILE),
    dirname(LOG_FILE)
];

foreach ($required_directories as $directory) {
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
}

// Set proper permissions for data directories
$data_directories = [
    DATA_PATH => 0755,
    UPLOADS_PATH => 0755,
    PRODUCT_IMAGES_PATH => 0755,
    SERVICE_IMAGES_PATH => 0755,
    USER_IMAGES_PATH => 0755
];

foreach ($data_directories as $directory => $permissions) {
    if (is_dir($directory)) {
        chmod($directory, $permissions);
    }
}

// Initialize application state
$GLOBALS['app_config'] = [
    'initialized' => true,
    'start_time' => microtime(true),
    'memory_start' => memory_get_usage(),
    'environment' => DEVELOPMENT_MODE ? 'development' : 'production',
    'version' => PLATFORM_VERSION,
    'features' => [
        'user_registration' => FEATURE_USER_REGISTRATION,
        'listing_creation' => FEATURE_LISTING_CREATION,
        'search_filtering' => FEATURE_SEARCH_FILTERING,
        'purchase_flow' => FEATURE_PURCHASE_FLOW,
        'admin_panel' => FEATURE_ADMIN_PANEL,
        'mobile_optimization' => FEATURE_MOBILE_OPTIMIZATION,
        'image_upload' => FEATURE_IMAGE_UPLOAD,
        'camera_capture' => FEATURE_CAMERA_CAPTURE,
        'location_services' => FEATURE_LOCATION_SERVICES,
        'google_maps' => FEATURE_GOOGLE_MAPS,
        'seller_approval' => FEATURE_SELLER_APPROVAL,
        'ratings_reviews' => FEATURE_RATINGS_REVIEWS,
        'messaging' => FEATURE_MESSAGING,
        'notifications' => FEATURE_NOTIFICATIONS,
        'analytics' => FEATURE_ANALYTICS,
        'api' => FEATURE_API,
        'webhooks' => FEATURE_WEBHOOKS,
        'sso' => FEATURE_SSO,
        '2fa' => FEATURE_2FA,
        'billing' => FEATURE_BILLING
    ]
];

// Function to get configuration value
function getConfig($key, $default = null) {
    global $app_config;
    
    if (isset($app_config[$key])) {
        return $app_config[$key];
    }
    
    return $default;
}

// Function to set configuration value
function setConfig($key, $value) {
    global $app_config;
    $app_config[$key] = $value;
}

// Function to check if feature is enabled
function isFeatureEnabled($feature) {
    global $app_config;
    
    if (isset($app_config['features'][$feature])) {
        return $app_config['features'][$feature];
    }
    
    return false;
}

// Function to get all configuration
function getAllConfig() {
    global $app_config;
    return $app_config;
}

// Function to reload configuration from file
function reloadConfig() {
    global $app_config;
    
    // Reload settings from JSON file
    if (file_exists(SETTINGS_FILE)) {
        $settings = json_decode(file_get_contents(SETTINGS_FILE), true);
        if ($settings && isset($settings['features'])) {
            $app_config['features'] = array_merge($app_config['features'], $settings['features']);
        }
    }
    
    return $app_config;
}

// Function to validate configuration
function validateConfig() {
    $errors = [];
    
    // Check required directories
    $required_dirs = [
        'DATA_PATH' => DATA_PATH,
        'UPLOADS_PATH' => UPLOADS_PATH,
        'SRC_PATH' => SRC_PATH,
        'PUBLIC_PATH' => PUBLIC_PATH
    ];
    
    foreach ($required_dirs as $name => $path) {
        if (!is_dir($path)) {
            $errors[] = "Required directory not found: $name ($path)";
        }
    }
    
    // Check required files
    $required_files = [
        'USERS_FILE' => USERS_FILE,
        'CATEGORIES_FILE' => CATEGORIES_FILE,
        'SETTINGS_FILE' => SETTINGS_FILE
    ];
    
    foreach ($required_files as $name => $file) {
        if (!file_exists($file)) {
            $errors[] = "Required file not found: $name ($file)";
        }
    }
    
    // Check permissions
    $writable_dirs = [
        'DATA_PATH' => DATA_PATH,
        'UPLOADS_PATH' => UPLOADS_PATH
    ];
    
    foreach ($writable_dirs as $name => $path) {
        if (!is_writable($path)) {
            $errors[] = "Directory not writable: $name ($path)";
        }
    }
    
    return $errors;
}

// Function to get system information
function getSystemInfo() {
    return [
        'php_version' => PHP_VERSION,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'server_os' => PHP_OS,
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'session_save_path' => session_save_path(),
        'gd_version' => extension_loaded('gd') ? gd_info()['Version'] : 'Not installed',
        'json_support' => extension_loaded('json'),
        'curl_support' => extension_loaded('curl'),
        'ssl_support' => extension_loaded('openssl')
    ];
}

// Function to check system requirements
function checkSystemRequirements() {
    $requirements = [
        'php_version' => [
            'required' => '7.4.0',
            'current' => PHP_VERSION,
            'status' => version_compare(PHP_VERSION, '7.4.0', '>=')
        ],
        'gd_extension' => [
            'required' => 'Installed',
            'current' => extension_loaded('gd') ? 'Installed' : 'Not installed',
            'status' => extension_loaded('gd')
        ],
        'json_extension' => [
            'required' => 'Installed',
            'current' => extension_loaded('json') ? 'Installed' : 'Not installed',
            'status' => extension_loaded('json')
        ],
        'session_support' => [
            'required' => 'Enabled',
            'current' => 'Enabled',
            'status' => true
        ],
        'file_uploads' => [
            'required' => 'Enabled',
            'current' => ini_get('file_uploads') ? 'Enabled' : 'Disabled',
            'status' => ini_get('file_uploads')
        ]
    ];
    
    return $requirements;
}

// Initialize configuration validation
$config_errors = validateConfig();
if (!empty($config_errors)) {
    if (DEVELOPMENT_MODE) {
        error_log('Configuration errors: ' . implode(', ', $config_errors));
    }
}

// Log configuration load
if (LOG_ERRORS) {
    error_log('Configuration loaded successfully at ' . date(DATETIME_FORMAT));
}