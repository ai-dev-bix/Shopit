# Codebase Structure Document
## Location-Based Marketplace Platform - Prototype

### Project Overview
This document outlines the complete file structure and organization of the location-based marketplace platform prototype. The structure is designed for simplicity, maintainability, and easy deployment.

### Root Directory Structure
```
/
├── workspace/              # Project documentation and management
├── ux-assets/             # Available UI frameworks and assets
├── data/                  # JSON data storage (to be created)
├── src/                   # PHP source code (to be created)
├── public/                # Web-accessible files (to be created)
├── docs/                  # User and technical documentation (to be created)
├── .gitignore            # Git ignore file
├── README.md             # Project overview
└── initial prompt        # Original project requirements
```

### Detailed Directory Structure

#### 1. Workspace Directory (`/workspace/`)
**Purpose**: Project management, documentation, and development tools
```
workspace/
├── README.md                 # Main workspace overview
├── prd.md                    # Product Requirements Document
├── technical-architecture.md # Technical architecture specification
├── development-roadmap.md    # Development phases and timeline
├── functional-mapping.md     # Feature-to-code mapping
├── platform-flows.md         # User journeys and system flows
├── notes.md                  # Development notes and reminders
├── todo.md                   # Development task list
├── bugs.md                   # Bug tracking and management
├── optimization.md           # Performance optimization guide
├── enhancements.md           # Future feature roadmap
└── codebase-structure.md     # This document
```

#### 2. UX Assets Directory (`/ux-assets/`)
**Purpose**: Available UI frameworks and design assets
```
ux-assets/
├── uikit-3.23.12/           # UIkit CSS framework
├── vanilla-framework-4.30.0/ # Ubuntu Vanilla framework
├── bulma-1.0.4/             # Bulma CSS framework (selected)
├── bulma-css-vars-master/   # Bulma CSS variables
├── beercss-main/            # BeerCSS framework
├── flyonui-main/            # FlyonUI framework
├── materialize-1-dev/       # Materialize CSS
├── mdl/                     # Material Design Lite
├── milligram-main/          # Milligram CSS
├── pico-main/               # Pico CSS
├── pure-main/               # Pure CSS
└── sprucecss-main/          # Spruce CSS
```

#### 3. Data Directory (`/data/`)
**Purpose**: JSON data storage and file organization
```
data/
├── users/                   # User data storage
│   ├── users.json          # User profiles and metadata
│   ├── sessions.json       # Active user sessions
│   └── ratings.json        # User ratings and reviews
├── listings/                # Product and service listings
│   ├── products.json       # Product listings data
│   ├── services.json       # Service listings data
│   └── categories.json     # Product/service categories
├── orders/                  # Order and transaction data
│   ├── orders.json         # Order transactions
│   ├── requests.json       # Approval-required requests
│   └── history.json        # Order history
├── images/                  # Image file storage
│   ├── products/           # Product images
│   ├── services/           # Service images
│   └── users/              # User profile images
└── system/                  # System configuration and data
    ├── tags.json           # Available tags
    ├── locations.json      # Location data
    ├── settings.json       # System configuration
    └── logs.json           # System activity logs
```

#### 4. Source Code Directory (`/src/`)
**Purpose**: PHP source code and application logic
```
src/
├── classes/                 # PHP classes and objects
│   ├── User.php            # User management class
│   ├── Listing.php         # Listing management class
│   ├── Order.php           # Order management class
│   ├── ImageHandler.php    # Image processing class
│   ├── Admin.php           # Admin functionality class
│   └── Database.php        # JSON data operations class
├── functions/               # Helper functions and utilities
│   ├── auth.php            # Authentication functions
│   ├── validation.php      # Input validation functions
│   ├── location.php        # Location utility functions
│   ├── utils.php           # General utility functions
│   ├── search.php          # Search functionality
│   ├── filters.php         # Filtering functions
│   ├── upload.php          # File upload functions
│   ├── camera.php          # Camera capture functions
│   ├── map.php             # Map integration functions
│   ├── orders.php          # Order processing functions
│   ├── approvals.php       # Approval system functions
│   ├── admin.php           # Admin utility functions
│   ├── data.php            # Data handling functions
│   ├── security.php        # Security functions
│   ├── session.php         # Session management
│   ├── config.php          # Configuration functions
│   ├── formatting.php      # Data formatting functions
│   └── performance.php     # Performance optimization
├── config/                  # Configuration files
│   ├── config.php          # Main configuration file
│   ├── constants.php       # System constants
│   ├── database.php        # Data structure configuration
│   ├── security.php        # Security configuration
│   └── upload.php          # Upload configuration
└── templates/               # HTML templates and components
    ├── header.php          # Page header template
    ├── footer.php          # Page footer template
    ├── navigation.php      # Navigation menu template
    ├── components/         # Reusable UI components
    │   ├── image-upload.php # Image upload component
    │   ├── camera-capture.php # Camera capture component
    │   ├── location-picker.php # Location selection component
    │   ├── search-filters.php # Search filter component
    │   ├── listing-card.php # Listing display component
    │   ├── order-summary.php # Order summary component
    │   └── pagination.php  # Pagination component
    └── admin/              # Admin-specific templates
        ├── dashboard.php   # Admin dashboard
        ├── user-management.php # User management interface
        ├── content-moderation.php # Content moderation
        └── system-stats.php # System statistics
```

#### 5. Public Directory (`/public/`)
**Purpose**: Web-accessible files and assets
```
public/
├── index.php               # Main entry point and router
├── .htaccess              # URL rewriting and security rules
├── assets/                 # Static assets
│   ├── css/               # Stylesheets
│   │   ├── bulma.min.css  # Bulma CSS framework
│   │   ├── custom.css     # Custom styles
│   │   ├── auth.css       # Authentication styles
│   │   ├── listing.css    # Listing styles
│   │   ├── search.css     # Search interface styles
│   │   ├── profile.css    # User profile styles
│   │   ├── orders.css     # Order management styles
│   │   ├── admin.css      # Admin interface styles
│   │   ├── mobile.css     # Mobile-specific styles
│   │   └── components.css # Component-specific styles
│   ├── js/                # JavaScript files
│   │   ├── main.js        # Main application logic
│   │   ├── auth.js        # Authentication functions
│   │   ├── registration.js # User registration
│   │   ├── listing-creation.js # Listing creation
│   │   ├── image-upload.js # Image upload handling
│   │   ├── camera.js      # Camera capture
│   │   ├── location.js    # Location services
│   │   ├── search.js      # Search functionality
│   │   ├── filters.js     # Filter application
│   │   ├── map.js         # Map integration
│   │   ├── purchase.js    # Purchase flow
│   │   ├── orders.js      # Order management
│   │   ├── admin.js       # Admin functionality
│   │   ├── utils.js       # Utility functions
│   │   └── performance.js # Performance monitoring
│   ├── images/             # Static images
│   │   ├── logo.png       # Platform logo
│   │   ├── icons/         # Icon set
│   │   ├── backgrounds/   # Background images
│   │   └── placeholders/  # Placeholder images
│   └── fonts/              # Web fonts
│       ├── roboto/         # Roboto font family
│       └── icons/          # Icon fonts
├── uploads/                # User uploads (publicly accessible)
│   ├── products/           # Product images
│   ├── services/           # Service images
│   └── users/              # User profile images
├── pages/                  # Individual page files
│   ├── register.php        # User registration page
│   ├── login.php           # User login page
│   ├── dashboard.php       # User dashboard
│   ├── profile.php         # User profile page
│   ├── create-listing.php  # Listing creation page
│   ├── edit-listing.php    # Listing editing page
│   ├── manage-listings.php # Listing management
│   ├── search.php          # Search and browse page
│   ├── listing-detail.php  # Individual listing view
│   ├── purchase.php        # Purchase flow page
│   ├── orders.php          # Order management page
│   ├── seller-approvals.php # Seller approval interface
│   ├── map-view.php        # Map view of listings
│   └── admin/              # Admin pages
│       ├── index.php       # Admin dashboard
│       ├── users.php       # User management
│       ├── listings.php    # Listing moderation
│       ├── orders.php      # Order monitoring
│       └── system.php      # System settings
└── api/                    # API endpoints
    ├── auth.php            # Authentication API
    ├── users.php           # User management API
    ├── listings.php        # Listing management API
    ├── search.php          # Search API
    ├── orders.php          # Order management API
    ├── uploads.php         # File upload API
    ├── location.php        # Location services API
    └── admin.php           # Admin API
```

#### 6. Documentation Directory (`/docs/`)
**Purpose**: User and technical documentation
```
docs/
├── user-guide/             # User documentation
│   ├── getting-started.md  # Getting started guide
│   ├── buyer-guide.md      # Guide for buyers
│   ├── seller-guide.md     # Guide for sellers
│   ├── admin-guide.md      # Guide for administrators
│   ├── faq.md              # Frequently asked questions
│   └── troubleshooting.md  # Troubleshooting guide
├── technical/              # Technical documentation
│   ├── api-reference.md    # API documentation
│   ├── deployment.md       # Deployment guide
│   ├── configuration.md    # Configuration guide
│   ├── security.md         # Security documentation
│   └── maintenance.md      # Maintenance procedures
└── assets/                 # Documentation assets
    ├── images/             # Documentation images
    ├── diagrams/           # System diagrams
    └── examples/           # Code examples
```

### File Naming Conventions

#### PHP Files
- **Classes**: PascalCase (e.g., `User.php`, `ListingManager.php`)
- **Functions**: snake_case (e.g., `auth.php`, `user_management.php`)
- **Configuration**: lowercase (e.g., `config.php`, `constants.php`)

#### JavaScript Files
- **Main files**: kebab-case (e.g., `main.js`, `image-upload.js`)
- **Component files**: descriptive names (e.g., `search.js`, `filters.js`)

#### CSS Files
- **Framework**: framework name (e.g., `bulma.min.css`)
- **Custom**: descriptive names (e.g., `custom.css`, `auth.css`)

#### JSON Files
- **Data files**: descriptive names (e.g., `users.json`, `products.json`)
- **Configuration**: descriptive names (e.g., `settings.json`, `categories.json`)

### Security Considerations

#### File Access Control
- **Public files**: Only files in `/public/` are web-accessible
- **Source code**: `/src/` directory is not web-accessible
- **Data files**: `/data/` directory is not web-accessible
- **Documentation**: `/docs/` directory is not web-accessible

#### .htaccess Configuration
```apache
# Prevent access to sensitive directories
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

# Protect source code
<DirectoryMatch "^/src/">
    Order allow,deny
    Deny from all
</DirectoryMatch>

# Protect data files
<DirectoryMatch "^/data/">
    Order allow,deny
    Deny from all
</DirectoryMatch>

# URL rewriting for clean URLs
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?route=$1 [QSA,L]
```

### Deployment Structure

#### Production Deployment
```
production/
├── public_html/            # Web root directory
│   ├── index.php          # Main entry point
│   ├── assets/            # Static assets
│   ├── uploads/           # User uploads
│   └── .htaccess          # Server configuration
├── src/                    # Source code (not web-accessible)
├── data/                   # Data storage (not web-accessible)
└── logs/                   # Application logs
```

#### Development Environment
```
development/
├── public/                 # Web root for development
├── src/                    # Source code
├── data/                   # Data storage
├── workspace/              # Development documentation
└── logs/                   # Development logs
```

### Data Flow Architecture

#### Request Flow
1. **User Request** → `public/index.php`
2. **Routing** → Parse URL and determine action
3. **Authentication** → Check user session and permissions
4. **Controller** → Load appropriate PHP class/function
5. **Data Access** → Read/write JSON files
6. **Response** → Return HTML/JSON response

#### File Access Patterns
- **Read Operations**: JSON files loaded into memory, cached when possible
- **Write Operations**: Data validated, then written to JSON files
- **Image Operations**: Files uploaded, processed, and stored in organized directories
- **Session Data**: Stored in PHP sessions and JSON files

### Maintenance and Updates

#### File Organization Benefits
- **Modularity**: Easy to locate and modify specific functionality
- **Scalability**: Structure supports future growth and changes
- **Security**: Clear separation of public and private resources
- **Deployment**: Simple deployment process with clear file organization
- **Backup**: Easy to backup specific data types and configurations

#### Update Procedures
1. **Code Updates**: Modify files in `/src/` directory
2. **Configuration Changes**: Update files in `/src/config/`
3. **Data Updates**: Modify JSON files in `/data/`
4. **Asset Updates**: Replace files in `/public/assets/`
5. **Documentation Updates**: Modify files in `/docs/` and `/workspace/`

---

**Document Version**: 1.0  
**Last Updated**: Initial Creation  
**Next Review**: After prototype completion  
**Technical Lead**: AI Assistant