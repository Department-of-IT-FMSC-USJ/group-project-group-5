<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - LicenseXpress</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/contact.css">
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
                    <a href="../index.php" class="nav-link">Home</a>
                    <a href="learners.php" class="nav-link">Find Schools</a>
                    <a href="about.php" class="nav-link">About Us</a>
                    <a href="contactus.php" class="nav-link active">Contact</a>
                    <a href="faq.php" class="nav-link">FAQ</a>
                    <a href="guidelines.php" class="nav-link">Guidelines</a>
                </nav>
                <div class="header-actions">
                    <a href="../login.php" class="btn btn-primary">Sign In</a>
                </div>
            </div>
        </div>
    </header>

    
    <main class="contact-main">
        <div class="container">
            
            <div class="hero-section">
                <h1 class="hero-title">Contact Us</h1>
                <p class="hero-subtitle">Get in touch with our support team for any questions or assistance</p>
            </div>

            
            <div class="contact-methods">
                <div class="contact-card glass-card">
                    <div class="contact-icon">📧</div>
                    <h3>Email Support</h3>
                    <p>Send us an email and we'll respond within 24 hours</p>
                    <a href="mailto:support@licensexpress.lk" class="contact-link">support@licensexpress.lk</a>
                </div>
                <div class="contact-card glass-card">
                    <div class="contact-icon">📞</div>
                    <h3>Phone Support</h3>
                    <p>Call us during business hours for immediate assistance</p>
                    <a href="tel:+94112345678" class="contact-link">+94 11 234 5678</a>
                </div>
                <div class="contact-card glass-card">
                    <div class="contact-icon">💬</div>
                    <h3>Live Chat</h3>
                    <p>Chat with our support team in real-time</p>
                    <button class="contact-link" onclick="openLiveChat()">Start Live Chat</button>
                </div>
            </div>

            
            <div class="contact-form-section">
                <div class="form-container glass-card">
                    <h2>Send us a Message</h2>
                    <p>Fill out the form below and we'll get back to you as soon as possible.</p>
                    
                    <form class="contact-form" id="contactForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="firstName" class="form-label">First Name *</label>
                                <input type="text" id="firstName" name="firstName" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label for="lastName" class="form-label">Last Name *</label>
                                <input type="text" id="lastName" name="lastName" class="form-input" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" id="email" name="email" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" id="phone" name="phone" class="form-input">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject" class="form-label">Subject *</label>
                            <select id="subject" name="subject" class="form-select" required>
                                <option value="">Select a subject</option>
                                <option value="general">General Inquiry</option>
                                <option value="technical">Technical Support</option>
                                <option value="application">Application Help</option>
                                <option value="payment">Payment Issues</option>
                                <option value="test">Test Scheduling</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="message" class="form-label">Message *</label>
                            <textarea id="message" name="message" class="form-textarea" rows="6" placeholder="Please describe your inquiry in detail..." required></textarea>
                        </div>
                        
                        <div class="form-group checkbox-group">
                            <label class="checkbox-container">
                                <input type="checkbox" id="newsletter" name="newsletter">
                                <span class="checkmark"></span>
                                <span class="checkbox-text">Subscribe to our newsletter for updates and tips</span>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-large">
                            <span class="btn-text">Send Message</span>
                            <div class="btn-spinner hidden">
                                <div class="spinner"></div>
                            </div>
                        </button>
                    </form>
                </div>
            </div>

            
            <div class="office-info">
                <h2>Our Office</h2>
                <div class="office-details glass-card">
                    <div class="office-content">
                        <div class="office-item">
                            <div class="office-icon">📍</div>
                            <div class="office-text">
                                <h3>Address</h3>
                                <p>123 Digital Avenue<br>Colombo 07, Sri Lanka</p>
                            </div>
                        </div>
                        <div class="office-item">
                            <div class="office-icon">🕒</div>
                            <div class="office-text">
                                <h3>Business Hours</h3>
                                <p>Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 9:00 AM - 1:00 PM<br>Sunday: Closed</p>
                            </div>
                        </div>
                        <div class="office-item">
                            <div class="office-icon">📞</div>
                            <div class="office-text">
                                <h3>Phone</h3>
                                <p>+94 11 234 5678<br>+94 77 123 4567</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="faq-links">
                <h2>Quick Help</h2>
                <div class="faq-grid">
                    <a href="faq.php" class="faq-link glass-card">
                        <div class="faq-icon">❓</div>
                        <h3>Frequently Asked Questions</h3>
                        <p>Find answers to common questions</p>
                    </a>
                    <a href="guidelines.php" class="faq-link glass-card">
                        <div class="faq-icon">📋</div>
                        <h3>Application Guidelines</h3>
                        <p>Step-by-step application guide</p>
                    </a>
                    <a href="../login.php" class="faq-link glass-card">
                        <div class="faq-icon">🔐</div>
                        <h3>Account Support</h3>
                        <p>Login and account assistance</p>
                    </a>
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
                        <li><a href="../index.php">Home</a></li>
                        <li><a href="../pages/about.php">About</a></li>
                        <li><a href="../pages/faq.php">FAQ</a></li>
                        <li><a href="../pages/guidelines.php">Guidelines</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="../pages/contactus.php">Contact Us</a></li>
                        <li><a href="mailto:support@licensexpress.lk">Email Support</a></li>
                        <li><a href="tel:+94112345678">Phone Support</a></li>
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

    <script src="../assets/js/app.js"></script>
    <script src="../assets/js/contact.js"></script>
</body>
</html>
