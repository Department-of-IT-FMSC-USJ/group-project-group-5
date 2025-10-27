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
    <link rel="stylesheet" href="assets/css/dashboard.css">
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
                        <h1 class="profile-name" id="profileName">Loading...</h1>
                        <p class="profile-nic" id="profileNIC">Loading...</p>
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
        </div>
    </main>

  
    <div id="confettiContainer"></div>

    <script src="assets/js/app.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            

            LicenseXpress.initializeUserData();

            // Check for recent exam results and update status
            const lastExamResult = JSON.parse(localStorage.getItem('lastExamResult') || 'null');
            if (lastExamResult && lastExamResult.timestamp) {
                const examTime = new Date(lastExamResult.timestamp);
                const now = new Date();
                const timeDiff = now - examTime;
                
                // If exam was completed within the last 5 minutes, update status
                if (timeDiff < 5 * 60 * 1000) {
                    console.log('Recent exam completed, updating status...');
                    if (lastExamResult.passed) {
                        // Update to theory_passed status
                        LicenseXpress.showToast('🎉 Congratulations! You passed your theory test!', 'success');
                        // Refresh the page to show updated status
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else {
                        // Update to theory_failed status
                        LicenseXpress.showToast(`Theory test completed. Score: ${lastExamResult.score}/${lastExamResult.total}`, 'info');
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }
                    
                    // Clear the exam result from localStorage
                    localStorage.removeItem('lastExamResult');
                }
            }

       
            const currentUser = JSON.parse(localStorage.getItem('currentUser') || '{}');
            
            if (!currentUser || !currentUser.nic) {
                console.error('No user NIC found');
                window.location.href = 'login.php';
                return;
            }

           
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

          
            updateStatusDetails(applicationState, userProfile, tests, license);

            updateTimeline(status, applicationState, tests, license);

           
            updateActionButton(status, applicationState, tests, license);

            
            updateDynamicContent(status, applicationState, userProfile, tests, license);

        
            if (status === 'license_issued') {
                setTimeout(() => {
                    LicenseXpress.createConfetti();
                }, 500);
            }
        }

        function updateStatusBadge(status) {
            const statusBadge = document.getElementById('statusBadge');
            
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
                    statusValue1.textContent = license?.license_number || 'Not issued';
                    statusLabel2.textContent = 'Issue Date';
                    statusValue2.textContent = LicenseXpress.formatDate(license?.issue_date);
                    statusLabel3.textContent = 'Expiry Date';
                    statusValue3.textContent = LicenseXpress.formatDate(license?.expiry_date);
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
                { id: 'theory_scheduled', icon: '📝', label: 'Theory Test Scheduled', date: tests.theory?.scheduled_date || tests.theory?.test_date, color: 'blue' },
                { id: 'theory_passed', icon: '🎓', label: 'Theory Passed', date: tests.theory?.passed_date || tests.theory?.test_date, color: 'green' },
                { id: 'practical_scheduled', icon: '🚗', label: 'Practical Scheduled', date: tests.practical?.scheduled_date, color: 'blue' },
                { id: 'license_issued', icon: '🏆', label: 'License Issued', date: license?.issue_date, color: 'gold' }
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
                'rejected': { text: 'Resubmit Documents', icon: '📝', action: 'application-form.php' },
                'verified': { text: 'Schedule Theory Test', icon: '📅', action: 'schedule-theory.php' },
                'theory_scheduled': { text: 'Take Theory Exam', icon: '📝', action: 'exam-window.php' },
                'theory_passed': { text: 'View Practical Details', icon: '🚗', action: 'view-practical-details' },
                'theory_failed': { text: 'Reschedule Theory Test', icon: '🔄', action: 'reschedule-exam.php' },
                'practical_scheduled': { text: 'View Test Details', icon: '👁️', action: 'view-practical-details' },
                'license_issued': { text: 'Download License', icon: '⬇️', action: 'download-license' }
            };

            // Hide action button for pending_verification status
            if (status === 'pending_verification') {
                actionButton.style.display = 'none';
                return;
            }

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

        async function fetchApplicationDetails() {
            try {
                // Get current user's NIC from localstorage
                const currentUser = JSON.parse(localStorage.getItem('currentUser') || '{}');
                const nic = currentUser.nic;
                
                if (!nic) {
                    throw new Error('User NIC not found. Please login again.');
                }
                
                // Show loading state in the details container
                const detailsContainer = document.getElementById('application-details-container');
                if (detailsContainer) {
                    detailsContainer.innerHTML = `
                        <div class="application-details-loading">
                            <div class="loading-spinner"></div>
                            <p>Loading application details...</p>
                        </div>
                    `;
                    detailsContainer.style.display = 'block';
                }
                
                // Fetch application data from the API with NIC parameter
                console.log('Fetching application details for NIC:', nic);
                const response = await fetch(`get_application_status.php?nic=${encodeURIComponent(nic)}`);
                console.log('API response status:', response.status);
                const data = await response.json();
                console.log('API response data:', data);
                
                if (data.success) {
                    const application = data.application;
                    const user = data.user;
                    const payment = data.payment;
                    
                    // Format applicationdetails - Only the essential information
                    const applicationDetails = `
                        <div class="application-details">
                            <div class="details-header">
                                <h4>📋 Application Information</h4>
                                <div class="status-badge pending">Under Review</div>
                            </div>
                            
                            <div class="details-grid">
                                <div class="detail-item">
                                    <div class="detail-icon">🆔</div>
                                    <div class="detail-content">
                                        <h5>Application ID</h5>
                                        <p>${application.application_id || 'LX-2025-001234'}</p>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-icon">📅</div>
                                    <div class="detail-content">
                                        <h5>Date Submitted</h5>
                                        <p>${LicenseXpress.formatDateTime(application.submitted_date || application.created_at)}</p>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-icon">📊</div>
                                    <div class="detail-content">
                                        <h5>Progress</h5>
                                        <p>${application.progress || 14}% Complete</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="details-footer">
                                <p>📧 You will be notified via email and SMS when verification is complete.</p>
                            </div>
                        </div>
                    `;
                    
                    // Update the details container with real data
                    if (detailsContainer) {
                        detailsContainer.innerHTML = applicationDetails;
                    }
                    
                } else {
                    throw new Error(data.error || 'Failed to fetch application details');
                }
                
            } catch (error) {
                console.error('Error fetching application details:', error);
                const detailsContainer = document.getElementById('application-details-container');
                if (detailsContainer) {
                    detailsContainer.innerHTML = `
                        <div class="application-details-error">
                            <div class="error-icon">❌</div>
                            <h4>Error Loading Details</h4>
                            <p>Unable to load application details. Please try again later.</p>
                            <p><small>Error: ${error.message}</small></p>
                        </div>
                    `;
                }
            }
        }

        function handleAction(action, applicationState, tests, license) {
            console.log('handleAction called with:', action);
            switch (action) {
                case 'view-application':
                    console.log('Calling fetchApplicationDetails...');
                    // Fetch real application data from database
                    fetchApplicationDetails();
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
                    window.location.href = 'schedule-practical.php';
                    break;

                case 'download-license':
                   
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
                <div class="welcome-section glass-card">
                    <div class="welcome-header">
                        <div class="welcome-icon">🚗</div>
                        <div class="welcome-content">
                            <h2>Welcome to LicenseXpress!</h2>
                            <p>Get your Sri Lankan driving license in just a few simple steps. Let's get started!</p>
                        </div>
                    </div>
                    
                    <div class="requirements-section">
                        <h3>📋 What You'll Need to Get Started</h3>
                        <div class="requirements-grid">
                            <div class="requirement-item">
                                <div class="req-icon">📄</div>
                                <div class="req-content">
                                    <h4>Birth Certificate</h4>
                                    <p>Digital copy (PDF or clear photo)</p>
                                    <span class="req-note">Must be official government document</span>
                                </div>
                            </div>
                            
                            <div class="requirement-item">
                                <div class="req-icon">🆔</div>
                                <div class="req-content">
                                    <h4>NIC Copy</h4>
                                    <p>Front and back sides</p>
                                    <span class="req-note">Clear, readable images</span>
                                </div>
                            </div>
                            
                            <div class="requirement-item">
                                <div class="req-icon">🏥</div>
                                <div class="req-content">
                                    <h4>Medical Certificate</h4>
                                    <p>Less than 6 months old</p>
                                    <span class="req-note">From registered medical practitioner</span>
                                </div>
                            </div>
                            
                            <div class="requirement-item">
                                <div class="req-icon">📸</div>
                                <div class="req-content">
                                    <h4>Passport Photo</h4>
                                    <p>White background</p>
                                    <span class="req-note">Professional quality, recent photo</span>
                                </div>
                            </div>
                            
                            <div class="requirement-item">
                                <div class="req-icon">💳</div>
                                <div class="req-content">
                                    <h4>Payment</h4>
                                    <p>Rs. 3,200</p>
                                    <span class="req-note">Credit/Debit card or bank transfer</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="process-info">
                        <h3>⚡ Quick Process Overview</h3>
                        <div class="process-steps">
                            <div class="process-step">
                                <div class="step-number">1</div>
                                <div class="step-content">
                                    <h4>Submit Application</h4>
                                    <p>Upload documents and pay fees</p>
                                </div>
                            </div>
                            <div class="step-arrow">→</div>
                            <div class="process-step">
                                <div class="step-number">2</div>
                                <div class="step-content">
                                    <h4>Verification</h4>
                                    <p>Documents reviewed (24-48 hours)</p>
                                </div>
                            </div>
                            <div class="step-arrow">→</div>
                            <div class="process-step">
                                <div class="step-number">3</div>
                                <div class="step-content">
                                    <h4>Theory Test</h4>
                                    <p>Online exam (50 questions)</p>
                                </div>
                            </div>
                            <div class="step-arrow">→</div>
                            <div class="process-step">
                                <div class="step-number">4</div>
                                <div class="step-content">
                                    <h4>Practical Test</h4>
                                    <p>Driving test at approved center</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="help-section">
                        <h3>❓ Need Help?</h3>
                        <div class="help-options">
                            <div class="help-item">
                                <div class="help-icon">📞</div>
                                <div class="help-content">
                                    <h4>Call Us</h4>
                                    <p>+94 11 234 5678</p>
                                </div>
                            </div>
                            <div class="help-item">
                                <div class="help-icon">📧</div>
                                <div class="help-content">
                                    <h4>Email Support</h4>
                                    <p>support@licensexpress.lk</p>
                                </div>
                            </div>
                            <div class="help-item">
                                <div class="help-icon">💬</div>
                                <div class="help-content">
                                    <h4>Live Chat</h4>
                                    <p>Available 9 AM - 6 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function generatePendingVerificationContent() {
            return `
                <div class="verification-status-card glass-card">
                    <div class="status-header">
                        <div class="status-icon">⏳</div>
                        <div class="status-content">
                            <h3>Application Under Review</h3>
                            <p>Your application is currently being verified by our team. This process typically takes up to 48 hours.</p>
                        </div>
                    </div>
                    
                    <div class="quick-actions">
                        <button class="btn btn-primary btn-large" onclick="handleAction('view-application')">
                            <span class="btn-icon">👁️</span>
                            View Application Details
                        </button>
                    </div>
                </div>
                
                <!-- Application Details Container -->
                <div id="application-details-container" class="application-details-container" style="display: none;">
                    <!-- Details will be loaded here -->
                </div>
                
                <div class="verification-progress-section glass-card">
                    <div class="progress-header">
                        <h3>📊 Verification Progress</h3>
                        <div class="progress-status">In Progress</div>
                    </div>
                    
                    <div class="progress-timeline">
                        <div class="timeline-item completed">
                            <div class="timeline-marker">✓</div>
                            <div class="timeline-content">
                                <h4>Application Submitted</h4>
                                <p>Your application has been successfully submitted</p>
                                <span class="timeline-time">Just now</span>
                            </div>
                        </div>
                        
                        <div class="timeline-item active">
                            <div class="timeline-marker">⏳</div>
                            <div class="timeline-content">
                                <h4>Document Review</h4>
                                <p>Our team is reviewing your submitted documents</p>
                                <span class="timeline-time">In progress</span>
                            </div>
                        </div>
                        
                        <div class="timeline-item pending">
                            <div class="timeline-marker">📋</div>
                            <div class="timeline-content">
                                <h4>Verification Complete</h4>
                                <p>Documents will be approved and you'll be notified</p>
                                <span class="timeline-time">Expected: 24-48 hours</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="verification-info-grid">
                <div class="info-card glass-card">
                        <div class="info-icon">📧</div>
                        <div class="info-content">
                            <h4>Email Notifications</h4>
                            <p>You'll receive email updates when:</p>
                            <ul>
                                <li>Verification starts</li>
                        <li>Documents are approved</li>
                                <li>Any issues are found</li>
                                <li>Verification is complete</li>
                    </ul>
                </div>
                    </div>
                    
                    <div class="info-card glass-card">
                        <div class="info-icon">📱</div>
                        <div class="info-content">
                            <h4>SMS Updates</h4>
                            <p>Important updates will also be sent via SMS to your registered phone number.</p>
                            <div class="contact-info">
                                <strong>Phone:</strong> +94 77 123 4567
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-card glass-card">
                        <div class="info-icon">⏰</div>
                        <div class="info-content">
                            <h4>Processing Time</h4>
                            <p>Typical verification timeline:</p>
                            <ul>
                                <li>Initial review: 2-4 hours</li>
                                <li>Document verification: 12-24 hours</li>
                                <li>Final approval: 24-48 hours</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="info-card glass-card">
                        <div class="info-icon">🆘</div>
                        <div class="info-content">
                            <h4>Need Help?</h4>
                            <p>If you have questions or concerns:</p>
                            <div class="help-contacts">
                                <div class="contact-item">
                                    <strong>📞 Call:</strong> +94 11 234 5678
                                </div>
                                <div class="contact-item">
                                    <strong>📧 Email:</strong> support@licensexpress.lk
                                </div>
                                <div class="contact-item">
                                    <strong>💬 Live Chat:</strong> Available 9 AM - 6 PM
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="verification-checklist glass-card">
                    <div class="checklist-header">
                        <h3>📋 Document Verification Status</h3>
                        <div class="checklist-summary">4 documents under review</div>
                    </div>
                    
                    <div class="checklist-items">
                        <div class="checklist-item">
                            <div class="item-icon">📄</div>
                            <div class="item-content">
                                <h4>Birth Certificate</h4>
                                <p>Under Review</p>
                                <div class="status-indicator pending"></div>
                            </div>
                        </div>
                        
                        <div class="checklist-item">
                            <div class="item-icon">🆔</div>
                            <div class="item-content">
                                <h4>NIC Copy</h4>
                                <p>Under Review</p>
                                <div class="status-indicator pending"></div>
                            </div>
                        </div>
                        
                        <div class="checklist-item">
                            <div class="item-icon">🏥</div>
                            <div class="item-content">
                                <h4>Medical Certificate</h4>
                                <p>Under Review</p>
                                <div class="status-indicator pending"></div>
                            </div>
                        </div>
                        
                        <div class="checklist-item">
                            <div class="item-icon">📸</div>
                            <div class="item-content">
                                <h4>Passport Photo</h4>
                                <p>Under Review</p>
                                <div class="status-indicator pending"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="checklist-footer">
                        <p>All documents are being reviewed by our verification team. You'll be notified of any issues or when verification is complete.</p>
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
                <div class="verified-clean-section">
                    <div class="success-message">
                        <div class="success-icon">✅</div>
                        <div class="success-text">
                            <h2>Great! Your documents are verified</h2>
                            <p>You can now schedule your theory test</p>
                        </div>
                    </div>
                    
                    <div class="test-info-box">
                        <h3>About the Theory Test</h3>
                        <div class="info-list">
                            <div class="info-item">
                                <span class="info-label">Questions:</span>
                                <span class="info-value">50 multiple choice</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Time:</span>
                                <span class="info-value">45 minutes</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Pass mark:</span>
                                <span class="info-value">40 out of 50 (80%)</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="next-action">
                        <p>📅 Click the "Schedule Theory Test" button above to book your exam</p>
                    </div>
                </div>
            `;
        }

        function generateTheoryScheduledContent(tests) {
            return `
                <div class="theory-test-card" style="
                    background: linear-gradient(135deg, rgba(0, 95, 115, 0.1) 0%, rgba(10, 147, 150, 0.1) 100%);
                    border: 2px solid rgba(0, 95, 115, 0.3);
                    border-radius: 20px;
                    padding: 30px;
                    margin: 20px 0;
                    box-shadow: 0 10px 40px rgba(0, 95, 115, 0.2);
                    backdrop-filter: blur(10px);
                ">
                    <!-- Header Section -->
                    <div style="
                        display: flex;
                        align-items: center;
                        margin-bottom: 30px;
                        padding-bottom: 20px;
                        border-bottom: 2px solid rgba(255, 255, 255, 0.1);
                    ">
                        <div style="
                            background: linear-gradient(135deg, #005F73, #0A9396);
                            border-radius: 15px;
                            width: 60px;
                            height: 60px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 28px;
                            margin-right: 20px;
                            box-shadow: 0 5px 15px rgba(0, 95, 115, 0.3);
                        ">📚</div>
                        <div>
                            <h2 style="
                                color: #FFFFFF;
                                font-size: 28px;
                                font-weight: 700;
                                margin: 0 0 8px 0;
                                text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
                            ">Theory Test Details</h2>
                            <p style="
                                color: #94B8C4;
                                font-size: 16px;
                                margin: 0;
                                font-weight: 400;
                            ">Your scheduled examination information</p>
                    </div>
                    </div>
                    
                    <!-- Test Information Grid -->
                    <div style="
                        display: grid;
                        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                        gap: 20px;
                        margin-bottom: 30px;
                    ">
                        <div style="
                            background: rgba(255, 255, 255, 0.08);
                            border: 1px solid rgba(255, 255, 255, 0.15);
                            border-radius: 15px;
                            padding: 20px;
                            transition: all 0.3s ease;
                            cursor: pointer;
                        " onmouseover="this.style.transform='translateY(-5px)'; this.style.background='rgba(255, 255, 255, 0.12)'" onmouseout="this.style.transform='translateY(0)'; this.style.background='rgba(255, 255, 255, 0.08)'">
                            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                <div style="
                                    background: linear-gradient(135deg, #EE9B00, #F9C74F);
                                    border-radius: 10px;
                                    width: 40px;
                                    height: 40px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    font-size: 18px;
                                    margin-right: 15px;
                                ">📅</div>
                                <div>
                                    <div style="color: #94B8C4; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Test Date</div>
                                    <div style="color: #FFFFFF; font-size: 18px; font-weight: 700;">${LicenseXpress.formatDate(tests.theory.date)}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div style="
                            background: rgba(255, 255, 255, 0.08);
                            border: 1px solid rgba(255, 255, 255, 0.15);
                            border-radius: 15px;
                            padding: 20px;
                            transition: all 0.3s ease;
                            cursor: pointer;
                        " onmouseover="this.style.transform='translateY(-5px)'; this.style.background='rgba(255, 255, 255, 0.12)'" onmouseout="this.style.transform='translateY(0)'; this.style.background='rgba(255, 255, 255, 0.08)'">
                            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                <div style="
                                    background: linear-gradient(135deg, #10B981, #34D399);
                                    border-radius: 10px;
                                    width: 40px;
                                    height: 40px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    font-size: 18px;
                                    margin-right: 15px;
                                ">🕐</div>
                                <div>
                                    <div style="color: #94B8C4; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Test Time</div>
                                    <div style="color: #FFFFFF; font-size: 18px; font-weight: 700;">${tests.theory.time}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div style="
                            background: rgba(255, 255, 255, 0.08);
                            border: 1px solid rgba(255, 255, 255, 0.15);
                            border-radius: 15px;
                            padding: 20px;
                            transition: all 0.3s ease;
                            cursor: pointer;
                        " onmouseover="this.style.transform='translateY(-5px)'; this.style.background='rgba(255, 255, 255, 0.12)'" onmouseout="this.style.transform='translateY(0)'; this.style.background='rgba(255, 255, 255, 0.08)'">
                            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                <div style="
                                    background: linear-gradient(135deg, #8B5CF6, #A78BFA);
                                    border-radius: 10px;
                                    width: 40px;
                                    height: 40px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    font-size: 18px;
                                    margin-right: 15px;
                                ">⏱️</div>
                                <div>
                                    <div style="color: #94B8C4; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Duration</div>
                                    <div style="color: #FFFFFF; font-size: 18px; font-weight: 700;">60 minutes</div>
                                </div>
                            </div>
                        </div>
                        
                        <div style="
                            background: rgba(255, 255, 255, 0.08);
                            border: 1px solid rgba(255, 255, 255, 0.15);
                            border-radius: 15px;
                            padding: 20px;
                            transition: all 0.3s ease;
                            cursor: pointer;
                        " onmouseover="this.style.transform='translateY(-5px)'; this.style.background='rgba(255, 255, 255, 0.12)'" onmouseout="this.style.transform='translateY(0)'; this.style.background='rgba(255, 255, 255, 0.08)'">
                            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                <div style="
                                    background: linear-gradient(135deg, #F59E0B, #FBBF24);
                                    border-radius: 10px;
                                    width: 40px;
                                    height: 40px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    font-size: 18px;
                                    margin-right: 15px;
                                ">💻</div>
                                <div>
                                    <div style="color: #94B8C4; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Format</div>
                                    <div style="color: #FFFFFF; font-size: 18px; font-weight: 700;">Online Examination</div>
                                </div>
                            </div>
                        </div>
                        
                        <div style="
                            background: rgba(255, 255, 255, 0.08);
                            border: 1px solid rgba(255, 255, 255, 0.15);
                            border-radius: 15px;
                            padding: 20px;
                            transition: all 0.3s ease;
                            cursor: pointer;
                        " onmouseover="this.style.transform='translateY(-5px)'; this.style.background='rgba(255, 255, 255, 0.12)'" onmouseout="this.style.transform='translateY(0)'; this.style.background='rgba(255, 255, 255, 0.08)'">
                            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                <div style="
                                    background: linear-gradient(135deg, #EF4444, #F87171);
                                    border-radius: 10px;
                                    width: 40px;
                                    height: 40px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    font-size: 18px;
                                    margin-right: 15px;
                                ">❓</div>
                                <div>
                                    <div style="color: #94B8C4; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Questions</div>
                                    <div style="color: #FFFFFF; font-size: 18px; font-weight: 700;">50 multiple choice</div>
                                </div>
                            </div>
                        </div>
                        
                        <div style="
                            background: rgba(255, 255, 255, 0.08);
                            border: 1px solid rgba(255, 255, 255, 0.15);
                            border-radius: 15px;
                            padding: 20px;
                            transition: all 0.3s ease;
                            cursor: pointer;
                        " onmouseover="this.style.transform='translateY(-5px)'; this.style.background='rgba(255, 255, 255, 0.12)'" onmouseout="this.style.transform='translateY(0)'; this.style.background='rgba(255, 255, 255, 0.08)'">
                            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                <div style="
                                    background: linear-gradient(135deg, #059669, #10B981);
                                    border-radius: 10px;
                                    width: 40px;
                                    height: 40px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    font-size: 18px;
                                    margin-right: 15px;
                                ">🎯</div>
                                <div>
                                    <div style="color: #94B8C4; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Pass Mark</div>
                                    <div style="color: #FFFFFF; font-size: 18px; font-weight: 700;">40/50 (80%)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Requirements Section -->
                    <div style="
                        background: rgba(16, 185, 129, 0.1);
                        border: 2px solid rgba(16, 185, 129, 0.3);
                        border-radius: 15px;
                        padding: 25px;
                        margin-bottom: 25px;
                    ">
                        <div style="margin-bottom: 20px;">
                            <h3 style="
                                color: #10B981;
                                font-size: 22px;
                                font-weight: 700;
                                margin: 0 0 8px 0;
                                display: flex;
                                align-items: center;
                            ">📋 What You Need</h3>
                            <p style="color: #94B8C4; font-size: 16px; margin: 0;">Essential requirements for your exam</p>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 15px;">
                            <div style="
                                background: rgba(16, 185, 129, 0.15);
                                border: 1px solid rgba(16, 185, 129, 0.3);
                                border-radius: 12px;
                                padding: 15px;
                                display: flex;
                                align-items: center;
                                transition: all 0.3s ease;
                            " onmouseover="this.style.background='rgba(16, 185, 129, 0.25)'" onmouseout="this.style.background='rgba(16, 185, 129, 0.15)'">
                                <div style="font-size: 20px; margin-right: 12px;">🌐</div>
                                <span style="color: #FFFFFF; font-weight: 600;">Stable internet connection</span>
                            </div>
                            <div style="
                                background: rgba(16, 185, 129, 0.15);
                                border: 1px solid rgba(16, 185, 129, 0.3);
                                border-radius: 12px;
                                padding: 15px;
                                display: flex;
                                align-items: center;
                                transition: all 0.3s ease;
                            " onmouseover="this.style.background='rgba(16, 185, 129, 0.25)'" onmouseout="this.style.background='rgba(16, 185, 129, 0.15)'">
                                <div style="font-size: 20px; margin-right: 12px;">🔇</div>
                                <span style="color: #FFFFFF; font-weight: 600;">Quiet environment</span>
                            </div>
                            <div style="
                                background: rgba(16, 185, 129, 0.15);
                                border: 1px solid rgba(16, 185, 129, 0.3);
                                border-radius: 12px;
                                padding: 15px;
                                display: flex;
                                align-items: center;
                                transition: all 0.3s ease;
                            " onmouseover="this.style.background='rgba(16, 185, 129, 0.25)'" onmouseout="this.style.background='rgba(16, 185, 129, 0.15)'">
                                <div style="font-size: 20px; margin-right: 12px;">📹</div>
                                <span style="color: #FFFFFF; font-weight: 600;">Webcam access (monitoring)</span>
                            </div>
                            <div style="
                                background: rgba(16, 185, 129, 0.15);
                                border: 1px solid rgba(16, 185, 129, 0.3);
                                border-radius: 12px;
                                padding: 15px;
                                display: flex;
                                align-items: center;
                                transition: all 0.3s ease;
                            " onmouseover="this.style.background='rgba(16, 185, 129, 0.25)'" onmouseout="this.style.background='rgba(16, 185, 129, 0.15)'">
                                <div style="font-size: 20px; margin-right: 12px;">💻</div>
                                <span style="color: #FFFFFF; font-weight: 600;">Computer/tablet device</span>
                            </div>
                            <div style="
                                background: rgba(16, 185, 129, 0.15);
                                border: 1px solid rgba(16, 185, 129, 0.3);
                                border-radius: 12px;
                                padding: 15px;
                                display: flex;
                                align-items: center;
                                transition: all 0.3s ease;
                            " onmouseover="this.style.background='rgba(16, 185, 129, 0.25)'" onmouseout="this.style.background='rgba(16, 185, 129, 0.15)'">
                                <div style="font-size: 20px; margin-right: 12px;">🆔</div>
                                <span style="color: #FFFFFF; font-weight: 600;">Government-issued ID</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Rules Section -->
                    <div style="
                        background: rgba(245, 158, 11, 0.1);
                        border: 2px solid rgba(245, 158, 11, 0.3);
                        border-radius: 15px;
                        padding: 25px;
                        margin-bottom: 25px;
                    ">
                        <div style="margin-bottom: 20px;">
                            <h3 style="
                                color: #F59E0B;
                                font-size: 22px;
                                font-weight: 700;
                                margin: 0 0 8px 0;
                                display: flex;
                                align-items: center;
                            ">⚠️ Important Rules</h3>
                            <p style="color: #94B8C4; font-size: 16px; margin: 0;">Follow these rules to avoid exam termination</p>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            <div style="
                                background: rgba(245, 158, 11, 0.15);
                                border: 1px solid rgba(245, 158, 11, 0.3);
                                border-radius: 12px;
                                padding: 15px;
                                display: flex;
                                align-items: center;
                                transition: all 0.3s ease;
                            " onmouseover="this.style.background='rgba(245, 158, 11, 0.25)'" onmouseout="this.style.background='rgba(245, 158, 11, 0.15)'">
                                <div style="font-size: 18px; margin-right: 12px;">🚫</div>
                                <span style="color: #FFFFFF; font-weight: 600;">No switching tabs or windows</span>
                            </div>
                            <div style="
                                background: rgba(245, 158, 11, 0.15);
                                border: 1px solid rgba(245, 158, 11, 0.3);
                                border-radius: 12px;
                                padding: 15px;
                                display: flex;
                                align-items: center;
                                transition: all 0.3s ease;
                            " onmouseover="this.style.background='rgba(245, 158, 11, 0.25)'" onmouseout="this.style.background='rgba(245, 158, 11, 0.15)'">
                                <div style="font-size: 18px; margin-right: 12px;">🚫</div>
                                <span style="color: #FFFFFF; font-weight: 600;">No right-clicking or shortcuts</span>
                            </div>
                            <div style="
                                background: rgba(245, 158, 11, 0.15);
                                border: 1px solid rgba(245, 158, 11, 0.3);
                                border-radius: 12px;
                                padding: 15px;
                                display: flex;
                                align-items: center;
                                transition: all 0.3s ease;
                            " onmouseover="this.style.background='rgba(245, 158, 11, 0.25)'" onmouseout="this.style.background='rgba(245, 158, 11, 0.15)'">
                                <div style="font-size: 18px; margin-right: 12px;">🚫</div>
                                <span style="color: #FFFFFF; font-weight: 600;">No screenshots or recording</span>
                            </div>
                            <div style="
                                background: rgba(245, 158, 11, 0.15);
                                border: 1px solid rgba(245, 158, 11, 0.3);
                                border-radius: 12px;
                                padding: 15px;
                                display: flex;
                                align-items: center;
                                transition: all 0.3s ease;
                            " onmouseover="this.style.background='rgba(245, 158, 11, 0.25)'" onmouseout="this.style.background='rgba(245, 158, 11, 0.15)'">
                                <div style="font-size: 18px; margin-right: 12px;">👁️</div>
                                <span style="color: #FFFFFF; font-weight: 600;">Camera monitoring is active</span>
                            </div>
                            <div style="
                                background: rgba(245, 158, 11, 0.15);
                                border: 1px solid rgba(245, 158, 11, 0.3);
                                border-radius: 12px;
                                padding: 15px;
                                display: flex;
                                align-items: center;
                                transition: all 0.3s ease;
                            " onmouseover="this.style.background='rgba(245, 158, 11, 0.25)'" onmouseout="this.style.background='rgba(245, 158, 11, 0.15)'">
                                <div style="font-size: 18px; margin-right: 12px;">⚡</div>
                                <span style="color: #FFFFFF; font-weight: 600;">Violations will terminate exam</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Test Link Section -->
                    <div style="
                        background: linear-gradient(135deg, rgba(0, 95, 115, 0.2) 0%, rgba(10, 147, 150, 0.2) 100%);
                        border: 2px solid rgba(0, 95, 115, 0.4);
                        border-radius: 15px;
                        padding: 25px;
                        text-align: center;
                    ">
                        <div style="margin-bottom: 20px;">
                            <h3 style="
                                color: #0A9396;
                                font-size: 22px;
                                font-weight: 700;
                                margin: 0 0 8px 0;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            ">🔗 Your Test Link</h3>
                            <p style="color: #94B8C4; font-size: 16px; margin: 0;">Access your exam 1 hour before scheduled time</p>
                        </div>
                        <div style="
                            background: rgba(255, 255, 255, 0.1);
                            border: 1px solid rgba(255, 255, 255, 0.2);
                            border-radius: 12px;
                            padding: 20px;
                            margin-bottom: 20px;
                        ">
                            <div style="color: #94B8C4; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Exam Link</div>
                            <div style="color: #FFFFFF; font-size: 16px; font-family: 'Courier New', monospace; word-break: break-all;">${tests.theory.testLink || 'Will be provided 1 hour before exam'}</div>
                        </div>
                        ${tests.theory.testLink ? `
                            <button onclick="window.open('${tests.theory.testLink}', '_blank')" style="
                                background: linear-gradient(135deg, #005F73, #0A9396);
                                color: white;
                                border: none;
                                border-radius: 12px;
                                padding: 15px 30px;
                                font-size: 16px;
                                font-weight: 700;
                                cursor: pointer;
                                transition: all 0.3s ease;
                                box-shadow: 0 5px 15px rgba(0, 95, 115, 0.3);
                                display: inline-flex;
                                align-items: center;
                                gap: 10px;
                            " onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(0, 95, 115, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 15px rgba(0, 95, 115, 0.3)'">
                                🚀 Start Exam
                            </button>
                        ` : `
                            <button disabled style="
                                background: rgba(255, 255, 255, 0.1);
                                color: #94B8C4;
                                border: 1px solid rgba(255, 255, 255, 0.2);
                                border-radius: 12px;
                                padding: 15px 30px;
                                font-size: 16px;
                                font-weight: 700;
                                cursor: not-allowed;
                                opacity: 0.6;
                                display: inline-flex;
                                align-items: center;
                                gap: 10px;
                            ">
                                ⏰ Link Available Soon
                            </button>
                        `}
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
            const fullName = rawFullName.replace(/\s+/g, ' ').trim(); 
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
                            <button class="btn btn-primary" onclick="handleAction('view-practical-details')">
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
                        </div>
                    </div>
                    
                    <div class="success-message">
                        <div class="success-icon">🚗</div>
                        <p><strong>Welcome to the road! Drive safely and responsibly.</strong></p>
                        <p class="reminder">Remember to always carry your physical license when driving.</p>
                    </div>
                </div>
            `;
        }
    </script>

</body>
</html>
