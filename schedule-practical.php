<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    
    header('Location: login.php');
    exit;
} else {
    require_once 'database/database_connection.php';

    
    $db = new Database();
    $userId = $_SESSION['user_id'];

    // Get spplication and theroy test info
    $sql = "SELECT a.*, u.full_name, u.nic, tt.scheduled_date as theory_date, tt.scheduled_time as theory_time, tt.completed_at
            FROM applications a
            JOIN users u ON a.user_id = u.id
            LEFT JOIN theory_tests tt ON a.id = tt.application_id
            WHERE u.user_id = :user_id
            ORDER BY a.created_at DESC
            LIMIT 1";
    $application = $db->fetch($sql, ['user_id' => $_SESSION['user_id']]);

    if (!$application) {
        
        $autoScheduledDate = date('Y-m-d', strtotime('+3 months'));
        $autoScheduledTime = '10:00:00';
        $practicalTest = null;
    } else {
        // practical exam date calculation(+three months)
        $theoryCompletedDate = $application['completed_at'] ? $application['completed_at'] : ($application['theory_date'] ? $application['theory_date'] : date('Y-m-d'));
        $autoScheduledDate = date('Y-m-d', strtotime($theoryCompletedDate . ' +3 months'));
        $autoScheduledTime = '10:00:00'; // Default time

        // Check if already scheduled
        $sql = "SELECT * FROM practical_tests WHERE application_id = :application_id";
        $practicalTest = $db->fetch($sql, ['application_id' => $application['id']]);
        
        
        if (!$practicalTest) {
            
            $sql = "SELECT id FROM test_centers WHERE is_active = 1 ORDER BY id LIMIT 1";
            $defaultTestCenter = $db->fetch($sql);
            
            if ($defaultTestCenter) {
                // Insert practical test record
                $sql = "INSERT INTO practical_tests (application_id, test_center_id, scheduled_date, scheduled_time, vehicle_type, vehicle_details) 
                        VALUES (:application_id, :test_center_id, :scheduled_date, :scheduled_time, :vehicle_type, :vehicle_details)";
                
                $practicalTestData = [
                    'application_id' => $application['id'],
                    'test_center_id' => $defaultTestCenter['id'],
                    'scheduled_date' => $autoScheduledDate,
                    'scheduled_time' => $autoScheduledTime,
                    'vehicle_type' => 'own',
                    'vehicle_details' => json_encode([])
                ];
                
                $db->query($sql, $practicalTestData);
                
                // Update status of application
                $sql = "UPDATE applications SET status = 'practical_scheduled', progress = 85, updated_at = NOW() 
                        WHERE id = :application_id";
                $db->query($sql, ['application_id' => $application['id']]);
                
                // Fetch rescheduled date
                $sql = "SELECT * FROM practical_tests WHERE application_id = :application_id";
                $practicalTest = $db->fetch($sql, ['application_id' => $application['id']]);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Practical Test - LicenseXpress</title>
    <link rel="stylesheet" href="assets/css/styles.css?v=1.0">
    <link rel="stylesheet" href="assets/css/schedule-practical.css?v=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        
        window.applicationData = {
            applicationId: '<?php echo $application ? $application['application_id'] : 'APP-DEMO-001'; ?>',
            userName: '<?php echo $application ? htmlspecialchars($application['full_name']) : 'Demo User'; ?>',
            userNIC: '<?php echo $application ? htmlspecialchars($application['nic']) : '200012345678'; ?>',
            theoryDate: '<?php echo $application && $application['completed_at'] ? $application['completed_at'] : date('Y-m-d'); ?>',
            autoScheduledDate: '<?php echo $autoScheduledDate; ?>',
            autoScheduledTime: '<?php echo $autoScheduledTime; ?>',
            hasPracticalScheduled: <?php echo $practicalTest ? 'true' : 'false'; ?>,
            practicalDate: '<?php echo $practicalTest ? $practicalTest['scheduled_date'] : $autoScheduledDate; ?>',
            practicalTime: '<?php echo $practicalTest ? $practicalTest['scheduled_time'] : $autoScheduledTime; ?>',
            testCenterId: '<?php echo $practicalTest ? $practicalTest['test_center_id'] : ''; ?>',
            rescheduleCount: <?php echo $practicalTest ? $practicalTest['reschedule_count'] : 0; ?>,
            sessionId: '<?php echo session_id(); ?>',
            debugInfo: {
                userId: '<?php echo $userId; ?>',
                sessionUserId: '<?php echo $_SESSION['user_id']; ?>',
                applicationId: '<?php echo $application ? $application['id'] : 'null'; ?>',
                applicationIdString: '<?php echo $application ? $application['application_id'] : 'null'; ?>',
                hasApplication: <?php echo $application ? 'true' : 'false'; ?>
            }
        };
    </script>
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
                        <div class="user-avatar" id="userAvatar">
                            <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
                        </div>
                        <div class="user-details">
                            <div class="user-name" id="userName"><?php echo htmlspecialchars($_SESSION['full_name']); ?></div>
                            <div class="user-nic" id="userNIC"><?php echo htmlspecialchars($_SESSION['nic']); ?></div>
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

    
    <main class="schedule-main">
        <div class="container">
            <div class="schedule-container">
                
                <div class="congratulations-section">
                    <div class="congrats-card glass-card">
                        <div class="congrats-icon">🎉</div>
                        <h1 class="congrats-title">Congratulations, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h1>
                        <p class="congrats-message">You have successfully passed your theory examination</p>
                        
                        <div class="scheduled-info">
                            <h3>Your Practical Test is Scheduled</h3>
                            <div class="schedule-details" id="currentScheduleDetails">
                                <div class="detail-item">
                                    <div class="detail-icon">📍</div>
                                    <div class="detail-content">
                                        <div class="detail-label">Test Center</div>
                                        <div class="detail-value" id="scheduledCenter">
                                            Colombo Werahera
                                        </div>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-icon">📅</div>
                                    <div class="detail-content">
                                        <div class="detail-label">Date</div>
                                        <div class="detail-value" id="scheduledDate">
                                            <?php 
                                            $displayDate = $practicalTest ? $practicalTest['scheduled_date'] : $autoScheduledDate;
                                            echo date('l, F j, Y', strtotime($displayDate)); 
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-icon">⏰</div>
                                    <div class="detail-content">
                                        <div class="detail-label">Time</div>
                                        <div class="detail-value" id="scheduledTime">
                                            <?php 
                                            $displayTime = $practicalTest ? $practicalTest['scheduled_time'] : $autoScheduledTime;
                                            echo date('g:i A', strtotime($displayTime)); 
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="auto-schedule-note">
                                <?php if (!$practicalTest): ?>
                                    <em>This is automatically scheduled 3 months from your theory exam completion date.</em>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>

                
                <div class="reschedule-section">
                    <button class="btn btn-secondary btn-reschedule" id="rescheduleBtn">
                        <span>📅</span> Reschedule Practical Test
                    </button>
                </div>

                
                <div class="test-center-section hidden" id="testCenterSection">
                    <div class="section-header">
                        <h2 class="section-title">📍 Select Your Preferred Test Center</h2>
                        <span class="required-badge">Required</span>
                    </div>
                    
                    <div class="test-centers-container glass-card">
                        <div class="centers-grid" id="testCentersGrid">
                            
                        </div>
                    </div>
                </div>

                
                <div class="calendar-section hidden" id="calendarSection">
                    <div class="section-header">
                        <h2 class="section-title">📅 Choose Your Test Date</h2>
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
                                <div class="legend-color today"></div>
                                <span>Today</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color original-date">📅</div>
                                <span>Original Date</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color too-soon">🔒</div>
                                <span>Before Original Date</span>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="time-slots-section hidden" id="timeSlotsSection">
                    <div class="section-header">
                        <h2 class="section-title">⏰ Select Time Slot</h2>
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

                    <div class="reschedule-actions">
                        <button class="btn btn-secondary" id="cancelReschedule">Cancel</button>
                        <button class="btn btn-primary" id="confirmReschedule" disabled>Confirm Reschedule</button>
                    </div>
                </div>

                
                <div class="vehicle-section hidden" id="vehicleSection">
                    <div class="section-header">
                        <h2 class="section-title">🚗 Vehicle Selection</h2>
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

                
                <div class="action-buttons">
                    <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
                </div>
            </div>
        </div>
    </main>

    
    <div class="success-modal hidden" id="successModal">
        <div class="modal-backdrop"></div>
        <div class="modal-content">
            <div class="success-icon">✅</div>
            <div class="success-title">Practical Test Confirmed Successfully!</div>
            <div class="success-message">
                Your practical driving test has been successfully confirmed.
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

    <script src="assets/js/app.js?v=1.0"></script>
    <script src="assets/js/schedule-practical.js?v=4.0"></script>
</body>
</html>

