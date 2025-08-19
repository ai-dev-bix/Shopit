<!-- Login Page -->
<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-5">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Sign In to Your Account
                        </p>
                    </header>
                    
                    <div class="card-content">
                        <form id="loginForm" method="POST" action="/api/auth/login">
                            <!-- CSRF Token -->
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            
                            <div class="field">
                                <label class="label" for="username">Username</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="text" id="username" name="username" 
                                           placeholder="Enter your username" required autofocus>
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-user"></i>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="field">
                                <label class="label" for="password">Password (Optional)</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="password" id="password" name="password" 
                                           placeholder="Enter your password (optional)">
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                </div>
                                <p class="help">
                                    <em>Note: This prototype uses username-only authentication. Password field is optional.</em>
                                </p>
                            </div>
                            
                            <div class="field">
                                <div class="control">
                                    <label class="checkbox">
                                        <input type="checkbox" name="remember" id="remember">
                                        Remember me for 30 days
                                    </label>
                                </div>
                            </div>
                            
                            <div class="field">
                                <div class="control">
                                    <button type="submit" class="button is-primary is-fullwidth" id="loginBtn">
                                        <span class="icon">
                                            <i class="fas fa-sign-in-alt"></i>
                                        </span>
                                        <span>Sign In</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <div class="has-text-centered mt-4">
                            <p>Don't have an account? <a href="/register">Create one here</a></p>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="has-text-centered">
                            <p class="has-text-grey">Quick Access (Demo Accounts)</p>
                            <div class="buttons is-centered mt-2">
                                <button class="button is-small is-info" onclick="quickLogin('admin1')">
                                    <i class="fas fa-user-shield mr-1"></i>
                                    Admin 1
                                </button>
                                <button class="button is-small is-info" onclick="quickLogin('admin2')">
                                    <i class="fas fa-user-shield mr-1"></i>
                                    Admin 2
                                </button>
                            </div>
                            <p class="is-size-7 has-text-grey mt-2">
                                These are demo accounts for testing purposes
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    
    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('loginBtn');
        submitBtn.classList.add('is-loading');
        
        // Basic validation
        const username = usernameInput.value.trim();
        
        if (!username) {
            showNotification('Please enter your username.', 'warning');
            submitBtn.classList.remove('is-loading');
            return;
        }
        
        // Submit form
        const formData = new FormData(form);
        
        fetch('/api/auth/login', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            submitBtn.classList.remove('is-loading');
            
            if (data.success) {
                showNotification('Login successful! Redirecting...', 'success');
                
                // Store remember me preference
                if (document.getElementById('remember').checked) {
                    localStorage.setItem('rememberLogin', 'true');
                }
                
                // Redirect to dashboard or intended page
                setTimeout(() => {
                    const redirectUrl = data.redirect_url || '/dashboard';
                    window.location.href = redirectUrl;
                }, 1000);
            } else {
                showNotification(data.message || 'Login failed. Please check your username.', 'danger');
                
                // Clear username field on failed login
                usernameInput.value = '';
                usernameInput.focus();
            }
        })
        .catch(error => {
            submitBtn.classList.remove('is-loading');
            showNotification('An error occurred. Please try again.', 'danger');
            console.error('Login error:', error);
        });
    });
    
    // Quick login for demo accounts
    window.quickLogin = function(username) {
        usernameInput.value = username;
        
        // Auto-submit form
        form.dispatchEvent(new Event('submit'));
    };
    
    // Check for remembered login preference
    if (localStorage.getItem('rememberLogin') === 'true') {
        document.getElementById('remember').checked = true;
    }
    
    // Username input focus
    usernameInput.focus();
    
    // Enter key navigation
    usernameInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            passwordInput.focus();
        }
    });
    
    passwordInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            form.dispatchEvent(new Event('submit'));
        }
    });
    
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