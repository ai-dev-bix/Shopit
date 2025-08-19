<?php
/**
 * Browse Template
 * Displays all available listings with filtering and sorting options
 */

// Ensure this file is included, not accessed directly
if (!defined('SECURE_ACCESS')) {
    http_response_code(403);
    exit('Access denied');
}
?>

<div class="container">
    <!-- Page Header -->
    <section class="hero is-light is-small">
        <div class="hero-body">
            <div class="container">
                <h1 class="title">
                    <i class="fas fa-th-large mr-2"></i>
                    Browse All Listings
                </h1>
                <p class="subtitle">
                    Discover amazing products and services in your area
                </p>
            </div>
        </div>
    </section>

    <!-- Filter and Sort Controls -->
    <div class="columns mt-4">
        <div class="column is-3">
            <!-- Filters Sidebar -->
            <div class="card">
                <header class="card-header">
                    <p class="card-header-title">
                        <i class="fas fa-filter mr-2"></i>
                        Filters
                    </p>
                </header>
                <div class="card-content">
                    <!-- Category Filter -->
                    <div class="field">
                        <label class="label">Category</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select id="categoryFilter">
                                    <option value="">All Categories</option>
                                    <option value="electronics">Electronics</option>
                                    <option value="home">Home & Garden</option>
                                    <option value="automotive">Automotive</option>
                                    <option value="fashion">Fashion & Beauty</option>
                                    <option value="sports">Sports & Recreation</option>
                                    <option value="professional">Professional Services</option>
                                    <option value="home-services">Home Services</option>
                                    <option value="health">Health & Wellness</option>
                                    <option value="education">Education & Training</option>
                                    <option value="entertainment">Entertainment</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Type Filter -->
                    <div class="field">
                        <label class="label">Type</label>
                        <div class="control">
                            <label class="radio">
                                <input type="radio" name="typeFilter" value="" checked>
                                All
                            </label>
                            <label class="radio">
                                <input type="radio" name="typeFilter" value="product">
                                Products
                            </label>
                            <label class="radio">
                                <input type="radio" name="typeFilter" value="service">
                                Services
                            </label>
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="field">
                        <label class="label">Price Range</label>
                        <div class="control">
                            <input class="input" type="number" id="minPrice" placeholder="Min Price" min="0">
                        </div>
                        <div class="control mt-2">
                            <input class="input" type="number" id="maxPrice" placeholder="Max Price" min="0">
                        </div>
                    </div>

                    <!-- Distance Filter -->
                    <div class="field">
                        <label class="label">Distance (km)</label>
                        <div class="control">
                            <input class="input" type="range" id="distanceFilter" min="1" max="50" value="25">
                            <span id="distanceValue">25 km</span>
                        </div>
                    </div>

                    <!-- Tags Filter -->
                    <div class="field">
                        <label class="label">Popular Tags</label>
                        <div class="control">
                            <div class="tags">
                                <span class="tag is-info is-light filter-tag" data-tag="electronics">Electronics</span>
                                <span class="tag is-success is-light filter-tag" data-tag="home">Home</span>
                                <span class="tag is-warning is-light filter-tag" data-tag="automotive">Auto</span>
                                <span class="tag is-danger is-light filter-tag" data-tag="fashion">Fashion</span>
                                <span class="tag is-info is-light filter-tag" data-tag="sports">Sports</span>
                            </div>
                        </div>
                    </div>

                    <!-- Apply Filters Button -->
                    <div class="field">
                        <div class="control">
                            <button class="button is-primary is-fullwidth" id="applyFilters">
                                <i class="fas fa-search mr-2"></i>
                                Apply Filters
                            </button>
                        </div>
                    </div>

                    <!-- Clear Filters Button -->
                    <div class="field">
                        <div class="control">
                            <button class="button is-light is-fullwidth" id="clearFilters">
                                <i class="fas fa-times mr-2"></i>
                                Clear All
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="column is-9">
            <!-- Sort and View Controls -->
            <div class="level">
                <div class="level-left">
                    <div class="level-item">
                        <div class="field has-addons">
                            <div class="control">
                                <span class="button is-static">Sort by:</span>
                            </div>
                            <div class="control">
                                <div class="select">
                                    <select id="sortBy">
                                        <option value="relevance">Relevance</option>
                                        <option value="price_low">Price: Low to High</option>
                                        <option value="price_high">Price: High to Low</option>
                                        <option value="distance">Distance</option>
                                        <option value="newest">Newest First</option>
                                        <option value="rating">Highest Rated</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="level-right">
                    <div class="level-item">
                        <div class="field has-addons">
                            <div class="control">
                                <button class="button" id="gridView">
                                    <i class="fas fa-th-large"></i>
                                </button>
                            </div>
                            <div class="control">
                                <button class="button" id="listView">
                                    <i class="fas fa-list"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Count -->
            <div class="notification is-info is-light">
                <p id="resultsCount">Loading listings...</p>
            </div>

            <!-- Loading Spinner -->
            <div id="loadingSpinner" class="has-text-centered py-6" style="display: none;">
                <span class="icon is-large">
                    <i class="fas fa-spinner fa-pulse fa-2x"></i>
                </span>
                <p class="mt-2">Loading listings...</p>
            </div>

            <!-- Listings Grid -->
            <div id="listingsContainer" class="columns is-multiline">
                <!-- Listings will be loaded here dynamically -->
            </div>

            <!-- Pagination -->
            <nav class="pagination is-centered mt-6" id="pagination" style="display: none;">
                <a class="pagination-previous" id="prevPage">Previous</a>
                <a class="pagination-next" id="nextPage">Next</a>
                <ul class="pagination-list" id="pageNumbers">
                    <!-- Page numbers will be generated here -->
                </ul>
            </nav>

            <!-- No Results Message -->
            <div id="noResults" class="has-text-centered py-6" style="display: none;">
                <span class="icon is-large has-text-grey-light">
                    <i class="fas fa-search fa-3x"></i>
                </span>
                <h3 class="title is-4 has-text-grey-light mt-4">No listings found</h3>
                <p class="subtitle is-6 has-text-grey-light">
                    Try adjusting your filters or search criteria
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Listing Card Template (Hidden) -->
<template id="listingCardTemplate">
    <div class="column is-4">
        <div class="card listing-card">
            <div class="card-image">
                <figure class="image is-4by3">
                    <img src="" alt="Listing Image" class="listing-image">
                </figure>
                <div class="card-image-overlay">
                    <span class="tag is-info is-light listing-type"></span>
                    <span class="tag is-success is-light listing-status"></span>
                </div>
            </div>
            <div class="card-content">
                <p class="title is-5 listing-title"></p>
                <p class="subtitle is-6 has-text-grey listing-category"></p>
                <p class="content listing-description"></p>
                <div class="level is-mobile">
                    <div class="level-left">
                        <span class="tag is-primary is-medium listing-price"></span>
                    </div>
                    <div class="level-right">
                        <span class="tag is-info is-light listing-distance"></span>
                    </div>
                </div>
                <div class="level is-mobile">
                    <div class="level-left">
                        <div class="stars">
                            <span class="icon has-text-warning">
                                <i class="fas fa-star"></i>
                            </span>
                            <span class="listing-rating"></span>
                            <span class="listing-rating-count"></span>
                        </div>
                    </div>
                    <div class="level-right">
                        <span class="tag is-light listing-location"></span>
                    </div>
                </div>
                <div class="tags listing-tags"></div>
            </div>
            <footer class="card-footer">
                <a class="card-footer-item view-listing">
                    <i class="fas fa-eye mr-2"></i>
                    View Details
                </a>
                <a class="card-footer-item contact-seller">
                    <i class="fas fa-envelope mr-2"></i>
                    Contact
                </a>
            </footer>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize browse functionality
    initializeBrowse();
    
    // Set up event listeners
    setupEventListeners();
    
    // Load initial listings
    loadListings();
});

let currentFilters = {
    category: '',
    type: '',
    minPrice: '',
    maxPrice: '',
    distance: 25,
    tags: [],
    sortBy: 'relevance',
    page: 1
};

let currentView = 'grid';
let allListings = [];
let filteredListings = [];

function initializeBrowse() {
    // Set initial view
    setView('grid');
    
    // Initialize distance slider
    const distanceSlider = document.getElementById('distanceFilter');
    const distanceValue = document.getElementById('distanceValue');
    
    distanceSlider.addEventListener('input', function() {
        distanceValue.textContent = this.value + ' km';
        currentFilters.distance = parseInt(this.value);
    });
}

function setupEventListeners() {
    // Filter controls
    document.getElementById('categoryFilter').addEventListener('change', function() {
        currentFilters.category = this.value;
    });
    
    document.querySelectorAll('input[name="typeFilter"]').forEach(radio => {
        radio.addEventListener('change', function() {
            currentFilters.type = this.value;
        });
    });
    
    document.getElementById('minPrice').addEventListener('input', function() {
        currentFilters.minPrice = this.value;
    });
    
    document.getElementById('maxPrice').addEventListener('input', function() {
        currentFilters.maxPrice = this.value;
    });
    
    // Tag filters
    document.querySelectorAll('.filter-tag').forEach(tag => {
        tag.addEventListener('click', function() {
            const tagValue = this.dataset.tag;
            if (currentFilters.tags.includes(tagValue)) {
                currentFilters.tags = currentFilters.tags.filter(t => t !== tagValue);
                this.classList.remove('is-info', 'is-success', 'is-warning', 'is-danger');
                this.classList.add('is-light');
            } else {
                currentFilters.tags.push(tagValue);
                this.classList.remove('is-light');
                if (this.classList.contains('is-info')) this.classList.add('is-info');
                else if (this.classList.contains('is-success')) this.classList.add('is-success');
                else if (this.classList.contains('is-warning')) this.classList.add('is-warning');
                else if (this.classList.contains('is-danger')) this.classList.add('is-danger');
            }
        });
    });
    
    // Sort control
    document.getElementById('sortBy').addEventListener('change', function() {
        currentFilters.sortBy = this.value;
        applyFilters();
    });
    
    // View controls
    document.getElementById('gridView').addEventListener('click', function() {
        setView('grid');
    });
    
    document.getElementById('listView').addEventListener('click', function() {
        setView('list');
    });
    
    // Filter buttons
    document.getElementById('applyFilters').addEventListener('click', function() {
        applyFilters();
    });
    
    document.getElementById('clearFilters').addEventListener('click', function() {
        clearFilters();
    });
    
    // Pagination
    document.getElementById('prevPage').addEventListener('click', function() {
        if (currentFilters.page > 1) {
            currentFilters.page--;
            displayListings();
        }
    });
    
    document.getElementById('nextPage').addEventListener('click', function() {
        const maxPages = Math.ceil(filteredListings.length / 12);
        if (currentFilters.page < maxPages) {
            currentFilters.page++;
            displayListings();
        }
    });
}

function setView(view) {
    currentView = view;
    const container = document.getElementById('listingsContainer');
    
    if (view === 'grid') {
        container.classList.remove('is-multiline');
        container.classList.add('columns', 'is-multiline');
        document.getElementById('gridView').classList.add('is-info');
        document.getElementById('listView').classList.remove('is-info');
    } else {
        container.classList.remove('columns', 'is-multiline');
        container.classList.add('is-multiline');
        document.getElementById('listView').classList.add('is-info');
        document.getElementById('gridView').classList.remove('is-info');
    }
    
    displayListings();
}

function loadListings() {
    showLoading(true);
    
    // Simulate API call - replace with actual API endpoint
    setTimeout(() => {
        // Mock data for demonstration
        allListings = generateMockListings();
        applyFilters();
        showLoading(false);
    }, 1000);
}

function generateMockListings() {
    const categories = ['electronics', 'home', 'automotive', 'fashion', 'sports', 'professional', 'home-services', 'health', 'education', 'entertainment'];
    const types = ['product', 'service'];
    const statuses = ['active', 'pending', 'sold'];
    
    const listings = [];
    
    for (let i = 1; i <= 50; i++) {
        const category = categories[Math.floor(Math.random() * categories.length)];
        const type = types[Math.floor(Math.random() * types.length)];
        const status = statuses[Math.floor(Math.random() * statuses.length)];
        
        listings.push({
            id: i,
            title: `Sample ${type.charAt(0).toUpperCase() + type.slice(1)} ${i}`,
            description: `This is a sample ${type} in the ${category} category. It's a great item with excellent quality and features.`,
            category: category,
            category_name: category.charAt(0).toUpperCase() + category.slice(1).replace('-', ' '),
            type: type,
            price: Math.floor(Math.random() * 1000) + 10,
            status: status,
            rating: (Math.random() * 5).toFixed(1),
            rating_count: Math.floor(Math.random() * 100),
            distance: (Math.random() * 25).toFixed(1),
            location: 'Sample Location',
            tags: [category, type],
            image: '/assets/images/placeholder.jpg',
            created_at: new Date(Date.now() - Math.random() * 30 * 24 * 60 * 60 * 1000).toISOString()
        });
    }
    
    return listings;
}

function applyFilters() {
    currentFilters.page = 1;
    
    filteredListings = allListings.filter(listing => {
        // Category filter
        if (currentFilters.category && listing.category !== currentFilters.category) {
            return false;
        }
        
        // Type filter
        if (currentFilters.type && listing.type !== currentFilters.type) {
            return false;
        }
        
        // Price filter
        if (currentFilters.minPrice && listing.price < parseFloat(currentFilters.minPrice)) {
            return false;
        }
        if (currentFilters.maxPrice && listing.price > parseFloat(currentFilters.maxPrice)) {
            return false;
        }
        
        // Distance filter
        if (listing.distance > currentFilters.distance) {
            return false;
        }
        
        // Tags filter
        if (currentFilters.tags.length > 0) {
            const hasMatchingTag = currentFilters.tags.some(tag => 
                listing.tags.includes(tag)
            );
            if (!hasMatchingTag) {
                return false;
            }
        }
        
        return true;
    });
    
    // Sort listings
    sortListings();
    
    // Display results
    displayListings();
    updateResultsCount();
}

function sortListings() {
    switch (currentFilters.sortBy) {
        case 'price_low':
            filteredListings.sort((a, b) => a.price - b.price);
            break;
        case 'price_high':
            filteredListings.sort((a, b) => b.price - a.price);
            break;
        case 'distance':
            filteredListings.sort((a, b) => a.distance - b.distance);
            break;
        case 'newest':
            filteredListings.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            break;
        case 'rating':
            filteredListings.sort((a, b) => parseFloat(b.rating) - parseFloat(a.rating));
            break;
        default: // relevance
            // Keep original order for now
            break;
    }
}

function displayListings() {
    const container = document.getElementById('listingsContainer');
    const template = document.getElementById('listingCardTemplate');
    
    // Clear container
    container.innerHTML = '';
    
    if (filteredListings.length === 0) {
        document.getElementById('noResults').style.display = 'block';
        document.getElementById('pagination').style.display = 'none';
        return;
    }
    
    document.getElementById('noResults').style.display = 'none';
    
    // Calculate pagination
    const itemsPerPage = 12;
    const startIndex = (currentFilters.page - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const pageListings = filteredListings.slice(startIndex, endIndex);
    
    // Display listings
    pageListings.forEach(listing => {
        const listingElement = template.content.cloneNode(true);
        
        // Set listing data
        listingElement.querySelector('.listing-image').src = listing.image;
        listingElement.querySelector('.listing-image').alt = listing.title;
        listingElement.querySelector('.listing-type').textContent = listing.type;
        listingElement.querySelector('.listing-status').textContent = listing.status;
        listingElement.querySelector('.listing-title').textContent = listing.title;
        listingElement.querySelector('.listing-category').textContent = listing.category_name;
        listingElement.querySelector('.listing-description').textContent = listing.description;
        listingElement.querySelector('.listing-price').textContent = '$' + listing.price;
        listingElement.querySelector('.listing-distance').textContent = listing.distance + ' km';
        listingElement.querySelector('.listing-rating').textContent = listing.rating;
        listingElement.querySelector('.listing-rating-count').textContent = `(${listing.rating_count})`;
        listingElement.querySelector('.listing-location').textContent = listing.location;
        
        // Set tags
        const tagsContainer = listingElement.querySelector('.listing-tags');
        listing.tags.forEach(tag => {
            const tagElement = document.createElement('span');
            tagElement.className = 'tag is-light is-small';
            tagElement.textContent = tag;
            tagsContainer.appendChild(tagElement);
        });
        
        // Set up links
        listingElement.querySelector('.view-listing').href = `/listing?id=${listing.id}`;
        listingElement.querySelector('.contact-seller').href = `/contact?seller=${listing.id}`;
        
        container.appendChild(listingElement);
    });
    
    // Update pagination
    updatePagination();
}

function updatePagination() {
    const itemsPerPage = 12;
    const totalPages = Math.ceil(filteredListings.length / itemsPerPage);
    
    if (totalPages <= 1) {
        document.getElementById('pagination').style.display = 'none';
        return;
    }
    
    document.getElementById('pagination').style.display = 'flex';
    
    // Update prev/next buttons
    document.getElementById('prevPage').disabled = currentFilters.page <= 1;
    document.getElementById('nextPage').disabled = currentFilters.page >= totalPages;
    
    // Generate page numbers
    const pageNumbers = document.getElementById('pageNumbers');
    pageNumbers.innerHTML = '';
    
    for (let i = 1; i <= totalPages; i++) {
        const li = document.createElement('li');
        const a = document.createElement('a');
        a.className = 'pagination-link';
        a.textContent = i;
        
        if (i === currentFilters.page) {
            a.classList.add('is-current');
        }
        
        a.addEventListener('click', function() {
            currentFilters.page = i;
            displayListings();
        });
        
        li.appendChild(a);
        pageNumbers.appendChild(li);
    }
}

function updateResultsCount() {
    const countElement = document.getElementById('resultsCount');
    countElement.textContent = `Showing ${filteredListings.length} listings`;
}

function clearFilters() {
    currentFilters = {
        category: '',
        type: '',
        minPrice: '',
        maxPrice: '',
        distance: 25,
        tags: [],
        sortBy: 'relevance',
        page: 1
    };
    
    // Reset form elements
    document.getElementById('categoryFilter').value = '';
    document.querySelectorAll('input[name="typeFilter"]').forEach(radio => {
        radio.checked = false;
    });
    document.querySelectorAll('input[name="typeFilter"]')[0].checked = true;
    document.getElementById('minPrice').value = '';
    document.getElementById('maxPrice').value = '';
    document.getElementById('distanceFilter').value = 25;
    document.getElementById('distanceValue').textContent = '25 km';
    document.getElementById('sortBy').value = 'relevance';
    
    // Reset tag filters
    document.querySelectorAll('.filter-tag').forEach(tag => {
        tag.classList.remove('is-info', 'is-success', 'is-warning', 'is-danger');
        tag.classList.add('is-light');
    });
    
    // Apply cleared filters
    applyFilters();
}

function showLoading(show) {
    const spinner = document.getElementById('loadingSpinner');
    const container = document.getElementById('listingsContainer');
    
    if (show) {
        spinner.style.display = 'block';
        container.style.display = 'none';
    } else {
        spinner.style.display = 'none';
        container.style.display = 'block';
    }
}
</script>

<style>
.card-image-overlay {
    position: absolute;
    top: 10px;
    right: 10px;
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.listing-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    height: 100%;
}

.listing-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.listing-image {
    object-fit: cover;
    width: 100%;
    height: 100%;
}

.stars {
    display: flex;
    align-items: center;
    gap: 5px;
}

.filter-tag {
    cursor: pointer;
    transition: all 0.2s ease;
}

.filter-tag:hover {
    transform: scale(1.05);
}

#distanceValue {
    font-weight: bold;
    color: var(--primary-color);
}

.pagination-link.is-current {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.button.is-info {
    background-color: var(--info-color);
    border-color: var(--info-color);
}

@media screen and (max-width: 768px) {
    .column.is-4 {
        width: 100%;
    }
    
    .level {
        flex-direction: column;
        align-items: stretch;
    }
    
    .level-left, .level-right {
        justify-content: center;
        margin-bottom: 1rem;
    }
}
</style>