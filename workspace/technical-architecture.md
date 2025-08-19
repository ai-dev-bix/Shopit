# Technical Architecture Document
## Location-Based Marketplace Platform - Prototype

### 1. System Overview

#### 1.1 Architecture Pattern
- **Type**: Monolithic web application
- **Pattern**: MVC-like structure with PHP backend and HTML/CSS/JS frontend
- **Data Storage**: File-based JSON storage system
- **Deployment**: Single-server deployment with Plesk compatibility

#### 1.2 Technology Stack
- **Frontend**: HTML5, CSS3 (Bulma framework), Vanilla JavaScript
- **Backend**: PHP 7.4+
- **Data Storage**: JSON files with file system organization
- **Web Server**: Apache with .htaccess for URL rewriting
- **Image Processing**: PHP GD library for image manipulation
- **Maps Integration**: Google Maps JavaScript API

### 2. System Architecture

#### 2.1 High-Level Architecture
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Web Browser  │    │   Mobile Device │    │   Admin Panel   │
│   (Desktop)    │    │   (Mobile Web)  │    │   (Web)         │
└─────────┬───────┘    └─────────┬───────┘    └─────────┬───────┘
          │                      │                      │
          └──────────────────────┼──────────────────────┘
                                 │
                    ┌─────────────▼─────────────┐
                    │      Web Server           │
                    │    (Apache + PHP)        │
                    └─────────────┬─────────────┘
                                  │
                    ┌─────────────▼─────────────┐
                    │    Application Layer      │
                    │   (PHP Classes/Functions)│
                    └─────────────┬─────────────┘
                                  │
                    ┌─────────────▼─────────────┐
                    │     Data Layer           │
                    │   (JSON Files + Images)  │
                    └───────────────────────────┘
```

#### 2.2 Component Architecture
```
┌─────────────────────────────────────────────────────────────┐
│                    Presentation Layer                       │
├─────────────────────────────────────────────────────────────┤
│  HTML Templates  │  CSS Styles  │  JavaScript Functions   │
│  (Bulma UI)     │  (Bulma)     │  (Vanilla JS)           │
└─────────────────────────────────────────────────────────────┘
                              │
┌─────────────────────────────────────────────────────────────┐
│                    Application Layer                        │
├─────────────────────────────────────────────────────────────┤
│  Router/Controller │  Business Logic │  Data Access Layer │
│  (index.php)      │  (Classes)      │  (JSON handlers)   │
└─────────────────────────────────────────────────────────────┘
                              │
┌─────────────────────────────────────────────────────────────┐
│                      Data Layer                            │
├─────────────────────────────────────────────────────────────┤
│  User Data  │  Listings  │  Orders  │  Images  │  System  │
│  (JSON)     │  (JSON)    │  (JSON)  │  (Files) │  (JSON)  │
└─────────────────────────────────────────────────────────────┘
```

### 3. Data Architecture

#### 3.1 Data Storage Strategy
- **Primary Storage**: JSON files for structured data
- **File Storage**: File system for images and uploads
- **Session Storage**: PHP sessions for user state
- **Backup Strategy**: File-based backups with versioning

#### 3.2 Data Organization
```
/data/
├── users/
│   ├── users.json          # User profiles and metadata
│   ├── sessions.json       # Active user sessions
│   └── ratings.json        # User ratings and reviews
├── listings/
│   ├── products.json       # Product listings
│   ├── services.json       # Service listings
│   └── categories.json     # Product/service categories
├── orders/
│   ├── orders.json         # Order transactions
│   ├── requests.json       # Approval-required requests
│   └── history.json        # Order history
├── images/
│   ├── products/           # Product images
│   ├── services/           # Service images
│   └── users/              # User profile images
└── system/
    ├── tags.json           # Available tags
    ├── locations.json      # Location data
    ├── settings.json       # System configuration
    └── logs.json           # System activity logs
```

#### 3.3 Data Models

**User Model**:
```json
{
  "id": "unique_user_id",
  "username": "unique_username",
  "type": "buyer|seller|both",
  "email": "user@example.com",
  "phone": "+1234567890",
  "location": {
    "lat": 40.7128,
    "lng": -74.0060,
    "address": "New York, NY"
  },
  "rating": 4.5,
  "total_ratings": 25,
  "created_at": "2024-01-01T00:00:00Z",
  "last_active": "2024-01-15T12:00:00Z",
  "status": "active|suspended|banned"
}
```

**Listing Model**:
```json
{
  "id": "unique_listing_id",
  "user_id": "seller_user_id",
  "type": "product|service",
  "title": "Product/Service Title",
  "description": "Detailed description...",
  "category": "electronics|home|automotive|etc",
  "price": {
    "amount": 99.99,
    "currency": "USD",
    "unit": "unit|hour|day|project"
  },
  "location": {
    "lat": 40.7128,
    "lng": -74.0060,
    "address": "New York, NY"
  },
  "tags": ["tag1", "tag2", "tag3"],
  "images": ["image1.jpg", "image2.jpg"],
  "availability": {
    "in_stock": 10,
    "show_stock": true
  },
  "approval_required": false,
  "status": "active|inactive|sold",
  "created_at": "2024-01-01T00:00:00Z",
  "updated_at": "2024-01-15T12:00:00Z"
}
```

**Order Model**:
```json
{
  "id": "unique_order_id",
  "buyer_id": "buyer_user_id",
  "seller_id": "seller_user_id",
  "listing_id": "listing_id",
  "quantity": 1,
  "total_amount": 99.99,
  "status": "pending|approved|completed|cancelled",
  "approval_required": false,
  "created_at": "2024-01-01T00:00:00Z",
  "completed_at": "2024-01-15T12:00:00Z",
  "notes": "Special instructions..."
}
```

### 4. Application Architecture

#### 4.1 File Structure
```
/
├── public/                    # Web root directory
│   ├── index.php            # Main entry point and router
│   ├── assets/              # Static assets
│   │   ├── css/            # Stylesheets
│   │   ├── js/             # JavaScript files
│   │   ├── images/         # Static images
│   │   └── fonts/          # Web fonts
│   ├── uploads/            # User uploads
│   │   ├── products/       # Product images
│   │   ├── services/       # Service images
│   │   └── users/          # User images
│   └── .htaccess           # URL rewriting rules
├── src/                     # Source code
│   ├── classes/            # PHP classes
│   │   ├── User.php        # User management
│   │   ├── Listing.php     # Listing management
│   │   ├── Order.php       # Order management
│   │   ├── ImageHandler.php # Image processing
│   │   └── Database.php    # JSON data operations
│   ├── functions/          # Helper functions
│   │   ├── auth.php        # Authentication
│   │   ├── validation.php  # Input validation
│   │   ├── location.php    # Location utilities
│   │   └── utils.php       # General utilities
│   ├── config/             # Configuration
│   │   ├── config.php      # Main configuration
│   │   ├── constants.php   # System constants
│   │   └── database.php    # Data structure config
│   └── templates/          # HTML templates
│       ├── header.php      # Page header
│       ├── footer.php      # Page footer
│       ├── navigation.php  # Navigation menu
│       └── components/     # Reusable components
├── data/                   # Data storage
└── workspace/              # Project documentation
```

#### 4.2 Core Classes

**User Class**:
```php
class User {
    private $id;
    private $username;
    private $type;
    private $data;
    
    public function __construct($username = null);
    public function create($userData);
    public function authenticate($username);
    public function update($data);
    public function delete();
    public function getProfile();
    public function getListings();
    public function getOrders();
}
```

**Listing Class**:
```php
class Listing {
    private $id;
    private $data;
    
    public function __construct($id = null);
    public function create($listingData);
    public function update($data);
    public function delete();
    public function getDetails();
    public function search($filters);
    public function getNearby($lat, $lng, $radius);
    public function uploadImages($images);
}
```

**Order Class**:
```php
class Order {
    private $id;
    private $data;
    
    public function __construct($id = null);
    public function create($orderData);
    public function update($data);
    public function approve();
    public function reject();
    public function complete();
    public function cancel();
    public function getDetails();
}
```

### 5. Security Architecture

#### 5.1 Authentication & Authorization
- **Session Management**: PHP sessions with secure cookies
- **User Validation**: Username uniqueness and format validation
- **Access Control**: Role-based permissions (admin, seller, buyer)
- **Session Security**: Session timeout and regeneration

#### 5.2 Input Validation & Sanitization
- **Data Validation**: Server-side validation for all inputs
- **XSS Prevention**: HTML entity encoding and output filtering
- **File Upload Security**: File type validation and size limits
- **SQL Injection**: Not applicable (JSON files)

#### 5.3 Data Protection
- **File Permissions**: Secure file system permissions
- **Data Encryption**: Not required for prototype
- **Backup Security**: Secure backup storage

### 6. Performance Architecture

#### 6.1 Caching Strategy
- **File Caching**: JSON data caching in memory
- **Image Caching**: Browser caching for static images
- **Session Caching**: PHP session optimization

#### 6.2 Optimization Techniques
- **Image Compression**: Automatic image resizing and compression
- **Lazy Loading**: Load images and data as needed
- **Minification**: CSS and JavaScript minification
- **CDN Ready**: Structure ready for CDN integration

#### 6.3 Scalability Considerations
- **Horizontal Scaling**: File-based storage allows easy replication
- **Load Balancing**: Stateless design supports multiple servers
- **Database Migration**: JSON structure can be migrated to SQL

### 7. Integration Architecture

#### 7.1 External Services
- **Google Maps API**: Location services and mapping
- **Camera API**: Device camera integration for mobile
- **GPS API**: Location detection services

#### 7.2 API Design
- **REST-like Endpoints**: Simple HTTP-based API
- **JSON Responses**: Consistent JSON response format
- **Error Handling**: Standardized error codes and messages

### 8. Deployment Architecture

#### 8.1 Server Requirements
- **Web Server**: Apache 2.4+ with mod_rewrite
- **PHP Version**: 7.4 or higher
- **Extensions**: GD, JSON, Session
- **Storage**: 100MB minimum, 1GB recommended

#### 8.2 Deployment Process
1. **Preparation**: Create deployment package
2. **Upload**: Upload zip file to server
3. **Extraction**: Extract to target directory
4. **Configuration**: Set file permissions
5. **Testing**: Verify functionality

#### 8.3 Configuration Management
- **Environment Variables**: Configuration file management
- **File Permissions**: Secure file system setup
- **Error Reporting**: Development vs production settings

### 9. Monitoring & Logging

#### 9.1 System Monitoring
- **Performance Metrics**: Response time and throughput
- **Error Tracking**: Error logging and monitoring
- **User Activity**: User behavior analytics

#### 9.2 Logging Strategy
- **Access Logs**: User access and activity
- **Error Logs**: System errors and exceptions
- **Audit Logs**: User actions and changes
- **Performance Logs**: System performance metrics

### 10. Testing Strategy

#### 10.1 Testing Levels
- **Unit Testing**: Individual class and function testing
- **Integration Testing**: Component interaction testing
- **User Acceptance Testing**: End-to-end user flow testing

#### 10.2 Testing Tools
- **Manual Testing**: Browser-based testing
- **Automated Testing**: PHPUnit for unit tests
- **Performance Testing**: Load testing tools

---

**Document Version**: 1.0  
**Last Updated**: Initial Creation  
**Next Review**: After prototype completion  
**Technical Lead**: AI Assistant