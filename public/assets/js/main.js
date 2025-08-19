/**
 * Main JavaScript File for Location-Based Marketplace Platform
 */

// Global configuration
const CONFIG = {
    API_BASE_URL: '/api',
    MAP_DEFAULT_ZOOM: 12,
    MAP_DEFAULT_CENTER: { lat: 40.7128, lng: -74.0060 },
    SEARCH_DEBOUNCE_MS: 300,
    IMAGE_MAX_SIZE: 5 * 1024 * 1024, // 5MB
    SUPPORTED_IMAGE_TYPES: ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']
};

// Main application class
class MarketplaceApp {
    constructor() {
        this.currentUser = null;
        this.currentLocation = null;
        this.map = null;
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.initializeComponents();
        this.loadUserData();
        this.setupServiceWorker();
    }
    
    setupEventListeners() {
        // Mobile navigation toggle
        document.addEventListener('DOMContentLoaded', () => {
            this.setupMobileNavigation();
            this.setupBackToTop();
            this.setupFlashMessages();
            this.setupFormValidation();
            this.setupModals();
            this.setupTooltips();
        });
        
        // Global event listeners
        document.addEventListener('click', this.handleGlobalClick.bind(this));
        window.addEventListener('scroll', this.handleScroll.bind(this));
        window.addEventListener('resize', this.handleResize.bind(this));
        
        // Form submissions
        document.addEventListener('submit', this.handleFormSubmit.bind(this));
        
        // AJAX error handling
        window.addEventListener('error', this.handleGlobalError.bind(this));
    }
    
    setupMobileNavigation() {
        const navbarBurgers = document.querySelectorAll('.navbar-burger');
        const navbarMenus = document.querySelectorAll('.navbar-menu');
        
        navbarBurgers.forEach(burger => {
            burger.addEventListener('click', () => {
                const target = burger.dataset.target;
                const targetMenu = document.getElementById(target);
                
                burger.classList.toggle('is-active');
                targetMenu.classList.toggle('is-active');
            });
        });
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.navbar')) {
                navbarMenus.forEach(menu => menu.classList.remove('is-active'));
                navbarBurgers.forEach(burger => burger.classList.remove('is-active'));
            }
        });
    }
    
    setupBackToTop() {
        const backToTopButton = document.getElementById('backToTop');
        if (!backToTopButton) return;
        
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopButton.style.display = 'block';
            } else {
                backToTopButton.style.display = 'none';
            }
        });
        
        backToTopButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    setupFlashMessages() {
        const notifications = document.querySelectorAll('.notification');
        notifications.forEach(notification => {
            // Auto-hide after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        if (notification.parentElement) {
                            notification.remove();
                        }
                    }, 300);
                }
            }, 5000);
            
            // Manual close button
            const closeButton = notification.querySelector('.delete');
            if (closeButton) {
                closeButton.addEventListener('click', () => {
                    notification.remove();
                });
            }
        });
    }
    
    setupFormValidation() {
        const forms = document.querySelectorAll('form[data-validate]');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                    return false;
                }
            });
            
            // Real-time validation
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('blur', () => {
                    this.validateField(input);
                });
                
                input.addEventListener('input', () => {
                    this.clearFieldError(input);
                });
            });
        });
    }
    
    setupModals() {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            const triggers = document.querySelectorAll(`[data-target="${modal.id}"]`);
            const closeButtons = modal.querySelectorAll('.delete, .modal-background');
            
            // Open modal
            triggers.forEach(trigger => {
                trigger.addEventListener('click', () => {
                    this.openModal(modal);
                });
            });
            
            // Close modal
            closeButtons.forEach(button => {
                button.addEventListener('click', () => {
                    this.closeModal(modal);
                });
            });
            
            // Close with Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && modal.classList.contains('is-active')) {
                    this.closeModal(modal);
                }
            });
        });
    }
    
    setupTooltips() {
        const tooltipElements = document.querySelectorAll('[data-tooltip]');
        tooltipElements.forEach(element => {
            element.addEventListener('mouseenter', (e) => {
                this.showTooltip(e.target, e.target.dataset.tooltip);
            });
            
            element.addEventListener('mouseleave', () => {
                this.hideTooltip();
            });
        });
    }
    
    initializeComponents() {
        // Initialize any components that need setup
        this.initializeLocationServices();
        this.initializeImageHandling();
        this.initializeSearch();
    }
    
    initializeLocationServices() {
        if ('geolocation' in navigator) {
            // Request location permission
            navigator.permissions.query({ name: 'geolocation' }).then(result => {
                if (result.state === 'granted') {
                    this.getCurrentLocation();
                }
            });
        }
    }
    
    initializeImageHandling() {
        // Set up image upload handlers
        const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
        imageInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                this.handleImageUpload(e);
            });
        });
        
        // Set up camera capture if available
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            this.setupCameraCapture();
        }
    }
    
    initializeSearch() {
        const searchInputs = document.querySelectorAll('.search-input');
        searchInputs.forEach(input => {
            const debouncedSearch = this.debounce(this.performSearch.bind(this), CONFIG.SEARCH_DEBOUNCE_MS);
            input.addEventListener('input', debouncedSearch);
        });
    }
    
    async getCurrentLocation() {
        try {
            const position = await new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 60000
                });
            });
            
            this.currentLocation = {
                lat: position.coords.latitude,
                lng: position.coords.longitude,
                accuracy: position.coords.accuracy
            };
            
            // Store in localStorage
            localStorage.setItem('userLocation', JSON.stringify(this.currentLocation));
            
            // Trigger location-based features
            this.onLocationUpdate();
            
            return this.currentLocation;
        } catch (error) {
            console.warn('Could not get current location:', error);
            return null;
        }
    }
    
    onLocationUpdate() {
        // Update location-dependent UI elements
        const locationElements = document.querySelectorAll('[data-location-dependent]');
        locationElements.forEach(element => {
            if (this.currentLocation) {
                element.style.display = 'block';
                // Update distance calculations, etc.
            } else {
                element.style.display = 'none';
            }
        });
        
        // Trigger custom event
        window.dispatchEvent(new CustomEvent('locationUpdate', {
            detail: { location: this.currentLocation }
        }));
    }
    
    setupCameraCapture() {
        const cameraButtons = document.querySelectorAll('[data-camera-capture]');
        cameraButtons.forEach(button => {
            button.addEventListener('click', async (e) => {
                e.preventDefault();
                await this.captureImage();
            });
        });
    }
    
    async captureImage() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true });
            const video = document.createElement('video');
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            
            video.srcObject = stream;
            video.play();
            
            // Create camera modal
            const modal = this.createCameraModal(video, canvas, context, stream);
            document.body.appendChild(modal);
            
            // Show modal
            this.openModal(modal);
            
        } catch (error) {
            console.error('Camera access denied:', error);
            this.showNotification('Camera access is required for this feature.', 'warning');
        }
    }
    
    createCameraModal(video, canvas, context, stream) {
        const modal = document.createElement('div');
        modal.className = 'modal is-active';
        modal.innerHTML = `
            <div class="modal-background"></div>
            <div class="modal-content">
                <div class="box">
                    <h3 class="title is-4">Take Photo</h3>
                    <video id="camera-video" autoplay playsinline style="width: 100%; max-width: 400px;"></video>
                    <canvas id="camera-canvas" style="display: none;"></canvas>
                    <div class="buttons mt-4">
                        <button class="button is-primary" id="capture-btn">
                            <i class="fas fa-camera mr-2"></i>Capture
                        </button>
                        <button class="button is-light" id="cancel-btn">Cancel</button>
                    </div>
                </div>
            </div>
        `;
        
        // Set up video element
        const videoElement = modal.querySelector('#camera-video');
        videoElement.srcObject = stream;
        
        // Set up event listeners
        modal.querySelector('#capture-btn').addEventListener('click', () => {
            this.captureFromCamera(videoElement, canvas, context, stream);
        });
        
        modal.querySelector('#cancel-btn').addEventListener('click', () => {
            this.closeCameraModal(modal, stream);
        });
        
        return modal;
    }
    
    captureFromCamera(video, canvas, context, stream) {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0);
        
        // Convert to blob
        canvas.toBlob((blob) => {
            // Create file input and trigger change event
            const fileInput = document.querySelector('input[type="file"][accept*="image"]');
            if (fileInput) {
                const file = new File([blob], 'camera-capture.jpg', { type: 'image/jpeg' });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;
                fileInput.dispatchEvent(new Event('change'));
            }
            
            // Close modal and stop stream
            this.closeCameraModal(modal, stream);
        }, 'image/jpeg', 0.8);
    }
    
    closeCameraModal(modal, stream) {
        stream.getTracks().forEach(track => track.stop());
        modal.remove();
    }
    
    handleImageUpload(event) {
        const files = event.target.files;
        if (!files.length) return;
        
        const file = files[0];
        
        // Validate file
        if (!this.validateImageFile(file)) {
            event.target.value = '';
            return;
        }
        
        // Preview image
        this.previewImage(file, event.target);
        
        // Upload image
        this.uploadImage(file);
    }
    
    validateImageFile(file) {
        // Check file size
        if (file.size > CONFIG.IMAGE_MAX_SIZE) {
            this.showNotification(`File size must be less than ${CONFIG.IMAGE_MAX_SIZE / (1024 * 1024)}MB`, 'error');
            return false;
        }
        
        // Check file type
        if (!CONFIG.SUPPORTED_IMAGE_TYPES.includes(file.type)) {
            this.showNotification('Only JPEG, PNG, and WebP images are supported', 'error');
            return false;
        }
        
        return true;
    }
    
    previewImage(file, input) {
        const reader = new FileReader();
        reader.onload = (e) => {
            // Find or create preview container
            let previewContainer = input.parentElement.querySelector('.image-preview');
            if (!previewContainer) {
                previewContainer = document.createElement('div');
                previewContainer.className = 'image-preview mt-2';
                input.parentElement.appendChild(previewContainer);
            }
            
            previewContainer.innerHTML = `
                <img src="${e.target.result}" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 4px;">
                <button class="button is-small is-danger ml-2" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            `;
        };
        reader.readAsDataURL(file);
    }
    
    async uploadImage(file) {
        try {
            this.showLoading();
            
            const formData = new FormData();
            formData.append('image', file);
            formData.append('csrf_token', this.getCSRFToken());
            
            const response = await fetch('/api/upload', {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) {
                throw new Error(`Upload failed: ${response.statusText}`);
            }
            
            const result = await response.json();
            this.showNotification('Image uploaded successfully!', 'success');
            
            // Trigger custom event
            window.dispatchEvent(new CustomEvent('imageUploaded', {
                detail: { result, file }
            }));
            
        } catch (error) {
            console.error('Image upload failed:', error);
            this.showNotification('Image upload failed. Please try again.', 'error');
        } finally {
            this.hideLoading();
        }
    }
    
    async performSearch(query) {
        if (!query || query.length < 2) return;
        
        try {
            this.showLoading();
            
            const response = await fetch(`/api/search?q=${encodeURIComponent(query)}`);
            const results = await response.json();
            
            // Update search results
            this.updateSearchResults(results);
            
        } catch (error) {
            console.error('Search failed:', error);
            this.showNotification('Search failed. Please try again.', 'error');
        } finally {
            this.hideLoading();
        }
    }
    
    updateSearchResults(results) {
        const resultsContainer = document.querySelector('.search-results');
        if (!resultsContainer) return;
        
        if (results.length === 0) {
            resultsContainer.innerHTML = '<p class="has-text-grey">No results found.</p>';
            return;
        }
        
        const resultsHTML = results.map(result => `
            <div class="card mb-3">
                <div class="card-content">
                    <h4 class="title is-5">${this.escapeHtml(result.title)}</h4>
                    <p class="has-text-grey">${this.escapeHtml(result.description)}</p>
                    <div class="tags mt-2">
                        <span class="tag is-primary">${result.category}</span>
                        <span class="tag is-info">${result.distance}km away</span>
                    </div>
                </div>
            </div>
        `).join('');
        
        resultsContainer.innerHTML = resultsHTML;
    }
    
    validateForm(form) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });
        
        return isValid;
    }
    
    validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        
        // Clear previous errors
        this.clearFieldError(field);
        
        // Required field validation
        if (field.hasAttribute('required') && !value) {
            this.showFieldError(field, 'This field is required');
            isValid = false;
        }
        
        // Email validation
        if (field.type === 'email' && value) {
            if (!this.isValidEmail(value)) {
                this.showFieldError(field, 'Please enter a valid email address');
                isValid = false;
            }
        }
        
        // Phone validation
        if (field.type === 'tel' && value) {
            if (!this.isValidPhone(value)) {
                this.showFieldError(field, 'Please enter a valid phone number');
                isValid = false;
            }
        }
        
        // URL validation
        if (field.type === 'url' && value) {
            if (!this.isValidURL(value)) {
                this.showFieldError(field, 'Please enter a valid URL');
                isValid = false;
            }
        }
        
        return isValid;
    }
    
    showFieldError(field, message) {
        field.classList.add('is-danger');
        
        // Create or update error message
        let errorElement = field.parentElement.querySelector('.help.is-danger');
        if (!errorElement) {
            errorElement = document.createElement('p');
            errorElement.className = 'help is-danger';
            field.parentElement.appendChild(errorElement);
        }
        
        errorElement.textContent = message;
    }
    
    clearFieldError(field) {
        field.classList.remove('is-danger');
        
        const errorElement = field.parentElement.querySelector('.help.is-danger');
        if (errorElement) {
            errorElement.remove();
        }
    }
    
    openModal(modal) {
        modal.classList.add('is-active');
        document.body.classList.add('is-clipped');
        
        // Focus first focusable element
        const focusableElement = modal.querySelector('input, button, select, textarea, [tabindex]:not([tabindex="-1"])');
        if (focusableElement) {
            focusableElement.focus();
        }
    }
    
    closeModal(modal) {
        modal.classList.remove('is-active');
        document.body.classList.remove('is-clipped');
    }
    
    showTooltip(element, text) {
        this.hideTooltip(); // Remove existing tooltip
        
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip is-tooltip-multiline';
        tooltip.textContent = text;
        tooltip.style.position = 'absolute';
        tooltip.style.zIndex = '1000';
        tooltip.id = 'current-tooltip';
        
        document.body.appendChild(tooltip);
        
        const rect = element.getBoundingClientRect();
        tooltip.style.left = rect.left + 'px';
        tooltip.style.top = (rect.bottom + 5) + 'px';
    }
    
    hideTooltip() {
        const tooltip = document.getElementById('current-tooltip');
        if (tooltip) {
            tooltip.remove();
        }
    }
    
    showLoading() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.classList.add('is-active');
        }
    }
    
    hideLoading() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.classList.remove('is-active');
        }
    }
    
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification is-${type} is-light`;
        notification.innerHTML = `
            <button class="delete" onclick="this.parentElement.remove();"></button>
            ${this.escapeHtml(message)}
        `;
        
        const container = document.querySelector('.container');
        if (container) {
            container.insertBefore(notification, container.firstChild);
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        if (notification.parentElement) {
                            notification.remove();
                        }
                    }, 300);
                }
            }, 5000);
        }
    }
    
    // Utility methods
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    isValidPhone(phone) {
        const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
        return phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ''));
    }
    
    isValidURL(url) {
        try {
            new URL(url);
            return true;
        } catch {
            return false;
        }
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }
    
    getCSRFToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }
    
    // Event handlers
    handleGlobalClick(e) {
        // Handle global click events
    }
    
    handleScroll(e) {
        // Handle scroll events
    }
    
    handleResize(e) {
        // Handle resize events
    }
    
    handleFormSubmit(e) {
        // Handle form submissions
    }
    
    handleGlobalError(e) {
        console.error('Global error:', e);
    }
    
    // Service Worker setup
    setupServiceWorker() {
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('SW registered: ', registration);
                    })
                    .catch(registrationError => {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
    }
    
    // User data management
    async loadUserData() {
        try {
            const response = await fetch('/api/user/current');
            if (response.ok) {
                this.currentUser = await response.json();
                this.onUserDataLoaded();
            }
        } catch (error) {
            console.log('No user logged in or error loading user data');
        }
    }
    
    onUserDataLoaded() {
        // Update UI based on user data
        const userElements = document.querySelectorAll('[data-user-dependent]');
        userElements.forEach(element => {
            if (this.currentUser) {
                element.style.display = 'block';
            } else {
                element.style.display = 'none';
            }
        });
        
        // Trigger custom event
        window.dispatchEvent(new CustomEvent('userDataLoaded', {
            detail: { user: this.currentUser }
        }));
    }
}

// Initialize the application
const app = new MarketplaceApp();

// Export to global scope for use in other scripts
window.MarketplaceApp = app;

// Utility functions available globally
window.MarketplaceUtils = {
    showLoading: () => app.showLoading(),
    hideLoading: () => app.hideLoading(),
    showNotification: (msg, type) => app.showNotification(msg, type),
    getCurrentLocation: () => app.getCurrentLocation(),
    validateForm: (form) => app.validateForm(form),
    debounce: (func, wait) => app.debounce(func, wait),
    throttle: (func, limit) => app.throttle(func, limit),
    escapeHtml: (text) => app.escapeHtml(text)
};