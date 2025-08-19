<?php
/**
 * System Constants
 * Location-Based Marketplace Platform
 */

// Prevent direct access
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

// Platform Information
define('PLATFORM_NAME', 'Location-Based Marketplace Platform');
define('PLATFORM_VERSION', '1.0.0');
define('PLATFORM_DESCRIPTION', 'Prototype for location-based buying and selling');

// File Paths
define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
define('DATA_PATH', ROOT_PATH . '/data');
define('SRC_PATH', ROOT_PATH . '/src');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOADS_PATH', PUBLIC_PATH . '/uploads');

// Data File Paths
define('USERS_FILE', DATA_PATH . '/users/users.json');
define('SESSIONS_FILE', DATA_PATH . '/users/sessions.json');
define('PRODUCTS_FILE', DATA_PATH . '/listings/products.json');
define('SERVICES_FILE', DATA_PATH . '/listings/services.json');
define('CATEGORIES_FILE', DATA_PATH . '/listings/categories.json');
define('ORDERS_FILE', DATA_PATH . '/orders/orders.json');
define('SETTINGS_FILE', DATA_PATH . '/system/settings.json');

// Upload Directories
define('PRODUCT_IMAGES_PATH', UPLOADS_PATH . '/products');
define('SERVICE_IMAGES_PATH', UPLOADS_PATH . '/services');
define('USER_IMAGES_PATH', UPLOADS_PATH . '/users');

// System Limits
define('MAX_IMAGES_PER_LISTING', 5);
define('MAX_FILE_SIZE_BYTES', 5 * 1024 * 1024); // 5MB
define('MAX_LISTINGS_PER_USER', 50);
define('MAX_TAGS_PER_LISTING', 5);
define('MAX_SEARCH_RADIUS_KM', 50);

// Allowed File Types
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'webp']);
define('ALLOWED_IMAGE_MIME_TYPES', [
    'image/jpeg',
    'image/jpg', 
    'image/png',
    'image/webp'
]);

// Security Settings
define('SESSION_TIMEOUT_SECONDS', 3600);
define('MAX_LOGIN_ATTEMPTS', 5);
define('PASSWORD_REQUIRED', false);
define('CSRF_PROTECTION', true);

// Performance Settings
define('ENABLE_CACHING', false);
define('IMAGE_COMPRESSION', true);
define('LAZY_LOADING', true);
define('MINIFY_ASSETS', false);

// User Types
define('USER_TYPE_BUYER', 'buyer');
define('USER_TYPE_SELLER', 'seller');
define('USER_TYPE_BOTH', 'both');
define('USER_TYPE_ADMIN', 'admin');

// Listing Types
define('LISTING_TYPE_PRODUCT', 'product');
define('LISTING_TYPE_SERVICE', 'service');

// Order Statuses
define('ORDER_STATUS_PENDING', 'pending');
define('ORDER_STATUS_APPROVED', 'approved');
define('ORDER_STATUS_IN_PROGRESS', 'in_progress');
define('ORDER_STATUS_COMPLETED', 'completed');
define('ORDER_STATUS_CANCELLED', 'cancelled');

// HTTP Status Codes
define('HTTP_OK', 200);
define('HTTP_CREATED', 201);
define('HTTP_BAD_REQUEST', 400);
define('HTTP_UNAUTHORIZED', 401);
define('HTTP_FORBIDDEN', 403);
define('HTTP_NOT_FOUND', 404);
define('HTTP_INTERNAL_ERROR', 500);

// Response Messages
define('MSG_SUCCESS', 'Operation completed successfully');
define('MSG_ERROR', 'An error occurred');
define('MSG_VALIDATION_ERROR', 'Validation failed');
define('MSG_NOT_FOUND', 'Resource not found');
define('MSG_UNAUTHORIZED', 'Access denied');
define('MSG_SESSION_EXPIRED', 'Session expired, please login again');

// Date Formats
define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('ISO_DATETIME_FORMAT', 'c');

// Currency
define('DEFAULT_CURRENCY', 'USD');
define('CURRENCY_SYMBOL', '$');

// Location Defaults
define('DEFAULT_LATITUDE', 40.7128);
define('DEFAULT_LONGITUDE', -74.0060);
define('DEFAULT_LOCATION', 'New York, NY');

// Pagination
define('DEFAULT_PAGE_SIZE', 20);
define('MAX_PAGE_SIZE', 100);

// Cache Settings
define('CACHE_TTL_DEFAULT', 3600);
define('CACHE_TTL_SHORT', 300);
define('CACHE_TTL_LONG', 86400);

// Error Reporting
define('ERROR_REPORTING_LEVEL', E_ALL & ~E_DEPRECATED & ~E_STRICT);
define('DISPLAY_ERRORS', false);
define('LOG_ERRORS', true);
define('ERROR_LOG_FILE', ROOT_PATH . '/logs/error.log');

// Development Mode
define('DEVELOPMENT_MODE', false);
define('DEBUG_MODE', false);

// API Settings
define('API_VERSION', 'v1');
define('API_RATE_LIMIT', 100); // requests per minute
define('API_TIMEOUT', 30); // seconds

// Google Maps API
define('GOOGLE_MAPS_API_KEY', '');
define('GOOGLE_MAPS_DEFAULT_ZOOM', 12);
define('GOOGLE_MAPS_DEFAULT_CENTER_LAT', 40.7128);
define('GOOGLE_MAPS_DEFAULT_CENTER_LNG', -74.0060);

// File Upload Settings
define('UPLOAD_MAX_FILESIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_MAX_FILES', 5);
define('UPLOAD_ALLOWED_EXTENSIONS', 'jpg,jpeg,png,webp');

// Session Settings
define('SESSION_NAME', 'marketplace_session');
define('SESSION_COOKIE_LIFETIME', 0);
define('SESSION_COOKIE_PATH', '/');
define('SESSION_COOKIE_DOMAIN', '');
define('SESSION_COOKIE_SECURE', false);
define('SESSION_COOKIE_HTTPONLY', true);

// Security Headers
define('SECURITY_HEADERS', [
    'X-Content-Type-Options' => 'nosniff',
    'X-Frame-Options' => 'DENY',
    'X-XSS-Protection' => '1; mode=block',
    'Referrer-Policy' => 'strict-origin-when-cross-origin'
]);

// Database Settings (for future migration)
define('DB_HOST', 'localhost');
define('DB_NAME', 'marketplace');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATION', 'utf8mb4_unicode_ci');

// Email Settings (for future implementation)
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_ENCRYPTION', 'tls');

// Logging Settings
define('LOG_LEVEL', 'INFO');
define('LOG_FILE', ROOT_PATH . '/logs/app.log');
define('LOG_MAX_SIZE', 10 * 1024 * 1024); // 10MB
define('LOG_MAX_FILES', 5);

// Maintenance Mode
define('MAINTENANCE_MODE', false);
define('MAINTENANCE_ALLOWED_IPS', ['127.0.0.1', '::1']);

// Feature Flags
define('FEATURE_USER_REGISTRATION', true);
define('FEATURE_LISTING_CREATION', true);
define('FEATURE_SEARCH_FILTERING', true);
define('FEATURE_PURCHASE_FLOW', true);
define('FEATURE_ADMIN_PANEL', true);
define('FEATURE_MOBILE_OPTIMIZATION', true);
define('FEATURE_IMAGE_UPLOAD', true);
define('FEATURE_CAMERA_CAPTURE', true);
define('FEATURE_LOCATION_SERVICES', true);
define('FEATURE_GOOGLE_MAPS', true);
define('FEATURE_SELLER_APPROVAL', true);
define('FEATURE_RATINGS_REVIEWS', false);
define('FEATURE_MESSAGING', false);
define('FEATURE_NOTIFICATIONS', false);
define('FEATURE_ANALYTICS', false);
define('FEATURE_API', false);
define('FEATURE_WEBHOOKS', false);
define('FEATURE_SSO', false);
define('FEATURE_2FA', false);
define('FEATURE_BILLING', false);