<?php
/**
 * Logout Page
 * Location-Based Marketplace Platform
 */

// Define secure access constant
define('SECURE_ACCESS', true);

// Load configuration
require_once __DIR__ . '/../config/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /');
    exit;
}

// Get username for logging
$username = $_SESSION['username'] ?? 'Unknown';

// Process logout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (isset($_POST['csrf_token']) && validateCSRFToken($_POST['csrf_token'])) {
        // Clear remember me cookie if it exists
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        }
        
        // Destroy session
        session_destroy();
        
        // Log successful logout
        if (LOG_ERRORS) {
            error_log("User logged out successfully: $username");
        }
        
        // Set success message and redirect
        $_SESSION['flash_message'] = 'You have been logged out successfully.';
        $_SESSION['flash_type'] = 'success';
        header('Location: /');
        exit;
    } else {
        $_SESSION['error_message'] = 'Invalid request. Please try again.';
    }
}

$pageTitle = 'Logout - ' . PLATFORM_NAME;
$pageDescription = 'Sign out of your account';

include __DIR__ . '/header.php';
?>

<!-- Logout Confirmation Page -->
<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-5">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Confirm Logout
                        </p>
                    </header>
                    
                    <div class="card-content">
                        <div class="content has-text-centered">
                            <div class="mb-4">
                                <i class="fas fa-question-circle fa-3x has-text-warning"></i>
                            </div>
                            
                            <h3 class="title is-4">Are you sure you want to logout?</h3>
                            <p class="has-text-grey">
                                You will be signed out of your account and redirected to the home page.
                            </p>
                            
                            <div class="mt-5">
                                <p class="has-text-grey is-size-7">
                                    Logged in as: <strong><?php echo sanitizeOutput($username); ?></strong>
                                </p>
                            </div>
                        </div>
                        
                        <form method="POST" class="mt-4">
                            <!-- CSRF Token -->
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            
                            <div class="field is-grouped is-grouped-centered">
                                <div class="control">
                                    <a href="/dashboard" class="button is-light">
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        Cancel
                                    </a>
                                </div>
                                <div class="control">
                                    <button type="submit" class="button is-danger">
                                        <i class="fas fa-sign-out-alt mr-2"></i>
                                        Yes, Logout
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="has-text-centered mt-4">
                    <p class="has-text-grey">
                        <a href="/dashboard">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Back to Dashboard
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add confirmation dialog for logout button
    const logoutForm = document.querySelector('form');
    
    logoutForm.addEventListener('submit', function(e) {
        const confirmed = confirm('Are you sure you want to logout?');
        if (!confirmed) {
            e.preventDefault();
        }
    });
});
</script>

<?php include __DIR__ . '/footer.php'; ?>