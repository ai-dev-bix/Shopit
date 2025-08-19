<!-- Search Page -->
<section class="section">
    <div class="container">
        <!-- Search Header -->
        <div class="columns is-centered mb-6">
            <div class="column is-8">
                <div class="notification is-info is-light">
                    <h1 class="title is-3">
                        <i class="fas fa-search mr-2"></i>
                        Find Products & Services
                    </h1>
                    <p class="subtitle is-6">
                        Discover amazing local offerings near you
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Search Form -->
        <div class="columns is-centered mb-6">
            <div class="column is-10">
                <div class="card">
                    <div class="card-content">
                        <form id="searchForm">
                            <div class="columns is-multiline">
                                <div class="column is-4">
                                    <div class="field">
                                        <label class="label" for="searchQuery">What are you looking for?</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="text" id="searchQuery" name="q" 
                                                   placeholder="e.g., laptop, plumbing service, furniture">
                                            <span class="icon is-small is-left">
                                                <i class="fas fa-search"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="column is-3">
                                    <div class="field">
                                        <label class="label" for="category">Category</label>
                                        <div class="control">
                                            <div class="select is-fullwidth">
                                                <select id="category" name="category">
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
                                </div>
                                
                                <div class="column is-3">
                                    <div class="field">
                                        <label class="label" for="listingType">Type</label>
                                        <div class="control">
                                            <div class="select is-fullwidth">
                                                <select id="listingType" name="type">
                                                    <option value="">All Types</option>
                                                    <option value="product">Products</option>
                                                    <option value="service">Services</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="column is-2">
                                    <div class="field">
                                        <label class="label">&nbsp;</label>
                                        <div class="control">
                                            <button type="submit" class="button is-primary is-fullwidth">
                                                <span class="icon">
                                                    <i class="fas fa-search"></i>
                                                </span>
                                                <span>Search</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Advanced Search Options -->
                            <div class="columns is-multiline mt-4" id="advancedOptions" style="display: none;">
                                <div class="column is-3">
                                    <div class="field">
                                        <label class="label" for="priceMin">Price Range</label>
                                        <div class="control">
                                            <input class="input" type="number" id="priceMin" name="price_min" 
                                                   placeholder="Min" min="0" step="0.01">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="column is-3">
                                    <div class="field">
                                        <label class="label">&nbsp;</label>
                                        <div class="control">
                                            <input class="input" type="number" id="priceMax" name="price_max" 
                                                   placeholder="Max" min="0" step="0.01">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="column is-3">
                                    <div class="field">
                                        <label class="label" for="radius">Distance (km)</label>
                                        <div class="control">
                                            <div class="select is-fullwidth">
                                                <select id="radius" name="radius">
                                                    <option value="5">5 km</option>
                                                    <option value="10">10 km</option>
                                                    <option value="25" selected>25 km</option>
                                                    <option value="50">50 km</option>
                                                    <option value="100">100 km</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="column is-3">
                                    <div class="field">
                                        <label class="label">&nbsp;</label>
                                        <div class="control">
                                            <button type="button" class="button is-info is-fullwidth" id="getLocationBtn">
                                                <span class="icon">
                                                    <i class="fas fa-location-arrow"></i>
                                                </span>
                                                <span>Use My Location</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="field has-text-centered">
                                <button type="button" class="button is-text" id="toggleAdvanced">
                                    <span class="icon">
                                        <i class="fas fa-chevron-down"></i>
                                    </span>
                                    <span>Advanced Search Options</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Search Results -->
        <div class="columns is-centered">
            <div class="column is-12">
                <div id="searchResults">
                    <div class="has-text-centered py-6">
                        <div class="has-text-grey mb-3">
                            <i class="fas fa-search fa-3x"></i>
                        </div>
                        <p class="title is-5">Ready to Search</p>
                        <p class="subtitle is-6">Enter your search criteria above to find products and services</p>
                    </div>
                </div>
                
                <!-- Loading State -->
                <div id="loadingState" style="display: none;">
                    <div class="has-text-centered py-6">
                        <div class="loading-spinner mb-3"></div>
                        <p class="title is-5">Searching...</p>
                        <p class="subtitle is-6">Please wait while we find the best matches for you</p>
                    </div>
                </div>
                
                <!-- No Results State -->
                <div id="noResults" style="display: none;">
                    <div class="has-text-centered py-6">
                        <div class="has-text-grey mb-3">
                            <i class="fas fa-search fa-3x"></i>
                        </div>
                        <p class="title is-5">No Results Found</p>
                        <p class="subtitle is-6">Try adjusting your search criteria or browse all categories</p>
                        <div class="mt-4">
                            <a href="/browse" class="button is-primary">
                                <span class="icon">
                                    <i class="fas fa-th-large"></i>
                                </span>
                                <span>Browse All Categories</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Popular Categories -->
        <div class="columns is-centered mt-6">
            <div class="column is-10">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            <i class="fas fa-fire mr-2"></i>
                            Popular Categories
                        </p>
                    </header>
                    
                    <div class="card-content">
                        <div class="columns is-multiline">
                            <div class="column is-2">
                                <a href="/search?category=electronics" class="button is-fullwidth is-outlined">
                                    <span class="icon">
                                        <i class="fas fa-laptop"></i>
                                    </span>
                                    <span>Electronics</span>
                                </a>
                            </div>
                            
                            <div class="column is-2">
                                <a href="/search?category=home" class="button is-fullwidth is-outlined">
                                    <span class="icon">
                                        <i class="fas fa-home"></i>
                                    </span>
                                    <span>Home & Garden</span>
                                </a>
                            </div>
                            
                            <div class="column is-2">
                                <a href="/search?category=automotive" class="button is-fullwidth is-outlined">
                                    <span class="icon">
                                        <i class="fas fa-car"></i>
                                    </span>
                                    <span>Automotive</span>
                                </a>
                            </div>
                            
                            <div class="column is-2">
                                <a href="/search?category=fashion" class="button is-fullwidth is-outlined">
                                    <span class="icon">
                                        <i class="fas fa-tshirt"></i>
                                    </span>
                                    <span>Fashion</span>
                                </a>
                            </div>
                            
                            <div class="column is-2">
                                <a href="/search?category=sports" class="button is-fullwidth is-outlined">
                                    <span class="icon">
                                        <i class="fas fa-futbol"></i>
                                    </span>
                                    <span>Sports</span>
                                </a>
                            </div>
                            
                            <div class="column is-2">
                                <a href="/search?category=professional" class="button is-fullwidth is-outlined">
                                    <span class="icon">
                                        <i class="fas fa-briefcase"></i>
                                    </span>
                                    <span>Services</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    const toggleAdvanced = document.getElementById('toggleAdvanced');
    const advancedOptions = document.getElementById('advancedOptions');
    const getLocationBtn = document.getElementById('getLocationBtn');
    const searchResults = document.getElementById('searchResults');
    const loadingState = document.getElementById('loadingState');
    const noResults = document.getElementById('noResults');
    
    // Toggle advanced search options
    toggleAdvanced.addEventListener('click', function() {
        const isVisible = advancedOptions.style.display !== 'none';
        advancedOptions.style.display = isVisible ? 'none' : 'block';
        
        const icon = this.querySelector('i');
        if (isVisible) {
            icon.className = 'fas fa-chevron-down';
            this.querySelector('span:last-child').textContent = 'Advanced Search Options';
        } else {
            icon.className = 'fas fa-chevron-up';
            this.querySelector('span:last-child').textContent = 'Hide Advanced Options';
        }
    });
    
    // Get current location
    getLocationBtn.addEventListener('click', function() {
        if (navigator.geolocation) {
            getLocationBtn.classList.add('is-loading');
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    // Store coordinates for search
                    localStorage.setItem('userLat', position.coords.latitude);
                    localStorage.setItem('userLng', position.coords.longitude);
                    
                    getLocationBtn.classList.remove('is-loading');
                    showNotification('Location updated successfully!', 'success');
                },
                function(error) {
                    getLocationBtn.classList.remove('is-loading');
                    showNotification('Could not get your location. Please enter coordinates manually.', 'warning');
                }
            );
        } else {
            showNotification('Geolocation is not supported by this browser.', 'warning');
        }
    });
    
    // Form submission
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(searchForm);
        const searchParams = new URLSearchParams();
        
        // Convert form data to search parameters
        for (let [key, value] of formData.entries()) {
            if (value) {
                searchParams.append(key, value);
            }
        }
        
        // Add user location if available
        const userLat = localStorage.getItem('userLat');
        const userLng = localStorage.getItem('userLng');
        if (userLat && userLng) {
            searchParams.append('user_lat', userLat);
            searchParams.append('user_lng', userLng);
        }
        
        // Perform search
        performSearch(searchParams.toString());
    });
    
    // Check for URL parameters on page load
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('q') || urlParams.has('category') || urlParams.has('type')) {
        // Pre-fill form with URL parameters
        if (urlParams.has('q')) {
            document.getElementById('searchQuery').value = urlParams.get('q');
        }
        if (urlParams.has('category')) {
            document.getElementById('category').value = urlParams.get('category');
        }
        if (urlParams.has('type')) {
            document.getElementById('listingType').value = urlParams.get('type');
        }
        
        // Auto-submit search
        searchForm.dispatchEvent(new Event('submit'));
    }
    
    function performSearch(searchParams) {
        // Show loading state
        searchResults.style.display = 'none';
        noResults.style.display = 'none';
        loadingState.style.display = 'block';
        
        // Update URL with search parameters
        const newUrl = `/search?${searchParams}`;
        window.history.pushState({}, '', newUrl);
        
        // Perform search
        fetch(`/api/search?${searchParams}`)
        .then(response => response.json())
        .then(data => {
            loadingState.style.display = 'none';
            
            if (data.success && data.results && data.results.length > 0) {
                displaySearchResults(data.results);
            } else {
                noResults.style.display = 'block';
            }
        })
        .catch(error => {
            loadingState.style.display = 'none';
            noResults.style.display = 'block';
            console.error('Search error:', error);
            showNotification('An error occurred during search. Please try again.', 'danger');
        });
    }
    
    function displaySearchResults(results) {
        searchResults.style.display = 'block';
        
        let html = '<div class="columns is-multiline">';
        
        results.forEach(result => {
            html += `
                <div class="column is-4">
                    <div class="card">
                        <div class="card-image">
                            <figure class="image is-4by3">
                                <img src="${result.image_url || '/assets/images/placeholder.jpg'}" 
                                     alt="${result.title}" 
                                     onerror="this.src='/assets/images/placeholder.jpg'">
                            </figure>
                        </div>
                        
                        <div class="card-content">
                            <p class="title is-5">${result.title}</p>
                            <p class="subtitle is-6 has-text-grey">${result.category_name}</p>
                            
                            <div class="content">
                                <p class="is-size-7">${result.description.substring(0, 100)}${result.description.length > 100 ? '...' : ''}</p>
                                
                                <div class="columns is-multiline is-mobile mt-3">
                                    <div class="column is-6">
                                        <span class="tag is-${result.type === 'product' ? 'info' : 'success'} is-small">
                                            ${result.type === 'product' ? 'Product' : 'Service'}
                                        </span>
                                    </div>
                                    <div class="column is-6 has-text-right">
                                        <span class="has-text-weight-bold has-text-primary">
                                            ${formatCurrency(result.price)}
                                        </span>
                                    </div>
                                </div>
                                
                                ${result.distance ? `
                                    <div class="mt-2">
                                        <small class="has-text-grey">
                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                            ${result.distance.toFixed(1)} km away
                                        </small>
                                    </div>
                                ` : ''}
                            </div>
                            
                            <div class="card-footer">
                                <a href="/listing?id=${result.id}" class="card-footer-item button is-primary is-small">
                                    <span class="icon">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                    <span>View Details</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        
        // Add pagination if needed
        if (results.length >= 20) {
            html += `
                <div class="has-text-centered mt-6">
                    <button class="button is-primary" onclick="loadMoreResults()">
                        <span class="icon">
                            <i class="fas fa-plus"></i>
                        </span>
                        <span>Load More Results</span>
                    </button>
                </div>
            `;
        }
        
        searchResults.innerHTML = html;
    }
    
    function loadMoreResults() {
        // Implementation for pagination
        showNotification('Pagination feature coming soon!', 'info');
    }
    
    function showNotification(message, type) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification is-${type} is-light`;
        notification.innerHTML = `
            <button class="delete" onclick="this.parentElement.remove();"></button>
            ${message}
        `;
        
        // Insert at top of page
        const container = document.querySelector('.container');
        container.insertBefore(notification, container.firstChild);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }
    
    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    }
});
</script>