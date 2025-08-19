<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Maintenance - <?php echo defined('PLATFORM_NAME') ? PLATFORM_NAME : 'Marketplace Platform'; ?></title>
    
    <!-- Bulma CSS Framework -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #3273dc;
            --primary-light: #4a9eff;
            --primary-dark: #1e4a8f;
            --secondary-color: #f14668;
            --success-color: #48c774;
            --warning-color: #ffdd57;
            --danger-color: #f14668;
            --info-color: #3298dc;
            --light-color: #f5f5f5;
            --dark-color: #363636;
            --text-color: #4a4a4a;
            --border-color: #dbdbdb;
            --shadow: 0 2px 4px rgba(0,0,0,0.1);
            --border-radius: 6px;
            --transition: all 0.3s ease;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        
        .maintenance-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }
        
        .maintenance-header {
            background: var(--primary-color);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .maintenance-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }
        
        .maintenance-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .maintenance-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .maintenance-content {
            padding: 2rem;
        }
        
        .maintenance-message {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .maintenance-message h3 {
            color: var(--text-color);
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }
        
        .maintenance-message p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        
        .maintenance-features {
            background: var(--light-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .maintenance-features h4 {
            color: var(--text-color);
            font-size: 1.1rem;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .feature-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
        }
        
        .feature-list li:last-child {
            border-bottom: none;
        }
        
        .feature-list li:before {
            content: "✓";
            color: var(--success-color);
            font-weight: bold;
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }
        
        .maintenance-actions {
            text-align: center;
        }
        
        .status-indicator {
            display: inline-flex;
            align-items: center;
            background: var(--warning-color);
            color: #333;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .status-indicator i {
            margin-right: 0.5rem;
            animation: pulse 2s infinite;
        }
        
        .progress-section {
            margin: 1.5rem 0;
        }
        
        .progress-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: #666;
        }
        
        .progress-bar {
            background: #e0e0e0;
            border-radius: 10px;
            height: 8px;
            overflow: hidden;
        }
        
        .progress-fill {
            background: linear-gradient(90deg, var(--success-color), var(--primary-color));
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s ease;
            animation: progressAnimation 2s ease-in-out infinite;
        }
        
        .contact-info {
            background: var(--light-color);
            border-radius: var(--border-radius);
            padding: 1rem;
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .contact-info p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .contact-info a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }
        
        .contact-info a:hover {
            text-decoration: underline;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
        
        @keyframes progressAnimation {
            0% {
                width: 0%;
            }
            50% {
                width: 75%;
            }
            100% {
                width: 0%;
            }
        }
        
        .refresh-button {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 1rem;
        }
        
        .refresh-button:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }
        
        .refresh-button:active {
            transform: translateY(0);
        }
        
        @media screen and (max-width: 768px) {
            .maintenance-card {
                margin: 10px;
            }
            
            .maintenance-header {
                padding: 1.5rem;
            }
            
            .maintenance-content {
                padding: 1.5rem;
            }
            
            .maintenance-title {
                font-size: 1.5rem;
            }
            
            .maintenance-icon {
                font-size: 3rem;
            }
        }
    </style>
</head>
<body>
    <div class="maintenance-card">
        <!-- Header -->
        <div class="maintenance-header">
            <div class="maintenance-icon">
                <i class="fas fa-tools"></i>
            </div>
            <h1 class="maintenance-title">System Maintenance</h1>
            <p class="maintenance-subtitle">We're working to improve your experience</p>
        </div>
        
        <!-- Content -->
        <div class="maintenance-content">
            <!-- Status Indicator -->
            <div class="status-indicator">
                <i class="fas fa-clock"></i>
                Maintenance in Progress
            </div>
            
            <!-- Main Message -->
            <div class="maintenance-message">
                <h3>We'll be back soon!</h3>
                <p>
                    We're currently performing scheduled maintenance to improve our platform and ensure better performance for all users.
                </p>
                <p>
                    This maintenance is expected to be completed shortly. We apologize for any inconvenience and appreciate your patience.
                </p>
            </div>
            
            <!-- What We're Working On -->
            <div class="maintenance-features">
                <h4>What We're Improving</h4>
                <ul class="feature-list">
                    <li>Enhanced security features</li>
                    <li>Improved performance and speed</li>
                    <li>Better user experience</li>
                    <li>New features and functionality</li>
                    <li>System stability improvements</li>
                </ul>
            </div>
            
            <!-- Progress Section -->
            <div class="progress-section">
                <div class="progress-info">
                    <span>Maintenance Progress</span>
                    <span id="progressText">In Progress</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="maintenance-actions">
                <button class="refresh-button" onclick="checkMaintenanceStatus()">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Check Status
                </button>
            </div>
            
            <!-- Contact Information -->
            <div class="contact-info">
                <p>
                    Need immediate assistance? Contact us at 
                    <a href="mailto:support@marketplace.com">support@marketplace.com</a>
                </p>
            </div>
        </div>
    </div>
    
    <script>
        // Simulate maintenance progress
        let progress = 0;
        let progressInterval;
        
        function startProgressAnimation() {
            progressInterval = setInterval(() => {
                progress += Math.random() * 5;
                if (progress > 100) progress = 100;
                
                updateProgress(progress);
                
                if (progress >= 100) {
                    clearInterval(progressInterval);
                    setTimeout(() => {
                        checkMaintenanceStatus();
                    }, 2000);
                }
            }, 1000);
        }
        
        function updateProgress(value) {
            const progressFill = document.getElementById('progressFill');
            const progressText = document.getElementById('progressText');
            
            progressFill.style.width = value + '%';
            
            if (value < 25) {
                progressText.textContent = 'Initializing...';
            } else if (value < 50) {
                progressText.textContent = 'In Progress...';
            } else if (value < 75) {
                progressText.textContent = 'Almost Done...';
            } else if (value < 100) {
                progressText.textContent = 'Finalizing...';
            } else {
                progressText.textContent = 'Complete!';
            }
        }
        
        function checkMaintenanceStatus() {
            const button = document.querySelector('.refresh-button');
            const originalText = button.innerHTML;
            
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Checking...';
            button.disabled = true;
            
            // Simulate API call
            setTimeout(() => {
                // Check if maintenance is still active
                const maintenanceActive = Math.random() > 0.3; // 70% chance maintenance is still active
                
                if (maintenanceActive) {
                    showNotification('Maintenance is still in progress. Please check back later.', 'info');
                    button.innerHTML = originalText;
                    button.disabled = false;
                } else {
                    // Maintenance complete - redirect to main site
                    showNotification('Maintenance complete! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = '/';
                    }, 2000);
                }
            }, 1500);
        }
        
        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification is-${type} is-light`;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 1000;
                max-width: 300px;
                animation: slideIn 0.3s ease-out;
            `;
            
            notification.innerHTML = `
                <button class="delete" onclick="this.parentElement.remove();"></button>
                ${message}
            `;
            
            // Add to page
            document.body.appendChild(notification);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        }
        
        // Add CSS for notification animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateX(100%);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
        `;
        document.head.appendChild(style);
        
        // Start progress animation when page loads
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(startProgressAnimation, 1000);
        });
        
        // Auto-refresh every 30 seconds
        setInterval(() => {
            if (progress < 100) {
                checkMaintenanceStatus();
            }
        }, 30000);
    </script>
</body>
</html>