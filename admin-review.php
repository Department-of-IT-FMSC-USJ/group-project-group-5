<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Applications - LicenseXpress Admin</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/admin-review.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    //test
    <header class="admin-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <div class="logo-icon">LX</div>
                    <span class="logo-text">LicenseXpress Admin</span>
                </div>
                <nav class="admin-nav">
                    <a href="admin-dashboard.php" class="nav-link">Dashboard</a>
                    <a href="admin-review.php" class="nav-link active">Review Applications</a>
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
            <span class="breadcrumb-current">Review Applications</span>
        </div>
    </div>

    
    <main class="admin-main">
        <div class="container">
            
            <div class="page-header">
                <h1 class="page-title">Review Applications</h1>
                <p class="page-subtitle">Review and verify user applications and documents</p>
            </div>

            
            <div class="filters-section glass-card">
                <div class="filters-header">
                    <h3>🔍 Filters & Search</h3>
                    <button class="btn btn-secondary btn-sm" id="clearFilters">Clear All</button>
                </div>
                <div class="filters-content">
                    <div class="filter-group">
                        <label class="filter-label">Status</label>
                        <select class="filter-select" id="statusFilter">
                            <option value="">All Statuses</option>
                            <option value="pending_verification">Pending Verification</option>
                            <option value="verified">Verified</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Date Range</label>
                        <select class="filter-select" id="dateFilter">
                            <option value="">All Dates</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Search</label>
                        <input type="text" class="filter-input" id="searchInput" placeholder="Search by name, NIC, or email...">
                    </div>
                </div>
            </div>

            
            <div class="applications-section">
                <div class="section-header">
                    <h2>📋 Applications</h2>
                    <div class="section-actions">
                        <span class="results-count" id="resultsCount">0 applications found</span>
                        <button class="btn btn-primary btn-sm" id="refreshApplications">Refresh</button>
                    </div>
                </div>

                <div class="applications-list" id="applicationsList">
                    
                </div>

                
                <div class="pagination" id="pagination">
                    
                </div>
            </div>
        </div>
    </main>

    
    <div class="review-modal hidden" id="reviewModal">
        <div class="modal-backdrop"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Review Application</h3>
                <button class="modal-close" id="closeModal">×</button>
            </div>
            <div class="modal-body">
        
                <div class="application-details">
                    <div class="detail-section">
                        <h4>👤 Personal Information</h4>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="detail-label">Full Name:</span>
                                <span class="detail-value" id="reviewFullName">John Doe</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">NIC Number:</span>
                                <span class="detail-value" id="reviewNIC">200012345678</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Date of Birth:</span>
                                <span class="detail-value" id="reviewDOB">January 23, 2000</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Gender:</span>
                                <span class="detail-value" id="reviewGender">Male</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Transmission:</span>
                                <span class="detail-value" id="reviewTransmission">Manual</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">District:</span>
                                <span class="detail-value" id="reviewDistrict">Colombo</span>
                            </div>
                        </div>
                    </div>

                    <div class="detail-section">
                        <h4>📁 Uploaded Documents</h4>
                        <div class="documents-grid">
                            <div class="document-item">
                                <div class="document-preview" id="birthCertPreview">
                                    <div class="preview-placeholder">📄</div>
                                    <div class="document-name">Birth Certificate</div>
                                </div>
                                <div class="document-actions">
                                    <button class="btn btn-secondary btn-sm" onclick="viewDocument('birthCertificate')">View</button>
                                    <div class="document-status approved">✓ Approved</div>
                                </div>
                            </div>
                            <div class="document-item">
                                <div class="document-preview" id="nicPreview">
                                    <div class="preview-placeholder">🪪</div>
                                    <div class="document-name">NIC Copy</div>
                                </div>
                                <div class="document-actions">
                                    <button class="btn btn-secondary btn-sm" onclick="viewDocument('nicCopy')">View</button>
                                    <div class="document-status pending">⏳ Pending</div>
                                </div>
                            </div>
                            <div class="document-item">
                                <div class="document-preview" id="medicalPreview">
                                    <div class="preview-placeholder">🏥</div>
                                    <div class="document-name">Medical Certificate</div>
                                </div>
                                <div class="document-actions">
                                    <button class="btn btn-secondary btn-sm" onclick="viewDocument('medicalCertificate')">View</button>
                                    <div class="document-status approved">✓ Approved</div>
                                </div>
                            </div>
                            <div class="document-item">
                                <div class="document-preview" id="photoPreview">
                                    <div class="preview-placeholder">📸</div>
                                    <div class="document-name">Passport Photo</div>
                                </div>
                                <div class="document-actions">
                                    <button class="btn btn-secondary btn-sm" onclick="viewDocument('photo')">View</button>
                                    <div class="document-status rejected">❌ Rejected</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="detail-section">
                        <h4>💳 Payment Information</h4>
                        <div class="payment-details">
                            <div class="payment-item">
                                <span class="payment-label">Amount:</span>
                                <span class="payment-value">Rs. 3,200.00</span>
                            </div>
                            <div class="payment-item">
                                <span class="payment-label">Method:</span>
                                <span class="payment-value">Credit Card</span>
                            </div>
                            <div class="payment-item">
                                <span class="payment-label">Status:</span>
                                <span class="payment-value success">✓ Paid</span>
                            </div>
                            <div class="payment-item">
                                <span class="payment-label">Transaction ID:</span>
                                <span class="payment-value">TXN-2025-001234</span>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="review-actions">
                    <div class="action-section">
                        <h4>📝 Review Decision</h4>
                        <div class="decision-options">
                            <div class="decision-option">
                                <input type="radio" name="decision" value="approve" id="approveDecision">
                                <label for="approveDecision" class="decision-label approve">
                                    <span class="decision-icon">✅</span>
                                    <span class="decision-text">Approve Application</span>
                                </label>
                            </div>
                            <div class="decision-option">
                                <input type="radio" name="decision" value="reject" id="rejectDecision">
                                <label for="rejectDecision" class="decision-label reject">
                                    <span class="decision-icon">❌</span>
                                    <span class="decision-text">Reject Application</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="action-section">
                        <h4>📝 Comments</h4>
                        <textarea class="review-comments" id="reviewComments" placeholder="Add your review comments here..."></textarea>
                    </div>

                    <div class="action-section">
                        <h4>⚠️ Rejection Reason (if applicable)</h4>
                        <div class="rejection-reasons">
                            <div class="reason-option">
                                <input type="checkbox" id="reason1" value="blurry_documents">
                                <label for="reason1">Documents are blurry or unreadable</label>
                            </div>
                            <div class="reason-option">
                                <input type="checkbox" id="reason2" value="invalid_format">
                                <label for="reason2">Invalid document format</label>
                            </div>
                            <div class="reason-option">
                                <input type="checkbox" id="reason3" value="missing_documents">
                                <label for="reason3">Missing required documents</label>
                            </div>
                            <div class="reason-option">
                                <input type="checkbox" id="reason4" value="expired_documents">
                                <label for="reason4">Documents are expired</label>
                            </div>
                            <div class="reason-option">
                                <input type="checkbox" id="reason5" value="other">
                                <label for="reason5">Other (specify in comments)</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="cancelReview">Cancel</button>
                <button class="btn btn-primary" id="submitReview">Submit Review</button>
            </div>
        </div>
    </div>

    
    <div class="document-modal hidden" id="documentModal">
        <div class="modal-backdrop"></div>
        <div class="modal-content document-viewer">
            <div class="modal-header">
                <h3 class="modal-title" id="documentTitle">Document Viewer</h3>
                <button class="modal-close" id="closeDocumentModal">×</button>
            </div>
            <div class="modal-body">
                <div class="document-viewer-content">
                    <div class="document-image" id="documentImage">
                        <div class="image-placeholder">
                            <div class="placeholder-icon">📄</div>
                            <div class="placeholder-text">Document Preview</div>
                        </div>
                    </div>
                    <div class="document-controls">
                        <button class="btn btn-secondary" id="zoomIn">🔍+</button>
                        <button class="btn btn-secondary" id="zoomOut">🔍-</button>
                        <button class="btn btn-secondary" id="rotateDocument">↻</button>
                        <button class="btn btn-primary" id="downloadDocument">⬇️ Download</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/app.js"></script>
    <script src="assets/js/admin-review.js"></script>
</body>
</html>
