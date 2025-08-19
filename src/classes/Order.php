<?php
/**
 * Order Class
 * Handles order creation and management for the marketplace platform
 */

// Prevent direct access
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

class Order {
    private $db;
    private $id;
    private $data;
    
    public function __construct($id = null) {
        $this->db = new Database();
        
        if ($id) {
            $this->loadById($id);
        }
    }
    
    /**
     * Create a new order
     * 
     * @param array $orderData Order data
     * @return string|false Generated order ID or false on error
     */
    public function create($orderData) {
        // Validate required fields
        if (!$this->validateOrderData($orderData)) {
            return false;
        }
        
        // Check if buyer exists and is active
        $buyer = new User();
        if (!$buyer->loadById($orderData['buyer_id'])) {
            $this->logError("Buyer not found: " . $orderData['buyer_id']);
            return false;
        }
        
        if ($buyer->getStatus() !== 'active') {
            $this->logError("Buyer account not active: " . $orderData['buyer_id']);
            return false;
        }
        
        // Check if listing exists and is active
        $listing = new Listing();
        if (!$listing->loadById($orderData['listing_id'])) {
            $this->logError("Listing not found: " . $orderData['listing_id']);
            return false;
        }
        
        if ($listing->getStatus() !== 'active') {
            $this->logError("Listing not active: " . $orderData['listing_id']);
            return false;
        }
        
        // Check if buyer is not the seller
        if ($listing->getUserId() === $orderData['buyer_id']) {
            $this->logError("Buyer cannot purchase their own listing: " . $orderData['listing_id']);
            return false;
        }
        
        // Get seller information
        $seller = new User();
        $seller->loadById($listing->getUserId());
        
        // Prepare order record
        $orderRecord = [
            'buyer_id' => $orderData['buyer_id'],
            'seller_id' => $listing->getUserId(),
            'listing_id' => $orderData['listing_id'],
            'listing_type' => $listing->getType(),
            'listing_title' => $listing->getTitle(),
            'quantity' => $orderData['quantity'] ?? 1,
            'unit_price' => $listing->getPrice(),
            'total_price' => ($orderData['quantity'] ?? 1) * $listing->getPrice(),
            'currency' => $listing->getCurrency(),
            'status' => ORDER_STATUS_PENDING,
            'payment_status' => 'pending',
            'payment_method' => $orderData['payment_method'] ?? 'cash',
            'delivery_method' => $orderData['delivery_method'] ?? 'pickup',
            'delivery_address' => $orderData['delivery_address'] ?? null,
            'delivery_instructions' => $orderData['delivery_instructions'] ?? '',
            'special_requests' => $orderData['special_requests'] ?? '',
            'estimated_delivery' => $orderData['estimated_delivery'] ?? null,
            'notes' => $orderData['notes'] ?? '',
            'metadata' => [
                'listing_category' => $listing->getCategoryId(),
                'listing_tags' => $listing->getTags(),
                'listing_location' => $listing->getLocation(),
                'buyer_location' => $buyer->getLocation(),
                'seller_location' => $seller->getLocation()
            ]
        ];
        
        // Add service-specific fields
        if ($listing->getType() === LISTING_TYPE_SERVICE) {
            $orderRecord['service_details'] = [
                'service_date' => $orderData['service_date'] ?? null,
                'service_time' => $orderData['service_time'] ?? null,
                'service_duration' => $orderData['service_duration'] ?? null,
                'service_location' => $orderData['service_location'] ?? null
            ];
        }
        
        // Insert order record
        $orderId = $this->db->insert(ORDERS_FILE, $orderRecord, 'orders');
        
        if ($orderId) {
            $this->id = $orderId;
            $this->data = $orderRecord;
            
            $this->logInfo("Order created successfully: $orderId");
            return $orderId;
        }
        
        return false;
    }
    
    /**
     * Load order by ID
     * 
     * @param string $id Order ID to load
     * @return bool Success status
     */
    public function loadById($id) {
        $order = $this->db->findById(ORDERS_FILE, $id, 'orders');
        
        if ($order) {
            $this->id = $order['id'];
            $this->data = $order;
            return true;
        }
        
        return false;
    }
    
    /**
     * Update order
     * 
     * @param array $updateData Data to update
     * @return bool Success status
     */
    public function update($updateData) {
        if (!$this->id) {
            $this->logError("Cannot update order: Order not loaded");
            return false;
        }
        
        // Validate update data
        if (!$this->validateUpdateData($updateData)) {
            return false;
        }
        
        // Check if user can update this order
        if (!$this->canUserModify($updateData['user_id'] ?? null)) {
            $this->logError("User cannot modify order: " . $this->id);
            return false;
        }
        
        // Update order record
        $success = $this->db->update(ORDERS_FILE, $this->id, $updateData, 'orders');
        
        if ($success) {
            // Reload order data
            $this->loadById($this->id);
            $this->logInfo("Order updated successfully: " . $this->id);
        }
        
        return $success;
    }
    
    /**
     * Update order status
     * 
     * @param string $status New status
     * @param string $userId User ID requesting the update
     * @param string $notes Optional notes about the status change
     * @return bool Success status
     */
    public function updateStatus($status, $userId, $notes = '') {
        if (!$this->id) {
            $this->logError("Cannot update status: Order not loaded");
            return false;
        }
        
        // Validate status
        $validStatuses = [
            ORDER_STATUS_PENDING,
            ORDER_STATUS_APPROVED,
            ORDER_STATUS_IN_PROGRESS,
            ORDER_STATUS_COMPLETED,
            ORDER_STATUS_CANCELLED
        ];
        
        if (!in_array($status, $validStatuses)) {
            $this->logError("Invalid status: $status");
            return false;
        }
        
        // Check if user can update status
        if (!$this->canUserModify($userId)) {
            $this->logError("User cannot update order status: " . $this->id);
            return false;
        }
        
        // Check status transition rules
        if (!$this->isValidStatusTransition($status)) {
            $this->logError("Invalid status transition from {$this->data['status']} to $status");
            return false;
        }
        
        // Update status
        $updateData = [
            'status' => $status,
            'status_updated_at' => date(ISO_DATETIME_FORMAT),
            'status_updated_by' => $userId
        ];
        
        // Add status notes if provided
        if (!empty($notes)) {
            $statusHistory = $this->data['status_history'] ?? [];
            $statusHistory[] = [
                'status' => $status,
                'timestamp' => date(ISO_DATETIME_FORMAT),
                'user_id' => $userId,
                'notes' => $notes
            ];
            $updateData['status_history'] = $statusHistory;
        }
        
        return $this->update($updateData);
    }
    
    /**
     * Cancel order
     * 
     * @param string $userId User ID requesting cancellation
     * @param string $reason Reason for cancellation
     * @return bool Success status
     */
    public function cancel($userId, $reason = '') {
        if (!$this->id) {
            $this->logError("Cannot cancel order: Order not loaded");
            return false;
        }
        
        // Check if order can be cancelled
        if (!in_array($this->data['status'], [ORDER_STATUS_PENDING, ORDER_STATUS_APPROVED])) {
            $this->logError("Order cannot be cancelled in current status: " . $this->data['status']);
            return false;
        }
        
        // Check if user can cancel this order
        if (!$this->canUserModify($userId)) {
            $this->logError("User cannot cancel order: " . $this->id);
            return false;
        }
        
        // Cancel order
        $updateData = [
            'status' => ORDER_STATUS_CANCELLED,
            'cancelled_at' => date(ISO_DATETIME_FORMAT),
            'cancelled_by' => $userId,
            'cancellation_reason' => $reason
        ];
        
        return $this->update($updateData);
    }
    
    /**
     * Complete order
     * 
     * @param string $userId User ID requesting completion
     * @param string $notes Optional completion notes
     * @return bool Success status
     */
    public function complete($userId, $notes = '') {
        if (!$this->id) {
            $this->logError("Cannot complete order: Order not loaded");
            return false;
        }
        
        // Check if order can be completed
        if ($this->data['status'] !== ORDER_STATUS_IN_PROGRESS) {
            $this->logError("Order cannot be completed in current status: " . $this->data['status']);
            return false;
        }
        
        // Check if user can complete this order
        if (!$this->canUserModify($userId)) {
            $this->logError("User cannot complete order: " . $this->id);
            return false;
        }
        
        // Complete order
        $updateData = [
            'status' => ORDER_STATUS_COMPLETED,
            'completed_at' => date(ISO_DATETIME_FORMAT),
            'completed_by' => $userId,
            'completion_notes' => $notes
        ];
        
        return $this->update($updateData);
    }
    
    /**
     * Get orders by user
     * 
     * @param string $userId User ID
     * @param string $role Role in order (buyer/seller)
     * @param string $status Order status filter
     * @return array User's orders
     */
    public static function getByUser($userId, $role = 'buyer', $status = null) {
        $db = new Database();
        $criteria = [];
        
        if ($role === 'buyer') {
            $criteria['buyer_id'] = $userId;
        } else {
            $criteria['seller_id'] = $userId;
        }
        
        if ($status) {
            $criteria['status'] = $status;
        }
        
        return $db->search(ORDERS_FILE, $criteria, 'orders');
    }
    
    /**
     * Get orders by listing
     * 
     * @param string $listingId Listing ID
     * @param string $status Order status filter
     * @return array Listing orders
     */
    public static function getByListing($listingId, $status = null) {
        $db = new Database();
        $criteria = ['listing_id' => $listingId];
        
        if ($status) {
            $criteria['status'] = $status;
        }
        
        return $db->search(ORDERS_FILE, $criteria, 'orders');
    }
    
    /**
     * Get orders by status
     * 
     * @param string $status Order status
     * @return array Orders with specified status
     */
    public static function getByStatus($status) {
        $db = new Database();
        return $db->search(ORDERS_FILE, ['status' => $status], 'orders');
    }
    
    /**
     * Get pending orders
     * 
     * @return array Pending orders
     */
    public static function getPending() {
        return self::getByStatus(ORDER_STATUS_PENDING);
    }
    
    /**
     * Get active orders (pending, approved, in progress)
     * 
     * @return array Active orders
     */
    public static function getActive() {
        $db = new Database();
        $allOrders = $db->getAll(ORDERS_FILE, 'orders');
        
        $activeOrders = [];
        foreach ($allOrders as $order) {
            if (in_array($order['status'], [
                ORDER_STATUS_PENDING,
                ORDER_STATUS_APPROVED,
                ORDER_STATUS_IN_PROGRESS
            ])) {
                $activeOrders[] = $order;
            }
        }
        
        return $activeOrders;
    }
    
    /**
     * Get order statistics
     * 
     * @return array Order statistics
     */
    public function getStats() {
        if (!$this->id) {
            return [];
        }
        
        return [
            'total_price' => $this->data['total_price'] ?? 0,
            'quantity' => $this->data['quantity'] ?? 1,
            'status' => $this->data['status'] ?? 'unknown',
            'payment_status' => $this->data['payment_status'] ?? 'unknown',
            'created_at' => $this->data['created_at'] ?? '',
            'updated_at' => $this->data['updated_at'] ?? ''
        ];
    }
    
    /**
     * Calculate order total
     * 
     * @param array $additionalFees Additional fees to add
     * @return float Total order amount
     */
    public function calculateTotal($additionalFees = []) {
        if (!$this->id) {
            return 0;
        }
        
        $total = $this->data['total_price'] ?? 0;
        
        foreach ($additionalFees as $fee) {
            if (isset($fee['amount']) && is_numeric($fee['amount'])) {
                $total += $fee['amount'];
            }
        }
        
        return $total;
    }
    
    /**
     * Check if order can be modified
     * 
     * @return bool Whether order can be modified
     */
    public function canBeModified() {
        if (!$this->id) {
            return false;
        }
        
        $modifiableStatuses = [
            ORDER_STATUS_PENDING,
            ORDER_STATUS_APPROVED
        ];
        
        return in_array($this->data['status'], $modifiableStatuses);
    }
    
    /**
     * Validate order data for creation
     * 
     * @param array $orderData Order data to validate
     * @return bool Validation success
     */
    private function validateOrderData($orderData) {
        // Check required fields
        $requiredFields = ['buyer_id', 'listing_id'];
        foreach ($requiredFields as $field) {
            if (empty($orderData[$field])) {
                $this->logError("Required field missing: $field");
                return false;
            }
        }
        
        // Validate quantity
        if (isset($orderData['quantity'])) {
            if (!is_numeric($orderData['quantity']) || $orderData['quantity'] < 1) {
                $this->logError("Invalid quantity: " . $orderData['quantity']);
                return false;
            }
        }
        
        // Validate payment method
        if (isset($orderData['payment_method'])) {
            $validPaymentMethods = ['cash', 'card', 'bank_transfer', 'digital_wallet'];
            if (!in_array($orderData['payment_method'], $validPaymentMethods)) {
                $this->logError("Invalid payment method: " . $orderData['payment_method']);
                return false;
            }
        }
        
        // Validate delivery method
        if (isset($orderData['delivery_method'])) {
            $validDeliveryMethods = ['pickup', 'delivery', 'shipping'];
            if (!in_array($orderData['delivery_method'], $validDeliveryMethods)) {
                $this->logError("Invalid delivery method: " . $orderData['delivery_method']);
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
        // Validate quantity if provided
        if (isset($updateData['quantity'])) {
            if (!is_numeric($updateData['quantity']) || $updateData['quantity'] < 1) {
                $this->logError("Invalid quantity in update: " . $updateData['quantity']);
                return false;
            }
        }
        
        // Validate total price if provided
        if (isset($updateData['total_price'])) {
            if (!is_numeric($updateData['total_price']) || $updateData['total_price'] < 0) {
                $this->logError("Invalid total price in update: " . $updateData['total_price']);
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Check if user can modify order
     * 
     * @param string $userId User ID to check
     * @return bool Whether user can modify
     */
    private function canUserModify($userId) {
        if (!$userId) {
            return false;
        }
        
        // Check if user is the buyer
        if ($this->data['buyer_id'] === $userId) {
            return true;
        }
        
        // Check if user is the seller
        if ($this->data['seller_id'] === $userId) {
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
     * Check if status transition is valid
     * 
     * @param string $newStatus New status to transition to
     * @return bool Whether transition is valid
     */
    private function isValidStatusTransition($newStatus) {
        $currentStatus = $this->data['status'];
        
        // Define valid status transitions
        $validTransitions = [
            ORDER_STATUS_PENDING => [ORDER_STATUS_APPROVED, ORDER_STATUS_CANCELLED],
            ORDER_STATUS_APPROVED => [ORDER_STATUS_IN_PROGRESS, ORDER_STATUS_CANCELLED],
            ORDER_STATUS_IN_PROGRESS => [ORDER_STATUS_COMPLETED, ORDER_STATUS_CANCELLED],
            ORDER_STATUS_COMPLETED => [],
            ORDER_STATUS_CANCELLED => []
        ];
        
        if (!isset($validTransitions[$currentStatus])) {
            return false;
        }
        
        return in_array($newStatus, $validTransitions[$currentStatus]);
    }
    
    /**
     * Log error message
     * 
     * @param string $message Error message
     */
    private function logError($message) {
        if (LOG_ERRORS) {
            error_log("[Order Error] $message");
        }
    }
    
    /**
     * Log info message
     * 
     * @param string $message Info message
     */
    private function logInfo($message) {
        if (LOG_ERRORS) {
            error_log("[Order Info] $message");
        }
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getData() { return $this->data; }
    public function getBuyerId() { return $this->data['buyer_id'] ?? ''; }
    public function getSellerId() { return $this->data['seller_id'] ?? ''; }
    public function getListingId() { return $this->data['listing_id'] ?? ''; }
    public function getListingType() { return $this->data['listing_type'] ?? ''; }
    public function getListingTitle() { return $this->data['listing_title'] ?? ''; }
    public function getQuantity() { return $this->data['quantity'] ?? 1; }
    public function getUnitPrice() { return $this->data['unit_price'] ?? 0; }
    public function getTotalPrice() { return $this->data['total_price'] ?? 0; }
    public function getCurrency() { return $this->data['currency'] ?? DEFAULT_CURRENCY; }
    public function getStatus() { return $this->data['status'] ?? 'unknown'; }
    public function getPaymentStatus() { return $this->data['payment_status'] ?? 'unknown'; }
    public function getPaymentMethod() { return $this->data['payment_method'] ?? ''; }
    public function getDeliveryMethod() { return $this->data['delivery_method'] ?? ''; }
    public function getDeliveryAddress() { return $this->data['delivery_address'] ?? null; }
    public function getDeliveryInstructions() { return $this->data['delivery_instructions'] ?? ''; }
    public function getSpecialRequests() { return $this->data['special_requests'] ?? ''; }
    public function getEstimatedDelivery() { return $this->data['estimated_delivery'] ?? null; }
    public function getNotes() { return $this->data['notes'] ?? ''; }
    public function getCreatedAt() { return $this->data['created_at'] ?? ''; }
    public function getUpdatedAt() { return $this->data['updated_at'] ?? ''; }
}