        </div> <!-- .page-content -->
    </main> <!-- .main-content -->
    
    <!-- Footer -->
    <footer class="footer has-background-dark has-text-white">
        <div class="container">
            <div class="columns">
                <div class="column is-4">
                    <h3 class="title is-4 has-text-white">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        <?php echo PLATFORM_NAME; ?>
                    </h3>
                    <p class="has-text-light">
                        <?php echo PLATFORM_DESCRIPTION; ?>
                    </p>
                    <div class="mt-3">
                        <a href="#" class="has-text-light mr-3">
                            <i class="fab fa-facebook fa-lg"></i>
                        </a>
                        <a href="#" class="has-text-light mr-3">
                            <i class="fab fa-twitter fa-lg"></i>
                        </a>
                        <a href="#" class="has-text-light mr-3">
                            <i class="fab fa-instagram fa-lg"></i>
                        </a>
                        <a href="#" class="has-text-light">
                            <i class="fab fa-linkedin fa-lg"></i>
                        </a>
                    </div>
                </div>
                
                <div class="column is-2">
                    <h4 class="title is-5 has-text-white">Platform</h4>
                    <ul class="has-text-light">
                        <li><a href="/about" class="has-text-light">About Us</a></li>
                        <li><a href="/how-it-works" class="has-text-light">How It Works</a></li>
                        <li><a href="/safety" class="has-text-light">Safety</a></li>
                        <li><a href="/support" class="has-text-light">Support</a></li>
                    </ul>
                </div>
                
                <div class="column is-2">
                    <h4 class="title is-5 has-text-white">For Users</h4>
                    <ul class="has-text-light">
                        <li><a href="/register" class="has-text-light">Join Now</a></li>
                        <li><a href="/browse" class="has-text-light">Browse Listings</a></li>
                        <li><a href="/create-listing" class="has-text-light">Create Listing</a></li>
                        <li><a href="/tips" class="has-text-light">Tips & Guides</a></li>
                    </ul>
                </div>
                
                <div class="column is-2">
                    <h4 class="title is-5 has-text-white">Legal</h4>
                    <ul class="has-text-light">
                        <li><a href="/terms" class="has-text-light">Terms of Service</a></li>
                        <li><a href="/privacy" class="has-text-light">Privacy Policy</a></li>
                        <li><a href="/cookies" class="has-text-light">Cookie Policy</a></li>
                        <li><a href="/contact" class="has-text-light">Contact Us</a></li>
                    </ul>
                </div>
                
                <div class="column is-2">
                    <h4 class="title is-5 has-text-white">Contact</h4>
                    <div class="has-text-light">
                        <p><i class="fas fa-envelope mr-2"></i> support@platform.com</p>
                        <p><i class="fas fa-phone mr-2"></i> +1 (555) 123-4567</p>
                        <p><i class="fas fa-map-marker-alt mr-2"></i> New York, NY</p>
                    </div>
                </div>
            </div>
            
            <hr class="has-background-grey-dark">
            
            <div class="columns is-vcentered">
                <div class="column">
                    <p class="has-text-light">
                        &copy; <?php echo date('Y'); ?> <?php echo PLATFORM_NAME; ?>. All rights reserved.
                    </p>
                </div>
                <div class="column has-text-right">
                    <p class="has-text-light">
                        Version <?php echo PLATFORM_VERSION; ?>
                    </p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Back to Top Button -->
    <button id="backToTop" class="button is-primary is-rounded" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000; display: none;">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="modal">
        <div class="modal-background"></div>
        <div class="modal-content has-text-centered">
            <div class="box has-background-white">
                <div class="loading-spinner mb-3"></div>
                <p class="has-text-grey">Loading...</p>
            </div>
        </div>
    </div>
    
    <!-- JavaScript Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="/assets/js/main.js"></script>
    
    <!-- Page-specific JavaScript -->
    <?php if (isset($pageScripts)): ?>
        <?php foreach ($pageScripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <script>
        // Mobile navigation toggle
        document.addEventListener('DOMContentLoaded', function() {
            // Get all "navbar-burger" elements
            const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);
            
            // Add a click event on each of them
            $navbarBurgers.forEach(el => {
                el.addEventListener('click', () => {
                    // Get the target from the "data-target" attribute
                    const target = el.dataset.target;
                    const $target = document.getElementById(target);
                    
                    // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
                    el.classList.toggle('is-active');
                    $target.classList.toggle('is-active');
                });
            });
            
            // Back to top button
            const backToTopButton = document.getElementById('backToTop');
            
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
            
            // Flash message auto-hide
            const notifications = document.querySelectorAll('.notification');
            notifications.forEach(notification => {
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
            });
            
            // Form validation
            const forms = document.querySelectorAll('form[data-validate]');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!validateForm(this)) {
                        e.preventDefault();
                        return false;
                    }
                });
            });
            
            // CSRF token for AJAX requests
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Add CSRF token to all AJAX requests
            if (typeof $ !== 'undefined') {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
            }
            
            // Initialize tooltips
            const tooltips = document.querySelectorAll('[data-tooltip]');
            tooltips.forEach(element => {
                element.addEventListener('mouseenter', showTooltip);
                element.addEventListener('mouseleave', hideTooltip);
            });
            
            // Initialize modals
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                const triggers = document.querySelectorAll(`[data-target="${modal.id}"]`);
                const closeButtons = modal.querySelectorAll('.delete, .modal-background');
                
                triggers.forEach(trigger => {
                    trigger.addEventListener('click', () => {
                        modal.classList.add('is-active');
                    });
                });
                
                closeButtons.forEach(button => {
                    button.addEventListener('click', () => {
                        modal.classList.remove('is-active');
                    });
                });
                
                // Close modal with Escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && modal.classList.contains('is-active')) {
                        modal.classList.remove('is-active');
                    }
                });
            });
        });
        
        // Form validation function
        function validateForm(form) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-danger');
                    isValid = false;
                } else {
                    field.classList.remove('is-danger');
                }
            });
            
            // Email validation
            const emailFields = form.querySelectorAll('input[type="email"]');
            emailFields.forEach(field => {
                if (field.value && !isValidEmail(field.value)) {
                    field.classList.add('is-danger');
                    isValid = false;
                }
            });
            
            return isValid;
        }
        
        // Email validation helper
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
        
        // Show tooltip
        function showTooltip(e) {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip is-tooltip-multiline';
            tooltip.textContent = e.target.dataset.tooltip;
            tooltip.style.position = 'absolute';
            tooltip.style.zIndex = '1000';
            
            document.body.appendChild(tooltip);
            
            const rect = e.target.getBoundingClientRect();
            tooltip.style.left = rect.left + 'px';
            tooltip.style.top = (rect.bottom + 5) + 'px';
            
            e.target._tooltip = tooltip;
        }
        
        // Hide tooltip
        function hideTooltip(e) {
            if (e.target._tooltip) {
                e.target._tooltip.remove();
                e.target._tooltip = null;
            }
        }
        
        // Show loading overlay
        function showLoading() {
            document.getElementById('loadingOverlay').classList.add('is-active');
        }
        
        // Hide loading overlay
        function hideLoading() {
            document.getElementById('loadingOverlay').classList.remove('is-active');
        }
        
        // Flash message helper
        function showFlashMessage(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification is-${type} is-light`;
            notification.innerHTML = `
                <button class="delete" onclick="this.parentElement.remove();"></button>
                ${message}
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
        
        // AJAX helper function
        function ajaxRequest(url, method = 'GET', data = null, options = {}) {
            const defaultOptions = {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            };
            
            if (data && method !== 'GET') {
                defaultOptions.body = JSON.stringify(data);
            }
            
            const finalOptions = { ...defaultOptions, ...options };
            
            return fetch(url, finalOptions)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .catch(error => {
                    console.error('AJAX request failed:', error);
                    showFlashMessage('An error occurred while processing your request.', 'danger');
                    throw error;
                });
        }
        
        // Location services helper
        function getCurrentLocation() {
            return new Promise((resolve, reject) => {
                if (!navigator.geolocation) {
                    reject(new Error('Geolocation is not supported by this browser.'));
                    return;
                }
                
                navigator.geolocation.getCurrentPosition(
                    position => {
                        resolve({
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        });
                    },
                    error => {
                        reject(error);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 60000
                    }
                );
            });
        }
        
        // Camera capture helper
        function captureImage(videoElement, canvasElement) {
            return new Promise((resolve, reject) => {
                try {
                    const context = canvasElement.getContext('2d');
                    canvasElement.width = videoElement.videoWidth;
                    canvasElement.height = videoElement.videoHeight;
                    context.drawImage(videoElement, 0, 0);
                    
                    canvasElement.toBlob(blob => {
                        resolve(blob);
                    }, 'image/jpeg', 0.8);
                } catch (error) {
                    reject(error);
                }
            });
        }
        
        // Utility functions
        function formatCurrency(amount, currency = 'USD') {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: currency
            }).format(amount);
        }
        
        function formatDate(date, options = {}) {
            const defaultOptions = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            
            const finalOptions = { ...defaultOptions, ...options };
            return new Date(date).toLocaleDateString('en-US', finalOptions);
        }
        
        function debounce(func, wait) {
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
        
        function throttle(func, limit) {
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
        
        // Export functions to global scope for use in other scripts
        window.MarketplaceUtils = {
            showLoading,
            hideLoading,
            showFlashMessage,
            ajaxRequest,
            getCurrentLocation,
            captureImage,
            formatCurrency,
            formatDate,
            debounce,
            throttle,
            validateForm,
            isValidEmail
        };
    </script>
</body>
</html>