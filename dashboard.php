<?php
session_start();


error_log("Dashboard - Session ID: " . session_id());
error_log("Dashboard - Session data: " . print_r($_SESSION, true));


if (!isset($_SESSION['user_id'])) {
    error_log("Dashboard - No user_id in session, redirecting to login");
    header('Location: login.php');
    exit;
}
error_log("Dashboard - User logged in: " . $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - LicenseXpress</title>
    <link rel="stylesheet" href="assets/css/styles.css">
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
                    <a href="dashboard.php" class="nav-link active">Dashboard</a>
                    <a href="application-form.php" class="nav-link">Application</a>
                    <a href="pages/about.php" class="nav-link">About</a>
                    <a href="pages/contactus.php" class="nav-link">Contact</a>
                </nav>
                <div class="header-actions">
                    <div class="user-avatar" id="userAvatar">J</div>
                <button class="logout-btn" id="logoutBtn" title="Logout" onclick="LicenseXpress.logout();">
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
            <span class="breadcrumb-current" id="breadcrumbCurrent">Overview</span>
        </div>
    </div>

    
    <main class="dashboard-main">
        <div class="container">
            
            <div class="profile-card glass-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <div class="avatar-circle" id="profileAvatar">J</div>
                    </div>
                    <div class="profile-info">
                        <h1 class="profile-name" id="profileName">John Doe</h1>
                        <p class="profile-nic" id="profileNIC">200012345678</p>
                    </div>
                </div>
                <div class="status-message" id="statusMessage">
                    Welcome to LicenseXpress! Start your license application journey today.
                </div>
            </div>

            
            <div class="status-details">
                <div class="status-card glass-card">
                    <div class="status-icon">📅</div>
                    <div class="status-content">
                        <div class="status-label" id="statusLabel1">Registered On</div>
                        <div class="status-value" id="statusValue1">January 20, 2025</div>
                    </div>
                </div>
                <div class="status-card glass-card">
                    <div class="status-icon">⏰</div>
                    <div class="status-content">
                        <div class="status-label" id="statusLabel2">Next Step</div>
                        <div class="status-value" id="statusValue2">Start Application</div>
                    </div>
                </div>
                <div class="status-card glass-card">
                    <div class="status-icon">📋</div>
                    <div class="status-content">
                        <div class="status-label" id="statusLabel3">Current Step</div>
                        <div class="status-value" id="statusValue3">Not Started</div>
                    </div>
                </div>
            </div>

            
            <div class="timeline-section">
                <h2 class="section-title">Process Progress</h2>
                <div class="timeline" id="applicationTimeline">
                    
                    <div class="timeline-step active">
                        <div class="timeline-icon" style="background: #f59e0b;">📋</div>
                        <div class="timeline-content">
                            <div class="timeline-label">Not Started</div>
                        </div>
                    </div>
                    <div class="timeline-step">
                        <div class="timeline-icon" style="background: rgba(255, 255, 255, 0.1);">⏳</div>
                        <div class="timeline-content">
                            <div class="timeline-label">Pending Verification</div>
                        </div>
                    </div>
                    <div class="timeline-step">
                        <div class="timeline-icon" style="background: rgba(255, 255, 255, 0.1);">✅</div>
                        <div class="timeline-content">
                            <div class="timeline-label">Verified</div>
                        </div>
                    </div>
                    <div class="timeline-step">
                        <div class="timeline-icon" style="background: rgba(255, 255, 255, 0.1);">📝</div>
                        <div class="timeline-content">
                            <div class="timeline-label">Theory Test Scheduled</div>
                        </div>
                    </div>
                    <div class="timeline-step">
                        <div class="timeline-icon" style="background: rgba(255, 255, 255, 0.1);">🎓</div>
                        <div class="timeline-content">
                            <div class="timeline-label">Theory Passed</div>
                        </div>
                    </div>
                    <div class="timeline-step">
                        <div class="timeline-icon" style="background: rgba(255, 255, 255, 0.1);">🚗</div>
                        <div class="timeline-content">
                            <div class="timeline-label">Practical Scheduled</div>
                        </div>
                    </div>
                    <div class="timeline-step">
                        <div class="timeline-icon" style="background: rgba(255, 255, 255, 0.1);">🏆</div>
                        <div class="timeline-content">
                            <div class="timeline-label">License Issued</div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="action-section">
                <button class="btn btn-primary btn-large btn-action" id="actionButton" onclick="window.location.href='application-form.php';">
                    <span class="btn-text">Start Application</span>
                    <span class="btn-icon">→</span>
                </button>
            </div>

            
            <div class="dynamic-content" id="dynamicContent">
                
            </div>

            
            <div id="statusDebug"></div>
        </div>
    </main>

  
    <div id="confettiContainer"></div>

    <script src="assets/js/app.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
<<<<<<< Updated upstream
          
=======
           
>>>>>>> Stashed changes
            LicenseXpress.initializeUserData();

       
            const currentUser = JSON.parse(localStorage.getItem('currentUser') || '{}');
            
            if (!currentUser || !currentUser.nic) {
                console.error('No user NIC found');
                window.location.href = 'login.php';
                return;
            }

<<<<<<< Updated upstream
            
=======
           
>>>>>>> Stashed changes
            fetch(`get_application_status.php?nic=${encodeURIComponent(currentUser.nic)}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Fetched application data from database:', data);
                    
                    if (data.success) {
                        
                        const applicationState = {
                            status: data.application?.status || 'not_started',
                            applicationId: data.application?.application_id || null,
                            submittedDate: data.application?.submitted_date || null,
                            verifiedDate: data.application?.verified_date || null,
                            rejectedDate: data.application?.rejected_date || null,
                            registeredDate: data.user.registration_date || data.user.created_at,
                            progress: data.application?.progress || 0,
                            payment: data.payment ? {
                                amount: data.payment.amount,
                                status: data.payment.payment_status,
                                method: data.payment.payment_method
                            } : null
                        };

                        const userProfile = {
                            fullName: data.user.full_name,
                            nic: data.user.nic,
                            email: data.user.email,
                            phone: data.user.phone,
                            dateOfBirth: data.user.date_of_birth,
                            gender: data.user.gender,
                            district: data.user.district,
                            transmissionType: data.user.transmission_type
                        };

                        const tests = {
                            theory: data.theory_test ? {
                                date: data.theory_test.scheduled_date,
                                time: data.theory_test.scheduled_time,
                                score: data.theory_test.score,
                                passed: data.theory_test.passed,
                                passedDate: data.theory_test.completed_at
                            } : {},
                            practical: data.practical_test ? {
                                date: data.practical_test.scheduled_date,
                                time: data.practical_test.scheduled_time,
                                score: data.practical_test.score,
                                passed: data.practical_test.passed,
                                center: 'Test Center Name',
                                address: 'Test Center Address'
                            } : {}
                        };

                        const license = data.license ? {
                            number: data.license.license_number,
                            issueDate: data.license.issue_date,
                            expiryDate: data.license.expiry_date,
                            status: data.license.status
                        } : {};

                        
                        localStorage.setItem('applicationState', JSON.stringify(applicationState));
                        localStorage.setItem('userProfile', JSON.stringify(userProfile));
                        localStorage.setItem('tests', JSON.stringify(tests));
                        localStorage.setItem('license', JSON.stringify(license));

                        console.log('Dashboard - Application State:', applicationState);
                        console.log('Dashboard - Status:', applicationState.status);

                        
                        updateUserInfo(currentUser, applicationState);

                      
                        updateDashboard(applicationState, userProfile, tests, license);
                    } else {
                        console.error('Failed to fetch application data:', data.error);
                        
                      
                        const applicationState = JSON.parse(localStorage.getItem('applicationState') || '{}');
                        const userProfile = JSON.parse(localStorage.getItem('userProfile') || '{}');
                        const tests = JSON.parse(localStorage.getItem('tests') || '{}');
                        const license = JSON.parse(localStorage.getItem('license') || '{}');
                        
                        updateUserInfo(currentUser, applicationState);
                        updateDashboard(applicationState, userProfile, tests, license);
                    }
                })
                .catch(error => {
                    console.error('Error fetching application data:', error);
                    
                   
                    const applicationState = JSON.parse(localStorage.getItem('applicationState') || '{}');
                    const userProfile = JSON.parse(localStorage.getItem('userProfile') || '{}');
                    const tests = JSON.parse(localStorage.getItem('tests') || '{}');
                    const license = JSON.parse(localStorage.getItem('license') || '{}');
                    
                    updateUserInfo(currentUser, applicationState);
                    updateDashboard(applicationState, userProfile, tests, license);
                });
        });

        function updateUserInfo(user, applicationState) {
            const userAvatar = document.getElementById('userAvatar');
            const profileName = document.getElementById('profileName');
            const profileNIC = document.getElementById('profileNIC');
            const profileAvatar = document.getElementById('profileAvatar');

            if (user) {
                const name = user.fullName || 'User';
                const initial = name.charAt(0).toUpperCase();
                
                userAvatar.textContent = initial;
                profileName.textContent = name;
                profileNIC.textContent = LicenseXpress.formatNIC(user.nic);
                profileAvatar.textContent = initial;
            }
        }

        function updateDashboard(applicationState, userProfile, tests, license) {
            const status = applicationState.status || 'not_started';
            
            console.log('Dashboard update - Status:', status);
            console.log('Application State:', applicationState);

        
            updateStatusBadge(status);

           
            updateBreadcrumb(status);

<<<<<<< Updated upstream
            updateStatusDetails(applicationState, userProfile, tests, license);

            
=======
          
            updateStatusDetails(applicationState, userProfile, tests, license);

>>>>>>> Stashed changes
            updateTimeline(status, applicationState, tests, license);

           
            updateActionButton(status, applicationState, tests, license);

            
            updateDynamicContent(status, applicationState, userProfile, tests, license);

<<<<<<< Updated upstream
            
            const statusDebug = document.getElementById('statusDebug');
            if (statusDebug) {
                statusDebug.innerHTML = `
                    <div style="background: rgba(255, 255, 255, 0.1); padding: 10px; border-radius: 8px; margin: 10px 0; font-size: 12px;">
                        <strong>Debug Info:</strong><br>
                        Status: ${status}<br>
                        Application ID: ${applicationState.applicationId || 'None'}<br>
                        Submitted Date: ${applicationState.submittedDate || 'None'}
                    </div>
                `;
            }

            
=======
        
>>>>>>> Stashed changes
            if (status === 'license_issued') {
                setTimeout(() => {
                    LicenseXpress.createConfetti();
                }, 500);
            }
        }

        function updateStatusBadge(status) {
            const statusBadge = document.getElementById('statusBadge');
            
<<<<<<< Updated upstream
          
=======
>>>>>>> Stashed changes
            if (!statusBadge) {
                console.log('Status badge element not found - skipping update');
                return;
            }
            
            const statusMessages = {
                'not_started': { text: '🚀 Not Started', class: 'pending' },
                'pending_verification': { text: '⏳ Pending Verification', class: 'pending' },
                'rejected': { text: '❌ Documents Rejected', class: 'rejected' },
                'verified': { text: '✅ Verified', class: 'verified' },
                'theory_scheduled': { text: '📝 Theory Test Scheduled', class: 'pending' },
                'theory_passed': { text: '🎓 Theory Test Passed', class: 'verified' },
                'theory_failed': { text: '❌ Theory Test Failed', class: 'rejected' },
                'practical_scheduled': { text: '🚗 Practical Test Scheduled', class: 'pending' },
                'license_issued': { text: '🏆 License Issued', class: 'verified' }
            };

            const statusInfo = statusMessages[status] || statusMessages['not_started'];
            statusBadge.textContent = statusInfo.text;
            statusBadge.className = `status-badge ${statusInfo.class}`;
        }

        function updateBreadcrumb(status) {
            const breadcrumbCurrent = document.getElementById('breadcrumbCurrent');
            const breadcrumbTexts = {
                'not_started': 'Overview',
                'pending_verification': 'Verification',
                'rejected': 'Action Required',
                'verified': 'Ready for Test',
                'theory_scheduled': 'Theory Test',
                'theory_passed': 'Theory Passed',
                'theory_failed': 'Theory Failed',
                'practical_scheduled': 'Practical Test',
                'license_issued': 'Complete'
            };

            breadcrumbCurrent.textContent = breadcrumbTexts[status] || 'Overview';
        }

        function updateStatusDetails(applicationState, userProfile, tests, license) {
            const statusLabel1 = document.getElementById('statusLabel1');
            const statusValue1 = document.getElementById('statusValue1');
            const statusLabel2 = document.getElementById('statusLabel2');
            const statusValue2 = document.getElementById('statusValue2');
            const statusLabel3 = document.getElementById('statusLabel3');
            const statusValue3 = document.getElementById('statusValue3');

            const status = applicationState.status || 'not_started';

            switch (status) {
                case 'not_started':
                    statusLabel1.textContent = 'Registered On';
                    statusValue1.textContent = LicenseXpress.formatDate(applicationState.registeredDate || new Date().toISOString());
                    statusLabel2.textContent = 'Next Step';
                    statusValue2.textContent = 'Start Application';
                    statusLabel3.textContent = 'Estimated Time';
                    statusValue3.textContent = '~15 minutes';
                    break;

                case 'pending_verification':
                    statusLabel1.textContent = 'Application ID';
                    statusValue1.textContent = applicationState.applicationId || 'APP-001';
                    statusLabel2.textContent = 'Submitted On';
                    statusValue2.textContent = LicenseXpress.formatDateTime(applicationState.submittedDate);
                    statusLabel3.textContent = 'Status';
                    statusValue3.textContent = '⏳ Pending Verification';
                    break;

                case 'rejected':
                    statusLabel1.textContent = 'Application ID';
                    statusValue1.textContent = applicationState.applicationId || 'APP-001';
                    statusLabel2.textContent = 'Rejected On';
                    statusValue2.textContent = LicenseXpress.formatDate(applicationState.rejectedDate || new Date().toISOString());
                    statusLabel3.textContent = 'Status';
                    statusValue3.textContent = '❌ Rejected';
                    break;

                case 'verified':
                    statusLabel1.textContent = 'Application ID';
                    statusValue1.textContent = applicationState.applicationId || 'APP-001';
                    statusLabel2.textContent = 'Verified On';
                    statusValue2.textContent = LicenseXpress.formatDate(applicationState.verifiedDate || new Date().toISOString());
                    statusLabel3.textContent = 'Status';
                    statusValue3.textContent = '✅ Verified';
                    break;

                case 'theory_scheduled':
                    statusLabel1.textContent = 'Test Date';
                    statusValue1.textContent = LicenseXpress.formatDate(tests.theory.date);
                    statusLabel2.textContent = 'Test Time';
                    statusValue2.textContent = tests.theory.time;
                    statusLabel3.textContent = 'Test Format';
                    statusValue3.textContent = 'Online Examination';
                    break;

                case 'theory_passed':
                    statusLabel1.textContent = 'Theory Score';
                    statusValue1.textContent = `${tests.theory.score}/50 (${Math.round(tests.theory.score/50*100)}%)`;
                    statusLabel2.textContent = 'Passed On';
                    statusValue2.textContent = LicenseXpress.formatDate(tests.theory.passedDate);
                    statusLabel3.textContent = 'Next Step';
                    statusValue3.textContent = 'Practical Auto-Scheduled';
                    break;

                case 'theory_failed':
                    statusLabel1.textContent = 'Theory Score';
                    statusValue1.textContent = `${tests.theory.score}/50 (${Math.round(tests.theory.score/50*100)}%)`;
                    statusLabel2.textContent = 'Failed On';
                    statusValue2.textContent = LicenseXpress.formatDate(tests.theory.passedDate);
                    statusLabel3.textContent = 'Next Step';
                    statusValue3.textContent = 'Reschedule Theory Test';
                    break;

                case 'practical_scheduled':
                    statusLabel1.textContent = 'Test Date';
                    statusValue1.textContent = LicenseXpress.formatDate(tests.practical.date);
                    statusLabel2.textContent = 'Test Center';
                    statusValue2.textContent = tests.practical.center;
                    statusLabel3.textContent = 'Time';
                    statusValue3.textContent = tests.practical.time;
                    break;

                case 'license_issued':
                    statusLabel1.textContent = 'License Number';
                    statusValue1.textContent = license.number;
                    statusLabel2.textContent = 'Issue Date';
                    statusValue2.textContent = LicenseXpress.formatDate(license.issueDate);
                    statusLabel3.textContent = 'Expiry Date';
                    statusValue3.textContent = LicenseXpress.formatDate(license.expiryDate);
                    break;
            }
        }

        function updateTimeline(status, applicationState, tests, license) {
            const timeline = document.getElementById('applicationTimeline');
            
            console.log('Updating timeline, status:', status);
            console.log('Timeline element:', timeline);
            
            if (!timeline) {
                console.error('Timeline element not found!');
                return;
            }
            
            const steps = [
                { id: 'not_started', icon: '📋', label: 'Not Started', date: null, color: 'gray' },
                { id: 'pending_verification', icon: '⏳', label: 'Pending Verification', date: applicationState.submittedDate, color: 'orange' },
                { id: 'verified', icon: '✅', label: 'Verified', date: applicationState.verifiedDate, color: 'green' },
                { id: 'theory_scheduled', icon: '📝', label: 'Theory Test Scheduled', date: tests.theory?.date, color: 'blue' },
                { id: 'theory_passed', icon: '🎓', label: 'Theory Passed', date: tests.theory?.passedDate, color: 'green' },
                { id: 'practical_scheduled', icon: '🚗', label: 'Practical Scheduled', date: tests.practical?.date, color: 'blue' },
                { id: 'license_issued', icon: '🏆', label: 'License Issued', date: license.issueDate, color: 'gold' }
            ];

            const timelineHTML = steps.map((step, index) => {
                const isActive = status === step.id || (status === 'rejected' && step.id === 'pending_verification');
                const isCompleted = getStepStatus(status, step.id);
                const isRejected = status === 'rejected' && step.id === 'pending_verification';

                console.log(`Step: ${step.id}, Status: ${status}, isActive: ${isActive}, isCompleted: ${isCompleted}`);

                return `
                    <div class="timeline-step ${isCompleted ? 'completed' : isActive ? 'active' : ''} ${isRejected ? 'rejected' : ''}">
                        <div class="timeline-icon" style="background: ${getStepColor(step.color, isActive, isCompleted, isRejected)}">${step.icon}</div>
                        <div class="timeline-content">
                            <div class="timeline-label">${step.label}</div>
                            ${step.date ? `<div class="timeline-date">${LicenseXpress.formatDate(step.date)}</div>` : ''}
                        </div>
                        ${index < steps.length - 1 ? '<div class="timeline-connector"></div>' : ''}
                    </div>
                `;
            }).join('');

            console.log('Timeline HTML:', timelineHTML);
            timeline.innerHTML = timelineHTML;
            
            
            timeline.style.display = 'flex';
            timeline.style.flexDirection = 'column';
            timeline.style.gap = '16px';
            timeline.style.padding = '20px';
            timeline.style.background = 'rgba(255, 255, 255, 0.05)';
            timeline.style.borderRadius = '12px';
            timeline.style.border = '1px solid rgba(255, 255, 255, 0.1)';
        }

        function getStepColor(color, isActive, isCompleted, isRejected) {
            if (isRejected) return '#ef4444'; 
            if (isCompleted) return '#10b981'; 
            if (isActive) {
                switch(color) {
                    case 'orange': return '#f59e0b';
                    case 'green': return '#10b981';
                    case 'red': return '#ef4444';
                    case 'blue': return '#3b82f6';
                    case 'gold': return '#f59e0b';
                    default: return '#6b7280';
                }
            }
            return 'rgba(255, 255, 255, 0.1)'; 
        }

        function getStepStatus(currentStatus, stepId) {
            const statusOrder = ['not_started', 'pending_verification', 'verified', 'theory_scheduled', 'theory_passed', 'practical_scheduled', 'license_issued'];
            const currentIndex = statusOrder.indexOf(currentStatus);
            const stepIndex = statusOrder.indexOf(stepId);
            
            if (currentStatus === 'rejected') {
                return stepId === 'not_started';
            }
            
            return stepIndex < currentIndex;
        }

        function updateActionButton(status, applicationState, tests, license) {
            const actionButton = document.getElementById('actionButton');
            const btnText = actionButton.querySelector('.btn-text');
            const btnIcon = actionButton.querySelector('.btn-icon');

            const buttonConfigs = {
                'not_started': { text: 'Start Application', icon: '→', action: 'application-form.php' },
                'pending_verification': { text: 'View Application', icon: '👁️', action: 'view-application' },
                'rejected': { text: 'Resubmit Documents', icon: '📝', action: 'application-form.php' },
                'verified': { text: 'Schedule Theory Test', icon: '📅', action: 'schedule-theory.php' },
                'theory_scheduled': { text: 'Take Theory Exam', icon: '📝', action: 'exam-window.php' },
                'theory_passed': { text: 'View Practical Details', icon: '🚗', action: 'view-practical-details' },
                'theory_failed': { text: 'Reschedule Theory Test', icon: '🔄', action: 'reschedule-exam.php' },
                'practical_scheduled': { text: 'View Test Details', icon: '👁️', action: 'view-practical-details' },
                'license_issued': { text: 'Download License', icon: '⬇️', action: 'download-license' }
            };

            const config = buttonConfigs[status] || buttonConfigs['not_started'];
            btnText.textContent = config.text;
            btnIcon.textContent = config.icon;

            actionButton.onclick = function() {
                if (config.action.startsWith('http') || config.action.includes('.php')) {
                    window.location.href = config.action;
                } else {
                    handleAction(config.action, applicationState, tests, license);
                }
            };
        }

        function handleAction(action, applicationState, tests, license) {
            switch (action) {
                case 'view-application':
                    LicenseXpress.showModal('Application Details', `
                        <div class="application-details">
                            <h4>Application Information</h4>
                            <p><strong>Application ID:</strong> ${applicationState.applicationId || 'LX-2025-001234'}</p>
                            <p><strong>Submitted:</strong> ${LicenseXpress.formatDateTime(applicationState.submittedDate)}</p>
                            <p><strong>Status:</strong> Under Review</p>
                            <p><strong>Payment:</strong> Rs. ${applicationState.payment?.amount || 3200} (Completed)</p>
                        </div>
                    `, [
                        { text: 'Close', action: 'close' }
                    ]);
                    break;

                case 'view-theory-details':
                    LicenseXpress.showModal('Theory Test Details', `
                        <div class="test-details">
                            <h4>Theory Test Information</h4>
                            <p><strong>Date:</strong> ${LicenseXpress.formatDate(tests.theory.date)}</p>
                            <p><strong>Time:</strong> ${tests.theory.time}</p>
                            <p><strong>Format:</strong> Online Examination</p>
                            <p><strong>Duration:</strong> 45 minutes</p>
                            <p><strong>Questions:</strong> 40 multiple choice</p>
                            <p><strong>Pass Mark:</strong> 35/40 (87.5%)</p>
                        </div>
                    `, [
                        { text: 'Close', action: 'close' }
                    ]);
                    break;

                case 'view-practical-details':
                    LicenseXpress.showModal('Practical Test Details', `
                        <div class="test-details">
                            <h4>Practical Test Information</h4>
                            <p><strong>Date:</strong> ${LicenseXpress.formatDate(tests.practical.date)}</p>
                            <p><strong>Time:</strong> ${tests.practical.time}</p>
                            <p><strong>Center:</strong> ${tests.practical.center}</p>
                            <p><strong>Address:</strong> ${tests.practical.address}</p>
                            <p><strong>Vehicle:</strong> ${tests.practical.vehicle}</p>
                            <p><strong>Examiner:</strong> ${tests.practical.examiner}</p>
                        </div>
                    `, [
                        { text: 'Close', action: 'close' }
                    ]);
                    break;

                case 'download-license':
<<<<<<< Updated upstream
                    LicenseXpress.showModal('Download License', `
                        <div class="license-download">
                            <h4>Your Digital License is Ready!</h4>
                            <p>License Number: ${license.number}</p>
                            <p>Issue Date: ${LicenseXpress.formatDate(license.issueDate)}</p>
                            <p>Expiry Date: ${LicenseXpress.formatDate(license.expiryDate)}</p>
                            <p>Category: ${license.category}</p>
                        </div>
                    `, [
                        { text: 'Download PDF', action: 'download-pdf', class: 'btn-primary' },
                        { text: 'Download Image', action: 'download-image', class: 'btn-secondary' },
                        { text: 'Close', action: 'close' }
                    ]);
=======
                   
                    const currentUser = LicenseXpress.getCurrentUser();
                    
                  const licenseData = {
                        number: license?.license_number || 'DL-2025-001234',
                        name: currentUser?.full_name || 'Not specified',
                        nic: currentUser?.nic || 'Not specified',
                        category: currentUser?.transmission_type || 'B',
                        issueDate: license?.issue_date || new Date().toISOString().split('T')[0],
                        expiryDate: license?.expiry_date || new Date(Date.now() + 5 * 365 * 24 * 60 * 60 * 1000).toISOString().split('T')[0]
                    };
                    
                  
                    const htmlContent = `
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <title>Sri Lanka Driving License</title>
                            <style>
                                body {
                                    font-family: Arial, sans-serif;
                                    margin: 0;
                                    padding: 20px;
                                    background: white;
                                }
                                .license-card {
                                    width: 800px;
                                    height: 500px;
                                    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
                                    border: 3px solid #dc2626;
                                    border-radius: 15px;
                                    margin: 0 auto;
                                    position: relative;
                                    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                                }
                                .license-header {
                                    background: linear-gradient(135deg, #dc2626, #ef4444);
                                    color: white;
                                    padding: 15px;
                                    text-align: center;
                                    font-size: 18px;
                                    font-weight: bold;
                                    letter-spacing: 2px;
                                    border-radius: 12px 12px 0 0;
                                }
                                .license-content {
                                    padding: 30px;
                                    display: grid;
                                    grid-template-columns: 200px 1fr;
                                    gap: 30px;
                                    height: calc(100% - 80px);
                                }
                                .photo-section {
                                    text-align: center;
                                }
                                .photo-placeholder {
                                    width: 150px;
                                    height: 180px;
                                    background: linear-gradient(135deg, #e5e7eb, #d1d5db);
                                    border: 2px solid #9ca3af;
                                    border-radius: 10px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    font-size: 14px;
                                    color: #6b7280;
                                    font-weight: bold;
                                    margin-bottom: 15px;
                                }
                                .valid-badge {
                                    background: linear-gradient(135deg, #10b981, #34d399);
                                    color: white;
                                    padding: 8px 15px;
                                    border-radius: 20px;
                                    font-size: 12px;
                                    font-weight: bold;
                                    display: inline-block;
                                }
                                .license-details {
                                    color: #1f2937;
                                }
                                .detail-row {
                                    margin-bottom: 15px;
                                    display: flex;
                                    align-items: center;
                                }
                                .detail-label {
                                    font-size: 12px;
                                    color: #6b7280;
                                    font-weight: bold;
                                    text-transform: uppercase;
                                    letter-spacing: 1px;
                                    width: 120px;
                                }
                                .detail-value {
                                    font-size: 16px;
                                    color: #1f2937;
                                    font-weight: 600;
                                    flex: 1;
                                }
                                .license-number {
                                    font-size: 24px;
                                    font-weight: bold;
                                    color: #dc2626;
                                    letter-spacing: 2px;
                                }
                                .license-footer {
                                    position: absolute;
                                    bottom: 0;
                                    left: 0;
                                    right: 0;
                                    background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
                                    padding: 15px;
                                    text-align: center;
                                    font-size: 12px;
                                    color: #64748b;
                                    font-weight: bold;
                                    border-radius: 0 0 12px 12px;
                                }
                                @media print {
                                    body { margin: 0; }
                                    .license-card { box-shadow: none; }
                                }
                            </style>
                        </head>
                        <body>
                            <div class="license-card">
                                <div class="license-header">
                                    🇱🇰 SRI LANKA DRIVING LICENSE
                                </div>
                                <div class="license-content">
                                    <div class="photo-section">
                                        <div class="photo-placeholder">PHOTO</div>
                                        <div class="valid-badge">VALID</div>
                                    </div>
                                    <div class="license-details">
                                        <div class="detail-row">
                                            <div class="detail-label">License Number</div>
                                            <div class="detail-value license-number">${licenseData.number}</div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">Full Name</div>
                                            <div class="detail-value">${licenseData.name}</div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">NIC Number</div>
                                            <div class="detail-value">${licenseData.nic}</div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">Address</div>
                                            <div class="detail-value">Colombo 07, Sri Lanka</div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">Vehicle Category</div>
                                            <div class="detail-value">${licenseData.category} - Light Motor Vehicle</div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">Transmission</div>
                                            <div class="detail-value">Manual</div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">Issue Date</div>
                                            <div class="detail-value">${new Date(licenseData.issueDate).toLocaleDateString('en-US', {
                                                year: 'numeric',
                                                month: 'long',
                                                day: 'numeric'
                                            })}</div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">Expiry Date</div>
                                            <div class="detail-value">${new Date(licenseData.expiryDate).toLocaleDateString('en-US', {
                                                year: 'numeric',
                                                month: 'long',
                                                day: 'numeric'
                                            })}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="license-footer">
                                    This is a digital copy of your official driving license
                                </div>
                            </div>
                        </body>
                        </html>
                    `;
                    
                    
                    const printWindow = window.open('', '_blank', 'width=800,height=600');
                    printWindow.document.write(htmlContent);
                    printWindow.document.close();
                    
                    
                    printWindow.onload = function() {
                        printWindow.print();
                        
                        setTimeout(() => {
                            printWindow.close();
                        }, 1000);
                    };
                    
                
                    LicenseXpress.showToast('Opening license for download/print...', 'success');
>>>>>>> Stashed changes
                    break;

                case 'download-study-guide':
                    LicenseXpress.showModal('Practical Test Study Guide', `
                        <div class="study-guide-content">
                            <h4>📚 Practical Test Preparation Guide</h4>
                            <div class="guide-section">
                                <h5>🚗 Vehicle Preparation</h5>
                                <ul>
                                    <li>Ensure your vehicle is roadworthy and properly insured</li>
                                    <li>Check all lights, indicators, and mirrors</li>
                                    <li>Verify brakes, steering, and tires are in good condition</li>
                                    <li>Clean the interior and ensure all controls work properly</li>
                                </ul>
                            </div>
                            
                            <div class="guide-section">
                                <h5>📋 Required Documents</h5>
                                <ul>
                                    <li>Valid theory test pass certificate</li>
                                    <li>National Identity Card (NIC)</li>
                                    <li>Vehicle registration certificate</li>
                                    <li>Valid insurance certificate</li>
                                    <li>Valid revenue license</li>
                                </ul>
                            </div>
                            
                            <div class="guide-section">
                                <h5>🎯 Test Areas Covered</h5>
                                <ul>
                                    <li>Vehicle controls and safety checks</li>
                                    <li>Starting and stopping procedures</li>
                                    <li>Turning and lane changing</li>
                                    <li>Parking (parallel and reverse)</li>
                                    <li>Traffic rules and road signs</li>
                                    <li>Emergency procedures</li>
                                </ul>
                            </div>
                            
                            <div class="guide-section">
                                <h5>💡 Tips for Success</h5>
                                <ul>
                                    <li>Practice regularly with a qualified instructor</li>
                                    <li>Familiarize yourself with the test route area</li>
                                    <li>Stay calm and follow examiner instructions</li>
                                    <li>Use mirrors frequently and signal properly</li>
                                    <li>Maintain appropriate speed and safe following distance</li>
                                </ul>
                            </div>
                            
                            <div class="important-notice">
                                <p><strong>Remember:</strong> The practical test focuses on safe driving practices and adherence to traffic rules. Confidence and preparation are key to success!</p>
                            </div>
                        </div>
                    `, [
                        { text: 'Download PDF', action: 'download-pdf' },
                        { text: 'Close', action: 'close' }
                    ]);
                    break;
            }
        }

        function updateDynamicContent(status, applicationState, userProfile, tests, license) {
            const dynamicContent = document.getElementById('dynamicContent');
            const statusMessage = document.getElementById('statusMessage');

           
            const statusMessages = {
                'not_started': 'Welcome to LicenseXpress! Start your license application journey today. Complete your application in just 3 simple steps.',
                'pending_verification': 'Your documents are being verified by our team. This process typically takes up to 48 hours. You\'ll receive an email and SMS notification once verification is complete.',
                'rejected': 'Your application requires corrections. Please review the feedback below and resubmit the required documents.',
                'verified': 'Congratulations! Your documents have been verified successfully. You can now schedule your theory test.',
                'theory_scheduled': 'Your theory test is scheduled. You can now take your online theory exam. The exam is available 24/7 and must be completed within 60 minutes.',
                'theory_passed': 'Excellent work! You\'ve passed the theory test. Your practical driving test has been automatically scheduled for 3 months from today.',
                'theory_failed': 'Unfortunately, you did not pass the theory test. You need to score at least 40 out of 50 to pass. You can reschedule and retake the exam.',
                'practical_scheduled': 'Your practical driving test is confirmed. Report to the test center 30 minutes before your scheduled time.',
                'license_issued': 'Congratulations! Your driving license has been successfully issued. You are now a licensed driver!'
            };

            statusMessage.textContent = statusMessages[status] || statusMessages['not_started'];

           
            let content = '';

            switch (status) {
                case 'not_started':
                    content = generateNotStartedContent();
                    break;
                case 'pending_verification':
                    content = generatePendingVerificationContent();
                    break;
                case 'rejected':
                    content = generateRejectedContent(applicationState);
                    break;
                case 'verified':
                    content = generateVerifiedContent();
                    break;
                case 'theory_scheduled':
                    content = generateTheoryScheduledContent(tests);
                    break;
                case 'theory_passed':
                    content = generateTheoryPassedContent(tests);
                    break;
                case 'theory_failed':
                    content = generateTheoryFailedContent(tests);
                    break;
                case 'practical_scheduled':
                    content = generatePracticalScheduledContent(tests);
                    break;
                case 'license_issued':
                    content = generateLicenseIssuedContent(license, userProfile);
                    break;
            }

            dynamicContent.innerHTML = content;
        }

        function generateNotStartedContent() {
            return `
                <div class="info-card glass-card">
                    <h3>📋 What you'll need:</h3>
                    <ul>
                        <li>Birth Certificate (digital copy)</li>
                        <li>NIC Copy (front and back)</li>
                        <li>Medical Certificate (< 6 months old)</li>
                        <li>Passport photo (white background)</li>
                        <li>Payment method (Rs. 3,200)</li>
                    </ul>
                </div>
            `;
        }

        function generatePendingVerificationContent() {
            return `
                <div class="info-card glass-card">
                    <h3>📧 Verification Status Updates</h3>
                    <p>You'll be notified via email and SMS when:</p>
                    <ul>
                        <li>Verification is in progress</li>
                        <li>Documents are approved</li>
                        <li>Any corrections are needed</li>
                    </ul>
                    <p>Check your email regularly for updates.</p>
                </div>
                <div class="verification-checklist glass-card">
                    <h3>📋 Verification Checklist</h3>
                    <div class="checklist-items">
                        <div class="checklist-item">☑ Birth Certificate - Under Review</div>
                        <div class="checklist-item">☑ NIC Copy - Under Review</div>
                        <div class="checklist-item">☑ Medical Certificate - Under Review</div>
                        <div class="checklist-item">☑ Passport Photo - Under Review</div>
                    </div>
                </div>
            `;
        }

        function generateRejectedContent(applicationState) {
            return `
                <div class="rejection-alert glass-card">
                    <h3>⚠️ Application Rejected</h3>
                    <p><strong>Rejection Reason:</strong></p>
                    <p>${applicationState.rejectionReason || 'Documents need to be resubmitted with corrections.'}</p>
                    <div class="rejected-documents">
                        <h4>Documents Requiring Correction:</h4>
                        <ul>
                            ${applicationState.rejectedDocuments?.map(doc => `<li>✗ ${doc} - Needs resubmission</li>`).join('') || ''}
                        </ul>
                    </div>
                </div>
                <div class="resubmission-guidelines glass-card">
                    <h3>📝 Resubmission Guidelines</h3>
                    <ul>
                        <li>Only resubmit rejected documents</li>
                        <li>Approved documents will be kept</li>
                        <li>No additional payment required</li>
                        <li>Review admin feedback carefully</li>
                        <li>Ensure documents meet all requirements</li>
                    </ul>
                </div>
            `;
        }

        function generateVerifiedContent() {
            return `
                <div class="success-card glass-card">
                    <h3>🎉 Verification Complete!</h3>
                    <p>Your documents have been approved. You can now proceed to schedule your theory test.</p>
                    <div class="test-info">
                        <h4>Theory Test Information:</h4>
                        <ul>
                            <li>Format: Online examination</li>
                            <li>Questions: 40 multiple choice</li>
                            <li>Duration: 45 minutes</li>
                            <li>Pass Mark: 35/40 (87.5%)</li>
                            <li>Attempts: Unlimited (with fees)</li>
                        </ul>
                        <p>The test can be taken from anywhere with a stable internet connection.</p>
                    </div>
                </div>
            `;
        }

        function generateTheoryScheduledContent(tests) {
            return `
                <div class="test-details-card glass-card">
                    <h3>📅 Theory Test Details</h3>
                    <div class="test-info">
                        <p><strong>Date:</strong> ${LicenseXpress.formatDate(tests.theory.date)}</p>
                        <p><strong>Time:</strong> ${tests.theory.time}</p>
                        <p><strong>Duration:</strong> 60 minutes</p>
                        <p><strong>Format:</strong> Online Examination</p>
                        <p><strong>Questions:</strong> 50 multiple choice</p>
                        <p><strong>Pass Mark:</strong> 40/50 (80%)</p>
                    </div>
                    <div class="test-requirements">
                        <h4>Exam Requirements:</h4>
                        <ul>
                            <li>✓ Stable internet connection</li>
                            <li>✓ Quiet environment</li>
                            <li>✓ Webcam access (required for monitoring)</li>
                            <li>✓ Computer/tablet (mobile not recommended)</li>
                            <li>✓ Government-issued ID</li>
                        </ul>
                    </div>
                    <div class="exam-notice">
                        <h4>⚠️ Important Exam Rules:</h4>
                        <ul>
                            <li>• No switching tabs or windows during exam</li>
                            <li>• No right-clicking or keyboard shortcuts</li>
                            <li>• No screenshots or screen recording</li>
                            <li>• Camera monitoring is active throughout</li>
                            <li>• Any violation will terminate the exam</li>
                        </ul>
                    </div>
                </div>
            `;
        }

        function generateTheoryPassedContent(tests) {
          
            const currentUser = JSON.parse(localStorage.getItem('currentUser') || '{}');
            const userProfile = JSON.parse(localStorage.getItem('userProfile') || '{}');
            const applicationState = JSON.parse(localStorage.getItem('applicationState') || '{}');
            
          
            const theoryData = tests.theory || {};
            const score = theoryData.score || 0;
            const scorePercentage = Math.round((score / 50) * 100);
            const scoreColor = scorePercentage >= 90 ? '#10B981' : scorePercentage >= 80 ? '#3B82F6' : '#F59E0B';
            
            
            const rawFullName = currentUser.fullName || userProfile.fullName || currentUser.full_name || userProfile.full_name || 'User';
            const fullName = rawFullName.replace(/\s+/g, ' ').trim(); // Clean up extra spaces
            const nic = currentUser.nic || userProfile.nic || '';
            const applicationId = applicationState.applicationId || 'LX-2025-001234';
            
            
            const testDate = theoryData.date ? LicenseXpress.formatDate(theoryData.date) : 
                           theoryData.passedDate ? LicenseXpress.formatDate(theoryData.passedDate) : 
                           'Not Available';
            
           
            let scoreLabel;
            if (score === 0 || scorePercentage === 0) {
                scoreLabel = 'Score Pending';
            } else if (scorePercentage >= 90) {
                scoreLabel = 'Excellent Score!';
            } else if (scorePercentage >= 80) {
                scoreLabel = 'Great Score!';
            } else if (scorePercentage >= 40) {
                scoreLabel = 'Good Score!';
            } else {
                scoreLabel = 'Passed!';
            }
            
            return `
                <div class="test-results-card glass-card success-card">
                    <div class="results-header">
                        <div class="success-icon">🎉</div>
                        <h3>Theory Test Results</h3>
                        <div class="status-badge passed">PASSED ✓</div>
                    </div>
                    
                    <!-- User Information Section -->
                    <div class="user-info-section">
                        <div class="user-avatar-large">
                            <div class="avatar-circle-large">${fullName.charAt(0).toUpperCase()}</div>
                        </div>
                        <div class="user-details">
                            <h4 class="user-name">${fullName}</h4>
                            <p class="user-nic">NIC: ${LicenseXpress.formatNIC(nic)}</p>
                            <p class="user-application-id">Application ID: ${applicationId}</p>
                        </div>
                    </div>
                    
                    <div class="results-summary">
                        <div class="score-display">
                            <div class="score-circle" style="background: conic-gradient(${scoreColor} ${scorePercentage * 3.6}deg, rgba(255,255,255,0.1) 0deg);">
                                <div class="score-inner">
                                    <span class="score-number">${score}</span>
                                    <span class="score-total">/50</span>
                                </div>
                            </div>
                            <div class="score-details">
                                <div class="score-percentage">${scorePercentage}%</div>
                                <div class="score-label">${scoreLabel}</div>
                            </div>
                        </div>
                        
                        <div class="results-grid">
                            <div class="result-item">
                                <div class="result-icon">📅</div>
                                <div class="result-content">
                                    <div class="result-label">Test Date</div>
                                    <div class="result-value">${testDate}</div>
                                </div>
                            </div>
                            <div class="result-item">
                                <div class="result-icon">⏱️</div>
                                <div class="result-content">
                                    <div class="result-label">Time Taken</div>
                                    <div class="result-value">${theoryData.timeTaken || '38 minutes'}</div>
                                </div>
                            </div>
                            <div class="result-item">
                                <div class="result-icon">🎯</div>
                                <div class="result-content">
                                    <div class="result-label">Pass Mark</div>
                                    <div class="result-value">40/50 (80%)</div>
                                </div>
                            </div>
                            <div class="result-item">
                                <div class="result-icon">📊</div>
                                <div class="result-content">
                                    <div class="result-label">Your Score</div>
                                    <div class="result-value">${score}/50 (${scorePercentage}%)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="achievement-message">
                        <p>🎊 Congratulations ${fullName.split(' ')[0]}! You've successfully passed your theory test. You're now one step closer to getting your driving license!</p>
                    </div>
                </div>
                
                <div class="next-steps-card glass-card">
                    <div class="next-steps-header">
                        <div class="next-icon">🚗</div>
                        <h3>What's Next?</h3>
                        <div class="progress-indicator">
                            <div class="progress-step completed">1</div>
                            <div class="progress-line"></div>
                            <div class="progress-step current">2</div>
                            <div class="progress-line"></div>
                            <div class="progress-step">3</div>
                        </div>
                    </div>
                    
                    <div class="next-steps-content">
                        <div class="step-description">
                            <h4>🎯 Ready for Your Practical Test!</h4>
                            <p>Your practical driving test has been automatically scheduled. Here's everything you need to know:</p>
                        </div>
                        
                        <div class="practical-info-grid">
                            <div class="info-card">
                                <div class="info-icon">📍</div>
                                <div class="info-content">
                                    <h5>Test Location</h5>
                                    <p>Approved DMT Test Center</p>
                                    <small>Address will be provided in your confirmation email</small>
                                </div>
                            </div>
                            
                            <div class="info-card">
                                <div class="info-icon">⏰</div>
                                <div class="info-content">
                                    <h5>Duration</h5>
                                    <p>Approximately 45 minutes</p>
                                    <small>Includes briefing and debriefing</small>
                                </div>
                            </div>
                            
                            <div class="info-card">
                                <div class="info-icon">🚙</div>
                                <div class="info-content">
                                    <h5>Vehicle</h5>
                                    <p>Your own vehicle or center rental</p>
                                    <small>Must be roadworthy and insured</small>
                                </div>
                            </div>
                            
                            <div class="info-card">
                                <div class="info-icon">👨‍🏫</div>
                                <div class="info-content">
                                    <h5>Examiner</h5>
                                    <p>DMT Certified Instructor</p>
                                    <small>Professional and experienced</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="important-notice">
                            <div class="notice-icon">⚠️</div>
                            <div class="notice-content">
                                <h5>Important Deadline</h5>
                                <p>You must pass the practical test within <strong>6 months</strong> of passing the theory test, or you'll need to retake the theory test.</p>
                            </div>
                        </div>
                        
                        <div class="action-buttons">
                            <button class="btn btn-primary" onclick="LicenseXpress.handleAction('view-practical-details')">
                                <span class="btn-icon">👁️</span>
                                View Practical Details
                            </button>
                            <button class="btn btn-secondary" onclick="LicenseXpress.handleAction('download-study-guide')">
                                <span class="btn-icon">📚</span>
                                Download Study Guide
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        function generateTheoryFailedContent(tests) {
            return `
                <div class="test-results-card glass-card failed">
                    <h3>❌ Theory Test Results</h3>
                    <div class="results-info">
                        <p><strong>Status:</strong> FAILED ❌</p>
                        <p><strong>Score:</strong> ${tests.theory.score}/50 (${Math.round(tests.theory.score/50*100)}%)</p>
                        <p><strong>Pass Mark:</strong> 40/50 (80%)</p>
                        <p><strong>Test Date:</strong> ${LicenseXpress.formatDate(tests.theory.passedDate)}</p>
                        <p><strong>Attempts:</strong> ${tests.theory.attempts || 1}</p>
                    </div>
                </div>
                <div class="failure-message-card glass-card">
                    <h3>📚 What's Next?</h3>
                    <p>You need to score at least 40 out of 50 questions to pass the theory test. Don't worry - you can retake the exam as many times as needed.</p>
                    <div class="study-tips">
                        <h4>Tips for your next attempt:</h4>
                        <ul>
                            <li>• Study the official driving handbook thoroughly</li>
                            <li>• Take practice tests to familiarize yourself with the format</li>
                            <li>• Review road signs and traffic rules</li>
                            <li>• Ensure you have a stable internet connection</li>
                        </ul>
                    </div>
                    <div class="study-resources">
                        <h4>📚 Study Resources:</h4>
                        <ul>
                            <li>• Official Handbook: <a href="#" class="link">Download PDF</a></li>
                            <li>• Practice Tests: <a href="#" class="link">Take Practice Test</a></li>
                            <li>• Road Signs Guide: <a href="#" class="link">View Guide</a></li>
                            <li>• Safety Rules: <a href="#" class="link">Study Material</a></li>
                        </ul>
                    </div>
                </div>
            `;
        }

        function generatePracticalScheduledContent(tests) {
            return `
                <div class="practical-details-card glass-card">
                    <h3>🚗 Practical Test Details</h3>
                    <div class="test-info">
                        <p><strong>Date:</strong> ${LicenseXpress.formatDate(tests.practical.date)}</p>
                        <p><strong>Time:</strong> ${tests.practical.time} (Report by ${tests.practical.reportTime || '30 minutes before'})</p>
                        <p><strong>Center:</strong> ${tests.practical.center}</p>
                        <p><strong>Address:</strong> ${tests.practical.address}</p>
                        <p><strong>Examiner:</strong> ${tests.practical.examiner}</p>
                        <p><strong>Vehicle:</strong> ${tests.practical.vehicle}</p>
                    </div>
                    <div class="test-components">
                        <h4>Test Components:</h4>
                        <ul>
                            <li>✓ Pre-drive vehicle check (5 min)</li>
                            <li>✓ Basic maneuvers (10 min)</li>
                            <li>✓ Road driving (25 min)</li>
                            <li>✓ Parking test (5 min)</li>
                        </ul>
                        <p><strong>Total Duration:</strong> ~45 minutes</p>
                    </div>
                </div>
                <div class="required-documents glass-card">
                    <h3>⚠️ Bring These Documents</h3>
                    <ul>
                        <li>Original NIC</li>
                        <li>Theory test pass certificate</li>
                        <li>Learner's permit</li>
                        <li>Vehicle registration (if using own vehicle)</li>
                        <li>Valid insurance certificate</li>
                    </ul>
                    <p><strong>Failure to bring any required document will result in test cancellation.</strong></p>
                </div>
            `;
        }

<<<<<<< Updated upstream
        function generateLicenseIssuedContent(license) {
            return `
                <div class="license-preview glass-card">
                    <h3>🏆 Your Digital License</h3>
                    <div class="license-card-preview">
                        <div class="license-header">🇱🇰 SRI LANKA DRIVING LICENSE</div>
                        <div class="license-content">
                            <div class="license-photo">[Photo]</div>
                            <div class="license-details">
                                <p><strong>LICENSE NUMBER:</strong> ${license.number}</p>
                                <p><strong>NAME:</strong> ${license.name || 'JOHN DOE'}</p>
                                <p><strong>NIC:</strong> ${license.nic || '200012345678'}</p>
                                <p><strong>ADDRESS:</strong> ${license.address || 'Colombo 07'}</p>
                                <p><strong>CATEGORY:</strong> ${license.category} - Light Motor Vehicle</p>
                                <p><strong>TRANSMISSION:</strong> ${license.transmission || 'Manual'}</p>
                                <p><strong>ISSUE DATE:</strong> ${LicenseXpress.formatDate(license.issueDate)}</p>
                                <p><strong>EXPIRY DATE:</strong> ${LicenseXpress.formatDate(license.expiryDate)}</p>
                            </div>
=======
        function generateLicenseIssuedContent(license, userProfile) {
            
            const formatDate = (dateString) => {
                if (!dateString) return 'Not specified';
                try {
                    const date = new Date(dateString);
                    if (isNaN(date.getTime())) return 'Not specified';
                    return date.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                } catch (e) {
                    return 'Not specified';
                }
            };

           
            const issueDate = license?.issue_date || new Date().toISOString().split('T')[0];
            const expiryDate = license?.expiry_date || new Date(Date.now() + 5 * 365 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
            
            return `
                <div class="glass-card">
                    <div class="congratulations-header">
                        <div class="celebration-icon">🎉</div>
                        <h2>Congratulations!</h2>
                        <h3>Your Driving License Has Been Successfully Issued</h3>
                        <p class="subtitle">You are now officially a licensed driver in Sri Lanka</p>
                    </div>
                    
                    <div class="license-info">
                        <h4>📋 Your License Details</h4>
                        <div class="info-row">
                            <span class="label">License Number:</span>
                            <span class="value highlight">${license?.license_number || 'DL-2025-001234'}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Full Name:</span>
                            <span class="value">${userProfile?.fullName || 'License Holder'}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">NIC Number:</span>
                            <span class="value">${userProfile?.nic || 'Not available'}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Vehicle Category:</span>
                            <span class="value">${userProfile?.transmissionType || 'B'} - Light Motor Vehicle</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Issue Date:</span>
                            <span class="value">${formatDate(issueDate)}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Expiry Date:</span>
                            <span class="value">${formatDate(expiryDate)}</span>
>>>>>>> Stashed changes
                        </div>
                    </div>
                </div>
                <div class="license-actions glass-card">
                    <h3>📄 Download Options</h3>
                    <div class="action-buttons">
                        <button class="btn btn-primary">Download Digital License</button>
                        <button class="btn btn-secondary">Download as Image</button>
                        <button class="btn btn-secondary">Print License</button>
                    </div>
                </div>
                <div class="achievement-summary glass-card">
                    <h3>🎊 Journey Summary</h3>
                    <div class="summary-content">
                        <p><strong>Started:</strong> ${LicenseXpress.formatDate(license.journeyStart || new Date(Date.now() - 46*24*60*60*1000).toISOString())}</p>
                        <p><strong>Completed:</strong> ${LicenseXpress.formatDate(license.issueDate)}</p>
                        <p><strong>Total Time:</strong> 46 days</p>
                        <div class="milestones">
                            <h4>Milestones:</h4>
                            <ul>
                                <li>✓ Application submitted</li>
                                <li>✓ Documents verified</li>
                                <li>✓ Theory test passed (38/40 - 95%)</li>
                                <li>✓ Practical test passed</li>
                                <li>✓ License issued</li>
                            </ul>
                            <p><strong>You're now a licensed driver! 🎉</strong></p>
                        </div>
                    </div>
                </div>
            `;
        }
    </script>

    <style>
<<<<<<< Updated upstream
=======
       
        .congratulations-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(34, 197, 94, 0.1));
            border-radius: 15px;
            border: 2px solid rgba(16, 185, 129, 0.3);
        }
        
        .celebration-icon {
            font-size: 48px;
            margin-bottom: 15px;
            animation: bounce 2s infinite;
        }
        
        .congratulations-header h2 {
            color: #10B981;
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 10px 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .congratulations-header h3 {
            color: #FFFFFF;
            font-size: 20px;
            font-weight: 600;
            margin: 0 0 8px 0;
        }
        
        .congratulations-header .subtitle {
            color: #94B8C4;
            font-size: 16px;
            margin: 0;
            font-style: italic;
        }
        
        .license-info h4 {
            color: #0A9396;
            font-size: 18px;
            font-weight: 600;
            margin: 0 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(10, 147, 150, 0.3);
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-row .label {
            color: #94B8C4;
            font-weight: 600;
            font-size: 14px;
        }
        
        .info-row .value {
            color: #FFFFFF;
            font-weight: 500;
            font-size: 16px;
        }
        
        .info-row .value.highlight {
            color: #10B981;
            font-weight: 700;
            font-size: 18px;
        }
        
        .success-message {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(251, 191, 36, 0.1));
            border-radius: 15px;
            border: 2px solid rgba(245, 158, 11, 0.3);
        }
        
        .success-icon {
            font-size: 32px;
            margin-bottom: 15px;
        }
        
        .success-message p {
            color: #FFFFFF;
            margin: 8px 0;
            font-size: 16px;
        }
        
        .success-message p strong {
            color: #F59E0B;
            font-size: 18px;
        }
        
        .success-message .reminder {
            color: #94B8C4;
            font-size: 14px;
            font-style: italic;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }
        
>>>>>>> Stashed changes
        .header {
            display: block;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: var(--gradient-1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }

        .logout-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--surface);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 16px;
        }

        .logout-btn:hover {
            background: var(--surface-hover);
            color: var(--text);
            transform: scale(1.05);
        }

        
        .btn, .logout-btn {
            pointer-events: auto;
            cursor: pointer;
            user-select: none;
        }

        .btn:disabled {
            pointer-events: none;
            opacity: 0.6;
        }

<<<<<<< Updated upstream
=======
        /* Timeline Styles */
>>>>>>> Stashed changes
        .timeline-section {
            margin: 40px 0;
        }

        .section-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 24px;
            text-align: center;
        }

        .timeline {
            display: flex;
            flex-direction: column;
            gap: 16px;
            position: relative;
            padding-left: 24px;
        }

        .timeline-step {
            display: flex;
            align-items: center;
            gap: 16px;
            position: relative;
            padding: 16px 0;
        }

        .timeline-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
            background: var(--surface);
            border: 2px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .timeline-step.active .timeline-icon {
            background: var(--gradient-1);
            border-color: var(--primary-light);
            box-shadow: 0 0 20px rgba(0, 95, 115, 0.3);
        }

        .timeline-step.completed .timeline-icon {
            background: var(--success);
            border-color: var(--success);
        }

        .timeline-step.rejected .timeline-icon {
            background: var(--error);
            border-color: var(--error);
        }

        .timeline-content {
            flex: 1;
        }

        .timeline-label {
            font-size: 16px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 4px;
        }

        .timeline-date {
            font-size: 14px;
            color: var(--text-muted);
        }

        .timeline-connector {
            position: absolute;
            left: 39px;
            top: 60px;
            width: 2px;
            height: 20px;
            background: rgba(255, 255, 255, 0.1);
        }

        .timeline-step.completed + .timeline-step .timeline-connector {
            background: var(--success);
        }

        .timeline-step.active + .timeline-step .timeline-connector {
            background: var(--gradient-1);
        }

        .breadcrumb {
            background: var(--bg-light);
            padding: 16px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .breadcrumb-item {
            color: var(--text-muted);
        }

        .breadcrumb-separator {
            color: var(--text-muted);
            margin: 0 8px;
        }

        .breadcrumb-current {
            color: var(--text);
            font-weight: 600;
        }

        .dashboard-main {
            padding: 40px 0;
        }

        .profile-card {
            margin-bottom: 32px;
            padding: 32px;
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 24px;
            margin-bottom: 24px;
        }

        .profile-avatar {
            flex-shrink: 0;
        }

        .avatar-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: var(--gradient-1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: 800;
            color: white;
            border: 4px solid var(--primary);
        }

        .profile-info {
            flex: 1;
        }

        .profile-name {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 8px;
            color: var(--text);
        }

        .profile-nic {
            font-size: 18px;
            color: var(--text-muted);
            margin-bottom: 24px;
        }


        .status-message {
            background: var(--surface);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 20px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .status-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .status-card {
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .status-icon {
            font-size: 32px;
            flex-shrink: 0;
        }

        .status-content {
            flex: 1;
        }

        .status-label {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .status-value {
            font-size: 18px;
            font-weight: 600;
            color: var(--text);
        }

        .timeline-section {
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 24px;
            color: var(--text);
        }

        .timeline {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .timeline-step {
            display: flex;
            align-items: center;
            gap: 16px;
            position: relative;
            opacity: 0.4;
            transition: all 0.3s ease;
        }

        .timeline-step.completed {
            opacity: 1;
        }

        .timeline-step.active {
            opacity: 1;
            transform: scale(1.02);
        }

        .timeline-step.rejected {
            opacity: 1;
        }

        .timeline-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: var(--surface);
            border: 2px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .timeline-step.completed .timeline-icon {
            background: var(--success);
            border-color: var(--success);
            color: white;
        }

        .timeline-step.active .timeline-icon {
            background: var(--gradient-1);
            border-color: var(--primary);
            color: white;
            animation: pulse 2s infinite;
        }

        .timeline-step.rejected .timeline-icon {
            background: var(--error);
            border-color: var(--error);
            color: white;
        }

        .timeline-content {
            flex: 1;
        }

        .timeline-label {
            font-size: 18px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 4px;
        }

        .timeline-date {
            font-size: 14px;
            color: var(--text-muted);
        }

        .timeline-connector {
            position: absolute;
            left: 32px;
            top: 64px;
            width: 2px;
            height: 24px;
            background: rgba(255, 255, 255, 0.1);
        }

        .timeline-step.completed + .timeline-step .timeline-connector {
            background: var(--success);
        }

        .action-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .btn-action {
            padding: 20px 40px;
            font-size: 20px;
        }

        .dynamic-content {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .info-card,
        .verification-checklist,
        .rejection-alert,
        .resubmission-guidelines,
        .success-card,
        .test-details-card,
        .test-results-card,
        .next-steps-card,
        .practical-details-card,
        .required-documents,
        .license-preview,
        .license-actions,
        .achievement-summary {
            padding: 24px;
        }

        .info-card h3,
        .verification-checklist h3,
        .rejection-alert h3,
        .resubmission-guidelines h3,
        .success-card h3,
        .test-details-card h3,
        .test-results-card h3,
        .next-steps-card h3,
        .practical-details-card h3,
        .required-documents h3,
        .license-preview h3,
        .license-actions h3,
        .achievement-summary h3 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 16px;
            color: var(--text);
        }

        .checklist-items {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
        }

        .checklist-item {
            padding: 12px;
            background: var(--surface);
            border-radius: 8px;
            font-size: 14px;
        }

        .rejection-alert {
            border-left: 4px solid var(--error);
        }

        .success-card {
            border-left: 4px solid var(--success);
        }

        .license-card-preview {
            background: var(--bg-light);
            border: 2px solid var(--primary);
            border-radius: 12px;
            padding: 24px;
            margin: 16px 0;
        }

        .license-header {
            text-align: center;
            font-weight: 800;
            font-size: 18px;
            margin-bottom: 20px;
            color: var(--primary);
        }

        .license-content {
            display: flex;
            gap: 20px;
        }

        .license-photo {
            width: 80px;
            height: 100px;
            background: var(--surface);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: var(--text-muted);
        }

        .license-details {
            flex: 1;
        }

        .license-details p {
            margin-bottom: 8px;
            font-size: 14px;
        }

        .action-buttons {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .summary-content p {
            margin-bottom: 12px;
        }

        .milestones {
            margin-top: 20px;
        }

        .milestones h4 {
            font-size: 16px;
            margin-bottom: 12px;
            color: var(--text);
        }

        .milestones ul {
            margin-bottom: 16px;
        }

        .milestones li {
            margin-bottom: 8px;
        }

       
        .test-results-card.success-card {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(34, 197, 94, 0.05) 100%);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .results-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .success-icon {
            font-size: 32px;
            margin-right: 12px;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.passed {
            background: rgba(16, 185, 129, 0.2);
            color: #10B981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .results-summary {
            display: flex;
            flex-direction: column;
            gap: 24px;
            margin-bottom: 8px;
        }

        .score-display {
            display: flex;
            align-items: center;
            gap: 24px;
            justify-content: center;
        }

        .score-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .score-inner {
            width: 100px;
            height: 100px;
            background: var(--bg-dark);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .score-number {
            font-size: 32px;
            font-weight: 800;
            color: var(--text);
            line-height: 1;
        }

        .score-total {
            font-size: 16px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .score-details {
            text-align: center;
        }

        .score-percentage {
            font-size: 28px;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 4px;
        }

        .score-label {
            font-size: 14px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 16px;
        }

        .result-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .result-item:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-2px);
        }

        .result-icon {
            font-size: 24px;
            flex-shrink: 0;
        }

        .result-content {
            flex: 1;
        }

        .result-label {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 4px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .result-value {
            font-size: 16px;
            color: var(--text);
            font-weight: 700;
        }

        .achievement-message {
            text-align: center;
            padding: 20px;
            background: rgba(16, 185, 129, 0.1);
            border-radius: 12px;
            border: 1px solid rgba(16, 185, 129, 0.2);
            margin-top: 32px;
        }

        .achievement-message p {
            font-size: 16px;
            color: var(--text);
            margin: 0;
            font-weight: 600;
        }

        
        .user-info-section {
            display: flex;
            align-items: flex-start;
            gap: 24px;
            padding: 28px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 24px;
            min-height: 140px;
        }

        .user-avatar-large {
            flex-shrink: 0;
        }

        .avatar-circle-large {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--gradient-1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 800;
            color: white;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .user-details {
            flex: 1;
            min-width: 0; 
        }

        .user-name {
            font-size: 24px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 8px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
            line-height: 1.2;
            hyphens: none;
            -webkit-hyphens: none;
            -moz-hyphens: none;
            max-width: 100%;
        }

        .user-nic {
            font-size: 16px;
            color: var(--text-muted);
            margin-bottom: 4px;
            font-weight: 600;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }

        .user-application-id {
            font-size: 14px;
            color: var(--text-muted);
            margin: 0;
            font-weight: 500;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }

       
        .next-steps-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .next-icon {
            font-size: 32px;
            margin-right: 12px;
        }

        .progress-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .progress-step {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .progress-step.completed {
            background: #10B981;
            color: white;
        }

        .progress-step.current {
            background: #3B82F6;
            color: white;
            animation: pulse 2s infinite;
        }

        .progress-step:not(.completed):not(.current) {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-muted);
        }

        .progress-line {
            width: 24px;
            height: 2px;
            background: rgba(255, 255, 255, 0.2);
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .next-steps-content {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .step-description {
            text-align: center;
        }

        .step-description h4 {
            font-size: 20px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 8px;
        }

        .step-description p {
            font-size: 16px;
            color: var(--text-muted);
            margin: 0;
        }

        .practical-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
        }

        .info-card {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .info-card:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-2px);
        }

        .info-icon {
            font-size: 24px;
            flex-shrink: 0;
            margin-top: 4px;
        }

        .info-content h5 {
            font-size: 16px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 4px;
        }

        .info-content p {
            font-size: 14px;
            color: var(--text);
            margin-bottom: 4px;
            font-weight: 600;
        }

        .info-content small {
            font-size: 12px;
            color: var(--text-muted);
        }

        .important-notice {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 20px;
            background: rgba(245, 158, 11, 0.1);
            border-radius: 12px;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .notice-icon {
            font-size: 24px;
            flex-shrink: 0;
            margin-top: 4px;
        }

        .notice-content h5 {
            font-size: 16px;
            font-weight: 700;
            color: var(--warning);
            margin-bottom: 8px;
        }

        .notice-content p {
            font-size: 14px;
            color: var(--text);
            margin: 0;
            line-height: 1.5;
        }

        .action-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 160px;
            justify-content: center;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        .btn-icon {
            font-size: 16px;
        }

        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }

            .status-details {
                grid-template-columns: 1fr;
            }

            .timeline {
                padding-left: 0;
            }

            .timeline-step {
                flex-direction: column;
                text-align: center;
            }

            .timeline-connector {
                display: none;
            }

            .action-buttons {
                flex-direction: column;
            }

            .license-content {
                flex-direction: column;
            }

            .score-display {
                flex-direction: column;
                text-align: center;
            }

            .results-grid {
                grid-template-columns: 1fr;
                margin-bottom: 20px;
            }

            .achievement-message {
                margin-top: 24px;
            }

            .practical-info-grid {
                grid-template-columns: 1fr;
            }

            .next-steps-header {
                flex-direction: column;
                gap: 16px;
                text-align: center;
            }

            .progress-indicator {
                justify-content: center;
            }

            .user-info-section {
                flex-direction: column;
                text-align: center;
                gap: 16px;
                padding: 24px;
                min-height: auto;
            }

            .avatar-circle-large {
                width: 60px;
                height: 60px;
                font-size: 24px;
            }

            .user-name {
                font-size: 20px;
                line-height: 1.3;
            }

            .user-nic {
                font-size: 14px;
            }

            .user-application-id {
                font-size: 12px;
            }
        }
    </style>
</body>
</html>
