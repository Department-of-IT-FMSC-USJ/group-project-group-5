<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guidelines - LicenseXpress</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/guidelines.css">
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
                    <a href="faq.php" class="nav-link">FAQ</a>
                    <a href="guidelines.php" class="nav-link active">Guidelines</a>
                </nav>
                <div class="header-actions">
                    <a href="../login.php" class="btn btn-primary">Sign In</a>
                </div>
            </div>
        </div>
    </header>

    
    <main class="guidelines-main">
        <div class="container">
            
            <div class="page-header">
                <h1 class="page-title">Application Guidelines</h1>
                <p class="page-subtitle">Complete guide to applying for your driving license through LicenseXpress</p>
            </div>

            
            <div class="toc-section glass-card">
                <h2>📋 Table of Contents</h2>
                <div class="toc-grid">
                    <a href="#eligibility" class="toc-item">
                        <span class="toc-icon">✅</span>
                        <span class="toc-text">Eligibility Requirements</span>
                    </a>
                    <a href="#documents" class="toc-item">
                        <span class="toc-icon">📄</span>
                        <span class="toc-text">Required Documents</span>
                    </a>
                    <a href="#application" class="toc-item">
                        <span class="toc-icon">📝</span>
                        <span class="toc-text">Application Process</span>
                    </a>
                    <a href="#theory" class="toc-item">
                        <span class="toc-icon">📚</span>
                        <span class="toc-text">Theory Test</span>
                    </a>
                    <a href="#practical" class="toc-item">
                        <span class="toc-icon">🚗</span>
                        <span class="toc-text">Practical Test</span>
                    </a>
                    <a href="#fees" class="toc-item">
                        <span class="toc-icon">💳</span>
                        <span class="toc-text">Fees & Payment</span>
                    </a>
                </div>
            </div>

            
            <section class="video-section">
                <div class="section-header">
                    <h2>📹 Video Guidelines</h2>
                    <p>Watch step-by-step video tutorials in your preferred language</p>
                </div>
                <div class="video-container glass-card">
                    
                    <div class="language-tabs">
                        <button class="language-tab active" data-lang="sinhala">
                            <span class="tab-icon">🇱🇰</span>
                            <span class="tab-text">සිංහල</span>
                        </button>
                        <button class="language-tab" data-lang="tamil">
                            <span class="tab-icon">🇱🇰</span>
                            <span class="tab-text">தமிழ்</span>
                        </button>
                        <button class="language-tab" data-lang="english">
                            <span class="tab-icon">🇬🇧</span>
                            <span class="tab-text">English</span>
                        </button>
                    </div>

                    
                    <div class="video-content">
                        
                        <div class="video-wrapper active" id="video-sinhala">
                            <div class="video-frame">
                                <iframe 
                                    width="100%" 
                                    height="500" 
                                    src="https://www.youtube.com/embed/BvmAk6c6zXw" 
                                    title="Sinhala License Application Guide"
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                                </iframe>
                            </div>
                            <div class="video-info">
                                <h3>ලයිසන්ස් අයදුම්පත්‍ර මාර්ගෝපදේශ</h3>
                                <p>LicenseXpress හරහා ඔබේ රියදුරු බලපත්‍රය සඳහා ඉල්ලුම් කරන ආකාරය පිළිබඳ සම්පූර්ණ මාර්ගෝපදේශය</p>
                            </div>
                        </div>

                
                        <div class="video-wrapper" id="video-tamil">
                            <div class="video-frame">
                                <iframe 
                                    width="100%" 
                                    height="500" 
                                    src="https://www.youtube.com/embed/w_O6eMpWMcY" 
                                    title="Tamil License Application Guide"
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                                </iframe>
                            </div>
                            <div class="video-info">
                                <h3>உரிம விண்ணப்ப வழிகாட்டுதல்கள்</h3>
                                <p>LicenseXpress மூலம் உங்கள் ஓட்டுநர் உரிமத்திற்கு விண்ணப்பிப்பது எப்படி என்பது பற்றிய முழுமையான வழிகாட்டி</p>
                            </div>
                        </div>

                        
                        <div class="video-wrapper" id="video-english">
                            <div class="video-frame">
                                <iframe 
                                    width="100%" 
                                    height="500" 
                                    src="https://www.youtube.com/embed/yhksFAe4uk4"
                                    title="English License Application Guide"
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                                </iframe>
                            </div>
                            <div class="video-info">
                                <h3>License Application Guidelines</h3>
                                <p>Complete guide on how to apply for your driving license through LicenseXpress</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            
            <section id="eligibility" class="guideline-section">
                <div class="section-header">
                    <h2>✅ Eligibility Requirements</h2>
                    <p>Who can apply for a driving license through LicenseXpress</p>
                </div>
                <div class="guideline-content glass-card">
                    <div class="requirement-grid">
                        <div class="requirement-item">
                            <div class="requirement-icon">🎂</div>
                            <div class="requirement-content">
                                <h3>Age Requirements</h3>
                                <ul>
                                    <li>Minimum age: 18 years</li>
                                    <li>Maximum age: 70 years</li>
                                    <li>Age verification through birth certificate</li>
                                </ul>
                            </div>
                        </div>
                        <div class="requirement-item">
                            <div class="requirement-icon">🆔</div>
                            <div class="requirement-content">
                                <h3>Identity Requirements</h3>
                                <ul>
                                    <li>Valid Sri Lankan NIC</li>
                                    <li>Valid email address</li>
                                    <li>Active mobile phone number</li>
                                </ul>
                            </div>
                        </div>
                        <div class="requirement-item">
                            <div class="requirement-icon">🏥</div>
                            <div class="requirement-content">
                                <h3>Medical Requirements</h3>
                                <ul>
                                    <li>Medical fitness certificate</li>
                                    <li>Issued within 6 months</li>
                                    <li>From registered medical officer</li>
                                </ul>
                            </div>
                        </div>
                        <div class="requirement-item">
                            <div class="requirement-icon">📱</div>
                            <div class="requirement-content">
                                <h3>Technical Requirements</h3>
                                <ul>
                                    <li>Internet connection</li>
                                    <li>Webcam for theory test</li>
                                    <li>Computer or tablet device</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        
            <section id="documents" class="guideline-section">
                <div class="section-header">
                    <h2>📄 Required Documents</h2>
                    <p>Essential documents you need to prepare for your application</p>
                </div>
                <div class="guideline-content glass-card">
                    <div class="document-grid">
                        <div class="document-item">
                            <div class="document-icon">📋</div>
                            <div class="document-info">
                                <h3>Birth Certificate</h3>
                                <p>Official birth certificate issued by the Registrar General</p>
                                <div class="document-requirements">
                                    <h4>Requirements:</h4>
                                    <ul>
                                        <li>Clear, readable scan or photo</li>
                                        <li>All text must be visible</li>
                                        <li>No damage or tears</li>
                                        <li>File format: PDF, JPG, or PNG</li>
                                        <li>Maximum file size: 5MB</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="document-item">
                            <div class="document-icon">🪪</div>
                            <div class="document-info">
                                <h3>National Identity Card (NIC)</h3>
                                <p>Both front and back of your NIC</p>
                                <div class="document-requirements">
                                    <h4>Requirements:</h4>
                                    <ul>
                                        <li>Front and back in same image or separate</li>
                                        <li>All text clearly visible</li>
                                        <li>No glare or shadows</li>
                                        <li>Card edges visible</li>
                                        <li>High resolution (minimum 1200x900 pixels)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="document-item">
                            <div class="document-icon">🏥</div>
                            <div class="document-info">
                                <h3>Medical Certificate</h3>
                                <p>Medical fitness certificate from a registered medical officer</p>
                                <div class="document-requirements">
                                    <h4>Requirements:</h4>
                                    <ul>
                                        <li>Issued within the last 6 months</li>
                                        <li>From registered medical officer</li>
                                        <li>Clear signature and stamp</li>
                                        <li>All text readable</li>
                                        <li>PDF format preferred</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="document-item">
                            <div class="document-icon">📸</div>
                            <div class="document-info">
                                <h3>Passport Size Photograph</h3>
                                <p>Recent passport size photograph with white background</p>
                                <div class="document-requirements">
                                    <h4>Requirements:</h4>
                                    <ul>
                                        <li>White background only</li>
                                        <li>Face clearly visible</li>
                                        <li>No glasses or hats</li>
                                        <li>Recent photo (within 6 months)</li>
                                        <li>Passport size (35mm x 45mm)</li>
                                        <li>Professional quality</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            
            <section id="application" class="guideline-section">
                <div class="section-header">
                    <h2>📝 Application Process</h2>
                    <p>Step-by-step guide to completing your application</p>
                </div>
                <div class="guideline-content glass-card">
                    <div class="process-steps">
                        <div class="step-item">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h3>Create Account</h3>
                                <p>Register with your personal details and create a secure account</p>
                                <ul>
                                    <li>Provide full name as per NIC</li>
                                    <li>Enter valid NIC number</li>
                                    <li>Use active email and phone number</li>
                                    <li>Create strong password</li>
                                </ul>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h3>Complete Application Form</h3>
                                <p>Fill in your personal information and preferences</p>
                                <ul>
                                    <li>Personal details (name, DOB, gender)</li>
                                    <li>Contact information</li>
                                    <li>Transmission type preference</li>
                                    <li>District selection</li>
                                </ul>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h3>Upload Documents</h3>
                                <p>Upload all required documents in the specified format</p>
                                <ul>
                                    <li>Ensure all documents are clear and readable</li>
                                    <li>Check file sizes and formats</li>
                                    <li>Preview before submitting</li>
                                    <li>All documents must be uploaded</li>
                                </ul>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-number">4</div>
                            <div class="step-content">
                                <h3>Payment</h3>
                                <p>Complete payment for your application</p>
                                <ul>
                                    <li>Total fee: Rs. 3,200</li>
                                    <li>Multiple payment methods available</li>
                                    <li>Secure payment processing</li>
                                    <li>Instant confirmation</li>
                                </ul>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-number">5</div>
                            <div class="step-content">
                                <h3>Document Verification</h3>
                                <p>Our team will review and verify your documents</p>
                                <ul>
                                    <li>Verification takes 2-3 business days</li>
                                    <li>Email and SMS notifications</li>
                                    <li>Resubmission if needed</li>
                                    <li>Approval notification</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            
            <section id="theory" class="guideline-section">
                <div class="section-header">
                    <h2>📚 Theory Test Guidelines</h2>
                    <p>Everything you need to know about the online theory examination</p>
                </div>
                <div class="guideline-content glass-card">
                    <div class="test-info-grid">
                        <div class="info-card">
                            <h3>📋 Test Format</h3>
                            <ul>
                                <li>50 multiple choice questions</li>
                                <li>60 minutes duration</li>
                                <li>Pass mark: 40/50 (80%)</li>
                                <li>Online examination</li>
                                <li>Instant results</li>
                            </ul>
                        </div>
                        <div class="info-card">
                            <h3>🔒 Security Measures</h3>
                            <ul>
                                <li>Webcam monitoring required</li>
                                <li>Screenshot prevention</li>
                                <li>Copy/paste blocking</li>
                                <li>Tab switching detection</li>
                                <li>Right-click disabled</li>
                            </ul>
                        </div>
                        <div class="info-card">
                            <h3>💻 Technical Requirements</h3>
                            <ul>
                                <li>Stable internet connection</li>
                                <li>Webcam access</li>
                                <li>Modern browser</li>
                                <li>Quiet environment</li>
                                <li>Government-issued ID</li>
                            </ul>
                        </div>
                        <div class="info-card">
                            <h3>📖 Study Resources</h3>
                            <ul>
                                <li>Official driving handbook</li>
                                <li>Practice tests available</li>
                                <li>Road signs guide</li>
                                <li>Safety rules reference</li>
                                <li>Online study materials</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="test-tips">
                        <h3>💡 Tips for Success</h3>
                        <div class="tips-grid">
                            <div class="tip-item">
                                <span class="tip-icon">📚</span>
                                <div class="tip-content">
                                    <h4>Study Thoroughly</h4>
                                    <p>Review the official handbook and take practice tests</p>
                                </div>
                            </div>
                            <div class="tip-item">
                                <span class="tip-icon">⏰</span>
                                <div class="tip-content">
                                    <h4>Manage Your Time</h4>
                                    <p>You have 60 minutes - pace yourself and don't rush</p>
                                </div>
                            </div>
                            <div class="tip-item">
                                <span class="tip-icon">🔍</span>
                                <div class="tip-content">
                                    <h4>Read Carefully</h4>
                                    <p>Read each question thoroughly before answering</p>
                                </div>
                            </div>
                            <div class="tip-item">
                                <span class="tip-icon">🌐</span>
                                <div class="tip-content">
                                    <h4>Test Your Setup</h4>
                                    <p>Ensure your internet and webcam work properly</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            
            <section id="practical" class="guideline-section">
                <div class="section-header">
                    <h2>🚗 Practical Test Guidelines</h2>
                    <p>Preparing for your practical driving examination</p>
                </div>
                <div class="guideline-content glass-card">
                    <div class="practical-info">
                        <div class="practical-overview">
                            <h3>📅 Test Scheduling</h3>
                            <ul>
                                <li>Automatically scheduled 3 months after theory pass</li>
                                <li>At approved test centers</li>
                                <li>With certified DMT examiners</li>
                                <li>Email and SMS confirmations</li>
                                <li>Rescheduling available (with fee)</li>
                            </ul>
                        </div>
                        
                        <div class="practical-requirements">
                            <h3>📋 What to Bring</h3>
                            <ul>
                                <li>Original NIC</li>
                                <li>Theory test pass certificate</li>
                                <li>Learner's permit</li>
                                <li>Vehicle registration (if using own vehicle)</li>
                                <li>Valid insurance certificate</li>
                            </ul>
                        </div>
                        
                        <div class="practical-vehicle">
                            <h3>🚙 Vehicle Options</h3>
                            <div class="vehicle-options">
                                <div class="vehicle-option">
                                    <h4>Own Vehicle</h4>
                                    <ul>
                                        <li>Must be in good working condition</li>
                                        <li>Valid registration and insurance</li>
                                        <li>Appropriate for license category</li>
                                        <li>Clean and presentable</li>
                                    </ul>
                                </div>
                                <div class="vehicle-option">
                                    <h4>Test Center Vehicle</h4>
                                    <ul>
                                        <li>Rental fee: Rs. 2,000</li>
                                        <li>Provided by test center</li>
                                        <li>Fully insured and roadworthy</li>
                                        <li>Automatic or manual transmission</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="practical-test">
                            <h3>🧪 Test Components</h3>
                            <div class="test-components">
                                <div class="component-item">
                                    <span class="component-icon">🔧</span>
                                    <div class="component-content">
                                        <h4>Pre-drive Vehicle Check</h4>
                                        <p>5 minutes - Basic vehicle inspection</p>
                                    </div>
                                </div>
                                <div class="component-item">
                                    <span class="component-icon">🔄</span>
                                    <div class="component-content">
                                        <h4>Basic Maneuvers</h4>
                                        <p>10 minutes - Parking and turning</p>
                                    </div>
                                </div>
                                <div class="component-item">
                                    <span class="component-icon">🛣️</span>
                                    <div class="component-content">
                                        <h4>Road Driving</h4>
                                        <p>25 minutes - Traffic navigation</p>
                                    </div>
                                </div>
                                <div class="component-item">
                                    <span class="component-icon">🅿️</span>
                                    <div class="component-content">
                                        <h4>Parking Test</h4>
                                        <p>5 minutes - Parallel parking</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            
            <section id="fees" class="guideline-section">
                <div class="section-header">
                    <h2>💳 Fees & Payment</h2>
                    <p>Complete breakdown of costs and payment options</p>
                </div>
                <div class="guideline-content glass-card">
                    <div class="fees-breakdown">
                        <h3>💰 Fee Structure</h3>
                        <div class="fee-table">
                            <div class="fee-row">
                                <span class="fee-item">Application Fee</span>
                                <span class="fee-amount">Rs. 1,500.00</span>
                            </div>
                            <div class="fee-row">
                                <span class="fee-item">Document Processing</span>
                                <span class="fee-amount">Rs. 500.00</span>
                            </div>
                            <div class="fee-row">
                                <span class="fee-item">Theory Test Fee</span>
                                <span class="fee-amount">Rs. 1,000.00</span>
                            </div>
                            <div class="fee-row">
                                <span class="fee-item">Service Charge</span>
                                <span class="fee-amount">Rs. 200.00</span>
                            </div>
                            <div class="fee-row total">
                                <span class="fee-item">Total</span>
                                <span class="fee-amount">Rs. 3,200.00</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="additional-fees">
                        <h3>💸 Additional Fees</h3>
                        <div class="additional-fee-grid">
                            <div class="additional-fee-item">
                                <h4>Theory Test Reschedule</h4>
                                <p>Rs. 500.00</p>
                            </div>
                            <div class="additional-fee-item">
                                <h4>Practical Test Reschedule</h4>
                                <p>Rs. 1,000.00</p>
                            </div>
                            <div class="additional-fee-item">
                                <h4>Test Center Vehicle Rental</h4>
                                <p>Rs. 2,000.00</p>
                            </div>
                            <div class="additional-fee-item">
                                <h4>Duplicate License</h4>
                                <p>Rs. 500.00</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="payment-methods">
                        <h3>💳 Payment Methods</h3>
                        <div class="payment-grid">
                            <div class="payment-method">
                                <div class="payment-icon">💳</div>
                                <div class="payment-info">
                                    <h4>Credit/Debit Cards</h4>
                                    <p>Visa, Mastercard, American Express</p>
                                </div>
                            </div>
                            <div class="payment-method">
                                <div class="payment-icon">🏦</div>
                                <div class="payment-info">
                                    <h4>Bank Transfer</h4>
                                    <p>Direct deposit to our bank account</p>
                                </div>
                            </div>
                            <div class="payment-method">
                                <div class="payment-icon">📱</div>
                                <div class="payment-info">
                                    <h4>Mobile Payments</h4>
                                    <p>eZ Cash, mCash, Genie, FriMi</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            
            <div class="support-section glass-card">
                <div class="support-content">
                    <div class="support-icon">💬</div>
                    <div class="support-text">
                        <h3>Need Help?</h3>
                        <p>Our support team is available to help you with any questions about the application process.</p>
                    </div>
                    <div class="support-actions">
                        <a href="contactus.php" class="btn btn-primary">Contact Support</a>
                        <a href="faq.php" class="btn btn-secondary">View FAQ</a>
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
    <script src="../assets/js/guidelines.js"></script>
</body>
</html>
