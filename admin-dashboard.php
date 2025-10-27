<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LicenseXpress</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/admin-dashboard.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    
    <header class="admin-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <div class="logo-icon">LX</div>
                    <span class="logo-text">LicenseXpress Admin</span>
                </div>
                <nav class="admin-nav">
                    <a href="admin-dashboard.php" class="nav-link active">Dashboard</a>
                    <a href="admin-review.php" class="nav-link">Review Applications</a>
                    <a href="admin-users.php" class="nav-link">User Management</a>
                    <a href="admin-reports.php" class="nav-link">Reports</a>
                </nav>
                <div class="header-actions">
                    <div class="admin-info">
                        <div class="admin-avatar">👨‍💼</div>
                        <div class="admin-details">
                            <div class="admin-name" id="adminName">Admin User</div>
                            <div class="admin-role">System Administrator</div>
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
            <span class="breadcrumb-item">Admin Dashboard</span>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">Overview</span>
        </div>
    </div>

    
    <main class="admin-main">
        <div class="container">
            
            <div class="dashboard-header">
                <h1 class="dashboard-title">Admin Dashboard</h1>
                <p class="dashboard-subtitle">Monitor and manage LicenseXpress applications</p>
            </div>

            
            <div class="stats-overview">
                <div class="stat-card glass-card">
                    <div class="stat-icon">📋</div>
                    <div class="stat-content">
                        <div class="stat-value" id="totalApplications">0</div>
                        <div class="stat-label">Total Applications</div>
                        <div class="stat-change positive">+12 this week</div>
                    </div>
                </div>
                <div class="stat-card glass-card">
                    <div class="stat-icon">⏳</div>
                    <div class="stat-content">
                        <div class="stat-value" id="pendingApplications">0</div>
                        <div class="stat-label">Pending Review</div>
                        <div class="stat-change warning">+3 today</div>
                    </div>
                </div>
                <div class="stat-card glass-card">
                    <div class="stat-icon">✅</div>
                    <div class="stat-content">
                        <div class="stat-value" id="approvedApplications">0</div>
                        <div class="stat-label">Approved Today</div>
                        <div class="stat-change positive">+8 today</div>
                    </div>
                </div>
                <div class="stat-card glass-card">
                    <div class="stat-icon">🎓</div>
                    <div class="stat-content">
                        <div class="stat-value" id="completedTests">0</div>
                        <div class="stat-label">Tests Completed</div>
                        <div class="stat-change positive">+15 this week</div>
                    </div>
                </div>
            </div>

            
            <div class="dashboard-grid">
                
                <div class="dashboard-card glass-card">
                    <div class="card-header">
                        <h3>📋 Recent Applications</h3>
                        <a href="admin-review.php" class="view-all-link">View All →</a>
                    </div>
                    <div class="card-content">
                        <div class="applications-list" id="recentApplications">
                            
                        </div>
                    </div>
                </div>

                
                <div class="dashboard-card glass-card">
                    <div class="card-header">
                        <h3>⏳ Pending Reviews</h3>
                        <span class="badge" id="pendingCount">0</span>
                    </div>
                    <div class="card-content">
                        <div class="pending-list" id="pendingReviews">
                            
                        </div>
                    </div>
                </div>

                
                <div class="dashboard-card glass-card">
                    <div class="card-header">
                        <h3>⚡ Quick Actions</h3>
                    </div>
                    <div class="card-content">
                        <div class="quick-actions">
                            <button class="action-btn" id="reviewApplications">
                                <span class="action-icon">📋</span>
                                <span class="action-text">Review Applications</span>
                            </button>
                            <button class="action-btn" id="generateReport">
                                <span class="action-icon">📊</span>
                                <span class="action-text">Generate Report</span>
                            </button>
                            <button class="action-btn" id="manageUsers">
                                <span class="action-icon">👥</span>
                                <span class="action-text">Manage Users</span>
                            </button>
                            <button class="action-btn" id="systemSettings">
                                <span class="action-icon">⚙️</span>
                                <span class="action-text">System Settings</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="dashboard-card glass-card full-width">
                <div class="card-header">
                    <h3>📝 Recent Activity</h3>
                    <button class="btn btn-secondary btn-sm" id="refreshActivity">Refresh</button>
                </div>
                <div class="card-content">
                    <div class="activity-log" id="activityLog">
                        
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="assets/js/app.js"></script>
    <script src="assets/js/admin-dashboard.js"></script>
</body>
</html>
