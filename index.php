
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LicenseXpress - Your Digital Path to Driving Freedom</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    
    <style>
        
        .background-animations {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        
       
        .floating-square {
            position: absolute;
            border: 1px solid var(--primary-light);
            opacity: 0;
            animation: floatSquare 20s linear infinite;
        }
        
        @keyframes floatSquare {
            0% {
                transform: translateX(-100px) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 0.1;
            }
            90% {
                opacity: 0.1;
            }
            100% {
                transform: translateX(calc(100vw + 100px)) rotate(360deg);
                opacity: 0;
            }
        }
        
        
        .gradient-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.3;
            animation: floatOrb 20s ease-in-out infinite;
        }
        
        .orb-1 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, var(--primary-light) 0%, transparent 70%);
            top: 10%;
            left: 10%;
        }
        
        .orb-2 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, var(--accent) 0%, transparent 70%);
            top: 60%;
            right: 10%;
        }
        
        .orb-3 {
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, var(--primary-bright) 0%, transparent 70%);
            bottom: 20%;
            left: 30%;
        }
        
        @keyframes floatOrb {
            0%, 100% { transform: translateY(0px) translateX(0px); }
            25% { transform: translateY(-20px) translateX(10px); }
            50% { transform: translateY(0px) translateX(-10px); }
            75% { transform: translateY(20px) translateX(5px); }
        }
        
        
        .wave-overlay {
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 20%, rgba(0, 95, 115, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 70% 80%, rgba(10, 147, 150, 0.1) 0%, transparent 50%);
            animation: waveRotate 30s linear infinite;
        }
        
        @keyframes waveRotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        
        .grid-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: gridMove 20s linear infinite;
        }
        
        @keyframes gridMove {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }
        
       
        .flow-line {
            position: absolute;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--primary-light), transparent);
            animation: flowLine 3s ease-out forwards;
        }
        
        @keyframes flowLine {
            0% {
                width: 0;
                opacity: 0;
                transform: translateX(-100px);
            }
            50% {
                opacity: 0.6;
            }
            100% {
                width: 200px;
                opacity: 0;
                transform: translateX(100vw);
            }
        }
        
       
        .ripple {
            position: absolute;
            border: 2px solid var(--primary-light);
            border-radius: 50%;
            animation: rippleExpand 4s ease-out forwards;
        }
        
        @keyframes rippleExpand {
            0% {
                width: 0;
                height: 0;
                opacity: 0.8;
            }
            100% {
                width: 300px;
                height: 300px;
                opacity: 0;
            }
        }
        
       
        .hero, .stats, .features, .how-it-works, .cta {
            position: relative;
            z-index: 1;
        }
        
        
        .features, .how-it-works {
            background: transparent;
        }
        
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr); 
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
       
        @media (max-width: 992px) {
            .features-grid {
                grid-template-columns: repeat(2, 1fr); 
                gap: 1.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .features-grid {
                grid-template-columns: 1fr; 
                gap: 1.25rem;
            }
        }
        
        
        .step-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 40px 32px;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .step-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            border-radius: 24px;
            z-index: -1;
        }
        
        .step-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            background: rgba(255, 255, 255, 0.08);
        }
        
        
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr); 
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        
        @media (max-width: 992px) {
            .steps-grid {
                grid-template-columns: repeat(2, 1fr); 
                gap: 1.5rem;
            }
            .step-card { 
                padding: 32px 24px; 
            }
        }
        
        @media (max-width: 768px) {
            .steps-grid {
                grid-template-columns: 1fr; 
                gap: 1.25rem;
            }
            .step-card { 
                padding: 24px 20px; 
            }
        }
    </style>
</head>
<body>
    
    <div class="background-animations" id="background-animations">
       
        <div class="gradient-orb orb-1"></div>
        <div class="gradient-orb orb-2"></div>
        <div class="gradient-orb orb-3"></div>
        
        
        <div class="wave-overlay"></div>
        
        
        <div class="grid-pattern"></div>
        
        
    </div>

   
    <div id="splash-screen" class="splash-screen">
        <div class="splash-content">
            <div class="splash-logo">
                <div class="logo-icon">LX</div>
            </div>
            <div class="splash-text">
                <h1 class="splash-title">
                    <span class="title-main">License</span>
                    <span class="title-accent">Xpress</span>
                </h1>
                <p class="splash-tagline">Your Digital Path to Driving Freedom</p>
            </div>
        </div>
    </div>

   
    <header class="header" id="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <div class="logo-icon">LX</div>
                    <span class="logo-text">LicenseXpress</span>
                </div>
                <nav class="nav">
                    <a href="#home" class="nav-link">Home</a>
                    <a href="pages/learners.php" class="nav-link">Find Schools</a>
                    <a href="pages/about.php" class="nav-link">About Us</a>
                    <a href="pages/contactus.php" class="nav-link">Contact</a>
                    <a href="pages/faq.php" class="nav-link">FAQ</a>
                    <a href="pages/guidelines.php" class="nav-link">Guidelines</a>
                </nav>
                <div class="header-actions">
                    <a href="login.php" class="btn btn-primary">Sign In</a>
                    <button class="mobile-menu-toggle" id="mobile-menu-toggle">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    
    <section class="hero" id="home">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">
                    Transform Your Driving License Journey
                </h1>
                <p class="hero-subtitle">
                    Experience the future of license applications with our cutting-edge digital platform. 
                    Streamlined, secure, and designed for the modern driver.
                </p>
                <div class="hero-actions">
                    <a href="register.php" class="btn btn-primary btn-large">Start Your Journey</a>
                    <a href="#features" class="btn btn-secondary btn-large">Explore Features</a>
                </div>
            </div>
        </div>
    </section>

   
    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card" data-target="98">
                    <div class="stat-icon">🏆</div>
                    <div class="stat-number" data-target="98">0%</div>
                    <div class="stat-label">Success Rate</div>
                </div>
                <div class="stat-card" data-target="15000">
                    <div class="stat-icon">👥</div>
                    <div class="stat-number" data-target="15000">0+</div>
                    <div class="stat-label">Happy Drivers</div>
                </div>
                <div class="stat-card" data-target="72">
                    <div class="stat-icon">⏱️</div>
                    <div class="stat-number" data-target="72">0</div>
                    <div class="stat-label">Hours Saved</div>
                </div>
                <div class="stat-card" data-target="24">
                    <div class="stat-icon">🕐</div>
                    <div class="stat-number" data-target="24">0/7</div>
                    <div class="stat-label">Available Support</div>
                </div>
            </div>
        </div>
    </section>

    
    <section class="features" id="features">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Why Choose LicenseXpress?</h2>
                <p class="section-subtitle">Discover the features that make us the preferred choice for digital license applications</p>
            </div>
            <div class="features-grid">
                <div class="feature-card glass-card">
                    <div class="feature-icon-container">
                        <div class="feature-icon">🚀</div>
                    </div>
                    <h3 class="feature-title">Lightning Fast Process</h3>
                    <p class="feature-description">Complete your entire application in minutes, not hours. Our streamlined digital process eliminates unnecessary paperwork and waiting.</p>
                </div>
                <div class="feature-card glass-card">
                    <div class="feature-icon-container">
                        <div class="feature-icon">🔒</div>
                    </div>
                    <h3 class="feature-title">Bank-Level Security</h3>
                    <p class="feature-description">Your personal data is protected with advanced encryption and security measures that exceed industry standards.</p>
                </div>
                <div class="feature-card glass-card">
                    <div class="feature-icon-container">
                        <div class="feature-icon">📱</div>
                    </div>
                    <h3 class="feature-title">Mobile-First Design</h3>
                    <p class="feature-description">Apply from anywhere, anytime. Our responsive platform works seamlessly across all devices and screen sizes.</p>
                </div>
                <div class="feature-card glass-card">
                    <div class="feature-icon-container">
                        <div class="feature-icon">🎯</div>
                    </div>
                    <h3 class="feature-title">Real-Time Tracking</h3>
                    <p class="feature-description">Monitor your application status with live updates and notifications. Know exactly where you stand at every step.</p>
                </div>
                <div class="feature-card glass-card">
                    <div class="feature-icon-container">
                        <div class="feature-icon">🏫</div>
                    </div>
                    <h3 class="feature-title">Driving School Network</h3>
                    <p class="feature-description">Connect with certified driving schools in your area. Compare prices, read reviews, and book lessons directly through our platform.</p>
                </div>
                <div class="feature-card glass-card">
                    <div class="feature-icon-container">
                        <div class="feature-icon">💳</div>
                    </div>
                    <h3 class="feature-title">Secure Payments</h3>
                    <p class="feature-description">Multiple payment options including digital wallets, bank transfers, and card payments. All transactions are encrypted and secure.</p>
                </div>
            </div>
        </div>
    </section>

    
    <section class="how-it-works">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">How It Works</h2>
                <p class="section-subtitle">Get your license in just 4 simple steps</p>
            </div>
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <div class="step-icon">📝</div>
                    <h3 class="step-title">Create Account</h3>
                    <p class="step-description">Sign up with your basic information and verify your identity</p>
                </div>
                <div class="step-card">
                    <div class="step-number">2</div>
                    <div class="step-icon">📁</div>
                    <h3 class="step-title">Upload Documents</h3>
                    <p class="step-description">Upload required documents with our smart verification system</p>
                </div>
                <div class="step-card">
                    <div class="step-number">3</div>
                    <div class="step-icon">📅</div>
                    <h3 class="step-title">Schedule Tests</h3>
                    <p class="step-description">Book your theory and practical tests at your convenience</p>
                </div>
                <div class="step-card">
                    <div class="step-number">4</div>
                    <div class="step-icon">🎓</div>
                    <h3 class="step-title">Get Your License</h3>
                    <p class="step-description">Receive your digital license instantly upon completion</p>
                </div>
            </div>
        </div>
    </section>

    
    <section class="cta">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Ready to Get Started?</h2>
                <p class="cta-description">
                    Join thousands of drivers who have already transformed their license application experience. 
                    Start your journey today and get your license faster than ever before.
                </p>
                <div class="cta-actions">
                    <a href="register.php" class="btn btn-primary btn-large">Create Free Account</a>
                    <a href="pages/about.php" class="btn btn-secondary btn-large">Learn More</a>
                </div>
            </div>
        </div>
    </section>

   
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <div class="logo-icon">LX</div>
                        <span class="logo-text">LicenseXpress</span>
                    </div>
                    <p class="footer-description">
                        Your trusted partner in digital license applications. 
                        Making driving accessible to everyone.
                    </p>
                </div>
                <div class="footer-section">
                    <h4 class="footer-title">Services</h4>
                    <ul class="footer-links">
                        <li><a href="register.php">Apply for License</a></li>
                        <li><a href="pages/learners.php">Find Schools</a></li>
                        <li><a href="pages/guidelines.php">Guidelines</a></li>
                        <li><a href="pages/faq.php">FAQ</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4 class="footer-title">Support</h4>
                    <ul class="footer-links">
                        <li><a href="pages/contactus.php">Contact Us</a></li>
                        <li><a href="pages/faq.php">Help Center</a></li>
                        <li><a href="#">Status</a></li>
                        <li><a href="#">Report Issue</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4 class="footer-title">Company</h4>
                    <ul class="footer-links">
                        <li><a href="pages/about.php">About Us</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Careers</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="footer-copyright">
                    © 2025 LicenseXpress. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <script src="assets/js/app.js"></script>
    <script>
        
        window.addEventListener('load', function() {
            const splash = document.getElementById('splash-screen');
            const header = document.getElementById('header');
            
            setTimeout(() => {
                splash.style.opacity = '0';
                splash.style.transform = 'scale(1.1)';
                setTimeout(() => {
                    splash.style.display = 'none';
                    header.style.display = 'block';
                }, 500);
            }, 3000);
        });

        
        function createFloatingSquares() {
            const container = document.getElementById('background-animations');
            
            for (let i = 0; i < 8; i++) {
                const square = document.createElement('div');
                square.className = 'floating-square';
                
               
                const size = Math.random() * 100 + 50;
                square.style.width = size + 'px';
                square.style.height = size + 'px';
                
               
                square.style.top = Math.random() * 100 + '%';
                square.style.left = '-100px';
                
                
                const duration = Math.random() * 10 + 15;
                square.style.animationDuration = duration + 's';
                
                
                const delay = Math.random() * 15;
                square.style.animationDelay = delay + 's';
                
                container.appendChild(square);
            }
        }

        
        function createFlowLines() {
            const container = document.getElementById('background-animations');
            
            setInterval(() => {
                const line = document.createElement('div');
                line.className = 'flow-line';
                
              
                line.style.top = Math.random() * 100 + '%';
                line.style.left = '0';
                
                container.appendChild(line);
                
                // Remove after animation completes
                setTimeout(() => {
                    if (line.parentNode) {
                        line.parentNode.removeChild(line);
                    }
                }, 3000);
            }, 3000);
        }

        // Create ripple effects
        function createRippleEffects() {
            const container = document.getElementById('background-animations');
            
            setInterval(() => {
                const ripple = document.createElement('div');
                ripple.className = 'ripple';
                
                // Random position
                ripple.style.left = Math.random() * 100 + '%';
                ripple.style.top = Math.random() * 100 + '%';
                
                container.appendChild(ripple);
                
                // Remove after animation completes
                setTimeout(() => {
                    if (ripple.parentNode) {
                        ripple.parentNode.removeChild(ripple);
                    }
                }, 4000);
            }, 4000);
        }

        // Parallax effect for background elements
        function initParallaxEffect() {
            let ticking = false;
            
            function updateParallax() {
                const scrolled = window.pageYOffset;
                const squares = document.querySelectorAll('.floating-square');
                const orbs = document.querySelectorAll('.gradient-orb');
                
                // Move squares 
                squares.forEach((square, index) => {
                    const speed = 0.5 + (index * 0.1);
                    square.style.transform = `translateY(${scrolled * speed}px)`;
                });
                
                // Move orbs at different speeds
                orbs.forEach((orb, index) => {
                    const speed = 0.3 + (index * 0.2);
                    orb.style.transform = `translateY(${scrolled * speed}px)`;
                });
                
                ticking = false;
            }
            
            window.addEventListener('scroll', () => {
                if (!ticking) {
                    requestAnimationFrame(updateParallax);
                    ticking = true;
                }
            });
        }

       
        function initMouseInteraction() {
            const orbs = document.querySelectorAll('.gradient-orb');
            let mouseX = 0, mouseY = 0;
            let animationId;
            
            function updateOrbPositions() {
                orbs.forEach((orb, index) => {
                    const speed = 0.02 * (index + 1);
                    const deltaX = (mouseX - window.innerWidth / 2) * speed;
                    const deltaY = (mouseY - window.innerHeight / 2) * speed;
                    
                    
                    const currentTransform = orb.style.transform || '';
                    const baseTransform = currentTransform.replace(/translate\([^)]*\)/g, '').trim();
                    orb.style.transform = `${baseTransform} translate(${deltaX}px, ${deltaY}px)`;
                });
                
                animationId = requestAnimationFrame(updateOrbPositions);
            }
            
            document.addEventListener('mousemove', (e) => {
                mouseX = e.clientX;
                mouseY = e.clientY;
                
                // Start animation loop if not already running
                if (!animationId) {
                    updateOrbPositions();
                }
            });
            
            // Stop animation when mouse leaves window
            document.addEventListener('mouseleave', () => {
                if (animationId) {
                    cancelAnimationFrame(animationId);
                    animationId = null;
                }
            });
        }

        // Initialize all background effects when page loads
        document.addEventListener('DOMContentLoaded', () => {
            createFloatingSquares();
            createFlowLines();
            createRippleEffects();
            initParallaxEffect();
            initMouseInteraction();
        });

        // Stats counter animation
        function animateCounters() {
            const statNumbers = document.querySelectorAll('.stat-number[data-target]');
            
            statNumbers.forEach(statNumber => {
                const target = parseInt(statNumber.getAttribute('data-target'));
                const duration = 2000;
                const startTime = Date.now();
                
                function updateCounter() {
                    const elapsed = Date.now() - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    
                    
                    const easeOut = 1 - Math.pow(1 - progress, 3);
                    const current = Math.floor(target * easeOut);
                    
                   
                    if (target === 98) {
                        statNumber.textContent = current + '%';
                    } else if (target === 15000) {
                        statNumber.textContent = current.toLocaleString() + '+';
                    } else if (target === 72) {
                        statNumber.textContent = current;
                    } else if (target === 24) {
                        statNumber.textContent = current + '/7';
                    }
                    
                    if (progress < 1) {
                        requestAnimationFrame(updateCounter);
                    }
                }
                
                updateCounter();
            });
        }

        s
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    if (entry.target.classList.contains('stats-grid')) {
                        animateCounters();
                       
                        observer.unobserve(entry.target);
                    } else if (entry.target.classList.contains('feature-card')) {
                        
                        const featureCards = document.querySelectorAll('.feature-card');
                        featureCards.forEach((card, index) => {
                            setTimeout(() => {
                                card.style.opacity = '1';
                                card.style.transform = 'translateY(0)';
                            }, index * 100);
                        });
                        observer.unobserve(entry.target);
                    } else if (entry.target.classList.contains('step-card')) {
                        
                        const stepCards = document.querySelectorAll('.step-card');
                        stepCards.forEach((card, index) => {
                            setTimeout(() => {
                                card.style.opacity = '1';
                                card.style.transform = 'translateY(0)';
                            }, index * 150);
                        });
                        observer.unobserve(entry.target);
                    }
                }
            });
        }, observerOptions);

       
        function initializeCardAnimations() {
            // Set initial state for feature cards
            const featureCards = document.querySelectorAll('.feature-card');
            featureCards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            });

            // Set initial state for step cards
            const stepCards = document.querySelectorAll('.step-card');
            stepCards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            });

            // Observe individual cards and stats grid
            const animateElements = document.querySelectorAll('.feature-card, .step-card, .stats-grid');
            animateElements.forEach(el => observer.observe(el));
        }

        
        document.addEventListener('DOMContentLoaded', () => {
            initializeCardAnimations();
        });
    </script>
</body>
</html>


