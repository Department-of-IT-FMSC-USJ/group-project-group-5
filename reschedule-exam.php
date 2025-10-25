<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reschedule Theory Exam - LicenseXpress</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/reschedule-exam.css">
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
                    <a href="reschedule-exam.php" class="nav-link active">Reschedule Exam</a>
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
            <span class="breadcrumb-current">Reschedule Theory Exam</span>
        </div>
    </div>

   
    <main class="reschedule-main">
        <div class="container">
            <div class="reschedule-container">
                
                <div class="reschedule-header">
                    <h1 class="reschedule-title">Reschedule Theory Exam</h1>
                    <p class="reschedule-subtitle">Select a new date and time for your theory examination</p>
                </div>

                
                <div class="previous-attempt glass-card">
                    <h3>📊 Previous Attempt Details</h3>
                    <div class="attempt-info">
                        <div class="attempt-item">
                            <span class="attempt-label">Score:</span>
                            <span class="attempt-value" id="previousScore">35/50 (70%)</span>
                        </div>
                        <div class="attempt-item">
                            <span class="attempt-label">Date:</span>
                            <span class="attempt-value" id="previousDate">January 15, 2025</span>
                        </div>
                        <div class="attempt-item">
                            <span class="attempt-label">Time Taken:</span>
                            <span class="attempt-value" id="previousTime">45 minutes</span>
                        </div>
                        <div class="attempt-item">
                            <span class="attempt-label">Attempts:</span>
                            <span class="attempt-value" id="attemptCount">1</span>
                        </div>
                    </div>
                </div>

                
                <div class="reschedule-form glass-card">
                    <h3>📅 Select New Exam Date</h3>
                    
                   
                    <div class="calendar-section">
                        <div class="calendar-header">
                            <button class="calendar-nav" id="prevMonth">‹</button>
                            <h4 class="calendar-month" id="calendarMonth">February 2025</h4>
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
                        </div>
                    </div>

                    
                    <div class="time-slots-section hidden" id="timeSlotsSection">
                        <h4>⏰ Select Time Slot</h4>
                        <div class="selected-date-info">
                            <span class="date-label">Selected Date:</span>
                            <span class="date-value" id="selectedDate">Wednesday, February 5, 2025</span>
                        </div>
                        
                        <div class="time-slots-container">
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

                   
                    <div class="reschedule-summary hidden" id="rescheduleSummary">
                        <h4>📋 Reschedule Summary</h4>
                        <div class="summary-cards">
                            <div class="summary-card">
                                <div class="card-icon">📅</div>
                                <div class="card-content">
                                    <div class="card-label">New Date</div>
                                    <div class="card-value" id="summaryDate">Wednesday, February 5, 2025</div>
                                </div>
                            </div>
                            <div class="summary-card">
                                <div class="card-icon">⏰</div>
                                <div class="card-content">
                                    <div class="card-label">New Time</div>
                                    <div class="card-value" id="summaryTime">10:00 AM</div>
                                </div>
                            </div>
                            <div class="summary-card">
                                <div class="card-icon">💰</div>
                                <div class="card-content">
                                    <div class="card-label">Reschedule Fee</div>
                                    <div class="card-value">Rs. 500.00</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="reschedule-notice">
                            <h4>⚠️ Important Notes:</h4>
                            <ul>
                                <li>• Reschedule fee of Rs. 500 is required</li>
                                <li>• You can reschedule up to 3 times</li>
                                <li>• New exam date must be at least 24 hours in advance</li>
                                <li>• Previous exam results will be cleared</li>
                            </ul>
                        </div>
                    </div>
                </div>

                
                <div class="action-buttons">
                    <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
                    <button class="btn btn-primary btn-large" id="confirmReschedule" disabled>
                        <span class="btn-text">Confirm Reschedule (Rs. 500)</span>
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
            <div class="success-title">Exam Rescheduled Successfully!</div>
            <div class="success-message">
                Your theory exam has been rescheduled to the new date and time.
            </div>
            <div class="success-details">
                <p><strong>📧 Confirmation email sent</strong></p>
                <p><strong>📱 SMS confirmation sent</strong></p>
                <p>You can now take your exam on the new scheduled date.</p>
            </div>
            <div class="success-actions">
                <button class="btn btn-primary" id="goToDashboard">Go to Dashboard</button>
            </div>
        </div>
    </div>

    <script src="assets/js/app.js"></script>
    <script src="assets/js/reschedule-exam.js"></script>
</body>
</html>
