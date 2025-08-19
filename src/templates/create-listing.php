<?php
/**
 * Create Listing Template
 * Form for creating new product and service listings
 */

// Ensure this file is included, not accessed directly
if (!defined('SECURE_ACCESS')) {
    http_response_code(403);
    exit('Access denied');
}

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: /login');
    exit;
}

// Get current user
$currentUser = getCurrentUser();
if (!$currentUser) {
    header('Location: /login');
    exit;
}
?>

<div class="container">
    <!-- Page Header -->
    <section class="hero is-light is-small">
        <div class="hero-body">
            <div class="container">
                <h1 class="title">
                    <i class="fas fa-plus mr-2"></i>
                    Create New Listing
                </h1>
                <p class="subtitle">
                    Add your product or service to the marketplace
                </p>
            </div>
        </div>
    </section>

    <!-- Create Listing Form -->
    <div class="columns mt-4">
        <div class="column is-8">
            <div class="card">
                <header class="card-header">
                    <p class="card-header-title">
                        <i class="fas fa-edit mr-2"></i>
                        Listing Information
                    </p>
                </header>
                <div class="card-content">
                    <form id="createListingForm" enctype="multipart/form-data">
                        <!-- CSRF Token -->
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <!-- Listing Type Selection -->
                        <div class="field">
                            <label class="label">Listing Type *</label>
                            <div class="control">
                                <div class="field">
                                    <label class="radio">
                                        <input type="radio" name="listing_type" value="product" checked>
                                        Product
                                    </label>
                                    <label class="radio">
                                        <input type="radio" name="listing_type" value="service">
                                        Service
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Title -->
                        <div class="field">
                            <label class="label">Title *</label>
                            <div class="control">
                                <input class="input" type="text" name="title" id="title" 
                                       placeholder="Enter a descriptive title for your listing" 
                                       maxlength="100" required>
                            </div>
                            <p class="help">Maximum 100 characters. Be descriptive and specific.</p>
                        </div>

                        <!-- Category -->
                        <div class="field">
                            <label class="label">Category *</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select name="category" id="category" required>
                                        <option value="">Select a category</option>
                                        <optgroup label="Products">
                                            <option value="electronics">Electronics</option>
                                            <option value="home">Home & Garden</option>
                                            <option value="automotive">Automotive</option>
                                            <option value="fashion">Fashion & Beauty</option>
                                            <option value="sports">Sports & Recreation</option>
                                        </optgroup>
                                        <optgroup label="Services">
                                            <option value="professional">Professional Services</option>
                                            <option value="home-services">Home Services</option>
                                            <option value="health">Health & Wellness</option>
                                            <option value="education">Education & Training</option>
                                            <option value="entertainment">Entertainment</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="field">
                            <label class="label">Description *</label>
                            <div class="control">
                                <textarea class="textarea" name="description" id="description" 
                                          placeholder="Provide a detailed description of your listing. Include features, specifications, condition, and any other relevant details." 
                                          rows="6" maxlength="1000" required></textarea>
                            </div>
                            <p class="help">Maximum 1000 characters. Be detailed and honest about your listing.</p>
                            <div class="has-text-right">
                                <span id="descriptionCount" class="has-text-grey">0/1000</span>
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="field">
                            <label class="label">Price *</label>
                            <div class="control has-icons-left">
                                <span class="icon is-small is-left">
                                    <i class="fas fa-dollar-sign"></i>
                                </span>
                                <input class="input" type="number" name="price" id="price" 
                                       placeholder="0.00" min="0" step="0.01" required>
                            </div>
                            <p class="help">Enter the price in USD. Use 0 for free items.</p>
                        </div>

                        <!-- Product-specific fields -->
                        <div id="productFields" style="display: none;">
                            <div class="field">
                                <label class="label">Condition</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select name="condition" id="condition">
                                            <option value="">Select condition</option>
                                            <option value="new">New</option>
                                            <option value="like_new">Like New</option>
                                            <option value="excellent">Excellent</option>
                                            <option value="good">Good</option>
                                            <option value="fair">Fair</option>
                                            <option value="poor">Poor</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Brand</label>
                                <div class="control">
                                    <input class="input" type="text" name="brand" id="brand" 
                                           placeholder="Brand name (optional)">
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Model</label>
                                <div class="control">
                                    <input class="input" type="text" name="model" id="model" 
                                           placeholder="Model number/name (optional)">
                                </div>
                            </div>
                        </div>

                        <!-- Service-specific fields -->
                        <div id="serviceFields" style="display: none;">
                            <div class="field">
                                <label class="label">Service Duration</label>
                                <div class="control">
                                    <div class="field has-addons">
                                        <div class="control">
                                            <input class="input" type="number" name="duration" id="duration" 
                                                   placeholder="Duration" min="1">
                                        </div>
                                        <div class="control">
                                            <div class="select">
                                                <select name="duration_unit" id="duration_unit">
                                                    <option value="hours">Hours</option>
                                                    <option value="days">Days</option>
                                                    <option value="weeks">Weeks</option>
                                                    <option value="months">Months</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Service Area</label>
                                <div class="control">
                                    <input class="input" type="number" name="service_radius" id="service_radius" 
                                           placeholder="Service radius in km" min="1" max="100">
                                </div>
                                <p class="help">How far are you willing to travel for this service?</p>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="field">
                            <label class="label">Tags</label>
                            <div class="control">
                                <input class="input" type="text" name="tags" id="tags" 
                                       placeholder="Enter tags separated by commas (e.g., electronics, gadgets, tech)">
                            </div>
                            <p class="help">Add relevant tags to help buyers find your listing. Maximum 5 tags.</p>
                            <div class="tags" id="tagDisplay"></div>
                        </div>

                        <!-- Images -->
                        <div class="field">
                            <label class="label">Images</label>
                            <div class="control">
                                <div class="file has-name is-fullwidth">
                                    <label class="file-label">
                                        <input class="file-input" type="file" id="imageUpload" 
                                               accept="image/*" multiple>
                                        <span class="file-cta">
                                            <span class="file-icon">
                                                <i class="fas fa-upload"></i>
                                            </span>
                                            <span class="file-label">
                                                Choose images...
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <p class="help">Upload up to 5 images. Supported formats: JPG, PNG, WebP. Maximum 5MB per image.</p>
                            
                            <!-- Image Preview -->
                            <div id="imagePreview" class="columns is-multiline mt-3" style="display: none;">
                                <!-- Image previews will be displayed here -->
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="field">
                            <label class="label">Location *</label>
                            <div class="control">
                                <div class="field has-addons">
                                    <div class="control is-expanded">
                                        <input class="input" type="text" name="address" id="address" 
                                               placeholder="Enter your address or location" required>
                                    </div>
                                    <div class="control">
                                        <button class="button" type="button" id="detectLocation">
                                            <i class="fas fa-location-arrow"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <p class="help">Your location helps buyers find listings near them.</p>
                        </div>

                        <!-- Hidden coordinates -->
                        <input type="hidden" name="latitude" id="latitude" value="">
                        <input type="hidden" name="longitude" id="longitude" value="">

                        <!-- Contact Information -->
                        <div class="field">
                            <label class="label">Contact Phone (Optional)</label>
                            <div class="control has-icons-left">
                                <span class="icon is-small is-left">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <input class="input" type="tel" name="contact_phone" id="contact_phone" 
                                       placeholder="Phone number for direct contact">
                            </div>
                        </div>

                        <div class="field">
                            <label class="label">Contact Email (Optional)</label>
                            <div class="control has-icons-left">
                                <span class="icon is-small is-left">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input class="input" type="email" name="contact_email" id="contact_email" 
                                       placeholder="Email for direct contact">
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="field">
                            <div class="control">
                                <label class="checkbox">
                                    <input type="checkbox" name="terms_accepted" id="terms_accepted" required>
                                    I agree to the <a href="/terms" target="_blank">Terms and Conditions</a> and 
                                    <a href="/privacy" target="_blank">Privacy Policy</a>
                                </label>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="field is-grouped">
                            <div class="control">
                                <button type="submit" class="button is-primary" id="submitButton">
                                    <i class="fas fa-save mr-2"></i>
                                    Create Listing
                                </button>
                            </div>
                            <div class="control">
                                <button type="button" class="button is-light" id="saveDraftButton">
                                    <i class="fas fa-save mr-2"></i>
                                    Save as Draft
                                </button>
                            </div>
                            <div class="control">
                                <a href="/dashboard" class="button is-light">
                                    <i class="fas fa-times mr-2"></i>
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Sidebar - Tips and Guidelines -->
        <div class="column is-4">
            <!-- Tips Card -->
            <div class="card">
                <header class="card-header">
                    <p class="card-header-title">
                        <i class="fas fa-lightbulb mr-2"></i>
                        Tips for Great Listings
                    </p>
                </header>
                <div class="card-content">
                    <div class="content">
                        <ul class="listing-tips">
                            <li><strong>Clear Title:</strong> Be specific and descriptive</li>
                            <li><strong>Quality Photos:</strong> Use good lighting and multiple angles</li>
                            <li><strong>Honest Description:</strong> Include all relevant details and any flaws</li>
                            <li><strong>Fair Pricing:</strong> Research similar items in your area</li>
                            <li><strong>Quick Response:</strong> Reply to buyer inquiries promptly</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Guidelines Card -->
            <div class="card mt-4">
                <header class="card-header">
                    <p class="card-header-title">
                        <i class="fas fa-info-circle mr-2"></i>
                        Listing Guidelines
                    </p>
                </header>
                <div class="card-content">
                    <div class="content">
                        <ul class="listing-guidelines">
                            <li>No counterfeit or illegal items</li>
                            <li>Accurate descriptions required</li>
                            <li>Clear, high-quality images</li>
                            <li>Reasonable pricing expected</li>
                            <li>Prompt communication with buyers</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Progress Card -->
            <div class="card mt-4">
                <header class="card-header">
                    <p class="card-header-title">
                        <i class="fas fa-tasks mr-2"></i>
                        Completion Progress
                    </p>
                </header>
                <div class="card-content">
                    <progress class="progress is-primary" value="0" max="100" id="completionProgress">0%</progress>
                    <p class="has-text-centered mt-2">
                        <span id="completionText">0% Complete</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Camera Capture Modal -->
<div class="modal" id="cameraModal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">
                <i class="fas fa-camera mr-2"></i>
                Take Photo
            </p>
            <button class="delete" aria-label="close" id="closeCameraModal"></button>
        </header>
        <section class="modal-card-body">
            <div class="has-text-centered">
                <video id="cameraVideo" autoplay playsinline style="width: 100%; max-width: 400px;"></video>
                <canvas id="cameraCanvas" style="display: none;"></canvas>
            </div>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-primary" id="capturePhoto">
                <i class="fas fa-camera mr-2"></i>
                Capture Photo
            </button>
            <button class="button is-light" id="retakePhoto" style="display: none;">
                <i class="fas fa-redo mr-2"></i>
                Retake
            </button>
            <button class="button is-light" id="usePhoto" style="display: none;">
                <i class="fas fa-check mr-2"></i>
                Use Photo
            </button>
        </footer>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeCreateListing();
});

let selectedImages = [];
let currentStream = null;

function initializeCreateListing() {
    setupFormHandlers();
    setupImageHandlers();
    setupLocationHandlers();
    setupProgressTracking();
    setupTypeToggle();
}

function setupFormHandlers() {
    const form = document.getElementById('createListingForm');
    const submitButton = document.getElementById('submitButton');
    const saveDraftButton = document.getElementById('saveDraftButton');
    
    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        handleFormSubmission();
    });
    
    // Save draft
    saveDraftButton.addEventListener('click', function() {
        handleSaveDraft();
    });
    
    // Description character count
    const description = document.getElementById('description');
    const descriptionCount = document.getElementById('descriptionCount');
    
    description.addEventListener('input', function() {
        const remaining = 1000 - this.value.length;
        descriptionCount.textContent = `${this.value.length}/1000`;
        
        if (remaining < 100) {
            descriptionCount.classList.add('has-text-danger');
        } else {
            descriptionCount.classList.remove('has-text-danger');
        }
    });
    
    // Tags handling
    const tagsInput = document.getElementById('tags');
    const tagDisplay = document.getElementById('tagDisplay');
    
    tagsInput.addEventListener('input', function() {
        const tags = this.value.split(',').map(tag => tag.trim()).filter(tag => tag.length > 0);
        displayTags(tags);
    });
}

function setupImageHandlers() {
    const imageUpload = document.getElementById('imageUpload');
    const imagePreview = document.getElementById('imagePreview');
    
    imageUpload.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        handleImageSelection(files);
    });
}

function setupLocationHandlers() {
    const detectLocationBtn = document.getElementById('detectLocation');
    
    detectLocationBtn.addEventListener('click', function() {
        detectUserLocation();
    });
}

function setupProgressTracking() {
    const form = document.getElementById('createListingForm');
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('input', updateProgress);
        input.addEventListener('change', updateProgress);
    });
    
    // Initial progress update
    updateProgress();
}

function setupTypeToggle() {
    const typeRadios = document.querySelectorAll('input[name="listing_type"]');
    const productFields = document.getElementById('productFields');
    const serviceFields = document.getElementById('serviceFields');
    
    typeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'product') {
                productFields.style.display = 'block';
                serviceFields.style.display = 'none';
            } else {
                productFields.style.display = 'none';
                serviceFields.style.display = 'block';
            }
            updateProgress();
        });
    });
}

function handleImageSelection(files) {
    if (selectedImages.length + files.length > 5) {
        showNotification('Maximum 5 images allowed', 'warning');
        return;
    }
    
    files.forEach(file => {
        if (file.size > 5 * 1024 * 1024) { // 5MB
            showNotification(`Image ${file.name} is too large. Maximum size is 5MB.`, 'warning');
            return;
        }
        
        if (!file.type.startsWith('image/')) {
            showNotification(`File ${file.name} is not an image.`, 'warning');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const imageData = {
                file: file,
                preview: e.target.result,
                id: Date.now() + Math.random()
            };
            
            selectedImages.push(imageData);
            displayImagePreview(imageData);
            updateProgress();
        };
        reader.readAsDataURL(file);
    });
}

function displayImagePreview(imageData) {
    const imagePreview = document.getElementById('imagePreview');
    const imageContainer = document.createElement('div');
    imageContainer.className = 'column is-4';
    imageContainer.dataset.imageId = imageData.id;
    
    imageContainer.innerHTML = `
        <div class="card">
            <div class="card-image">
                <figure class="image is-4by3">
                    <img src="${imageData.preview}" alt="Preview">
                </figure>
            </div>
            <div class="card-content">
                <div class="buttons">
                    <button class="button is-small is-danger" onclick="removeImage('${imageData.id}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    imagePreview.appendChild(imageContainer);
    imagePreview.style.display = 'block';
}

function removeImage(imageId) {
    selectedImages = selectedImages.filter(img => img.id !== imageId);
    const container = document.querySelector(`[data-image-id="${imageId}"]`);
    if (container) {
        container.remove();
    }
    
    if (selectedImages.length === 0) {
        document.getElementById('imagePreview').style.display = 'none';
    }
    
    updateProgress();
}

function displayTags(tags) {
    const tagDisplay = document.getElementById('tagDisplay');
    tagDisplay.innerHTML = '';
    
    tags.slice(0, 5).forEach(tag => {
        const tagElement = document.createElement('span');
        tagElement.className = 'tag is-info is-light';
        tagElement.textContent = tag;
        tagDisplay.appendChild(tagElement);
    });
    
    if (tags.length > 5) {
        const warningElement = document.createElement('span');
        warningElement.className = 'tag is-warning is-light';
        warningElement.textContent = `+${tags.length - 5} more`;
        tagDisplay.appendChild(warningElement);
    }
}

function detectUserLocation() {
    if (!navigator.geolocation) {
        showNotification('Geolocation is not supported by your browser', 'warning');
        return;
    }
    
    const detectBtn = document.getElementById('detectLocation');
    detectBtn.classList.add('is-loading');
    
    navigator.geolocation.getCurrentPosition(
        function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            
            // Reverse geocoding to get address
            reverseGeocode(lat, lng);
            
            detectBtn.classList.remove('is-loading');
            showNotification('Location detected successfully!', 'success');
        },
        function(error) {
            detectBtn.classList.remove('is-loading');
            let errorMessage = 'Unable to detect location';
            
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    errorMessage = 'Location permission denied';
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMessage = 'Location information unavailable';
                    break;
                case error.TIMEOUT:
                    errorMessage = 'Location request timed out';
                    break;
            }
            
            showNotification(errorMessage, 'warning');
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 60000
        }
    );
}

function reverseGeocode(lat, lng) {
    // TODO: Implement reverse geocoding with Google Maps API
    // For now, just show coordinates
    document.getElementById('address').value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
}

function updateProgress() {
    const form = document.getElementById('createListingForm');
    const requiredFields = form.querySelectorAll('[required]');
    const filledFields = Array.from(requiredFields).filter(field => {
        if (field.type === 'checkbox') {
            return field.checked;
        }
        return field.value.trim().length > 0;
    });
    
    const progress = Math.round((filledFields.length / requiredFields.length) * 100);
    const progressBar = document.getElementById('completionProgress');
    const progressText = document.getElementById('completionText');
    
    progressBar.value = progress;
    progressText.textContent = `${progress}% Complete`;
    
    // Update progress bar color
    if (progress < 50) {
        progressBar.classList.remove('is-success', 'is-warning');
        progressBar.classList.add('is-danger');
    } else if (progress < 100) {
        progressBar.classList.remove('is-danger', 'is-success');
        progressBar.classList.add('is-warning');
    } else {
        progressBar.classList.remove('is-danger', 'is-warning');
        progressBar.classList.add('is-success');
    }
}

function handleFormSubmission() {
    const form = document.getElementById('createListingForm');
    const formData = new FormData(form);
    
    // Add selected images
    selectedImages.forEach((image, index) => {
        formData.append(`images[${index}]`, image.file);
    });
    
    // Validate form
    if (!validateForm(formData)) {
        return;
    }
    
    // Show loading state
    const submitButton = document.getElementById('submitButton');
    const originalText = submitButton.innerHTML;
    submitButton.classList.add('is-loading');
    submitButton.disabled = true;
    
    // Submit form
    fetch('/api/listings/create', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Listing created successfully!', 'success');
            setTimeout(() => {
                window.location.href = `/listing?id=${data.listing_id}`;
            }, 1500);
        } else {
            showNotification(data.message || 'Failed to create listing', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while creating the listing', 'danger');
    })
    .finally(() => {
        submitButton.classList.remove('is-loading');
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    });
}

function handleSaveDraft() {
    const form = document.getElementById('createListingForm');
    const formData = new FormData(form);
    formData.append('status', 'draft');
    
    // TODO: Implement draft saving functionality
    showNotification('Draft saving functionality coming soon!', 'info');
}

function validateForm(formData) {
    const title = formData.get('title');
    const description = formData.get('description');
    const price = formData.get('price');
    const category = formData.get('category');
    const address = formData.get('address');
    
    if (!title || title.trim().length < 10) {
        showNotification('Title must be at least 10 characters long', 'warning');
        return false;
    }
    
    if (!description || description.trim().length < 50) {
        showNotification('Description must be at least 50 characters long', 'warning');
        return false;
    }
    
    if (!price || parseFloat(price) < 0) {
        showNotification('Please enter a valid price', 'warning');
        return false;
    }
    
    if (!category) {
        showNotification('Please select a category', 'warning');
        return false;
    }
    
    if (!address || address.trim().length < 5) {
        showNotification('Please enter a valid address', 'warning');
        return false;
    }
    
    if (selectedImages.length === 0) {
        showNotification('Please upload at least one image', 'warning');
        return false;
    }
    
    return true;
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
.listing-tips, .listing-guidelines {
    list-style: none;
    padding: 0;
}

.listing-tips li, .listing-guidelines li {
    margin-bottom: 0.5rem;
    padding-left: 1rem;
    position: relative;
}

.listing-tips li:before {
    content: "✓";
    color: var(--success-color);
    font-weight: bold;
    position: absolute;
    left: 0;
}

.listing-guidelines li:before {
    content: "•";
    color: var(--info-color);
    font-weight: bold;
    position: absolute;
    left: 0;
}

#imagePreview .card {
    margin-bottom: 0;
}

#imagePreview .card-image img {
    object-fit: cover;
    width: 100%;
    height: 100%;
}

.progress {
    height: 8px;
}

.field.has-addons .control:not(:last-child) .button,
.field.has-addons .control:not(:last-child) .input,
.field.has-addons .control:not(:last-child) .select select {
    border-right: 0;
}

.field.has-addons .control:not(:first-child) .button,
.field.has-addons .control:not(:first-child) .input,
.field.has-addons .control:not(:first-child) .select select {
    border-left: 0;
}

@media screen and (max-width: 768px) {
    .column.is-8, .column.is-4 {
        width: 100%;
    }
    
    .field.is-grouped {
        flex-direction: column;
    }
    
    .field.is-grouped .control {
        margin-bottom: 0.5rem;
    }
    
    .field.is-grouped .control:last-child {
        margin-bottom: 0;
    }
}
</style>