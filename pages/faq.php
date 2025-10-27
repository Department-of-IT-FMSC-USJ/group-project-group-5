<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - LicenseXpress</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/faq.css">
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
                    <a href="contactus.php" class="nav-link">Contact</a>
                    <a href="faq.php" class="nav-link active">FAQ</a>
                    <a href="guidelines.php" class="nav-link">Guidelines</a>
                </nav>
                <div class="header-actions">
                    <a href="../login.php" class="btn btn-primary">Sign In</a>
                </div>
            </div>
        </div>
    </header>

    
    <main class="faq-main">
        <div class="container">
           
            <div class="page-header">
                <h1 class="page-title">Frequently Asked Questions</h1>
                <p class="page-subtitle">Find answers to common questions about LicenseXpress</p>
            </div>

           
            <div class="search-section glass-card">
                <div class="search-header">
                    <h2>🔍 Search FAQ</h2>
                    <p>Can't find what you're looking for? Search our FAQ database</p>
                </div>
                <div class="search-container">
                    <input type="text" class="search-input" id="faqSearch" placeholder="Search for questions, topics, or keywords...">
                    <button class="search-btn" id="searchBtn">Search</button>
                </div>
            </div>

            
            <div class="faq-categories">
                <div class="category-card glass-card">
                    <div class="category-header">
                        <div class="category-icon">🚀</div>
                        <h3>Getting Started</h3>
                        <p>Basic information about using LicenseXpress</p>
                    </div>
                    <div class="category-questions">
                        <div class="faq-item">
                            <div class="faq-question">
                                <h4>What is LicenseXpress?</h4>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                <p>LicenseXpress is a digital platform that streamlines the driving license application process in Sri Lanka. It allows you to apply for your driving license online, upload documents, schedule tests, and track your application progress from anywhere.</p>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question">
                                <h4>How do I create an account?</h4>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                <p>Creating an account is simple:</p>
                                <ol>
                                    <li>Click "Sign Up" on the homepage</li>
                                    <li>Fill in your personal details (name, NIC, email, phone)</li>
                                    <li>Create a secure password</li>
                                    <li>Verify your email address</li>
                                    <li>You're ready to start your application!</li>
                                </ol>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question">
                                <h4>What documents do I need?</h4>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                <p>You'll need the following documents:</p>
                                <ul>
                                    <li>Birth Certificate (digital copy)</li>
                                    <li>NIC Copy (front and back)</li>
                                    <li>Medical Certificate (issued within 6 months)</li>
                                    <li>Passport Size Photograph (white background)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="category-card glass-card">
                    <div class="category-header">
                        <div class="category-icon">📋</div>
                        <h3>Application Process</h3>
                        <p>Step-by-step guide to applying for your license</p>
                    </div>
                    <div class="category-questions">
                        <div class="faq-item">
                            <div class="faq-question">
                                <h4>How long does the application process take?</h4>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                <p>The complete process typically takes 2-3 months:</p>
                                <ul>
                                    <li>Application submission: Immediate</li>
                                    <li>Document verification: 2-3 business days</li>
                                    <li>Theory test scheduling: Available immediately after verification</li>
                                    <li>Theory test: 60 minutes (online)</li>
                                    <li>Practical test: Auto-scheduled 3 months after theory pass</li>
                                    <li>License issuance: 1-2 weeks after practical test</li>
                                </ul>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question">
                                <h4>What happens if my documents are rejected?</h4>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                <p>If your documents are rejected:</p>
                                <ul>
                                    <li>You'll receive an email with specific feedback</li>
                                    <li>Only rejected documents need to be resubmitted</li>
                                    <li>No additional payment is required</li>
                                    <li>You have 30 days to resubmit</li>
                                    <li>Approved documents remain valid</li>
                                </ul>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question">
                                <h4>Can I track my application progress?</h4>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                <p>Yes! Your dashboard shows real-time progress:</p>
                                <ul>
                                    <li>Application status and percentage complete</li>
                                    <li>Next steps and requirements</li>
                                    <li>Test schedules and results</li>
                                    <li>Document verification status</li>
                                    <li>Timeline with important dates</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="category-card glass-card">
                    <div class="category-header">
                        <div class="category-icon">📝</div>
                        <h3>Theory Test</h3>
                        <p>Everything about the online theory examination</p>
                    </div>
                    <div class="category-questions">
                        <div class="faq-item">
                            <div class="faq-question">
                                <h4>How does the online theory test work?</h4>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                <p>The theory test is conducted online with the following features:</p>
                                <ul>
                                    <li>50 multiple choice questions</li>
                                    <li>60 minutes duration</li>
                                    <li>Pass mark: 40/50 (80%)</li>
                                    <li>Webcam monitoring for security</li>
                                    <li>Instant results after completion</li>
                                    <li>Can be taken from anywhere with internet</li>
                                </ul>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question">
                                <h4>What if I fail the theory test?</h4>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                <p>If you fail the theory test:</p>
                                <ul>
                                    <li>You can retake it as many times as needed</li>
                                    <li>Reschedule fee: Rs. 500 per attempt</li>
                                    <li>Study materials are provided</li>
                                    <li>Practice tests are available</li>
                                    <li>No time limit between attempts</li>
                                </ul>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question">
                                <h4>What security measures are in place?</h4>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                <p>Our theory test includes comprehensive security:</p>
                                <ul>
                                    <li>Webcam monitoring throughout the exam</li>
                                    <li>Screenshot and screen recording prevention</li>
                                    <li>Copy/paste blocking</li>
                                    <li>Tab switching detection</li>
                                    <li>Right-click disabled</li>
                                    <li>Developer tools blocked</li>
                                    <li>Automatic termination on violations</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="category-card glass-card">
                    <div class="category-header">
                        <div class="category-icon">🚗</div>
                        <h3>Practical Test</h3>
                        <p>Information about the practical driving examination</p>
                    </div>
                    <div class="category-questions">
                        <div class="faq-item">
                            <div class="faq-question">
                                <h4>When is the practical test scheduled?</h4>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                <p>The practical test is automatically scheduled:</p>
                                <ul>
                                    <li>3 months after passing the theory test</li>
                                    <li>At an approved test center</li>
                                    <li>With a certified DMT examiner</li>
                                    <li>You'll receive confirmation via email and SMS</li>
                                    <li>Can be rescheduled if needed (with fee)</li>
                                </ul>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question">
                                <h4>What should I bring to the practical test?</h4>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                <p>Required items for the practical test:</p>
                                <ul>
                                    <li>Original NIC</li>
                                    <li>Theory test pass certificate</li>
                                    <li>Learner's permit</li>
                                    <li>Vehicle registration (if using own vehicle)</li>
                                    <li>Valid insurance certificate</li>
                                    <li>Arrive 30 minutes before scheduled time</li>
                                </ul>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question">
                                <h4>Can I use my own vehicle for the practical test?</h4>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                <p>Yes, you can use your own vehicle if:</p>
                                <ul>
                                    <li>It's in good working condition</li>
                                    <li>Valid registration and insurance</li>
                                    <li>Appropriate for your license category</li>
                                    <li>Clean and presentable</li>
                                    <li>Alternatively, you can rent a vehicle from the test center (Rs. 2,000 fee)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="category-card glass-card">
                    <div class="category-header">
                        <div class="category-icon">💳</div>
                        <h3>Payment & Fees</h3>
                        <p>Information about costs and payment methods</p>
                    </div>
                    <div class="category-questions">
                        <div class="faq-item">
                            <div class="faq-question">
                                <h4>What are the fees for a driving license?</h4>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                <p>The total cost is Rs. 3,200, which includes:</p>
                                <ul>
                                    <li>Application Fee: Rs. 1,500</li>
                                    <li>Document Processing: Rs. 500</li>
                                    <li>Theory Test Fee: Rs. 1,000</li>
                                    <li>Service Charge: Rs. 200</li>
                                    <li>Additional fees: Theory reschedule (Rs. 500), Practical reschedule (Rs. 1,000)</li>
                                </ul>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question">
                                <h4>What payment methods are accepted?</h4>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                <p>We accept multiple payment methods:</p>
                                <ul>
                                    <li>Credit/Debit Cards (Visa, Mastercard, Amex)</li>
                                    <li>Bank Transfer/Direct Deposit</li>
                                    <li>Mobile Payments (eZ Cash, mCash, Genie, FriMi)</li>
                                    <li>All transactions are secure and encrypted</li>
                                </ul>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question">
                                <h4>Is the payment refundable?</h4>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                <p>Payment refunds are available in limited circumstances:</p>
                                <ul>
                                    <li>Technical issues preventing test completion</li>
                                    <li>System errors on our end</li>
                                    <li>Duplicate payments</li>
                                    <li>Refunds are not available for failed tests or missed appointments</li>
                                    <li>Contact support for refund requests</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="category-card glass-card">
                    <div class="category-header">
                        <div class="category-icon">🆘</div>
                        <h3>Technical Support</h3>
                        <p>Help with technical issues and troubleshooting</p>
                    </div>
                    <div class="category-questions">
                        <div class="faq-item">
                            <div class="faq-question">
                                <h4>What if I can't access my account?</h4>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                <p>If you can't access your account:</p>
                                <ul>
                                    <li>Try resetting your password using "Forgot Password"</li>
                                    <li>Check your email for reset instructions</li>
                                    <li>Ensure you're using the correct NIC number</li>
                                    <li>Clear your browser cache and cookies</li>
                                    <li>Contact support if issues persist</li>
                                </ul>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question">
                                <h4>What if I can't upload documents?</h4>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                <p>Document upload issues can be resolved by:</p>
                                <ul>
                                    <li>Checking file size (max 5MB per file)</li>
                                    <li>Ensuring file format is JPG, PNG, or PDF</li>
                                    <li>Using a stable internet connection</li>
                                    <li>Clearing browser cache</li>
                                    <li>Trying a different browser</li>
                                    <li>Contacting support for assistance</li>
                                </ul>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question">
                                <h4>What browsers are supported?</h4>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                <p>LicenseXpress works best with:</p>
                                <ul>
                                    <li>Chrome (latest version)</li>
                                    <li>Firefox (latest version)</li>
                                    <li>Safari (latest version)</li>
                                    <li>Edge (latest version)</li>
                                    <li>Ensure JavaScript is enabled</li>
                                    <li>Use a modern browser for best experience</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

           
            <div class="support-section glass-card">
                <div class="support-content">
                    <div class="support-icon">💬</div>
                    <div class="support-text">
                        <h3>Still have questions?</h3>
                        <p>Our support team is here to help you with any questions or issues you may have.</p>
                    </div>
                    <div class="support-actions">
                        <a href="contactus.php" class="btn btn-primary">Contact Support</a>
                        <a href="mailto:support@licensexpress.lk" class="btn btn-secondary">Email Us</a>
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
    <script src="../assets/js/faq.js"></script>
</body>
</html>
