<!-- Registration Page -->
<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-6">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            <i class="fas fa-user-plus mr-2"></i>
                            Create Your Account
                        </p>
                    </header>
                    
                    <div class="card-content">
                        <form id="registerForm" method="POST" action="/api/auth/register">
                            <!-- CSRF Token -->
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            
                            <div class="field">
                                <label class="label" for="username">Username</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="text" id="username" name="username" 
                                           placeholder="Choose a username" required 
                                           pattern="[a-zA-Z0-9_]{3,20}" 
                                           title="Username must be 3-20 characters, letters, numbers, and underscores only">
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-user"></i>
                                    </span>
                                </div>
                                <p class="help">Username must be 3-20 characters, letters, numbers, and underscores only</p>
                            </div>
                            
                            <div class="field">
                                <label class="label" for="email">Email (Optional)</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="email" id="email" name="email" 
                                           placeholder="your.email@example.com">
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                </div>
                                <p class="help">We'll use this to send you important updates</p>
                            </div>
                            
                            <div class="field">
                                <label class="label" for="phone">Phone (Optional)</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="tel" id="phone" name="phone" 
                                           placeholder="+1 (555) 123-4567">
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                </div>
                                <p class="help">For buyers and sellers to contact you</p>
                            </div>
                            
                            <div class="field">
                                <label class="label" for="userType">Account Type</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select id="userType" name="type" required>
                                            <option value="">Select account type</option>
                                            <option value="buyer">Buyer - I want to buy products and services</option>
                                            <option value="seller">Seller - I want to sell products and services</option>
                                            <option value="both">Both - I want to buy and sell</option>
                                        </select>
                                    </div>
                                </div>
                                <p class="help">You can change this later in your profile</p>
                            </div>
                            
                            <div class="field">
                                <label class="label">Location</label>
                                <div class="columns">
                                    <div class="column">
                                        <label class="label is-small" for="latitude">Latitude</label>
                                        <input class="input" type="number" id="latitude" name="latitude" 
                                               step="0.000001" placeholder="40.7128" 
                                               value="<?php echo DEFAULT_LATITUDE; ?>">
                                    </div>
                                    <div class="column">
                                        <label class="label is-small" for="longitude">Longitude</label>
                                        <input class="input" type="number" id="longitude" name="longitude" 
                                               step="0.000001" placeholder="-74.0060" 
                                               value="<?php echo DEFAULT_LONGITUDE; ?>">
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="control">
                                        <button type="button" class="button is-info is-small" id="getLocationBtn">
                                            <i class="fas fa-location-arrow mr-1"></i>
                                            Use My Current Location
                                        </button>
                                    </div>
                                </div>
                                <p class="help">This helps buyers and sellers find you</p>
                            </div>
                            
                            <div class="field">
                                <label class="label" for="address">Address (Optional)</label>
                                <div class="control">
                                    <input class="input" type="text" id="address" name="address" 
                                           placeholder="City, State, Country">
                                </div>
                            </div>
                            
                            <div class="field">
                                <div class="control">
                                    <label class="checkbox">
                                        <input type="checkbox" name="terms" required>
                                        I agree to the <a href="/terms" target="_blank">Terms of Service</a> and 
                                        <a href="/privacy" target="_blank">Privacy Policy</a>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="field">
                                <div class="control">
                                    <button type="submit" class="button is-primary is-fullwidth" id="registerBtn">
                                        <span class="icon">
                                            <i class="fas fa-user-plus"></i>
                                        </span>
                                        <span>Create Account</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <div class="has-text-centered mt-4">
                            <p>Already have an account? <a href="/login">Sign in here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const getLocationBtn = document.getElementById('getLocationBtn');
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');
    
    // Get current location
    getLocationBtn.addEventListener('click', function() {
        if (navigator.geolocation) {
            getLocationBtn.classList.add('is-loading');
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    latitudeInput.value = position.coords.latitude.toFixed(6);
                    longitudeInput.value = position.coords.longitude.toFixed(6);
                    getLocationBtn.classList.remove('is-loading');
                    
                    // Show success message
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
    
    // Form validation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('registerBtn');
        submitBtn.classList.add('is-loading');
        
        // Basic validation
        const username = document.getElementById('username').value.trim();
        const userType = document.getElementById('userType').value;
        const terms = document.querySelector('input[name="terms"]').checked;
        
        if (!username || !userType || !terms) {
            showNotification('Please fill in all required fields and accept the terms.', 'warning');
            submitBtn.classList.remove('is-loading');
            return;
        }
        
        // Submit form
        const formData = new FormData(form);
        
        fetch('/api/auth/register', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            submitBtn.classList.remove('is-loading');
            
            if (data.success) {
                showNotification('Account created successfully! Redirecting to login...', 'success');
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);
            } else {
                showNotification(data.message || 'Registration failed. Please try again.', 'danger');
            }
        })
        .catch(error => {
            submitBtn.classList.remove('is-loading');
            showNotification('An error occurred. Please try again.', 'danger');
            console.error('Registration error:', error);
        });
    });
    
    // Username availability check
    const usernameInput = document.getElementById('username');
    let usernameTimeout;
    
    usernameInput.addEventListener('input', function() {
        clearTimeout(usernameTimeout);
        const username = this.value.trim();
        
        if (username.length >= 3) {
            usernameTimeout = setTimeout(() => {
                checkUsernameAvailability(username);
            }, 500);
        }
    });
    
    function checkUsernameAvailability(username) {
        fetch(`/api/users/check-username?username=${encodeURIComponent(username)}`)
        .then(response => response.json())
        .then(data => {
            if (data.available) {
                usernameInput.classList.remove('is-danger');
                usernameInput.classList.add('is-success');
            } else {
                usernameInput.classList.remove('is-success');
                usernameInput.classList.add('is-danger');
            }
        })
        .catch(error => {
            console.error('Username check error:', error);
        });
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
});
</script>