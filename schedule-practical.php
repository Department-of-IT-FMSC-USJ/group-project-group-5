<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Practical Test - LicenseXpress</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/schedule-practical.css">
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
                    <a href="schedule-practical.php" class="nav-link active">Schedule Test</a>
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
            <span class="breadcrumb-current">Schedule Practical Test</span>
        </div>
    </div>

    
    <div class="progress-steps">
        <div class="container">
            <div class="steps-container">
                <div class="step completed">
                    <div class="step-icon">✓</div>
                    <div class="step-label">Theory Passed</div>
                </div>
                <div class="step completed">
                    <div class="step-icon">✓</div>
                    <div class="step-label">Ready for Practical</div>
                </div>
                <div class="step active">
                    <div class="step-icon">3</div>
                    <div class="step-label">Schedule Practical</div>
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
                    <h1 class="schedule-title">Schedule Your Practical Driving Test</h1>
                    <p class="schedule-subtitle">Select your preferred test center, date, and time for your practical examination</p>
                </div>

                
                <div class="info-alerts">
                    <div class="info-alert">
                        <div class="alert-icon">ℹ️</div>
                        <div class="alert-content">
                            <strong>Important Information:</strong>
                            <p>The Practical Test must be conducted at an approved test center with a certified DMT examiner. You must bring your own vehicle or use the center's vehicle (additional fee applies).</p>
                        </div>
                    </div>
                    <div class="warning-alert">
                        <div class="alert-icon">⚠️</div>
                        <div class="alert-content">
                            <strong>Test Requirements:</strong>
                            <ul>
                                <li>• Valid learner's permit required</li>
                                <li>• Theory test pass certificate required</li>
                                <li>• Tests must be scheduled at least 7 days in advance</li>
                                <li>• Bring your NIC and theory certificate on test day</li>
                                <li>• Arrive 30 minutes before scheduled time</li>
                            </ul>
                        </div>
                    </div>
                </div>

                
                <div class="test-center-section">
                    <div class="section-header">
                        <h2 class="section-title">📍 Step 1: Select Test Center</h2>
                        <span class="required-badge">Required</span>
                    </div>
                    
                    <div class="test-centers-container glass-card">
                        <div class="centers-grid" id="testCentersGrid">
                            
                        </div>
                    </div>
                </div>

                
                <div class="calendar-section">
                    <div class="section-header">
                        <h2 class="section-title">📅 Step 2: Choose Your Test Date</h2>
                        <span class="required-badge">Required</span>
                    </div>
                    
                    <div class="calendar-container glass-card">
                        <div class="calendar-header">
                            <button class="calendar-nav" id="prevMonth">‹</button>
                            <h3 class="calendar-month" id="calendarMonth">February 2025</h3>
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
                                <span>Too Soon (7-day minimum)</span>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="time-slots-section hidden" id="timeSlotsSection">
                    <div class="section-header">
                        <h2 class="section-title">⏰ Step 3: Select Time Slot</h2>
                        <span class="required-badge">Required</span>
                    </div>
                    
                    <div class="selected-date-info">
                        <div class="date-info-content">
                            <span class="date-label">Selected Date:</span>
                            <span class="date-value" id="selectedDate">Wednesday, February 15, 2025</span>
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

            
                <div class="vehicle-section hidden" id="vehicleSection">
                    <div class="section-header">
                        <h2 class="section-title">🚗 Step 4: Vehicle Selection</h2>
                        <span class="required-badge">Required</span>
                    </div>
                    
                    <div class="vehicle-options glass-card">
                        <div class="vehicle-option" data-vehicle="own">
                            <div class="option-header">
                                <input type="radio" name="vehicleType" value="own" id="ownVehicle" required>
                                <label for="ownVehicle" class="option-label">
                                    <span class="option-icon">🚗</span>
                                    <span class="option-title">Use My Own Vehicle</span>
                                </label>
                            </div>
                            <div class="option-content">
                                <p>Bring your own vehicle for the test</p>
                                <div class="vehicle-requirements">
                                    <h4>Vehicle Requirements:</h4>
                                    <ul>
                                        <li>• Valid registration and insurance</li>
                                        <li>• Roadworthy condition</li>
                                        <li>• Appropriate for license category</li>
                                        <li>• Clean and presentable</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="vehicle-option" data-vehicle="rental">
                            <div class="option-header">
                                <input type="radio" name="vehicleType" value="rental" id="rentalVehicle" required>
                                <label for="rentalVehicle" class="option-label">
                                    <span class="option-icon">🚙</span>
                                    <span class="option-title">Use Test Center Vehicle</span>
                                </label>
                            </div>
                            <div class="option-content">
                                <p>Rent a vehicle from the test center</p>
                                <div class="rental-info">
                                    <h4>Rental Information:</h4>
                                    <ul>
                                        <li>• Additional fee: Rs. 2,000</li>
                                        <li>• Vehicle provided by test center</li>
                                        <li>• Fully insured and roadworthy</li>
                                        <li>• Automatic or manual transmission</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

        
                <div class="booking-summary hidden" id="bookingSummary">
                    <div class="section-header">
                        <h2 class="section-title">📋 Booking Summary</h2>
                    </div>
                    
                    <div class="summary-cards">
                        <div class="summary-card glass-card">
                            <div class="card-icon">📍</div>
                            <div class="card-content">
                                <div class="card-label">Test Center</div>
                                <div class="card-value" id="summaryCenter">Colombo - Werahera Test Center</div>
                            </div>
                        </div>
                        <div class="summary-card glass-card">
                            <div class="card-icon">📅</div>
                            <div class="card-content">
                                <div class="card-label">Test Date</div>
                                <div class="card-value" id="summaryDate">Wednesday, February 15, 2025</div>
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
                            <div class="card-icon">🚗</div>
                            <div class="card-content">
                                <div class="card-label">Vehicle</div>
                                <div class="card-value" id="summaryVehicle">Own Vehicle</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="test-info-box glass-card">
                        <div class="info-icon">📋</div>
                        <div class="info-content">
                            <strong>Test Day Checklist:</strong>
                            <ul>
                                <li>• Arrive 30 minutes before your scheduled time</li>
                                <li>• Bring your NIC and theory certificate</li>
                                <li>• Ensure vehicle is in good condition</li>
                                <li>• Wear comfortable clothes and shoes</li>
                                <li>• Get adequate rest the night before</li>
                            </ul>
                        </div>
                    </div>
                </div>

                
                <div class="action-buttons">
                    <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
                    <button class="btn btn-primary btn-large" id="confirmBooking" disabled>
                        <span class="btn-text">Confirm Practical Test Booking</span>
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
            <div class="success-title">Practical Test Booked Successfully!</div>
            <div class="success-message">
                Your practical driving test has been successfully scheduled.
            </div>
            <div class="success-details">
                <p><strong>📧 Confirmation email sent</strong></p>
                <p><strong>📱 SMS confirmation sent</strong></p>
                <p>You will receive a reminder 24 hours before your test.</p>
            </div>
            <div class="success-actions">
                <button class="btn btn-primary" id="goToDashboard">Go to Dashboard</button>
            </div>
        </div>
    </div>

    <script src="assets/js/app.js"></script>
    <script src="assets/js/schedule-practical.js"></script>
</body>
</html>
