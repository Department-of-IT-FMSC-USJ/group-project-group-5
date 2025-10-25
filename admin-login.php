<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - LicenseXpress</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/admin-login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <div class="logo-icon">LX</div>
                    <span class="logo-text">LicenseXpress</span>
                </div>
                <nav class="nav">
                    <a href="index.php" class="nav-link">Home</a>
                    <a href="login.php" class="nav-link">User Login</a>
                    <a href="pages/about.php" class="nav-link">About</a>
                    <a href="pages/contactus.php" class="nav-link">Contact</a>
                </nav>
            </div>
        </div>
    </header>

   
    <main class="admin-login-main">
        <div class="container">
            <div class="login-container">

                <div class="login-card glass-card">
                    <div class="login-header">
                        <div class="admin-icon">👨‍💼</div>
                        <h1 class="login-title">Admin Login</h1>
                        <p class="login-subtitle">Access the LicenseXpress administration panel</p>
                    </div>

                    <form class="login-form" id="adminLoginForm">
                        
                        <div class="form-group">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-container">
                                <input type="text" id="username" name="username" class="form-input" placeholder="Enter your username" required>
                                <div class="input-icon">👤</div>
                                <div class="validation-icon"></div>
                            </div>
                            <div class="error-message"></div>
                        </div>

                        
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-container">
                                <input type="password" id="password" name="password" class="form-input" placeholder="Enter your password" required>
                                <div class="input-icon">🔒</div>
                                <div class="validation-icon"></div>
                                <button type="button" class="password-toggle" id="passwordToggle">
                                    <span class="toggle-icon">👁️</span>
                                </button>
                            </div>
                            <div class="error-message"></div>
                        </div>

                        
                        <div class="form-group checkbox-group">
                            <label class="checkbox-container">
                                <input type="checkbox" id="rememberMe" name="rememberMe">
                                <span class="checkmark"></span>
                                <span class="checkbox-text">Remember me for 7 days</span>
                            </label>
                        </div>

                        
                        <button type="submit" class="btn btn-primary btn-large" id="loginBtn">
                            <span class="btn-text">Sign In to Admin Panel</span>
                            <div class="btn-spinner hidden">
                                <div class="spinner"></div>
                            </div>
                        </button>
                    </form>

                    
                    <div class="admin-info">
                        <h3>🔐 Admin Access</h3>
                        <p>This is a secure area for authorized personnel only. All activities are logged and monitored.</p>
                        <div class="admin-features">
                            <h4>Available Features:</h4>
                            <ul>
                                <li>• Review and verify user applications</li>
                                <li>• Manage document submissions</li>
                                <li>• Monitor exam results</li>
                                <li>• Generate reports and analytics</li>
                                <li>• User account management</li>
                            </ul>
                        </div>
                    </div>
                </div>

                
                <div class="security-notice glass-card">
                    <div class="notice-icon">🛡️</div>
                    <div class="notice-content">
                        <h3>Security Notice</h3>
                        <p>This admin panel is protected by advanced security measures. All login attempts are monitored and logged.</p>
                        <div class="security-features">
                            <div class="security-item">
                                <span class="security-icon">🔒</span>
                                <span>Encrypted connections</span>
                            </div>
                            <div class="security-item">
                                <span class="security-icon">📊</span>
                                <span>Activity monitoring</span>
                            </div>
                            <div class="security-item">
                                <span class="security-icon">🚨</span>
                                <span>Intrusion detection</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>LicenseXpress</h4>
                    <p>Digital driving license application platform</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="login.php">User Login</a></li>
                        <li><a href="pages/about.php">About</a></li>
                        <li><a href="pages/contactus.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Documentation</a></li>
                        <li><a href="#">System Status</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 LicenseXpress. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/app.js"></script>
    <script src="assets/js/admin-login.js"></script>
</body>
</html>
