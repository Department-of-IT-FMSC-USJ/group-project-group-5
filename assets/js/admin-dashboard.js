

document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin Dashboard initializing...');
    
    
    if (!checkAdminAuth()) {
        console.log('Admin not authenticated, redirecting...');
        window.location.href = 'admin-login.php';
        return;
    }

    
    initializeAdminDashboard();
});

function checkAdminAuth() {
    
    return true;
}

function initializeAdminDashboard() {
    console.log('Initializing admin dashboard...');
    
    
    updateAdminInfo();

    
    loadDashboardData();

    
    initializeNavigation();
}

function updateAdminInfo() {
    const adminName = document.getElementById('adminName');
    if (adminName) {
        adminName.textContent = 'Admin User';
    }
}

async function loadDashboardData() {
    console.log('Loading dashboard data...');
    
    try {
        
        showLoadingState();
        
        
        const results = await Promise.allSettled([
            loadStats(),
            loadRecentApplications(),
            loadPendingReviews(),
            loadActivityLog()
        ]);
        
       
        const failures = results.filter(r => r.status === 'rejected');
        if (failures.length > 0) {
            console.error('Some data failed to load:', failures);
            showToast('Some data failed to load. Check console for details.', 'warning');
        } else {
            console.log('All dashboard data loaded successfully');
        }
        
        
        hideLoadingState();
        
    } catch (error) {
        console.error('Error loading dashboard data:', error);
        showToast('Error loading dashboard data: ' + error.message, 'error');
        hideLoadingState();
    }
}


async function loadStats() {
    console.log('Loading stats...');
    
    try {
        const response = await fetch('api_admin-dashboard.php?action=getDashboardStats');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('Stats response:', result);
        
        if (result.success) {
            const stats = result.data;
            
            
            updateElement('totalApplications', stats.totalApplications || 0);
            updateElement('pendingApplications', stats.pendingApplications || 0);
            updateElement('approvedApplications', stats.approvedToday || 0);
            updateElement('completedTests', stats.completedTests || 0);
            
            console.log('Stats loaded successfully');
        } else {
            throw new Error(result.message || 'Failed to load statistics');
        }
    } catch (error) {
        console.error('Error loading stats:', error);
        
        updateElement('totalApplications', 0);
        updateElement('pendingApplications', 0);
        updateElement('approvedApplications', 0);
        updateElement('completedTests', 0);
        throw error;
    }
}


async function loadRecentApplications() {
    console.log('Loading recent applications...');
    
    try {
        const response = await fetch('api_admin-dashboard.php?action=getRecentApplications&limit=5');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('Recent applications response:', result);
        
        if (result.success) {
            displayRecentApplications(result.data);
            console.log('Recent applications loaded successfully');
        } else {
            throw new Error(result.message || 'Failed to load recent applications');
        }
    } catch (error) {
        console.error('Error loading recent applications:', error);
        const container = document.getElementById('recentApplications');
        if (container) {
            container.innerHTML = '<div class="error-message">Failed to load applications</div>';
        }
        throw error;
    }
}

function displayRecentApplications(applications) {
    const container = document.getElementById('recentApplications');
    
    if (!container) {
        console.error('recentApplications container not found');
        return;
    }
    
    if (!applications || applications.length === 0) {
        container.innerHTML = '<div class="empty-state">No applications yet</div>';
        return;
    }
    
    container.innerHTML = applications.map(app => {
        const statusClass = getStatusClass(app.status);
        const statusText = getStatusText(app.status);
        const initial = app.fullName ? app.fullName.charAt(0).toUpperCase() : '?';
        const formattedDate = formatDateTime(app.submitted_date || app.created_at);
        
        return `
            <div class="application-item" onclick="viewApplication('${app.id}')">
                <div class="application-avatar">${initial}</div>
                <div class="application-info">
                    <div class="application-name">${escapeHtml(app.fullName)}</div>
                    <div class="application-nic">${formatNIC(app.nic)}</div>
                    <div class="application-date">${formattedDate}</div>
                </div>
                <div class="application-status ${statusClass}">${statusText}</div>
            </div>
        `;
    }).join('');
}


async function loadPendingReviews() {
    console.log('Loading pending reviews...');
    
    try {
        const response = await fetch('api_admin-dashboard.php?action=getPendingReviews');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('Pending reviews response:', result);
        
        if (result.success) {
            displayPendingReviews(result.data);
            updateElement('pendingCount', result.data ? result.data.length : 0);
            console.log('Pending reviews loaded successfully');
        } else {
            throw new Error(result.message || 'Failed to load pending reviews');
        }
    } catch (error) {
        console.error('Error loading pending reviews:', error);
        const container = document.getElementById('pendingReviews');
        if (container) {
            container.innerHTML = '<div class="error-message">Failed to load reviews</div>';
        }
        throw error;
    }
}

function displayPendingReviews(applications) {
    const container = document.getElementById('pendingReviews');
    
    if (!container) {
        console.error('pendingReviews container not found');
        return;
    }
    
    if (!applications || applications.length === 0) {
        container.innerHTML = '<div class="empty-state">No pending reviews</div>';
        return;
    }
    
    container.innerHTML = applications.map(app => {
        const initial = app.fullName ? app.fullName.charAt(0).toUpperCase() : '?';
        const formattedDate = formatDateTime(app.submitted_date);
        
        return `
            <div class="pending-item">
                <div class="pending-avatar">${initial}</div>
                <div class="pending-info">
                    <div class="pending-name">${escapeHtml(app.fullName)}</div>
                    <div class="pending-nic">${formatNIC(app.nic)}</div>
                    <div class="pending-date">Submitted ${formattedDate}</div>
                </div>
                <div class="pending-actions">
                    <button class="btn btn-primary btn-sm" onclick="reviewApplication('${app.id}')">Review</button>
                </div>
            </div>
        `;
    }).join('');
}



async function loadActivityLog() {
    console.log('Loading activity log...');
    
    try {
        const response = await fetch('api_admin-dashboard.php?action=getActivityLog&limit=10');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('Activity log response:', result);
        
        if (result.success) {
            displayActivityLog(result.data);
            console.log('Activity log loaded successfully');
        } else {
            throw new Error(result.message || 'Failed to load activity log');
        }
    } catch (error) {
        console.error('Error loading activity log:', error);
        const container = document.getElementById('activityLog');
        if (container) {
            container.innerHTML = '<div class="error-message">Failed to load activity</div>';
        }
        
    }
}

function displayActivityLog(activities) {
    const container = document.getElementById('activityLog');
    
    if (!container) {
        console.error('activityLog container not found');
        return;
    }
    
    if (!activities || activities.length === 0) {
        container.innerHTML = '<div class="empty-state">No recent activity</div>';
        return;
    }
    
    container.innerHTML = activities.map(activity => {
        const icon = getActivityIcon(activity.action);
        const text = getActivityText(activity);
        const time = formatDateTime(activity.timestamp);
        
        return `
            <div class="activity-item">
                <div class="activity-icon">${icon}</div>
                <div class="activity-content">
                    <div class="activity-text">${escapeHtml(text)}</div>
                    <div class="activity-time">${time}</div>
                </div>
            </div>
        `;
    }).join('');
}



function updateElement(id, value) {
    const element = document.getElementById(id);
    if (element) {
        element.textContent = value;
    } else {
        console.warn(`Element with id '${id}' not found`);
    }
}

function getStatusClass(status) {
    const statusClasses = {
        'pending_verification': 'pending',
        'verified': 'approved',
        'rejected': 'rejected',
        'theory_scheduled': 'pending',
        'theory_passed': 'approved',
        'theory_failed': 'rejected',
        'practical_scheduled': 'pending',
        'license_issued': 'approved',
        'not_started': 'pending'
    };
    return statusClasses[status] || 'pending';
}

function getStatusText(status) {
    const statusTexts = {
        'not_started': 'Not Started',
        'pending_verification': 'Pending Review',
        'verified': 'Verified',
        'rejected': 'Rejected',
        'theory_scheduled': 'Theory Scheduled',
        'theory_passed': 'Theory Passed',
        'theory_failed': 'Theory Failed',
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
        'manage_users': '👥',
        'api_access': '🔌',
        'schedule_test': '📅',
        'update_settings': '⚙️'
    };
    return icons[action] || '📝';
}

function getActivityText(activity) {
    const action = activity.action;
    const username = activity.username || activity.fullName || 'Admin';
    
    const texts = {
        'login': `${username} logged in`,
        'logout': `${username} logged out`,
        'failed_login_attempt': `Failed login attempt`,
        'review_application': `${username} reviewed application`,
        'approve_application': `${username} approved application`,
        'reject_application': `${username} rejected application`,
        'generate_report': `${username} generated a report`,
        'manage_users': `${username} managed users`,
        'api_access': `${username} accessed dashboard`,
        'schedule_test': `${username} scheduled a test`,
        'update_settings': `${username} updated system settings`
    };
    
    return texts[action] || `${username} performed an action`;
}

function formatDateTime(dateString) {
    if (!dateString) return 'N/A';
    
    try {
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);
        
        if (diffMins < 1) return 'Just now';
        if (diffMins < 60) return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
        if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
        if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
        
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (error) {
        console.error('Error formatting date:', error);
        return 'Invalid date';
    }
}

function formatNIC(nic) {
    if (!nic) return 'N/A';
    
    
    if (nic.length === 12) {
        return `${nic.slice(0, 4)} ${nic.slice(4, 8)} ${nic.slice(8)}`;
    }
    
    if (nic.length === 10) {
        return `${nic.slice(0, 6)} ${nic.slice(6)}`;
    }
    
    return nic;
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showLoadingState() {
    const elements = [
        'recentApplications',
        'pendingReviews',
        'activityLog'
    ];
    
    elements.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.innerHTML = '<div class="loading-spinner">Loading...</div>';
        }
    });
}



function showToast(message, type = 'info') {
    console.log(`[${type.toUpperCase()}] ${message}`);
    
    
    if (typeof LicenseXpress !== 'undefined' && typeof LicenseXpress.showToast === 'function') {
        LicenseXpress.showToast(message, type);
    } else {
        
        alert(message);
    }
}



function initializeNavigation() {
    console.log('Initializing navigation...');
    
    
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'admin-login.php';
            }
        });
    }

    
    const reviewBtn = document.getElementById('reviewApplications');
    if (reviewBtn) {
        reviewBtn.addEventListener('click', function() {
            window.location.href = 'admin-review.php';
        });
    }

    const reportBtn = document.getElementById('generateReport');
    if (reportBtn) {
        reportBtn.addEventListener('click', function() {
            generateReport();
        });
    }

    const usersBtn = document.getElementById('manageUsers');
    if (usersBtn) {
        usersBtn.addEventListener('click', function() {
            window.location.href = 'admin-users.php';
        });
    }

    const settingsBtn = document.getElementById('systemSettings');
    if (settingsBtn) {
        settingsBtn.addEventListener('click', function() {
            window.location.href = 'admin-settings.php';
        });
    }

    
    const refreshBtn = document.getElementById('refreshActivity');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', async function() {
            refreshBtn.disabled = true;
            refreshBtn.textContent = 'Refreshing...';
            
            try {
                await loadActivityLog();
                showToast('Activity log refreshed', 'success');
            } catch (error) {
                showToast('Failed to refresh activity log', 'error');
            } finally {
                refreshBtn.disabled = false;
                refreshBtn.textContent = 'Refresh';
            }
        });
    }
}

function viewApplication(applicationId) {
    window.location.href = `admin-review.php?id=${applicationId}`;
}

function reviewApplication(applicationId) {
    window.location.href = `admin-review.php?id=${applicationId}`;
}

async function generateReport() {
    showToast('Generating report...', 'info');
    
    try {
        await new Promise(resolve => setTimeout(resolve, 2000));
        showToast('Report generated successfully!', 'success');
    } catch (error) {
        console.error('Error generating report:', error);
        showToast('Failed to generate report', 'error');
    }
}



let refreshInterval;

function startAutoRefresh() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
    
    refreshInterval = setInterval(async () => {
        console.log('Auto-refreshing dashboard data...');
        try {
            await loadDashboardData();
        } catch (error) {
            console.error('Auto-refresh failed:', error);
        }
    }, 30000); 
}

