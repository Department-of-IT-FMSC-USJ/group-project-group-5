
document.addEventListener('DOMContentLoaded', function() {
    
    if (!LicenseXpress.checkAdminAuth()) {
        window.location.href = 'admin-login.php';
        return;
    }

    
    initializeAdminDashboard();
});

function initializeAdminDashboard() {
    const currentAdmin = LicenseXpress.getCurrentAdmin();
    
    
    updateAdminInfo(currentAdmin);

    
    loadDashboardData();

    
    initializeNavigation();
}

function updateAdminInfo(admin) {
    const adminName = document.getElementById('adminName');
    
    if (admin) {
        adminName.textContent = admin.username || 'Admin User';
    }
}

function loadDashboardData() {
   
    loadApplicationsData();
    
   
    loadSystemStats();
    
    
    loadActivityLog();
}

function loadApplicationsData() {
    
    const applications = getAllApplications();
    

    updateStats(applications);
    

    loadRecentApplications(applications);
    

    loadPendingReviews(applications);
}

function getAllApplications() {
    
    const sampleApplications = [
        {
            id: 'LX-2025-001234',
            fullName: 'John Doe',
            nic: '200012345678',
            email: 'john@example.com',
            phone: '+94771234567',
            status: 'pending_verification',
            submittedDate: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000).toISOString(),
            documents: {
                birthCertificate: { uploaded: true, fileName: 'birth_cert.pdf' },
                nicCopy: { uploaded: true, fileName: 'nic_copy.pdf' },
                medicalCertificate: { uploaded: true, fileName: 'medical_cert.pdf' },
                photo: { uploaded: true, fileName: 'photo.jpg' }
            }
        },
        {
            id: 'LX-2025-001235',
            fullName: 'Jane Smith',
            nic: '199512345678',
            email: 'jane@example.com',
            phone: '+94771234568',
            status: 'verified',
            submittedDate: new Date(Date.now() - 5 * 24 * 60 * 60 * 1000).toISOString(),
            verifiedDate: new Date(Date.now() - 1 * 24 * 60 * 60 * 1000).toISOString()
        },
        {
            id: 'LX-2025-001236',
            fullName: 'Bob Johnson',
            nic: '200112345678',
            email: 'bob@example.com',
            phone: '+94771234569',
            status: 'rejected',
            submittedDate: new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString(),
            rejectedDate: new Date(Date.now() - 3 * 24 * 60 * 60 * 1000).toISOString(),
            rejectionReason: 'NIC copy is blurry and unreadable'
        }
    ];

    return sampleApplications;
}

function updateStats(applications) {
    const totalApplications = applications.length;
    const pendingApplications = applications.filter(app => app.status === 'pending_verification').length;
    const approvedToday = applications.filter(app => 
        app.status === 'verified' && 
        new Date(app.verifiedDate).toDateString() === new Date().toDateString()
    ).length;
    const completedTests = applications.filter(app => 
        app.status === 'theory_passed' || app.status === 'practical_scheduled' || app.status === 'license_issued'
    ).length;

    document.getElementById('totalApplications').textContent = totalApplications;
    document.getElementById('pendingApplications').textContent = pendingApplications;
    document.getElementById('approvedApplications').textContent = approvedToday;
    document.getElementById('completedTests').textContent = completedTests;
}

function loadRecentApplications(applications) {
    const recentApplications = document.getElementById('recentApplications');
    
    
    const sortedApplications = applications.sort((a, b) => 
        new Date(b.submittedDate) - new Date(a.submittedDate)
    ).slice(0, 5);

    recentApplications.innerHTML = sortedApplications.map(app => {
        const statusClass = getStatusClass(app.status);
        const statusText = getStatusText(app.status);
        const initial = app.fullName.charAt(0).toUpperCase();
        
        return `
            <div class="application-item">
                <div class="application-avatar">${initial}</div>
                <div class="application-info">
                    <div class="application-name">${app.fullName}</div>
                    <div class="application-nic">${LicenseXpress.formatNIC(app.nic)}</div>
                    <div class="application-date">${LicenseXpress.formatDateTime(app.submittedDate)}</div>
                </div>
                <div class="application-status ${statusClass}">${statusText}</div>
            </div>
        `;
    }).join('');
}

function loadPendingReviews(applications) {
    const pendingReviews = document.getElementById('pendingReviews');
    const pendingCount = document.getElementById('pendingCount');
    
    const pendingApplications = applications.filter(app => app.status === 'pending_verification');
    
    pendingCount.textContent = pendingApplications.length;

    pendingReviews.innerHTML = pendingApplications.map(app => {
        const initial = app.fullName.charAt(0).toUpperCase();
        
        return `
            <div class="pending-item">
                <div class="pending-avatar">${initial}</div>
                <div class="pending-info">
                    <div class="pending-name">${app.fullName}</div>
                    <div class="pending-nic">${LicenseXpress.formatNIC(app.nic)}</div>
                    <div class="pending-date">Submitted ${LicenseXpress.formatDateTime(app.submittedDate)}</div>
                </div>
                <div class="pending-actions">
                    <button class="btn btn-primary btn-sm" onclick="reviewApplication('${app.id}')">Review</button>
                </div>
            </div>
        `;
    }).join('');
}

function loadSystemStats() {
    
    const activeUsers = Math.floor(Math.random() * 50) + 20;
    document.getElementById('activeUsers').textContent = activeUsers;
}

function loadActivityLog() {
    const activityLog = document.getElementById('activityLog');
    const activities = JSON.parse(localStorage.getItem('adminActivityLog') || '[]');
    
    
    const sortedActivities = activities.sort((a, b) => 
        new Date(b.timestamp) - new Date(a.timestamp)
    ).slice(0, 10);

    activityLog.innerHTML = sortedActivities.map(activity => {
        const icon = getActivityIcon(activity.action);
        const text = getActivityText(activity);
        const time = LicenseXpress.formatDateTime(activity.timestamp);
        
        return `
            <div class="activity-item">
                <div class="activity-icon">${icon}</div>
                <div class="activity-content">
                    <div class="activity-text">${text}</div>
                    <div class="activity-time">${time}</div>
                </div>
            </div>
        `;
    }).join('');
}

function getStatusClass(status) {
    const statusClasses = {
        'pending_verification': 'pending',
        'verified': 'approved',
        'rejected': 'rejected',
        'theory_scheduled': 'pending',
        'theory_passed': 'approved',
        'practical_scheduled': 'pending',
        'license_issued': 'approved'
    };
    return statusClasses[status] || 'pending';
}

function getStatusText(status) {
    const statusTexts = {
        'pending_verification': 'Pending Review',
        'verified': 'Approved',
        'rejected': 'Rejected',
        'theory_scheduled': 'Test Scheduled',
        'theory_passed': 'Theory Passed',
        'practical_scheduled': 'Practical Scheduled',
        'license_issued': 'License Issued'
    };
    return statusTexts[status] || 'Unknown';
}

function getActivityIcon(action) {
    const icons = {
        'login': '🔐',
        'logout': '🚪',
        'failed_login_attempt': '❌',
        'review_application': '📋',
        'approve_application': '✅',
        'reject_application': '❌',
        'generate_report': '📊',
        'manage_users': '👥'
    };
    return icons[action] || '📝';
}

function getActivityText(activity) {
    const texts = {
        'login': `${activity.username} logged in`,
        'logout': `${activity.username} logged out`,
        'failed_login_attempt': 'Failed login attempt detected',
        'review_application': `${activity.username} reviewed an application`,
        'approve_application': `${activity.username} approved an application`,
        'reject_application': `${activity.username} rejected an application`,
        'generate_report': `${activity.username} generated a report`,
        'manage_users': `${activity.username} managed users`
    };
    return texts[activity.action] || 'Unknown activity';
}

function initializeNavigation() {
    
    const logoutBtn = document.getElementById('logoutBtn');
    logoutBtn.addEventListener('click', function() {
        LicenseXpress.adminLogout();
    });

    
    document.getElementById('reviewApplications').addEventListener('click', function() {
        window.location.href = 'admin-review.php';
    });

    document.getElementById('generateReport').addEventListener('click', function() {
        generateReport();
    });

    document.getElementById('manageUsers').addEventListener('click', function() {
        window.location.href = 'admin-users.php';
    });

    document.getElementById('systemSettings').addEventListener('click', function() {
        window.location.href = 'admin-settings.php';
    });

    
    document.getElementById('refreshActivity').addEventListener('click', function() {
        loadActivityLog();
        LicenseXpress.showToast('Activity log refreshed', 'success');
    });
}

function reviewApplication(applicationId) {
    window.location.href = `admin-review.php?id=${applicationId}`;
}

function generateReport() {
    
    LicenseXpress.showToast('Generating report...', 'info');
    
    setTimeout(() => {
        LicenseXpress.showToast('Report generated successfully!', 'success');
        
        
        const currentAdmin = LicenseXpress.getCurrentAdmin();
        logAdminActivity('generate_report', currentAdmin.username);
    }, 2000);
}

function logAdminActivity(action, username) {
    const activityLog = JSON.parse(localStorage.getItem('adminActivityLog') || '[]');
    
    activityLog.push({
        action: action,
        username: username,
        timestamp: new Date().toISOString(),
        ip: '127.0.0.1',
        userAgent: navigator.userAgent
    });

    
    if (activityLog.length > 100) {
        activityLog.splice(0, activityLog.length - 100);
    }

    localStorage.setItem('adminActivityLog', JSON.stringify(activityLog));
}


setInterval(() => {
    loadDashboardData();
}, 30000);
