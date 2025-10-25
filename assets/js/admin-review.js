

document.addEventListener('DOMContentLoaded', function() {
    
    if (!LicenseXpress.checkAdminAuth()) {
        window.location.href = 'admin-login.php';
        return;
    }


    initializeAdminReview();
});

function initializeAdminReview() {
    const currentAdmin = LicenseXpress.getCurrentAdmin();
    
    
    updateAdminInfo(currentAdmin);

    
    loadApplications();

    
    initializeFilters();

    
    initializeNavigation();
}

function updateAdminInfo(admin) {
    const adminName = document.getElementById('adminName');
    
    if (admin) {
        adminName.textContent = admin.username || 'Admin User';
    }
}

function loadApplications() {

    const applications = JSON.parse(localStorage.getItem('applications') || '[]');
    
    
    const filteredApplications = filterApplications(applications);
    
    
    updateResultsCount(filteredApplications.length);
    
    
    renderApplications(filteredApplications);
}

function getAllApplications() {
    return JSON.parse(localStorage.getItem('applications') || '[]');
}

function renderApplications(applications) {
    const applicationsList = document.getElementById('applicationsList');
    
    if (applications.length === 0) {
        applicationsList.innerHTML = `
            <div class="no-applications">
                <div class="no-applications-icon">📋</div>
                <h3>No Applications Found</h3>
                <p>No applications match your current filter criteria.</p>
            </div>
        `;
        return;
    }
    
    applicationsList.innerHTML = applications.map(app => `
        <div class="application-card" data-application-id="${app.applicationId}">
            <div class="application-header">
                <div class="application-info">
                    <div class="application-avatar">${app.fullName.charAt(0).toUpperCase()}</div>
                    <div class="application-details">
                        <h3>${app.fullName}</h3>
                        <p class="application-nic">${app.nic}</p>
                        <p class="application-date">Submitted: ${new Date(app.submittedDate).toLocaleDateString()}</p>
                    </div>
                </div>
                <div class="application-status">
                    <span class="status-badge ${app.status}">${getStatusText(app.status)}</span>
                </div>
            </div>
            <div class="application-actions">
                <button class="btn btn-primary btn-sm" onclick="viewApplication('${app.applicationId}')">View Details</button>
                <button class="btn btn-success btn-sm" onclick="approveApplication('${app.applicationId}')">Approve</button>
                <button class="btn btn-danger btn-sm" onclick="rejectApplication('${app.applicationId}')">Reject</button>
            </div>
        </div>
    `).join('');
}

function getStatusText(status) {
    const statusMap = {
        'pending_verification': 'Pending Verification',
        'verified': 'Verified',
        'rejected': 'Rejected'
    };
    return statusMap[status] || status;
}
    renderApplications(filteredApplications);
    

    renderPagination(filteredApplications);



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
                birthCertificate: { uploaded: true, fileName: 'birth_cert.pdf', status: 'approved' },
                nicCopy: { uploaded: true, fileName: 'nic_copy.pdf', status: 'pending' },
                medicalCertificate: { uploaded: true, fileName: 'medical_cert.pdf', status: 'approved' },
                photo: { uploaded: true, fileName: 'photo.jpg', status: 'rejected' }
            },
            payment: {
                amount: 3200,
                method: 'Credit Card',
                status: 'paid',
                transactionId: 'TXN-2025-001234'
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
            verifiedDate: new Date(Date.now() - 1 * 24 * 60 * 60 * 1000).toISOString(),
            documents: {
                birthCertificate: { uploaded: true, fileName: 'birth_cert.pdf', status: 'approved' },
                nicCopy: { uploaded: true, fileName: 'nic_copy.pdf', status: 'approved' },
                medicalCertificate: { uploaded: true, fileName: 'medical_cert.pdf', status: 'approved' },
                photo: { uploaded: true, fileName: 'photo.jpg', status: 'approved' }
            },
            payment: {
                amount: 3200,
                method: 'Bank Transfer',
                status: 'paid',
                transactionId: 'TXN-2025-001235'
            }
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
            rejectionReason: 'NIC copy is blurry and unreadable',
            documents: {
                birthCertificate: { uploaded: true, fileName: 'birth_cert.pdf', status: 'approved' },
                nicCopy: { uploaded: true, fileName: 'nic_copy.pdf', status: 'rejected' },
                medicalCertificate: { uploaded: true, fileName: 'medical_cert.pdf', status: 'approved' },
                photo: { uploaded: true, fileName: 'photo.jpg', status: 'rejected' }
            },
            payment: {
                amount: 3200,
                method: 'Mobile Payment',
                status: 'paid',
                transactionId: 'TXN-2025-001236'
            }
        }
    ];

    return sampleApplications;
}

function filterApplications(applications) {
    const statusFilter = document.getElementById('statusFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    const searchInput = document.getElementById('searchInput').value.toLowerCase();

    return applications.filter(app => {
        
        if (statusFilter && app.status !== statusFilter) {
            return false;
        }

        
        if (dateFilter) {
            const appDate = new Date(app.submittedDate);
            const now = new Date();
            
            switch (dateFilter) {
                case 'today':
                    if (appDate.toDateString() !== now.toDateString()) return false;
                    break;
                case 'week':
                    const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                    if (appDate < weekAgo) return false;
                    break;
                case 'month':
                    const monthAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
                    if (appDate < monthAgo) return false;
                    break;
            }
        }

        
        if (searchInput) {
            const searchText = `${app.fullName} ${app.nic} ${app.email}`.toLowerCase();
            if (!searchText.includes(searchInput)) return false;
        }

        return true;
    });
}

function updateResultsCount(count) {
    const resultsCount = document.getElementById('resultsCount');
    resultsCount.textContent = `${count} application${count !== 1 ? 's' : ''} found`;
}

function renderApplications(applications) {
    const applicationsList = document.getElementById('applicationsList');
    
    if (applications.length === 0) {
        applicationsList.innerHTML = `
            <div class="no-results">
                <div class="no-results-icon">📋</div>
                <div class="no-results-text">No applications found</div>
                <div class="no-results-subtext">Try adjusting your filters</div>
            </div>
        `;
        return;
    }

    applicationsList.innerHTML = applications.map(app => {
        const statusClass = getStatusClass(app.status);
        const statusText = getStatusText(app.status);
        const initial = app.fullName.charAt(0).toUpperCase();
        
        return `
            <div class="application-card" data-application-id="${app.id}">
                <div class="application-header">
                    <div class="application-info">
                        <div class="application-avatar">${initial}</div>
                        <div class="application-details">
                            <h3>${app.fullName}</h3>
                            <div class="application-nic">${LicenseXpress.formatNIC(app.nic)}</div>
                            <div class="application-date">Submitted ${LicenseXpress.formatDateTime(app.submittedDate)}</div>
                        </div>
                    </div>
                    <div class="application-status ${statusClass}">${statusText}</div>
                </div>
                <div class="application-content">
                    <div class="content-item">
                        <div class="content-label">Email</div>
                        <div class="content-value">${app.email}</div>
                    </div>
                    <div class="content-item">
                        <div class="content-label">Phone</div>
                        <div class="content-value">${app.phone}</div>
                    </div>
                    <div class="content-item">
                        <div class="content-label">Payment</div>
                        <div class="content-value">Rs. ${app.payment.amount.toLocaleString()}</div>
                    </div>
                    <div class="content-item">
                        <div class="content-label">Documents</div>
                        <div class="content-value">${getDocumentStatus(app.documents)}</div>
                    </div>
                </div>
                <div class="application-actions">
                    <button class="btn btn-primary btn-sm" onclick="reviewApplication('${app.id}')">Review</button>
                    <button class="btn btn-secondary btn-sm" onclick="viewApplication('${app.id}')">View Details</button>
                </div>
            </div>
        `;
    }).join('');
}

function getStatusClass(status) {
    const statusClasses = {
        'pending_verification': 'pending',
        'verified': 'verified',
        'rejected': 'rejected'
    };
    return statusClasses[status] || 'pending';
}

function getStatusText(status) {
    const statusTexts = {
        'pending_verification': 'Pending Review',
        'verified': 'Verified',
        'rejected': 'Rejected'
    };
    return statusTexts[status] || 'Unknown';
}

function getDocumentStatus(documents) {
    const total = Object.keys(documents).length;
    const approved = Object.values(documents).filter(doc => doc.status === 'approved').length;
    const rejected = Object.values(documents).filter(doc => doc.status === 'rejected').length;
    
    if (approved === total) return 'All Approved';
    if (rejected > 0) return `${rejected} Rejected`;
    return `${approved}/${total} Approved`;
}

function renderPagination(applications) {
    const pagination = document.getElementById('pagination');
    const itemsPerPage = 10;
    const totalPages = Math.ceil(applications.length / itemsPerPage);
    
    if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
    }

    let paginationHTML = '';
    

    paginationHTML += `
        <button class="pagination-btn" onclick="changePage(1)" disabled>
            ‹‹
        </button>
    `;
    
    
    for (let i = 1; i <= totalPages; i++) {
        paginationHTML += `
            <button class="pagination-btn ${i === 1 ? 'active' : ''}" onclick="changePage(${i})">
                ${i}
            </button>
        `;
    }
    
    
    paginationHTML += `
        <button class="pagination-btn" onclick="changePage(${totalPages})">
            ››
        </button>
    `;
    
    pagination.innerHTML = paginationHTML;
}

function changePage(page) {

    document.querySelectorAll('.pagination-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function initializeFilters() {
    
    document.getElementById('statusFilter').addEventListener('change', function() {
        loadApplications();
    });


    document.getElementById('dateFilter').addEventListener('change', function() {
        loadApplications();
    });

    
    document.getElementById('searchInput').addEventListener('input', function() {
        loadApplications();
    });

    
    document.getElementById('clearFilters').addEventListener('click', function() {
        document.getElementById('statusFilter').value = '';
        document.getElementById('dateFilter').value = '';
        document.getElementById('searchInput').value = '';
        loadApplications();
    });

    
    document.getElementById('refreshApplications').addEventListener('click', function() {
        loadApplications();
        LicenseXpress.showToast('Applications refreshed', 'success');
    });
}

function initializeNavigation() {
    
    const logoutBtn = document.getElementById('logoutBtn');
    logoutBtn.addEventListener('click', function() {
        LicenseXpress.adminLogout();
    });
}

function reviewApplication(applicationId) {
    
    const applications = getAllApplications();
    const application = applications.find(app => app.id === applicationId);
    
    if (!application) {
        LicenseXpress.showToast('Application not found', 'error');
        return;
    }

    
    populateReviewModal(application);
    
    
    const reviewModal = document.getElementById('reviewModal');
    reviewModal.classList.remove('hidden');
}

function populateReviewModal(application) {
    
    document.getElementById('reviewFullName').textContent = application.fullName;
    document.getElementById('reviewNIC').textContent = LicenseXpress.formatNIC(application.nic);
    document.getElementById('reviewDOB').textContent = 'January 23, 2000'; 
    document.getElementById('reviewGender').textContent = 'Male'; 
    document.getElementById('reviewTransmission').textContent = 'Manual'; 
    document.getElementById('reviewDistrict').textContent = 'Colombo'; 

    
    updateDocumentStatus('birthCertPreview', application.documents.birthCertificate.status);
    updateDocumentStatus('nicPreview', application.documents.nicCopy.status);
    updateDocumentStatus('medicalPreview', application.documents.medicalCertificate.status);
    updateDocumentStatus('photoPreview', application.documents.photo.status);
}

function updateDocumentStatus(elementId, status) {
    const element = document.getElementById(elementId);
    const statusElement = element.parentElement.querySelector('.document-status');
    
    statusElement.className = `document-status ${status}`;
    statusElement.textContent = getStatusIcon(status) + ' ' + getStatusText(status);
}

function getStatusIcon(status) {
    const icons = {
        'approved': '✓',
        'pending': '⏳',
        'rejected': '❌'
    };
    return icons[status] || '⏳';
}

function viewApplication(applicationId) {
    
    LicenseXpress.showToast('Opening application details...', 'info');
}

function viewDocument(documentType) {
    
    const documentModal = document.getElementById('documentModal');
    const documentTitle = document.getElementById('documentTitle');
    
    documentTitle.textContent = `${documentType} - Document Viewer`;
    documentModal.classList.remove('hidden');
}

function initializeNavigation() {
    
    const logoutBtn = document.getElementById('logoutBtn');
    logoutBtn.addEventListener('click', function() {
        LicenseXpress.adminLogout();
    });

    
    document.getElementById('closeModal').addEventListener('click', function() {
        document.getElementById('reviewModal').classList.add('hidden');
    });

    document.getElementById('closeDocumentModal').addEventListener('click', function() {
        document.getElementById('documentModal').classList.add('hidden');
    });

    
    document.getElementById('submitReview').addEventListener('click', function() {
        submitReview();
    });

    
    document.getElementById('cancelReview').addEventListener('click', function() {
        document.getElementById('reviewModal').classList.add('hidden');
    });
}

function submitReview() {
    const decision = document.querySelector('input[name="decision"]:checked');
    const comments = document.getElementById('reviewComments').value;
    
    if (!decision) {
        LicenseXpress.showToast('Please select a decision', 'error');
        return;
    }

    if (decision.value === 'reject' && !comments.trim()) {
        LicenseXpress.showToast('Please provide rejection reason', 'error');
        return;
    }


    const submitBtn = document.getElementById('submitReview');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Processing...';
    submitBtn.disabled = true;

    
    setTimeout(() => {
        processReview(decision.value, comments);
        
        
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        
        
        document.getElementById('reviewModal').classList.add('hidden');
        
        
        loadApplications();
        
        LicenseXpress.showToast('Review submitted successfully', 'success');
    }, 1500);
}

function processReview(decision, comments) {
    
    const currentAdmin = LicenseXpress.getCurrentAdmin();
    logAdminActivity(`review_${decision}`, currentAdmin.username);
    
    
    const applications = getAllApplications();
    
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
