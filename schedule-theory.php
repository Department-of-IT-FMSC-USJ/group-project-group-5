<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Theory Test - LicenseXpress</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/schedule-theory.css">
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
                    <a href="dashboard.php" class="nav-link">Dashboard</a>
                    <a href="schedule-theory.php" class="nav-link active">Schedule Test</a>
                    <a href="pages/about.php" class="nav-link">About</a>
                    <a href="pages/contactus.php" class="nav-link">Contact</a>
                </nav>
                <div class="header-actions">
                    <div class="user-info">
                        <div class="user-avatar" id="userAvatar">J</div>
                        <div class="user-details">
                            <div class="user-name" id="userName">John Doe</div>
                            <div class="user-nic" id="userNIC">200012345678</div>
                        </div>
                    </div>
                    <button class="logout-btn" id="logoutBtn" title="Logout">
                        <span>🚪</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

   
    <div class="breadcrumb">
        <div class="container">
            <span class="breadcrumb-item">Dashboard</span>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">Schedule Theory Test</span>
        </div>
    </div>

   
    <div class="progress-steps">
        <div class="container">
            <div class="steps-container">
                <div class="step completed">
                    <div class="step-icon">✓</div>
                    <div class="step-label">Documents Uploaded</div>
                </div>
                <div class="step completed">
                    <div class="step-icon">✓</div>
                    <div class="step-label">Verification Complete</div>
                </div>
                <div class="step active">
                    <div class="step-icon">3</div>
                    <div class="step-label">Schedule Test</div>
                </div>
                <div class="step">
                    <div class="step-icon">4</div>
                    <div class="step-label">Confirmation</div>
                </div>
            </div>
        </div>
    </div>

   
    <main class="schedule-main">
        <div class="container">
            <div class="schedule-container">
                
                <div class="schedule-header">
                    <h1 class="schedule-title">Schedule Your Theory Test</h1>
                    <p class="schedule-subtitle">Select your preferred date and time slot for your theory examination</p>
                </div>

               
                <div class="info-alerts">
                    <div class="info-alert">
                        <div class="alert-icon">ℹ️</div>
                        <div class="alert-content">
                            <strong>Important Information:</strong>
                            <p>The Theory Test is conducted online. You can take it from anywhere with a stable internet connection. The Practical Test date will be automatically scheduled 3 months after you pass the Theory Test.</p>
                        </div>
                    </div>
                    <div class="warning-alert">
                        <div class="alert-icon">⚠️</div>
                        <div class="alert-content">
                            <strong>Booking Policy:</strong>
                            <p>Tests must be scheduled at least 48 hours (2 days) in advance. Same-day and next-day bookings are not available.</p>
                        </div>
                    </div>
                </div>

                
                <div class="calendar-section">
                    <div class="section-header">
                        <h2 class="section-title">📅 Step 1: Choose Your Test Date</h2>
                        <span class="required-badge">Required</span>
                    </div>
                    
                    <div class="calendar-container glass-card">
                        <div class="calendar-header">
                            <button class="calendar-nav" id="prevMonth">‹</button>
                            <h3 class="calendar-month" id="calendarMonth">January 2025</h3>
                            <button class="calendar-nav" id="nextMonth">›</button>
                        </div>
                        
                        <div class="calendar-grid">
                            <div class="calendar-weekdays">
                                <div class="weekday">Sun</div>
                                <div class="weekday">Mon</div>
                                <div class="weekday">Tue</div>
                                <div class="weekday">Wed</div>
                                <div class="weekday">Thu</div>
                                <div class="weekday">Fri</div>
                                <div class="weekday">Sat</div>
                            </div>
                            <div class="calendar-days" id="calendarDays">
                                
                            </div>
                        </div>
                        
                        <div class="calendar-legend">
                            <div class="legend-item">
                                <div class="legend-color selected"></div>
                                <span>Selected</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color unavailable"></div>
                                <span>Unavailable</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color today"></div>
                                <span>Today</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color too-soon">🔒</div>
                                <span>Too Soon (2-day minimum)</span>
                            </div>
                        </div>
                    </div>
                </div>

           
                <div class="time-slots-section hidden" id="timeSlotsSection">
                    <div class="section-header">
                        <h2 class="section-title">⏰ Step 2: Select Time Slot</h2>
                        <span class="required-badge">Required</span>
                    </div>
                    
                    <div class="selected-date-info">
                        <div class="date-info-content">
                            <span class="date-label">Selected Date:</span>
                            <span class="date-value" id="selectedDate">Wednesday, January 15, 2025</span>
                        </div>
                    </div>
                    
                    <div class="time-slots-container glass-card">
                        <div class="loading-state" id="timeSlotsLoading">
                            <div class="loading-spinner">
                                <div class="spinner"></div>
                            </div>
                            <div class="loading-text">Loading available time slots...</div>
                        </div>
                        
                        <div class="time-slots-grid hidden" id="timeSlotsGrid">
                        
                        </div>
                    </div>
                </div>

                
                <div class="booking-summary hidden" id="bookingSummary">
                    <div class="section-header">
                        <h2 class="section-title">📋 Booking Summary</h2>
                    </div>
                    
                    <div class="summary-cards">
                        <div class="summary-card glass-card">
                            <div class="card-icon">📝</div>
                            <div class="card-content">
                                <div class="card-label">Test Type</div>
                                <div class="card-value">Theory Test (Online)</div>
                            </div>
                        </div>
                        <div class="summary-card glass-card">
                            <div class="card-icon">📅</div>
                            <div class="card-content">
                                <div class="card-label">Test Date</div>
                                <div class="card-value" id="summaryDate">Wednesday, January 15, 2025</div>
                            </div>
                        </div>
                        <div class="summary-card glass-card">
                            <div class="card-icon">⏰</div>
                            <div class="card-content">
                                <div class="card-label">Time Slot</div>
                                <div class="card-value" id="summaryTime">10:00 AM</div>
                            </div>
                        </div>
                        <div class="summary-card glass-card">
                            <div class="card-icon">💻</div>
                            <div class="card-content">
                                <div class="card-label">Test Format</div>
                                <div class="card-value">Online Examination</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="test-info-box glass-card">
                        <div class="info-icon">📱</div>
                        <div class="info-content">
                            <strong>Online Test:</strong> You'll receive a link via email to take the test online from anywhere with internet access.
                        </div>
                    </div>
                </div>

               
                <div class="action-buttons">
                    <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
                    <button class="btn btn-primary btn-large" id="confirmBooking" disabled>
                        <span class="btn-text">Confirm Booking →</span>
                        <div class="btn-spinner hidden">
                            <div class="spinner"></div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </main>

   
    <div class="success-modal hidden" id="successModal">
        <div class="modal-backdrop"></div>
        <div class="modal-content">
            <div class="success-icon">✅</div>
            <div class="success-title">Booking Confirmed!</div>
            <div class="success-message">
                Your online theory test has been successfully scheduled.
            </div>
            <div class="success-details">
                <p><strong>📧 Test link and instructions sent to email</strong></p>
                <p><strong>📱 SMS confirmation sent to your phone</strong></p>
                <p>You will receive a reminder 24 hours before your test via email and SMS with the online test link.</p>
            </div>
            <div class="success-actions">
                <button class="btn btn-primary" id="goToDashboard">Go to Dashboard</button>
            </div>
        </div>
    </div>

    <script src="assets/js/app.js"></script>
    <script src="assets/js/schedule-theory.js"></script>
</body>
</html>
