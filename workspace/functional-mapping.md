# Functional Mapping Document
## Location-Based Marketplace Platform - Prototype

### Overview
This document maps each platform feature to its corresponding code components, files, and implementation details. It serves as a reference for developers to understand where specific functionality is implemented and how different components interact.

### 1. User Management System

#### 1.1 User Registration
**Feature**: Allow users to create new accounts with unique usernames

**Frontend Components**:
- **File**: `public/register.php`
- **Template**: `src/templates/register-form.php`
- **CSS**: `public/assets/css/auth.css`
- **JavaScript**: `public/assets/js/registration.js`

**Backend Components**:
- **Class**: `src/classes/User.php`
- **Functions**: `src/functions/validation.php`
- **Data Storage**: `data/users/users.json`

**Key Functions**:
```php
// User.php
public function create($userData)
public function isUsernameUnique($username)

// validation.php
function validateUsername($username)
function validateUserData($data)
```

**Data Flow**:
1. User submits registration form
2. Frontend validates input
3. Backend validates and creates user
4. User data saved to JSON file
5. User redirected to dashboard

#### 1.2 User Authentication
**Feature**: Simple username-based login system

**Frontend Components**:
- **File**: `public/login.php`
- **Template**: `src/templates/login-form.php`
- **CSS**: `public/assets/css/auth.css`
- **JavaScript**: `public/assets/js/authentication.js`

**Backend Components**:
- **Class**: `src/classes/User.php`
- **Functions**: `src/functions/auth.php`
- **Session Management**: PHP sessions

**Key Functions**:
```php
// User.php
public function authenticate($username)
public function getProfile()

// auth.php
function startUserSession($username)
function endUserSession()
function isUserLoggedIn()
```

**Data Flow**:
1. User enters username
2. System validates username exists
3. Session created for user
4. User redirected to dashboard

#### 1.3 User Profiles
**Feature**: Display and manage user information

**Frontend Components**:
- **File**: `public/profile.php`
- **Template**: `src/templates/user-profile.php`
- **CSS**: `public/assets/css/profile.css`
- **JavaScript**: `public/assets/js/profile.js`

**Backend Components**:
- **Class**: `src/classes/User.php`
- **Functions**: `src/functions/profile.php`
- **Data Storage**: `data/users/users.json`

**Key Functions**:
```php
// User.php
public function getProfile()
public function update($data)
public function getListings()
public function getOrders()
```

### 2. Listing Management System

#### 2.1 Product Listing Creation
**Feature**: Create new product listings with images and details

**Frontend Components**:
- **File**: `public/create-listing.php`
- **Template**: `src/templates/listing-form.php`
- **CSS**: `public/assets/css/listing.css`
- **JavaScript**: `public/assets/js/listing-creation.js`

**Backend Components**:
- **Class**: `src/classes/Listing.php`
- **Class**: `src/classes/ImageHandler.php`
- **Functions**: `src/functions/validation.php`
- **Data Storage**: `data/listings/products.json`

**Key Functions**:
```php
// Listing.php
public function create($listingData)
public function uploadImages($images)

// ImageHandler.php
public function processImage($image)
public function saveImage($image, $path)
public function compressImage($image)
```

**Data Flow**:
1. User fills listing form
2. Images uploaded and processed
3. Location data captured/entered
4. Listing data validated
5. Listing saved to JSON file

#### 2.2 Service Listing Creation
**Feature**: Create service listings with availability and booking

**Frontend Components**:
- **File**: `public/create-service.php`
- **Template**: `src/templates/service-form.php`
- **CSS**: `public/assets/css/service.css`
- **JavaScript**: `public/assets/js/service-creation.js`

**Backend Components**:
- **Class**: `src/classes/Listing.php`
- **Functions**: `src/functions/service.php`
- **Data Storage**: `data/listings/services.json`

**Key Functions**:
```php
// Listing.php
public function createService($serviceData)
public function setAvailability($slots)

// service.php
function validateServiceData($data)
function processAvailabilitySlots($slots)
```

#### 2.3 Listing Management
**Feature**: Edit, delete, and manage existing listings

**Frontend Components**:
- **File**: `public/manage-listings.php`
- **Template**: `src/templates/listing-management.php`
- **CSS**: `public/assets/css/management.css`
- **JavaScript**: `public/assets/js/listing-management.js`

**Backend Components**:
- **Class**: `src/classes/Listing.php`
- **Functions**: `src/functions/listing.php`
- **Data Storage**: `data/listings/`

**Key Functions**:
```php
// Listing.php
public function update($data)
public function delete()
public function toggleStatus()
public function getListingsByUser($userId)
```

### 3. Search & Discovery System

#### 3.1 Basic Search
**Feature**: Keyword-based search across listings

**Frontend Components**:
- **File**: `public/search.php`
- **Template**: `src/templates/search-results.php`
- **CSS**: `public/assets/css/search.css`
- **JavaScript**: `public/assets/js/search.js`

**Backend Components**:
- **Class**: `src/classes/Listing.php`
- **Functions**: `src/functions/search.php`
- **Data Storage**: `data/listings/`

**Key Functions**:
```php
// Listing.php
public function search($query)
public function searchByCategory($category)

// search.php
function performSearch($query, $filters)
function highlightSearchTerms($text, $query)
```

#### 3.2 Advanced Filtering
**Feature**: Filter by price, distance, tags, and categories

**Frontend Components**:
- **File**: `public/search.php`
- **Template**: `src/templates/search-filters.php`
- **CSS**: `public/assets/css/filters.css`
- **JavaScript**: `public/assets/js/filters.js`

**Backend Components**:
- **Class**: `src/classes/Listing.php`
- **Functions**: `src/functions/filters.php`
- **Data Storage**: `data/listings/`

**Key Functions**:
```php
// Listing.php
public function filterByPrice($min, $max)
public function filterByDistance($lat, $lng, $radius)
public function filterByTags($tags)

// filters.php
function applyFilters($listings, $filters)
function calculateDistance($lat1, $lng1, $lat2, $lng2)
```

#### 3.3 Location-Based Search
**Feature**: Find listings within specified radius

**Frontend Components**:
- **File**: `public/search.php`
- **Template**: `src/templates/map-view.php`
- **CSS**: `public/assets/css/map.css`
- **JavaScript**: `public/assets/js/map.js`

**Backend Components**:
- **Class**: `src/classes/Listing.php`
- **Functions**: `src/functions/location.php`
- **External API**: Google Maps JavaScript API

**Key Functions**:
```php
// Listing.php
public function getNearby($lat, $lng, $radius)

// location.php
function calculateRadius($lat, $lng, $radius)
function getAddressFromCoordinates($lat, $lng)
```

### 4. Purchase & Order System

#### 4.1 Purchase Flow
**Feature**: Complete purchase process for products/services

**Frontend Components**:
- **File**: `public/purchase.php`
- **Template**: `src/templates/purchase-form.php`
- **CSS**: `public/assets/css/purchase.css`
- **JavaScript**: `public/assets/js/purchase.js`

**Backend Components**:
- **Class**: `src/classes/Order.php`
- **Functions**: `src/functions/purchase.php`
- **Data Storage**: `data/orders/orders.json`

**Key Functions**:
```php
// Order.php
public function create($orderData)
public function calculateTotal($quantity, $price)

// purchase.php
function validatePurchaseData($data)
function processPurchase($orderData)
```

#### 4.2 Order Management
**Feature**: Track and manage order status

**Frontend Components**:
- **File**: `public/orders.php`
- **Template**: `src/templates/order-management.php`
- **CSS**: `public/assets/css/orders.css`
- **JavaScript**: `public/assets/js/orders.js`

**Backend Components**:
- **Class**: `src/classes/Order.php`
- **Functions**: `src/functions/orders.php`
- **Data Storage**: `data/orders/`

**Key Functions**:
```php
// Order.php
public function updateStatus($status)
public function getOrdersByUser($userId)
public function approve()
public function reject()
```

#### 4.3 Seller Approval System
**Feature**: Allow sellers to approve/reject purchase requests

**Frontend Components**:
- **File**: `public/seller-approvals.php`
- **Template**: `src/templates/approval-interface.php`
- **CSS**: `public/assets/css/approvals.css`
- **JavaScript**: `public/assets/js/approvals.js`

**Backend Components**:
- **Class**: `src/classes/Order.php`
- **Functions**: `src/functions/approvals.php`
- **Data Storage**: `data/orders/requests.json`

**Key Functions**:
```php
// Order.php
public function requiresApproval()
public function approve()
public function reject()

// approvals.php
function getPendingApprovals($sellerId)
function processApproval($orderId, $action)
```

### 5. Admin System

#### 5.1 Admin Dashboard
**Feature**: Overview of system activity and management tools

**Frontend Components**:
- **File**: `public/admin/`
- **Template**: `src/templates/admin/dashboard.php`
- **CSS**: `public/assets/css/admin.css`
- **JavaScript**: `public/assets/js/admin.js`

**Backend Components**:
- **Class**: `src/classes/Admin.php`
- **Functions**: `src/functions/admin.php`
- **Data Storage**: `data/system/`

**Key Functions**:
```php
// Admin.php
public function getSystemStats()
public function getRecentActivity()
public function moderateContent()

// admin.php
function isAdmin($username)
function getAdminData()
```

#### 5.2 User Management
**Feature**: Admin tools for managing users and content

**Frontend Components**:
- **File**: `public/admin/users.php`
- **Template**: `src/templates/admin/user-management.php`
- **CSS**: `public/assets/css/admin.css`
- **JavaScript**: `public/assets/js/admin-users.js`

**Backend Components**:
- **Class**: `src/classes/Admin.php`
- **Class**: `src/classes/User.php`
- **Functions**: `src/functions/admin.php`

**Key Functions**:
```php
// Admin.php
public function suspendUser($userId)
public function banUser($userId)
public function viewUserActivity($userId)

// admin.php
function getUsersList()
function performAdminAction($action, $userId)
```

### 6. Image Management System

#### 6.1 Image Upload
**Feature**: Handle image uploads from file system

**Frontend Components**:
- **File**: Various listing forms
- **Template**: `src/templates/components/image-upload.php`
- **CSS**: `public/assets/css/upload.css`
- **JavaScript**: `public/assets/js/image-upload.js`

**Backend Components**:
- **Class**: `src/classes/ImageHandler.php`
- **Functions**: `src/functions/upload.php`
- **Data Storage**: `public/uploads/`

**Key Functions**:
```php
// ImageHandler.php
public function handleUpload($file)
public function validateImage($file)
public function resizeImage($image, $width, $height)
public function saveImage($image, $path)
```

#### 6.2 Camera Capture
**Feature**: Capture images using device camera

**Frontend Components**:
- **File**: Various listing forms
- **Template**: `src/templates/components/camera-capture.php`
- **CSS**: `public/assets/css/camera.css`
- **JavaScript**: `public/assets/js/camera.js`

**Backend Components**:
- **Class**: `src/classes/ImageHandler.php`
- **Functions**: `src/functions/camera.php`

**Key Functions**:
```php
// ImageHandler.php
public function processCameraImage($imageData)
public function convertBase64ToImage($base64Data)
```

### 7. Location Services

#### 7.1 GPS Detection
**Feature**: Automatically detect user location

**Frontend Components**:
- **File**: Various forms
- **Template**: `src/templates/components/location-picker.php`
- **CSS**: `public/assets/css/location.css`
- **JavaScript**: `public/assets/js/location.js`

**Backend Components**:
- **Functions**: `src/functions/location.php`
- **External API**: Browser Geolocation API

**Key Functions**:
```php
// location.php
function validateCoordinates($lat, $lng)
function getAddressFromCoordinates($lat, $lng)
function calculateDistance($lat1, $lng1, $lat2, $lng2)
```

#### 7.2 Map Integration
**Feature**: Display listings on interactive map

**Frontend Components**:
- **File**: `public/map-view.php`
- **Template**: `src/templates/map-view.php`
- **CSS**: `public/assets/css/map.css`
- **JavaScript**: `public/assets/js/map.js`

**Backend Components**:
- **Class**: `src/classes/Listing.php`
- **Functions**: `src/functions/map.php`
- **External API**: Google Maps JavaScript API

**Key Functions**:
```php
// Listing.php
public function getListingsForMap($bounds)

// map.php
function generateMapMarkers($listings)
function calculateMapBounds($listings)
```

### 8. Data Management System

#### 8.1 JSON Data Handling
**Feature**: Manage data storage in JSON files

**Backend Components**:
- **Class**: `src/classes/Database.php`
- **Functions**: `src/functions/data.php`
- **Data Storage**: `data/` directory

**Key Functions**:
```php
// Database.php
public function read($file)
public function write($file, $data)
public function append($file, $data)
public function delete($file, $id)
public function search($file, $criteria)
```

#### 8.2 Data Validation
**Feature**: Validate data integrity and format

**Backend Components**:
- **Functions**: `src/functions/validation.php`
- **Functions**: `src/functions/sanitization.php`

**Key Functions**:
```php
// validation.php
function validateUserData($data)
function validateListingData($data)
function validateOrderData($data)

// sanitization.php
function sanitizeInput($input)
function sanitizeOutput($output)
function preventXSS($data)
```

### 9. Security System

#### 9.1 Input Validation
**Feature**: Validate and sanitize user inputs

**Backend Components**:
- **Functions**: `src/functions/validation.php`
- **Functions**: `src/functions/security.php`

**Key Functions**:
```php
// validation.php
function validateUsername($username)
function validateEmail($email)
function validatePrice($price)

// security.php
function sanitizeInput($input)
function validateFileUpload($file)
function preventCSRF()
```

#### 9.2 Session Management
**Feature**: Secure user session handling

**Backend Components**:
- **Functions**: `src/functions/auth.php`
- **Functions**: `src/functions/session.php`

**Key Functions**:
```php
// auth.php
function startUserSession($username)
function endUserSession()
function isUserLoggedIn()

// session.php
function regenerateSession()
function setSessionTimeout()
function validateSession()
```

### 10. Utility Functions

#### 10.1 Helper Functions
**Feature**: Common utility functions used across the system

**Backend Components**:
- **Functions**: `src/functions/utils.php`
- **Functions**: `src/functions/formatting.php`

**Key Functions**:
```php
// utils.php
function generateUniqueId()
function formatCurrency($amount)
function formatDate($date)

// formatting.php
function formatPhoneNumber($phone)
function formatAddress($address)
function formatTags($tags)
```

#### 10.2 Configuration Management
**Feature**: Manage system configuration and settings

**Backend Components**:
- **File**: `src/config/config.php`
- **File**: `src/config/constants.php`
- **Functions**: `src/functions/config.php`

**Key Functions**:
```php
// config.php
function getConfig($key)
function setConfig($key, $value)
function loadConfiguration()

// constants.php
define('MAX_IMAGES_PER_LISTING', 5)
define('MAX_FILE_SIZE', 5242880)
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png'])
```

---

**Document Version**: 1.0  
**Last Updated**: Initial Creation  
**Next Review**: After prototype completion  
**Technical Lead**: AI Assistant