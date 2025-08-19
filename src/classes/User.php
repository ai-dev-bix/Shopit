<?php
/**
 * User Class
 * Handles user management operations for the marketplace platform
 */

// Prevent direct access
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

class User {
    private $db;
    private $id;
    private $username;
    private $type;
    private $data;
    
    public function __construct($username = null) {
        try {
            $this->db = new Database();
            
            if ($username) {
                $this->loadByUsername($username);
            }
        } catch (Exception $e) {
            $this->logError("Failed to initialize User class: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Create a new user
     * 
     * @param array $userData User data
     * @return string|false Generated user ID or false on error
     */
    public function create($userData) {
        // Validate required fields
        if (!$this->validateUserData($userData)) {
            return false;
        }
        
        // Check if username already exists
        if ($this->isUsernameTaken($userData['username'])) {
            $this->logError("Username already taken: " . $userData['username']);
            return false;
        }
        
        // Prepare user record
        $userRecord = [
            'username' => $userData['username'],
            'type' => $userData['type'] ?? USER_TYPE_BUYER,
            'email' => $userData['email'] ?? '',
            'phone' => $userData['phone'] ?? '',
            'location' => $userData['location'] ?? [
                'lat' => DEFAULT_LATITUDE,
                'lng' => DEFAULT_LONGITUDE,
                'address' => DEFAULT_LOCATION
            ],
            'rating' => 0,
            'total_ratings' => 0,
            'status' => 'active'
        ];
        
        // Insert user record
        $userId = $this->db->insert(USERS_FILE, $userRecord, 'users');
        
        if ($userId) {
            $this->id = $userId;
            $this->username = $userData['username'];
            $this->type = $userRecord['type'];
            $this->data = $userRecord;
            
            $this->logInfo("User created successfully: $userId");
            return $userId;
        }
        
        return false;
    }
    
    /**
     * Load user by username
     * 
     * @param string $username Username to load
     * @return bool Success status
     */
    public function loadByUsername($username) {
        // Validate and sanitize username
        if (empty($username) || !is_string($username)) {
            return false;
        }
        
        $username = trim($username);
        if (strlen($username) < 3 || strlen($username) > 20) {
            return false;
        }
        
        // Validate username format
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            return false;
        }
        
        $users = $this->db->getAll(USERS_FILE, 'users');
        
        foreach ($users as $user) {
            if ($user['username'] === $username) {
                $this->id = $user['id'];
                $this->username = $user['username'];
                $this->type = $user['type'];
                $this->data = $user;
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Load user by ID
     * 
     * @param string $id User ID to load
     * @return bool Success status
     */
    public function loadById($id) {
        $user = $this->db->findById(USERS_FILE, $id, 'users');
        
        if ($user) {
            $this->id = $user['id'];
            $this->username = $user['username'];
            $this->type = $user['type'];
            $this->data = $user;
            return true;
        }
        
        return false;
    }
    
    /**
     * Authenticate user
     * 
     * @param string $username Username to authenticate
     * @return bool Authentication success
     */
    public function authenticate($username) {
        // Validate input
        if (empty($username) || !is_string($username)) {
            return false;
        }
        
        // Check for brute force attempts
        if ($this->isAccountLocked($username)) {
            $this->logError("Account locked due to too many failed attempts: $username");
            return false;
        }
        
        if ($this->loadByUsername($username)) {
            // Check if user is active
            if ($this->data['status'] !== 'active') {
                $this->logError("User account not active: $username");
                return false;
            }
            
            // Check if account is suspended
            if (isset($this->data['status']) && $this->data['status'] === 'suspended') {
                $this->logError("User account suspended: $username");
                return false;
            }
            
            // Update last active timestamp
            $this->updateLastActive();
            
            // Reset failed login attempts
            $this->resetFailedLoginAttempts($username);
            
            $this->logInfo("User authenticated successfully: $username");
            return true;
        } else {
            // Log failed attempt
            $this->logFailedLoginAttempt($username);
        }
        
        return false;
    }
    
    /**
     * Update user profile
     * 
     * @param array $updateData Data to update
     * @return bool Success status
     */
    public function update($updateData) {
        if (!$this->id) {
            $this->logError("Cannot update user: User not loaded");
            return false;
        }
        
        // Validate update data
        if (!$this->validateUpdateData($updateData)) {
            return false;
        }
        
        // Update user record
        $success = $this->db->update(USERS_FILE, $this->id, $updateData, 'users');
        
        if ($success) {
            // Reload user data
            $this->loadById($this->id);
            $this->logInfo("User updated successfully: " . $this->id);
        }
        
        return $success;
    }
    
    /**
     * Delete user account
     * 
     * @return bool Success status
     */
    public function delete() {
        if (!$this->id) {
            $this->logError("Cannot delete user: User not loaded");
            return false;
        }
        
        // Check if user has active listings
        if ($this->hasActiveListings()) {
            $this->logError("Cannot delete user with active listings: " . $this->id);
            return false;
        }
        
        // Check if user has active orders
        if ($this->hasActiveOrders()) {
            $this->logError("Cannot delete user with active orders: " . $this->id);
            return false;
        }
        
        // Delete user record
        $success = $this->db->delete(USERS_FILE, $this->id, 'users');
        
        if ($success) {
            $this->logInfo("User deleted successfully: " . $this->id);
            $this->clearUserData();
        }
        
        return $success;
    }
    
    /**
     * Suspend user account
     * 
     * @param string $reason Reason for suspension
     * @return bool Success status
     */
    public function suspend($reason = '') {
        if (!$this->id) {
            $this->logError("Cannot suspend user: User not loaded");
            return false;
        }
        
        $updateData = [
            'status' => 'suspended',
            'suspended_at' => date(ISO_DATETIME_FORMAT),
            'suspension_reason' => $reason
        ];
        
        return $this->update($updateData);
    }
    
    /**
     * Activate user account
     * 
     * @return bool Success status
     */
    public function activate() {
        if (!$this->id) {
            $this->logError("Cannot activate user: User not loaded");
            return false;
        }
        
        $updateData = [
            'status' => 'active',
            'activated_at' => date(ISO_DATETIME_FORMAT)
        ];
        
        // Remove suspension data
        unset($this->data['suspended_at']);
        unset($this->data['suspension_reason']);
        
        return $this->update($updateData);
    }
    
    /**
     * Get user profile
     * 
     * @return array|false User profile data or false on error
     */
    public function getProfile() {
        if (!$this->id) {
            return false;
        }
        
        // Return safe profile data (exclude sensitive information)
        $profile = $this->data;
        unset($profile['suspended_at']);
        unset($profile['suspension_reason']);
        
        return $profile;
    }
    
    /**
     * Get user listings
     * 
     * @param string $type Listing type (product/service)
     * @return array User listings
     */
    public function getListings($type = null) {
        if (!$this->id) {
            return [];
        }
        
        $listings = [];
        
        // Get product listings
        if (!$type || $type === LISTING_TYPE_PRODUCT) {
            $products = $this->db->search(PRODUCTS_FILE, ['user_id' => $this->id], 'products');
            $listings = array_merge($listings, $products);
        }
        
        // Get service listings
        if (!$type || $type === LISTING_TYPE_SERVICE) {
            $services = $this->db->search(SERVICES_FILE, ['user_id' => $this->id], 'services');
            $listings = array_merge($listings, $services);
        }
        
        return $listings;
    }
    
    /**
     * Get user orders
     * 
     * @param string $role Role in order (buyer/seller)
     * @return array User orders
     */
    public function getOrders($role = 'buyer') {
        if (!$this->id) {
            return [];
        }
        
        $field = $role === 'seller' ? 'seller_id' : 'buyer_id';
        return $this->db->search(ORDERS_FILE, [$field => $this->id], 'orders');
    }
    
    /**
     * Check if username is taken
     * 
     * @param string $username Username to check
     * @return bool Whether username is taken
     */
    public function isUsernameTaken($username) {
        $users = $this->db->getAll(USERS_FILE, 'users');
        
        foreach ($users as $user) {
            if ($user['username'] === $username) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if user is admin
     * 
     * @return bool Whether user is admin
     */
    public function isAdmin() {
        return $this->type === USER_TYPE_ADMIN;
    }
    
    /**
     * Check if user is seller
     * 
     * @return bool Whether user is seller
     */
    public function isSeller() {
        return in_array($this->type, [USER_TYPE_SELLER, USER_TYPE_BOTH, USER_TYPE_ADMIN]);
    }
    
    /**
     * Check if user is buyer
     * 
     * @return bool Whether user is buyer
     */
    public function isBuyer() {
        return in_array($this->type, [USER_TYPE_BUYER, USER_TYPE_BOTH, USER_TYPE_ADMIN]);
    }
    
    /**
     * Update user rating
     * 
     * @param float $rating New rating
     * @return bool Success status
     */
    public function updateRating($rating) {
        if (!$this->id) {
            return false;
        }
        
        if ($rating < 0 || $rating > 5) {
            $this->logError("Invalid rating value: $rating");
            return false;
        }
        
        $currentRating = $this->data['rating'] ?? 0;
        $totalRatings = $this->data['total_ratings'] ?? 0;
        
        // Calculate new average rating
        $newTotalRating = ($currentRating * $totalRatings) + $rating;
        $newTotalRatings = $totalRatings + 1;
        $newAverageRating = $newTotalRating / $newTotalRatings;
        
        $updateData = [
            'rating' => round($newAverageRating, 2),
            'total_ratings' => $newTotalRatings
        ];
        
        return $this->update($updateData);
    }
    
    /**
     * Update user location
     * 
     * @param float $lat Latitude
     * @param float $lng Longitude
     * @param string $address Address
     * @return bool Success status
     */
    public function updateLocation($lat, $lng, $address = '') {
        if (!$this->id) {
            return false;
        }
        
        // Validate coordinates
        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            $this->logError("Invalid coordinates: lat=$lat, lng=$lng");
            return false;
        }
        
        $updateData = [
            'location' => [
                'lat' => $lat,
                'lng' => $lng,
                'address' => $address
            ]
        ];
        
        return $this->update($updateData);
    }
    
    /**
     * Get user statistics
     * 
     * @return array User statistics
     */
    public function getStats() {
        if (!$this->id) {
            return [];
        }
        
        $listings = $this->getListings();
        $buyerOrders = $this->getOrders('buyer');
        $sellerOrders = $this->getOrders('seller');
        
        return [
            'total_listings' => count($listings),
            'active_listings' => count(array_filter($listings, fn($l) => $l['status'] === 'active')),
            'total_buyer_orders' => count($buyerOrders),
            'total_seller_orders' => count($sellerOrders),
            'completed_orders' => count(array_filter($buyerOrders, fn($o) => $o['status'] === 'completed')) +
                                 count(array_filter($sellerOrders, fn($o) => $o['status'] === 'completed')),
            'rating' => $this->data['rating'] ?? 0,
            'total_ratings' => $this->data['total_ratings'] ?? 0
        ];
    }
    
    /**
     * Search users
     * 
     * @param array $criteria Search criteria
     * @return array Search results
     */
    public static function searchUsers($criteria) {
        $db = new Database();
        return $db->search(USERS_FILE, $criteria, 'users');
    }
    
    /**
     * Get all users
     * 
     * @return array All users
     */
    public static function getAllUsers() {
        $db = new Database();
        return $db->getAll(USERS_FILE, 'users');
    }
    
    /**
     * Get user by ID
     * 
     * @param string $id User ID
     * @return array|false User data or false if not found
     */
    public static function getById($id) {
        $db = new Database();
        return $db->findById(USERS_FILE, $id, 'users');
    }
    
    /**
     * Get user by username
     * 
     * @param string $username Username
     * @return array|false User data or false if not found
     */
    public static function getByUsername($username) {
        $users = self::getAllUsers();
        
        foreach ($users as $user) {
            if ($user['username'] === $username) {
                return $user;
            }
        }
        
        return false;
    }
    
    /**
     * Validate user data for creation
     * 
     * @param array $userData User data to validate
     * @return bool Validation success
     */
    private function validateUserData($userData) {
        // Check required fields
        if (empty($userData['username'])) {
            $this->logError("Username is required");
            return false;
        }
        
        // Sanitize username
        $userData['username'] = trim($userData['username']);
        
        // Validate username format
        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $userData['username'])) {
            $this->logError("Invalid username format: " . $userData['username']);
            return false;
        }
        
        // Validate user type
        $validTypes = [USER_TYPE_BUYER, USER_TYPE_SELLER, USER_TYPE_BOTH];
        if (isset($userData['type']) && !in_array($userData['type'], $validTypes)) {
            $this->logError("Invalid user type: " . $userData['type']);
            return false;
        }
        
        // Validate email if provided
        if (isset($userData['email']) && !empty($userData['email'])) {
            $userData['email'] = trim($userData['email']);
            if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
                $this->logError("Invalid email format: " . $userData['email']);
                return false;
            }
        }
        
        // Validate phone if provided
        if (isset($userData['phone']) && !empty($userData['phone'])) {
            $userData['phone'] = trim($userData['phone']);
            if (!preg_match('/^[\+]?[1-9][\d\s\-\(\)]{0,15}$/', $userData['phone'])) {
                $this->logError("Invalid phone format: " . $userData['phone']);
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Validate update data
     * 
     * @param array $updateData Data to validate
     * @return bool Validation success
     */
    private function validateUpdateData($updateData) {
        // Validate email if provided
        if (isset($updateData['email']) && !empty($updateData['email'])) {
            if (!filter_var($updateData['email'], FILTER_VALIDATE_EMAIL)) {
                $this->logError("Invalid email format: " . $updateData['email']);
                return false;
            }
        }
        
        // Validate location if provided
        if (isset($updateData['location'])) {
            $location = $updateData['location'];
            if (isset($location['lat']) && isset($location['lng'])) {
                if ($location['lat'] < -90 || $location['lat'] > 90 || 
                    $location['lng'] < -180 || $location['lng'] > 180) {
                    $this->logError("Invalid coordinates in update data");
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * Update last active timestamp
     */
    private function updateLastActive() {
        $updateData = ['last_active' => date(ISO_DATETIME_FORMAT)];
        $this->db->update(USERS_FILE, $this->id, $updateData, 'users');
    }
    
    /**
     * Check if user has active listings
     * 
     * @return bool Whether user has active listings
     */
    private function hasActiveListings() {
        $listings = $this->getListings();
        foreach ($listings as $listing) {
            if ($listing['status'] === 'active') {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Check if user has active orders
     * 
     * @return bool Whether user has active orders
     */
    private function hasActiveOrders() {
        $buyerOrders = $this->getOrders('buyer');
        $sellerOrders = $this->getOrders('seller');
        
        $allOrders = array_merge($buyerOrders, $sellerOrders);
        
        foreach ($allOrders as $order) {
            if (in_array($order['status'], ['pending', 'approved', 'in_progress'])) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Clear user data
     */
    private function clearUserData() {
        $this->id = null;
        $this->username = null;
        $this->type = null;
        $this->data = null;
    }
    
    /**
     * Log error message
     * 
     * @param string $message Error message
     */
    private function logError($message) {
        if (LOG_ERRORS) {
            $logData = [
                'timestamp' => date('Y-m-d H:i:s'),
                'level' => 'ERROR',
                'component' => 'User',
                'message' => $message,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown'
            ];
            error_log('[User Error] ' . json_encode($logData));
        }
    }
    
    /**
     * Check if account is locked due to too many failed attempts
     * 
     * @param string $username Username to check
     * @return bool Whether account is locked
     */
    private function isAccountLocked($username) {
        $failedAttemptsFile = ROOT_PATH . '/data/system/failed_logins.json';
        
        if (!file_exists($failedAttemptsFile)) {
            return false;
        }
        
        $failedAttempts = json_decode(file_get_contents($failedAttemptsFile), true) ?: [];
        
        if (isset($failedAttempts[$username])) {
            $attempts = $failedAttempts[$username];
            $lastAttempt = $attempts['last_attempt'] ?? 0;
            $count = $attempts['count'] ?? 0;
            
            // Lock account for 15 minutes after 5 failed attempts
            if ($count >= 5 && (time() - $lastAttempt) < 900) {
                return true;
            }
            
            // Reset count if more than 15 minutes have passed
            if ((time() - $lastAttempt) >= 900) {
                unset($failedAttempts[$username]);
                file_put_contents($failedAttemptsFile, json_encode($failedAttempts));
            }
        }
        
        return false;
    }
    
    /**
     * Log failed login attempt
     * 
     * @param string $username Username that failed
     */
    private function logFailedLoginAttempt($username) {
        $failedAttemptsFile = ROOT_PATH . '/data/system/failed_logins.json';
        
        $failedAttempts = [];
        if (file_exists($failedAttemptsFile)) {
            $failedAttempts = json_decode(file_get_contents($failedAttemptsFile), true) ?: [];
        }
        
        if (!isset($failedAttempts[$username])) {
            $failedAttempts[$username] = [
                'count' => 0,
                'last_attempt' => 0
            ];
        }
        
        $failedAttempts[$username]['count']++;
        $failedAttempts[$username]['last_attempt'] = time();
        
        file_put_contents($failedAttemptsFile, json_encode($failedAttempts));
        
        // Log security event
        $this->logError("Failed login attempt for username: $username - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    }
    
    /**
     * Reset failed login attempts for successful login
     * 
     * @param string $username Username that succeeded
     */
    private function resetFailedLoginAttempts($username) {
        $failedAttemptsFile = ROOT_PATH . '/data/system/failed_logins.json';
        
        if (file_exists($failedAttemptsFile)) {
            $failedAttempts = json_decode(file_get_contents($failedAttemptsFile), true) ?: [];
            
            if (isset($failedAttempts[$username])) {
                unset($failedAttempts[$username]);
                file_put_contents($failedAttemptsFile, json_encode($failedAttempts));
            }
        }
    }
    
    /**
     * Log info message
     * 
     * @param string $message Info message
     */
    private function logInfo($message) {
        if (LOG_ERRORS) {
            $logData = [
                'timestamp' => date('Y-m-d H:i:s'),
                'level' => 'INFO',
                'component' => 'User',
                'message' => $message,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown'
            ];
            error_log('[User Info] ' . json_encode($logData));
        }
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getType() { return $this->type; }
    public function getData() { return $this->data; }
    public function getEmail() { return $this->data['email'] ?? ''; }
    public function getPhone() { return $this->data['phone'] ?? ''; }
    public function getLocation() { return $this->data['location'] ?? []; }
    public function getRating() { return $this->data['rating'] ?? 0; }
    public function getStatus() { return $this->data['status'] ?? 'unknown'; }
    public function getCreatedAt() { return $this->data['created_at'] ?? ''; }
    public function getLastActive() { return $this->data['last_active'] ?? ''; }
}