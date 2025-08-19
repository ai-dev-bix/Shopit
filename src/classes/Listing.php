<?php
/**
 * Listing Class
 * Handles product and service listing management for the marketplace platform
 */

// Prevent direct access
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

class Listing {
    private $db;
    private $id;
    private $type;
    private $data;
    
    public function __construct($id = null) {
        $this->db = new Database();
        
        if ($id) {
            $this->loadById($id);
        }
    }
    
    /**
     * Create a new listing
     * 
     * @param array $listingData Listing data
     * @return string|false Generated listing ID or false on error
     */
    public function create($listingData) {
        // Validate required fields
        if (!$this->validateListingData($listingData)) {
            return false;
        }
        
        // Check if user exists and is active
        $user = new User();
        if (!$user->loadById($listingData['user_id'])) {
            $this->logError("User not found: " . $listingData['user_id']);
            return false;
        }
        
        if ($user->getStatus() !== 'active') {
            $this->logError("User account not active: " . $listingData['user_id']);
            return false;
        }
        
        // Check if user can create listings
        if (!$user->isSeller()) {
            $this->logError("User cannot create listings: " . $listingData['user_id']);
            return false;
        }
        
        // Check user's listing limit
        if ($this->hasReachedListingLimit($listingData['user_id'])) {
            $this->logError("User has reached listing limit: " . $listingData['user_id']);
            return false;
        }
        
        // Prepare listing record
        $listingRecord = [
            'user_id' => $listingData['user_id'],
            'type' => $listingData['type'],
            'title' => $listingData['title'],
            'description' => $listingData['description'],
            'price' => $listingData['price'],
            'currency' => $listingData['currency'] ?? DEFAULT_CURRENCY,
            'category_id' => $listingData['category_id'],
            'tags' => $listingData['tags'] ?? [],
            'location' => $listingData['location'] ?? [
                'lat' => DEFAULT_LATITUDE,
                'lng' => DEFAULT_LONGITUDE,
                'address' => DEFAULT_LOCATION
            ],
            'images' => $listingData['images'] ?? [],
            'status' => 'active',
            'featured' => false,
            'views' => 0,
            'favorites' => 0,
            'rating' => 0,
            'total_ratings' => 0
        ];
        
        // Add service-specific fields
        if ($listingData['type'] === LISTING_TYPE_SERVICE) {
            $listingRecord['service_details'] = [
                'duration' => $listingData['duration'] ?? '',
                'availability' => $listingData['availability'] ?? [],
                'service_area' => $listingData['service_area'] ?? [],
                'certifications' => $listingData['certifications'] ?? []
            ];
        }
        
        // Add product-specific fields
        if ($listingData['type'] === LISTING_TYPE_PRODUCT) {
            $listingRecord['product_details'] = [
                'condition' => $listingData['condition'] ?? 'new',
                'brand' => $listingData['brand'] ?? '',
                'model' => $listingData['model'] ?? '',
                'warranty' => $listingData['warranty'] ?? false,
                'shipping' => $listingData['shipping'] ?? false,
                'pickup' => $listingData['pickup'] ?? true
            ];
        }
        
        // Insert listing record
        $filePath = $listingData['type'] === LISTING_TYPE_SERVICE ? SERVICES_FILE : PRODUCTS_FILE;
        $key = $listingData['type'] === LISTING_TYPE_SERVICE ? 'services' : 'products';
        
        $listingId = $this->db->insert($filePath, $listingRecord, $key);
        
        if ($listingId) {
            $this->id = $listingId;
            $this->type = $listingData['type'];
            $this->data = $listingRecord;
            
            $this->logInfo("Listing created successfully: $listingId");
            return $listingId;
        }
        
        return false;
    }
    
    /**
     * Load listing by ID
     * 
     * @param string $id Listing ID to load
     * @return bool Success status
     */
    public function loadById($id) {
        // Try to find in products first
        $product = $this->db->findById(PRODUCTS_FILE, $id, 'products');
        if ($product) {
            $this->id = $product['id'];
            $this->type = LISTING_TYPE_PRODUCT;
            $this->data = $product;
            return true;
        }
        
        // Try to find in services
        $service = $this->db->findById(SERVICES_FILE, $id, 'services');
        if ($service) {
            $this->id = $service['id'];
            $this->type = LISTING_TYPE_SERVICE;
            $this->data = $service;
            return true;
        }
        
        return false;
    }
    
    /**
     * Update listing
     * 
     * @param array $updateData Data to update
     * @return bool Success status
     */
    public function update($updateData) {
        if (!$this->id) {
            $this->logError("Cannot update listing: Listing not loaded");
            return false;
        }
        
        // Validate update data
        if (!$this->validateUpdateData($updateData)) {
            return false;
        }
        
        // Check if user can update this listing
        if (!$this->canUserModify($updateData['user_id'] ?? null)) {
            $this->logError("User cannot modify listing: " . $this->id);
            return false;
        }
        
        // Update listing record
        $filePath = $this->type === LISTING_TYPE_SERVICE ? SERVICES_FILE : PRODUCTS_FILE;
        $key = $this->type === LISTING_TYPE_SERVICE ? 'services' : 'products';
        
        $success = $this->db->update($filePath, $this->id, $updateData, $key);
        
        if ($success) {
            // Reload listing data
            $this->loadById($this->id);
            $this->logInfo("Listing updated successfully: " . $this->id);
        }
        
        return $success;
    }
    
    /**
     * Delete listing
     * 
     * @param string $userId User ID requesting deletion
     * @return bool Success status
     */
    public function delete($userId = null) {
        if (!$this->id) {
            $this->logError("Cannot delete listing: Listing not loaded");
            return false;
        }
        
        // Check if user can delete this listing
        if (!$this->canUserModify($userId)) {
            $this->logError("User cannot delete listing: " . $this->id);
            return false;
        }
        
        // Check if listing has active orders
        if ($this->hasActiveOrders()) {
            $this->logError("Cannot delete listing with active orders: " . $this->id);
            return false;
        }
        
        // Delete listing record
        $filePath = $this->type === LISTING_TYPE_SERVICE ? SERVICES_FILE : PRODUCTS_FILE;
        $key = $this->type === LISTING_TYPE_SERVICE ? 'services' : 'products';
        
        $success = $this->db->delete($filePath, $this->id, $key);
        
        if ($success) {
            $this->logInfo("Listing deleted successfully: " . $this->id);
            $this->clearListingData();
        }
        
        return $success;
    }
    
    /**
     * Search listings
     * 
     * @param array $criteria Search criteria
     * @param string $type Listing type (product/service)
     * @return array Search results
     */
    public static function search($criteria, $type = null) {
        $db = new Database();
        $results = [];
        
        // Search products
        if (!$type || $type === LISTING_TYPE_PRODUCT) {
            $products = $db->search(PRODUCTS_FILE, $criteria, 'products');
            $results = array_merge($results, $products);
        }
        
        // Search services
        if (!$type || $type === LISTING_TYPE_SERVICE) {
            $services = $db->search(SERVICES_FILE, $criteria, 'services');
            $results = array_merge($results, $services);
        }
        
        // Sort results by relevance or date
        usort($results, function($a, $b) {
            // Prioritize featured listings
            if ($a['featured'] && !$b['featured']) return -1;
            if (!$a['featured'] && $b['featured']) return 1;
            
            // Then by creation date (newest first)
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return $results;
    }
    
    /**
     * Get listings by user
     * 
     * @param string $userId User ID
     * @param string $type Listing type (product/service)
     * @return array User's listings
     */
    public static function getByUser($userId, $type = null) {
        $criteria = ['user_id' => $userId];
        return self::search($criteria, $type);
    }
    
    /**
     * Get listings by category
     * 
     * @param string $categoryId Category ID
     * @param string $type Listing type (product/service)
     * @return array Category listings
     */
    public static function getByCategory($categoryId, $type = null) {
        $criteria = ['category_id' => $categoryId];
        return self::search($criteria, $type);
    }
    
    /**
     * Get listings by location
     * 
     * @param float $lat Latitude
     * @param float $lng Longitude
     * @param float $radius Search radius in kilometers
     * @param string $type Listing type (product/service)
     * @return array Location-based listings
     */
    public static function getByLocation($lat, $lng, $radius = 50, $type = null) {
        $allListings = [];
        
        // Get all listings of the specified type
        if (!$type || $type === LISTING_TYPE_PRODUCT) {
            $products = (new Database())->getAll(PRODUCTS_FILE, 'products');
            $allListings = array_merge($allListings, $products);
        }
        
        if (!$type || $type === LISTING_TYPE_SERVICE) {
            $services = (new Database())->getAll(SERVICES_FILE, 'services');
            $allListings = array_merge($allListings, $services);
        }
        
        // Filter by distance
        $nearbyListings = [];
        foreach ($allListings as $listing) {
            if (isset($listing['location']['lat']) && isset($listing['location']['lng'])) {
                $distance = getDistance($lat, $lng, $listing['location']['lat'], $listing['location']['lng']);
                if ($distance <= $radius) {
                    $listing['distance'] = $distance;
                    $nearbyListings[] = $listing;
                }
            }
        }
        
        // Sort by distance
        usort($nearbyListings, function($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });
        
        return $nearbyListings;
    }
    
    /**
     * Get featured listings
     * 
     * @param string $type Listing type (product/service)
     * @param int $limit Maximum number of listings
     * @return array Featured listings
     */
    public static function getFeatured($type = null, $limit = 10) {
        $criteria = ['featured' => true];
        $featured = self::search($criteria, $type);
        
        return array_slice($featured, 0, $limit);
    }
    
    /**
     * Get recent listings
     * 
     * @param string $type Listing type (product/service)
     * @param int $limit Maximum number of listings
     * @return array Recent listings
     */
    public static function getRecent($type = null, $limit = 20) {
        $allListings = [];
        
        if (!$type || $type === LISTING_TYPE_PRODUCT) {
            $products = (new Database())->getAll(PRODUCTS_FILE, 'products');
            $allListings = array_merge($allListings, $products);
        }
        
        if (!$type || $type === LISTING_TYPE_SERVICE) {
            $services = (new Database())->getAll(SERVICES_FILE, 'services');
            $allListings = array_merge($allListings, $services);
        }
        
        // Sort by creation date (newest first)
        usort($allListings, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return array_slice($allListings, 0, $limit);
    }
    
    /**
     * Increment view count
     * 
     * @return bool Success status
     */
    public function incrementViews() {
        if (!$this->id) {
            return false;
        }
        
        $updateData = ['views' => ($this->data['views'] ?? 0) + 1];
        return $this->update($updateData);
    }
    
    /**
     * Toggle favorite status
     * 
     * @param string $userId User ID
     * @return bool Success status
     */
    public function toggleFavorite($userId) {
        if (!$this->id) {
            return false;
        }
        
        // This would typically update a separate favorites table
        // For now, we'll just increment the favorites count
        $updateData = ['favorites' => ($this->data['favorites'] ?? 0) + 1];
        return $this->update($updateData);
    }
    
    /**
     * Update rating
     * 
     * @param float $rating New rating (1-5)
     * @return bool Success status
     */
    public function updateRating($rating) {
        if (!$this->id) {
            return false;
        }
        
        if ($rating < 1 || $rating > 5) {
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
     * Get listing statistics
     * 
     * @return array Listing statistics
     */
    public function getStats() {
        if (!$this->id) {
            return [];
        }
        
        return [
            'views' => $this->data['views'] ?? 0,
            'favorites' => $this->data['favorites'] ?? 0,
            'rating' => $this->data['rating'] ?? 0,
            'total_ratings' => $this->data['total_ratings'] ?? 0,
            'created_at' => $this->data['created_at'] ?? '',
            'updated_at' => $this->data['updated_at'] ?? ''
        ];
    }
    
    /**
     * Validate listing data for creation
     * 
     * @param array $listingData Listing data to validate
     * @return bool Validation success
     */
    private function validateListingData($listingData) {
        // Check required fields
        $requiredFields = ['user_id', 'type', 'title', 'description', 'price', 'category_id'];
        foreach ($requiredFields as $field) {
            if (empty($listingData[$field])) {
                $this->logError("Required field missing: $field");
                return false;
            }
        }
        
        // Validate listing type
        if (!in_array($listingData['type'], [LISTING_TYPE_PRODUCT, LISTING_TYPE_SERVICE])) {
            $this->logError("Invalid listing type: " . $listingData['type']);
            return false;
        }
        
        // Validate price
        if (!is_numeric($listingData['price']) || $listingData['price'] < 0) {
            $this->logError("Invalid price: " . $listingData['price']);
            return false;
        }
        
        // Validate title length
        if (strlen($listingData['title']) < 3 || strlen($listingData['title']) > 100) {
            $this->logError("Title length invalid: " . strlen($listingData['title']));
            return false;
        }
        
        // Validate description length
        if (strlen($listingData['description']) < 10 || strlen($listingData['description']) > 2000) {
            $this->logError("Description length invalid: " . strlen($listingData['description']));
            return false;
        }
        
        // Validate tags
        if (isset($listingData['tags']) && is_array($listingData['tags'])) {
            if (count($listingData['tags']) > MAX_TAGS_PER_LISTING) {
                $this->logError("Too many tags: " . count($listingData['tags']));
                return false;
            }
            
            foreach ($listingData['tags'] as $tag) {
                if (strlen($tag) < 2 || strlen($tag) > 20) {
                    $this->logError("Invalid tag length: $tag");
                    return false;
                }
            }
        }
        
        // Validate images
        if (isset($listingData['images']) && is_array($listingData['images'])) {
            if (count($listingData['images']) > MAX_IMAGES_PER_LISTING) {
                $this->logError("Too many images: " . count($listingData['images']));
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
        // Validate price if provided
        if (isset($updateData['price'])) {
            if (!is_numeric($updateData['price']) || $updateData['price'] < 0) {
                $this->logError("Invalid price in update: " . $updateData['price']);
                return false;
            }
        }
        
        // Validate title length if provided
        if (isset($updateData['title'])) {
            if (strlen($updateData['title']) < 3 || strlen($updateData['title']) > 100) {
                $this->logError("Title length invalid in update: " . strlen($updateData['title']));
                return false;
            }
        }
        
        // Validate description length if provided
        if (isset($updateData['description'])) {
            if (strlen($updateData['description']) < 10 || strlen($updateData['description']) > 2000) {
                $this->logError("Description length invalid in update: " . strlen($updateData['description']));
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Check if user can modify listing
     * 
     * @param string $userId User ID to check
     * @return bool Whether user can modify
     */
    private function canUserModify($userId) {
        if (!$userId) {
            return false;
        }
        
        // Check if user is the owner
        if ($this->data['user_id'] === $userId) {
            return true;
        }
        
        // Check if user is admin
        $user = new User();
        if ($user->loadById($userId) && $user->isAdmin()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if user has reached listing limit
     * 
     * @param string $userId User ID to check
     * @return bool Whether limit reached
     */
    private function hasReachedListingLimit($userId) {
        $userListings = self::getByUser($userId);
        return count($userListings) >= MAX_LISTINGS_PER_USER;
    }
    
    /**
     * Check if listing has active orders
     * 
     * @return bool Whether listing has active orders
     */
    private function hasActiveOrders() {
        // This would check the orders table
        // For now, return false as Order class doesn't exist yet
        return false;
    }
    
    /**
     * Clear listing data
     */
    private function clearListingData() {
        $this->id = null;
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
            error_log("[Listing Error] $message");
        }
    }
    
    /**
     * Log info message
     * 
     * @param string $message Info message
     */
    private function logInfo($message) {
        if (LOG_ERRORS) {
            error_log("[Listing Info] $message");
        }
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getType() { return $this->type; }
    public function getData() { return $this->data; }
    public function getUserId() { return $this->data['user_id'] ?? ''; }
    public function getTitle() { return $this->data['title'] ?? ''; }
    public function getDescription() { return $this->data['description'] ?? ''; }
    public function getPrice() { return $this->data['price'] ?? 0; }
    public function getCurrency() { return $this->data['currency'] ?? DEFAULT_CURRENCY; }
    public function getCategoryId() { return $this->data['category_id'] ?? ''; }
    public function getTags() { return $this->data['tags'] ?? []; }
    public function getLocation() { return $this->data['location'] ?? []; }
    public function getImages() { return $this->data['images'] ?? []; }
    public function getStatus() { return $this->data['status'] ?? 'unknown'; }
    public function isFeatured() { return $this->data['featured'] ?? false; }
    public function getViews() { return $this->data['views'] ?? 0; }
    public function getFavorites() { return $this->data['favorites'] ?? 0; }
    public function getRating() { return $this->data['rating'] ?? 0; }
    public function getTotalRatings() { return $this->data['total_ratings'] ?? 0; }
    public function getCreatedAt() { return $this->data['created_at'] ?? ''; }
    public function getUpdatedAt() { return $this->data['updated_at'] ?? ''; }
}