# Platform Flows Document
## Location-Based Marketplace Platform - Prototype

### Overview
This document outlines the user journeys, system flows, and interaction patterns for the location-based marketplace platform. It serves as a guide for understanding how users interact with the system and how different components work together.

### 1. User Registration & Onboarding Flow

#### 1.1 New User Registration
**Flow Path**: Landing Page → Registration → Dashboard

**Step-by-Step Flow**:
1. **Landing Page** (`/`)
   - User arrives at homepage
   - Sees platform overview and benefits
   - Clicks "Get Started" or "Register"

2. **Registration Page** (`/register`)
   - User sees registration form
   - Enters desired username
   - System validates username uniqueness
   - User clicks "Create Account"

3. **Account Creation**
   - System creates user account
   - Generates unique user ID
   - Sets default user type to "buyer"
   - Creates user profile

4. **Dashboard Redirect**
   - User automatically logged in
   - Redirected to user dashboard
   - Sees welcome message and quick start guide

**User Experience Elements**:
- Simple, single-field registration
- Immediate feedback on username availability
- No password required (prototype simplicity)
- Instant account activation

**System Actions**:
- Validate username format and uniqueness
- Create user record in `data/users/users.json`
- Initialize user session
- Generate user profile data

**Error Handling**:
- Username already taken → Show error message
- Invalid username format → Show validation error
- System error → Show generic error message

#### 1.2 Admin Account Setup
**Flow Path**: System Initialization → Admin Creation → Admin Dashboard

**Step-by-Step Flow**:
1. **System Initialization**
   - System checks for admin accounts
   - Creates default admin accounts if none exist
   - Sets up admin privileges

2. **Admin Account Creation**
   - Creates `admin1` and `admin2` accounts
   - Assigns admin role and permissions
   - Sets up admin dashboard access

3. **Admin Access**
   - Admins can access `/admin/` routes
   - Full system overview and management
   - User and content moderation tools

### 2. User Authentication Flow

#### 2.1 User Login
**Flow Path**: Login Page → Authentication → Dashboard

**Step-by-Step Flow**:
1. **Login Page** (`/login`)
   - User sees login form
   - Enters username
   - Clicks "Sign In"

2. **Authentication**
   - System validates username exists
   - Creates user session
   - Sets session variables

3. **Dashboard Access**
   - User redirected to dashboard
   - Session maintained across pages
   - Access to user-specific features

**Security Features**:
- Session-based authentication
- Session timeout after inactivity
- Secure session cookies
- CSRF protection

#### 2.2 User Logout
**Flow Path**: Dashboard → Logout → Landing Page

**Step-by-Step Flow**:
1. **Logout Request**
   - User clicks logout button
   - System confirms logout action

2. **Session Cleanup**
   - Destroys user session
   - Clears session data
   - Removes authentication cookies

3. **Redirect**
   - User redirected to landing page
   - Session completely terminated

### 3. Listing Creation Flow

#### 3.1 Product Listing Creation
**Flow Path**: Dashboard → Create Listing → Product Form → Submission → Confirmation

**Step-by-Step Flow**:
1. **Dashboard Action**
   - User clicks "Create Listing"
   - Chooses "Product" option
   - System loads product creation form

2. **Product Form** (`/create-listing?type=product`)
   - User fills in product details:
     - Title (required)
     - Description (required)
     - Category selection
     - Price per unit
     - Quantity in stock
     - Tags (up to 5)
   - User uploads images (max 5)
   - User sets location (GPS or manual)

3. **Form Validation**
   - Frontend validates required fields
   - Backend validates data integrity
   - Image validation and processing

4. **Submission & Processing**
   - User clicks "Create Listing"
   - System processes form data
   - Images uploaded and processed
   - Listing data saved to JSON

5. **Confirmation**
   - Success message displayed
   - User redirected to listing management
   - New listing appears in user's listings

**Image Handling**:
- File upload from device
- Camera capture (mobile devices)
- Image compression and resizing
- Secure file storage

**Location Handling**:
- GPS auto-detection
- Manual coordinate input
- Address validation
- Distance calculations

#### 3.2 Service Listing Creation
**Flow Path**: Dashboard → Create Listing → Service Form → Availability Setup → Submission

**Step-by-Step Flow**:
1. **Service Form** (`/create-listing?type=service`)
   - User fills service details:
     - Title and description
     - Service category
     - Pricing (per hour/day/project)
     - Location information
     - Tags and keywords

2. **Availability Setup**
   - User sets available time slots
   - Configures recurring availability
   - Sets booking requirements

3. **Submission Process**
   - Similar to product listing
   - Additional availability data processing
   - Service-specific validation

### 4. Search & Discovery Flow

#### 4.1 Basic Search
**Flow Path**: Search Page → Query Input → Results Display

**Step-by-Step Flow**:
1. **Search Page** (`/search`)
   - User sees search interface
   - Enters search keywords
   - Clicks search button

2. **Search Processing**
   - System searches across listings
   - Matches keywords in titles and descriptions
   - Filters by active listings only

3. **Results Display**
   - Search results shown in grid/list view
   - Results paginated for large datasets
   - Search terms highlighted in results

**Search Algorithm**:
- Keyword matching in titles and descriptions
- Tag-based matching
- Category-based filtering
- Relevance scoring

#### 4.2 Advanced Filtering
**Flow Path**: Search Results → Filter Panel → Filtered Results

**Step-by-Step Flow**:
1. **Filter Panel**
   - User sees available filters:
     - Price range slider
     - Distance radius selector
     - Category checkboxes
     - Tag selection
     - Availability filters

2. **Filter Application**
   - User selects desired filters
   - System applies filters in real-time
   - Results update dynamically

3. **Filtered Results**
   - Only matching listings displayed
   - Filter summary shown
   - Clear filters option available

**Filter Types**:
- **Price**: Min/max price range
- **Distance**: Radius-based location filtering
- **Category**: Product/service categories
- **Tags**: Keyword-based filtering
- **Availability**: In-stock/available slots

#### 4.3 Location-Based Search
**Flow Path**: Location Detection → Radius Selection → Nearby Results

**Step-by-Step Flow**:
1. **Location Detection**
   - System requests user location
   - GPS coordinates captured
   - Address reverse-geocoded

2. **Radius Selection**
   - User selects search radius (1-50 km)
   - System calculates search area
   - Distance calculations performed

3. **Nearby Results**
   - Listings within radius displayed
   - Distance from user shown
   - Results sorted by distance

**Distance Calculation**:
- Haversine formula for accurate distances
- Real-time distance updates
- Location-based sorting

### 5. Purchase Flow

#### 5.1 Standard Purchase (No Approval Required)
**Flow Path**: Listing View → Purchase Form → Order Confirmation → Order Created

**Step-by-Step Flow**:
1. **Listing View**
   - User views listing details
   - Clicks "Buy Now" or "Purchase"
   - System loads purchase form

2. **Purchase Form**
   - User selects quantity
   - Reviews pricing and total
   - Adds special instructions (optional)
   - Clicks "Confirm Purchase"

3. **Order Processing**
   - System validates purchase data
   - Creates order record
   - Updates listing availability
   - Sends notification to seller

4. **Order Confirmation**
   - Success message displayed
   - Order details shown
   - User redirected to orders page

**Order Data**:
- Buyer and seller information
- Product/service details
- Quantity and pricing
- Order timestamp
- Status tracking

#### 5.2 Approval-Required Purchase
**Flow Path**: Purchase Request → Seller Approval → Purchase Completion

**Step-by-Step Flow**:
1. **Purchase Request**
   - User submits purchase request
   - System creates pending order
   - Seller notified of request

2. **Seller Review**
   - Seller sees pending requests
   - Reviews buyer and order details
   - Approves or rejects request

3. **Request Processing**
   - If approved: Purchase proceeds normally
   - If rejected: User notified, order cancelled
   - System updates order status

**Approval Interface**:
- Seller dashboard with pending requests
- Request details and buyer information
- Approve/reject buttons
- Reason for rejection (optional)

### 6. Order Management Flow

#### 6.1 Buyer Order Tracking
**Flow Path**: Orders Page → Order Details → Status Updates

**Step-by-Step Flow**:
1. **Orders Page** (`/orders`)
   - User sees all their orders
   - Orders grouped by status
   - Recent orders highlighted

2. **Order Details**
   - Click on order for full details
   - Order status and timeline
   - Seller contact information
   - Order notes and instructions

3. **Status Updates**
   - Real-time status changes
   - Email notifications (future)
   - Order completion confirmation

**Order Statuses**:
- Pending (approval required)
- Approved (payment processing)
- In Progress (seller fulfilling)
- Completed (delivered/service provided)
- Cancelled (user or seller cancelled)

#### 6.2 Seller Order Management
**Flow Path**: Seller Dashboard → Order Management → Fulfillment

**Step-by-Step Flow**:
1. **Order Dashboard**
   - Seller sees incoming orders
   - Orders sorted by priority
   - Quick action buttons

2. **Order Processing**
   - Review order details
   - Update order status
   - Communicate with buyer
   - Process fulfillment

3. **Order Completion**
   - Mark order as completed
   - Update inventory (if applicable)
   - Request buyer confirmation

### 7. Admin Management Flow

#### 7.1 Admin Dashboard
**Flow Path**: Admin Login → Dashboard → Management Tools

**Step-by-Step Flow**:
1. **Admin Access**
   - Admin logs in with admin credentials
   - System validates admin privileges
   - Redirects to admin dashboard

2. **Dashboard Overview**
   - System statistics and metrics
   - Recent activity feed
   - Quick action buttons
   - Alert notifications

3. **Management Tools**
   - User management interface
   - Content moderation tools
   - System configuration
   - Activity logs

**Admin Capabilities**:
- View all users and listings
- Suspend or ban users
- Moderate content and listings
- Access system logs and analytics
- Manage system settings

#### 7.2 User Moderation
**Flow Path**: User List → User Details → Moderation Actions

**Step-by-Step Flow**:
1. **User List**
   - Admin sees all registered users
   - Users sorted by activity/status
   - Search and filter options

2. **User Details**
   - Click user for detailed view
   - User activity history
   - Listing and order information
   - Account status

3. **Moderation Actions**
   - Suspend user account
   - Ban user permanently
   - Review user content
   - Send warnings

### 8. Image Management Flow

#### 8.1 Image Upload
**Flow Path**: Form Selection → File Upload → Processing → Storage

**Step-by-Step Flow**:
1. **File Selection**
   - User clicks upload button
   - File picker opens
   - User selects image files
   - Multiple file selection supported

2. **Upload Processing**
   - Files uploaded to server
   - File type validation
   - Size and format checking
   - Security scanning

3. **Image Processing**
   - Image compression and resizing
   - Thumbnail generation
   - Metadata extraction
   - Quality optimization

4. **Storage & Organization**
   - Images saved to appropriate folders
   - File names standardized
   - Database references updated
   - Backup copies created

#### 8.2 Camera Capture
**Flow Path**: Camera Access → Image Capture → Processing → Storage

**Step-by-Step Flow**:
1. **Camera Access**
   - User clicks camera button
   - Browser requests camera permission
   - Camera interface opens

2. **Image Capture**
   - User takes photo
   - Preview image shown
   - User confirms or retakes

3. **Image Processing**
   - Base64 image data captured
   - Image converted to file format
   - Same processing as file uploads
   - Stored in system

### 9. Location Services Flow

#### 9.1 GPS Detection
**Flow Path**: Location Request → Permission → Coordinates → Address

**Step-by-Step Flow**:
1. **Location Request**
   - System requests location access
   - User sees permission dialog
   - User grants or denies access

2. **GPS Capture**
   - If permitted: GPS coordinates captured
   - If denied: Manual input required
   - Coordinates validated for accuracy

3. **Address Resolution**
   - Coordinates converted to address
   - Address displayed to user
   - User can edit if needed

#### 9.2 Map Integration
**Flow Path**: Map View → Listing Markers → Interactive Features

**Step-by-Step Flow**:
1. **Map Loading**
   - Google Maps API loads
   - Map centered on user location
   - Zoom level set appropriately

2. **Listing Markers**
   - Listings plotted on map
   - Markers show listing info
   - Clustering for dense areas

3. **Interactive Features**
   - Click markers for details
   - Pan and zoom navigation
   - Search within map bounds
   - Route planning (future)

### 10. Error Handling & Recovery Flows

#### 10.1 Form Validation Errors
**Flow Path**: Error Detection → User Notification → Correction → Resubmission

**Step-by-Step Flow**:
1. **Error Detection**
   - Frontend validates input
   - Backend validates data
   - Error conditions identified

2. **User Notification**
   - Error messages displayed
   - Specific field highlighting
   - Helpful error descriptions

3. **Error Correction**
   - User corrects errors
   - Real-time validation feedback
   - Form ready for resubmission

#### 10.2 System Error Recovery
**Flow Path**: Error Occurrence → Error Logging → User Notification → Recovery

**Step-by-Step Flow**:
1. **Error Occurrence**
   - System encounters error
   - Error logged with details
   - User session preserved

2. **User Notification**
   - Friendly error message shown
   - Recovery suggestions provided
   - Contact information displayed

3. **System Recovery**
   - Error logged for analysis
   - System continues operation
   - User can retry action

### 11. Mobile Experience Flows

#### 11.1 Mobile Navigation
**Flow Path**: Mobile Detection → Responsive Layout → Touch Optimization

**Step-by-Step Flow**:
1. **Mobile Detection**
   - System detects mobile device
   - Responsive CSS applied
   - Touch-friendly interface loaded

2. **Mobile Layout**
   - Single-column layout
   - Large touch targets
   - Swipe gestures supported
   - Mobile-optimized navigation

3. **Touch Optimization**
   - Touch-friendly buttons
   - Swipe navigation
   - Pinch-to-zoom support
   - Mobile-specific features

#### 11.2 Mobile-Specific Features
**Flow Path**: Feature Detection → Mobile Interface → Mobile Functionality

**Step-by-Step Flow**:
1. **Feature Detection**
   - Camera API availability
   - GPS capability
   - Touch support
   - Screen size detection

2. **Mobile Interface**
   - Simplified navigation
   - Large input fields
   - Touch-friendly controls
   - Mobile-optimized forms

3. **Mobile Functionality**
   - Camera capture
   - GPS location
   - Touch gestures
   - Mobile notifications

### 12. Data Flow Patterns

#### 12.1 Data Creation Flow
**Pattern**: User Input → Validation → Processing → Storage → Confirmation

**Components Involved**:
- Frontend forms and validation
- Backend processing classes
- Data storage handlers
- User feedback systems

#### 12.2 Data Retrieval Flow
**Pattern**: Request → Authentication → Query → Processing → Response

**Components Involved**:
- User authentication
- Data query handlers
- Processing and formatting
- Response generation

#### 12.3 Data Update Flow
**Pattern**: Change Request → Validation → Update → Storage → Notification

**Components Involved**:
- Change detection
- Validation systems
- Update processors
- Notification systems

---

**Document Version**: 1.0  
**Last Updated**: Initial Creation  
**Next Review**: After prototype completion  
**UX Lead**: AI Assistant