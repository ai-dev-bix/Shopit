<?php
/**
 * Listing Detail Template
 * Displays detailed information about a specific listing
 */

// Ensure this file is included, not accessed directly
if (!defined('SECURE_ACCESS')) {
    http_response_code(403);
    exit('Access denied');
}

// Get listing ID from query parameters
$listingId = $_GET['id'] ?? null;
if (!$listingId) {
    // Redirect to browse page if no ID provided
    header('Location: /browse');
    exit;
}

// TODO: Load actual listing data from database
// For now, we'll use mock data
$listing = [
    'id' => $listingId,
    'title' => 'Sample Product Listing',
    'description' => 'This is a detailed description of the product. It includes all the important features, specifications, and details that potential buyers need to know.',
    'category' => 'electronics',
    'category_name' => 'Electronics',
    'type' => 'product',
    'price' => 299.99,
    'status' => 'active',
    'rating' => 4.5,
    'rating_count' => 127,
    'distance' => 2.3,
    'location' => 'Downtown Area',
    'tags' => ['electronics', 'gadgets', 'tech'],
    'images' => [
        '/assets/images/placeholder.jpg',
        '/assets/images/placeholder.jpg',
        '/assets/images/placeholder.jpg'
    ],
    'seller' => [
        'id' => 'seller1',
        'username' => 'techseller',
        'rating' => 4.8,
        'rating_count' => 45,
        'location' => 'Downtown Area',
        'joined' => '2023-01-15'
    ],
    'features' => [
        'Brand new condition',
        'Includes warranty',
        'Free local delivery',
        '30-day return policy'
    ],
    'specifications' => [
        'Brand' => 'Sample Brand',
        'Model' => 'XYZ-123',
        'Color' => 'Black',
        'Condition' => 'New',
        'Warranty' => '1 Year'
    ],
    'created_at' => '2024-01-15',
    'updated_at' => '2024-01-20'
];
?>

<div class="container">
    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb" aria-label="breadcrumbs">
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="/browse">Browse</a></li>
            <li><a href="/browse?category=<?php echo urlencode($listing['category']); ?>"><?php echo sanitizeOutput($listing['category_name']); ?></a></li>
            <li class="is-active"><a href="#" aria-current="page"><?php echo sanitizeOutput($listing['title']); ?></a></li>
        </ul>
    </nav>

    <!-- Main Listing Content -->
    <div class="columns">
        <!-- Left Column - Images and Basic Info -->
        <div class="column is-8">
            <!-- Image Gallery -->
            <div class="card">
                <div class="card-content p-0">
                    <div class="columns is-mobile is-multiline m-0">
                        <div class="column is-12">
                            <figure class="image is-4by3" id="mainImage">
                                <img src="<?php echo sanitizeOutput($listing['images'][0]); ?>" 
                                     alt="<?php echo sanitizeOutput($listing['title']); ?>"
                                     class="listing-main-image">
                            </figure>
                        </div>
                        <?php if (count($listing['images']) > 1): ?>
                        <div class="column is-12">
                            <div class="columns is-mobile is-multiline">
                                <?php foreach ($listing['images'] as $index => $image): ?>
                                <div class="column is-3">
                                    <figure class="image is-square thumbnail-image" 
                                            data-image="<?php echo sanitizeOutput($image); ?>">
                                        <img src="<?php echo sanitizeOutput($image); ?>" 
                                             alt="Thumbnail <?php echo $index + 1; ?>"
                                             class="listing-thumbnail">
                                    </figure>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Listing Details -->
            <div class="card mt-4">
                <header class="card-header">
                    <p class="card-header-title">
                        <i class="fas fa-info-circle mr-2"></i>
                        Product Details
                    </p>
                </header>
                <div class="card-content">
                    <div class="content">
                        <p><?php echo nl2br(sanitizeOutput($listing['description'])); ?></p>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <?php if (!empty($listing['features'])): ?>
            <div class="card mt-4">
                <header class="card-header">
                    <p class="card-header-title">
                        <i class="fas fa-star mr-2"></i>
                        Key Features
                    </p>
                </header>
                <div class="card-content">
                    <ul class="listing-features">
                        <?php foreach ($listing['features'] as $feature): ?>
                        <li><i class="fas fa-check has-text-success mr-2"></i><?php echo sanitizeOutput($feature); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>

            <!-- Specifications -->
            <?php if (!empty($listing['specifications'])): ?>
            <div class="card mt-4">
                <header class="card-header">
                    <p class="card-header-title">
                        <i class="fas fa-cogs mr-2"></i>
                        Specifications
                    </p>
                </header>
                <div class="card-content">
                    <div class="columns is-multiline">
                        <?php foreach ($listing['specifications'] as $key => $value): ?>
                        <div class="column is-6">
                            <div class="specification-item">
                                <strong><?php echo sanitizeOutput($key); ?>:</strong>
                                <span><?php echo sanitizeOutput($value); ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Tags -->
            <?php if (!empty($listing['tags'])): ?>
            <div class="card mt-4">
                <header class="card-header">
                    <p class="card-header-title">
                        <i class="fas fa-tags mr-2"></i>
                        Tags
                    </p>
                </header>
                <div class="card-content">
                    <div class="tags">
                        <?php foreach ($listing['tags'] as $tag): ?>
                        <span class="tag is-info is-light"><?php echo sanitizeOutput($tag); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Right Column - Purchase Info and Seller Details -->
        <div class="column is-4">
            <!-- Purchase Card -->
            <div class="card is-sticky">
                <header class="card-header">
                    <p class="card-header-title">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Purchase
                    </p>
                </header>
                <div class="card-content">
                    <!-- Price -->
                    <div class="has-text-centered mb-4">
                        <p class="title is-2 has-text-primary">
                            <?php echo formatCurrency($listing['price']); ?>
                        </p>
                        <p class="subtitle is-6 has-text-grey">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            <?php echo sanitizeOutput($listing['distance']); ?> km away
                        </p>
                    </div>

                    <!-- Status -->
                    <div class="field">
                        <div class="control">
                            <span class="tag is-<?php echo $listing['status'] === 'active' ? 'success' : 'warning'; ?> is-medium is-fullwidth">
                                <i class="fas fa-circle mr-2"></i>
                                <?php echo ucfirst(sanitizeOutput($listing['status'])); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Quantity (for products) -->
                    <?php if ($listing['type'] === 'product'): ?>
                    <div class="field">
                        <label class="label">Quantity</label>
                        <div class="control">
                            <div class="field has-addons">
                                <div class="control">
                                    <button class="button" id="decreaseQuantity">-</button>
                                </div>
                                <div class="control">
                                    <input class="input has-text-centered" type="number" id="quantity" value="1" min="1" max="99">
                                </div>
                                <div class="control">
                                    <button class="button" id="increaseQuantity">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Date/Time Selection (for services) -->
                    <?php if ($listing['type'] === 'service'): ?>
                    <div class="field">
                        <label class="label">Preferred Date</label>
                        <div class="control">
                            <input class="input" type="date" id="serviceDate" min="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Preferred Time</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select id="serviceTime">
                                    <option value="">Select time</option>
                                    <option value="09:00">9:00 AM</option>
                                    <option value="10:00">10:00 AM</option>
                                    <option value="11:00">11:00 AM</option>
                                    <option value="12:00">12:00 PM</option>
                                    <option value="13:00">1:00 PM</option>
                                    <option value="14:00">2:00 PM</option>
                                    <option value="15:00">3:00 PM</option>
                                    <option value="16:00">4:00 PM</option>
                                    <option value="17:00">5:00 PM</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="field">
                        <div class="control">
                            <button class="button is-primary is-fullwidth is-medium" id="purchaseButton">
                                <i class="fas fa-shopping-cart mr-2"></i>
                                <?php echo $listing['type'] === 'product' ? 'Add to Cart' : 'Book Service'; ?>
                            </button>
                        </div>
                    </div>

                    <div class="field">
                        <div class="control">
                            <button class="button is-outlined is-info is-fullwidth" id="contactSellerButton">
                                <i class="fas fa-envelope mr-2"></i>
                                Contact Seller
                            </button>
                        </div>
                    </div>

                    <div class="field">
                        <div class="control">
                            <button class="button is-outlined is-success is-fullwidth" id="favoriteButton">
                                <i class="fas fa-heart mr-2"></i>
                                Add to Favorites
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seller Information -->
            <div class="card mt-4">
                <header class="card-header">
                    <p class="card-header-title">
                        <i class="fas fa-user mr-2"></i>
                        Seller Information
                    </p>
                </header>
                <div class="card-content">
                    <div class="media">
                        <div class="media-left">
                            <figure class="image is-48x48">
                                <img src="/assets/images/avatar-placeholder.jpg" alt="Seller Avatar" class="is-rounded">
                            </figure>
                        </div>
                        <div class="media-content">
                            <p class="title is-5"><?php echo sanitizeOutput($listing['seller']['username']); ?></p>
                            <p class="subtitle is-6 has-text-grey">
                                <i class="fas fa-star has-text-warning mr-1"></i>
                                <?php echo sanitizeOutput($listing['seller']['rating']); ?> 
                                (<?php echo sanitizeOutput($listing['seller']['rating_count']); ?> reviews)
                            </p>
                            <p class="is-size-7 has-text-grey">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                <?php echo sanitizeOutput($listing['seller']['location']); ?>
                            </p>
                            <p class="is-size-7 has-text-grey">
                                <i class="fas fa-calendar mr-1"></i>
                                Member since <?php echo formatDate($listing['seller']['joined']); ?>
                            </p>
                        </div>
                    </div>

                    <div class="buttons mt-3">
                        <button class="button is-small is-outlined is-info" id="viewSellerProfile">
                            <i class="fas fa-user mr-1"></i>
                            View Profile
                        </button>
                        <button class="button is-small is-outlined is-success" id="messageSeller">
                            <i class="fas fa-comment mr-1"></i>
                            Message
                        </button>
                    </div>
                </div>
            </div>

            <!-- Location Information -->
            <div class="card mt-4">
                <header class="card-header">
                    <p class="card-header-title">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        Location
                    </p>
                </header>
                <div class="card-content">
                    <p class="has-text-grey">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        <?php echo sanitizeOutput($listing['location']); ?>
                    </p>
                    <p class="has-text-grey is-size-7">
                        <i class="fas fa-ruler-horizontal mr-2"></i>
                        <?php echo sanitizeOutput($listing['distance']); ?> km from your location
                    </p>
                    
                    <!-- Map placeholder -->
                    <div class="has-text-centered mt-3">
                        <div class="box has-background-light">
                            <span class="icon is-large has-text-grey">
                                <i class="fas fa-map fa-2x"></i>
                            </span>
                            <p class="is-size-7 has-text-grey mt-2">Map view coming soon</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="card mt-4">
        <header class="card-header">
            <p class="card-header-title">
                <i class="fas fa-star mr-2"></i>
                Reviews & Ratings
            </p>
            <div class="card-header-icon">
                <span class="tag is-info">
                    <?php echo sanitizeOutput($listing['rating']); ?> / 5.0
                    (<?php echo sanitizeOutput($listing['rating_count']); ?> reviews)
                </span>
            </div>
        </header>
        <div class="card-content">
            <!-- Rating Summary -->
            <div class="columns is-multiline">
                <div class="column is-3">
                    <div class="has-text-centered">
                        <p class="title is-1 has-text-primary"><?php echo sanitizeOutput($listing['rating']); ?></p>
                        <div class="stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="icon has-text-<?php echo $i <= $listing['rating'] ? 'warning' : 'grey-light'; ?>">
                                    <i class="fas fa-star"></i>
                                </span>
                            <?php endfor; ?>
                        </div>
                        <p class="is-size-7 has-text-grey"><?php echo sanitizeOutput($listing['rating_count']); ?> total reviews</p>
                    </div>
                </div>
                <div class="column is-9">
                    <!-- Rating Bars -->
                    <div class="rating-bars">
                        <?php for ($rating = 5; $rating >= 1; $rating--): ?>
                        <div class="rating-bar-item">
                            <span class="rating-label"><?php echo $rating; ?> stars</span>
                            <progress class="progress is-small" value="0" max="100"></progress>
                            <span class="rating-count">0</span>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <!-- Review List -->
            <div class="reviews-list mt-4">
                <h4 class="title is-5">Recent Reviews</h4>
                
                <!-- Sample Reviews -->
                <div class="review-item">
                    <div class="media">
                        <div class="media-left">
                            <figure class="image is-32x32">
                                <img src="/assets/images/avatar-placeholder.jpg" alt="Reviewer" class="is-rounded">
                            </figure>
                        </div>
                        <div class="media-content">
                            <div class="level is-mobile">
                                <div class="level-left">
                                    <p class="title is-6">John Doe</p>
                                </div>
                                <div class="level-right">
                                    <div class="stars">
                                        <span class="icon has-text-warning"><i class="fas fa-star"></i></span>
                                        <span class="icon has-text-warning"><i class="fas fa-star"></i></span>
                                        <span class="icon has-text-warning"><i class="fas fa-star"></i></span>
                                        <span class="icon has-text-warning"><i class="fas fa-star"></i></span>
                                        <span class="icon has-text-grey-light"><i class="fas fa-star"></i></span>
                                    </div>
                                </div>
                            </div>
                            <p class="subtitle is-6 has-text-grey">Great product, exactly as described!</p>
                            <p class="is-size-7 has-text-grey">Posted on January 15, 2024</p>
                        </div>
                    </div>
                </div>

                <div class="review-item">
                    <div class="media">
                        <div class="media-left">
                            <figure class="image is-32x32">
                                <img src="/assets/images/avatar-placeholder.jpg" alt="Reviewer" class="is-rounded">
                            </figure>
                        </div>
                        <div class="media-content">
                            <div class="level is-mobile">
                                <div class="level-left">
                                    <p class="title is-6">Jane Smith</p>
                                </div>
                                <div class="level-right">
                                    <div class="stars">
                                        <span class="icon has-text-warning"><i class="fas fa-star"></i></span>
                                        <span class="icon has-text-warning"><i class="fas fa-star"></i></span>
                                        <span class="icon has-text-warning"><i class="fas fa-star"></i></span>
                                        <span class="icon has-text-warning"><i class="fas fa-star"></i></span>
                                        <span class="icon has-text-warning"><i class="fas fa-star"></i></span>
                                    </div>
                                </div>
                            </div>
                            <p class="subtitle is-6 has-text-grey">Excellent service and fast delivery!</p>
                            <p class="is-size-7 has-text-grey">Posted on January 12, 2024</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Write Review Button -->
            <div class="has-text-centered mt-4">
                <button class="button is-outlined is-primary" id="writeReviewButton">
                    <i class="fas fa-edit mr-2"></i>
                    Write a Review
                </button>
            </div>
        </div>
    </div>

    <!-- Related Listings -->
    <div class="card mt-4">
        <header class="card-header">
            <p class="card-header-title">
                <i class="fas fa-th-large mr-2"></i>
                Related Listings
            </p>
        </header>
        <div class="card-content">
            <div class="columns is-multiline" id="relatedListings">
                <!-- Related listings will be loaded here -->
                <div class="column is-3">
                    <div class="card">
                        <div class="card-image">
                            <figure class="image is-4by3">
                                <img src="/assets/images/placeholder.jpg" alt="Related listing">
                            </div>
                        <div class="card-content">
                            <p class="title is-6">Related Product 1</p>
                            <p class="subtitle is-6 has-text-primary">$199.99</p>
                        </div>
                    </div>
                </div>
                <!-- More related listings... -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeListingDetail();
});

function initializeListingDetail() {
    // Image gallery functionality
    setupImageGallery();
    
    // Quantity controls
    setupQuantityControls();
    
    // Button event listeners
    setupButtonListeners();
    
    // Load related listings
    loadRelatedListings();
}

function setupImageGallery() {
    const mainImage = document.getElementById('mainImage');
    const thumbnails = document.querySelectorAll('.thumbnail-image');
    
    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            const imageSrc = this.dataset.image;
            mainImage.querySelector('img').src = imageSrc;
            
            // Update active thumbnail
            thumbnails.forEach(t => t.classList.remove('is-active'));
            this.classList.add('is-active');
        });
    });
}

function setupQuantityControls() {
    const quantityInput = document.getElementById('quantity');
    const decreaseBtn = document.getElementById('decreaseQuantity');
    const increaseBtn = document.getElementById('increaseQuantity');
    
    if (decreaseBtn && increaseBtn && quantityInput) {
        decreaseBtn.addEventListener('click', function() {
            const currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        });
        
        increaseBtn.addEventListener('click', function() {
            const currentValue = parseInt(quantityInput.value);
            if (currentValue < 99) {
                quantityInput.value = currentValue + 1;
            }
        });
        
        quantityInput.addEventListener('change', function() {
            let value = parseInt(this.value);
            if (isNaN(value) || value < 1) {
                this.value = 1;
            } else if (value > 99) {
                this.value = 99;
            }
        });
    }
}

function setupButtonListeners() {
    // Purchase button
    const purchaseBtn = document.getElementById('purchaseButton');
    if (purchaseBtn) {
        purchaseBtn.addEventListener('click', function() {
            handlePurchase();
        });
    }
    
    // Contact seller button
    const contactBtn = document.getElementById('contactSellerButton');
    if (contactBtn) {
        contactBtn.addEventListener('click', function() {
            handleContactSeller();
        });
    }
    
    // Favorite button
    const favoriteBtn = document.getElementById('favoriteButton');
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', function() {
            handleAddToFavorites();
        });
    }
    
    // Other buttons
    const viewProfileBtn = document.getElementById('viewSellerProfile');
    if (viewProfileBtn) {
        viewProfileBtn.addEventListener('click', function() {
            window.open('/profile?seller=<?php echo sanitizeOutput($listing['seller']['id']); ?>', '_blank');
        });
    }
    
    const messageBtn = document.getElementById('messageSeller');
    if (messageBtn) {
        messageBtn.addEventListener('click', function() {
            handleMessageSeller();
        });
    }
    
    const writeReviewBtn = document.getElementById('writeReviewButton');
    if (writeReviewBtn) {
        writeReviewBtn.addEventListener('click', function() {
            handleWriteReview();
        });
    }
}

function handlePurchase() {
    const listingType = '<?php echo $listing['type']; ?>';
    let purchaseData = {
        listing_id: '<?php echo $listing['id']; ?>',
        seller_id: '<?php echo $listing['seller']['id']; ?>'
    };
    
    if (listingType === 'product') {
        const quantity = document.getElementById('quantity')?.value || 1;
        purchaseData.quantity = parseInt(quantity);
    } else {
        const serviceDate = document.getElementById('serviceDate')?.value;
        const serviceTime = document.getElementById('serviceTime')?.value;
        
        if (!serviceDate || !serviceTime) {
            showNotification('Please select a date and time for the service', 'warning');
            return;
        }
        
        purchaseData.service_date = serviceDate;
        purchaseData.service_time = serviceTime;
    }
    
    // TODO: Implement actual purchase logic
    showNotification('Purchase functionality coming soon!', 'info');
    console.log('Purchase data:', purchaseData);
}

function handleContactSeller() {
    // TODO: Implement contact seller functionality
    showNotification('Contact functionality coming soon!', 'info');
}

function handleAddToFavorites() {
    const button = document.getElementById('favoriteButton');
    const icon = button.querySelector('i');
    
    if (button.classList.contains('is-success')) {
        button.classList.remove('is-success');
        button.classList.add('is-outlined', 'is-success');
        icon.classList.remove('fas');
        icon.classList.add('far');
        showNotification('Removed from favorites', 'info');
    } else {
        button.classList.remove('is-outlined');
        button.classList.add('is-success');
        icon.classList.remove('far');
        icon.classList.add('fas');
        showNotification('Added to favorites', 'success');
    }
}

function handleMessageSeller() {
    // TODO: Implement messaging functionality
    showNotification('Messaging functionality coming soon!', 'info');
}

function handleWriteReview() {
    // TODO: Implement review writing functionality
    showNotification('Review functionality coming soon!', 'info');
}

function loadRelatedListings() {
    // TODO: Load actual related listings from API
    // For now, this is just a placeholder
    console.log('Loading related listings...');
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification is-${type} is-light`;
    notification.innerHTML = `
        <button class="delete" onclick="this.parentElement.remove();"></button>
        ${message}
    `;
    
    // Add to page
    const container = document.querySelector('.container');
    container.insertBefore(notification, container.firstChild);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}
</script>

<style>
.is-sticky {
    position: sticky;
    top: 20px;
}

.listing-main-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 6px;
}

.listing-thumbnail {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 4px;
    cursor: pointer;
    transition: opacity 0.2s ease;
}

.listing-thumbnail:hover {
    opacity: 0.8;
}

.thumbnail-image.is-active .listing-thumbnail {
    border: 2px solid var(--primary-color);
}

.listing-features {
    list-style: none;
    padding: 0;
}

.listing-features li {
    margin-bottom: 0.5rem;
}

.specification-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.specification-item:last-child {
    border-bottom: none;
}

.rating-bars {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.rating-bar-item {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.rating-label {
    min-width: 60px;
    font-size: 0.875rem;
}

.rating-count {
    min-width: 30px;
    text-align: right;
    font-size: 0.875rem;
    color: #666;
}

.progress {
    flex: 1;
    margin: 0;
}

.review-item {
    padding: 1rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.review-item:last-child {
    border-bottom: none;
}

.stars {
    display: flex;
    gap: 2px;
}

.stars .icon {
    font-size: 0.875rem;
}

@media screen and (max-width: 768px) {
    .column.is-8, .column.is-4 {
        width: 100%;
    }
    
    .is-sticky {
        position: static;
    }
    
    .rating-bar-item {
        flex-direction: column;
        align-items: stretch;
        gap: 0.25rem;
    }
    
    .rating-label, .rating-count {
        min-width: auto;
    }
}
</style>