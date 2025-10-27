

let currentApplicationId = null;
let currentApplicationData = null;
let currentDocumentId = null;

document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin Review initializing...');
    initializeAdminReview();
});

function initializeAdminReview() {
    loadApplications();
    initializeFilters();
    initializeNavigation();
    initializeModals();
}


// LOAD APPLICATIONS


async function loadApplications() {
    console.log('Loading applications...');
    
    try {
        const statusFilter = document.getElementById('statusFilter')?.value || '';
        const dateFilter = document.getElementById('dateFilter')?.value || '';
        const searchInput = document.getElementById('searchInput')?.value || '';
        
        const params = new URLSearchParams();
        if (statusFilter) params.append('status', statusFilter);
        if (dateFilter) params.append('dateFilter', dateFilter);
        if (searchInput) params.append('search', searchInput);
        
        const response = await fetch(`api_admin-review.php?action=getAllApplications&${params.toString()}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('Applications response:', result);
        
        if (result.success) {
            displayApplications(result.data);
            updateResultsCount(result.data.length);
        } else {
            throw new Error(result.message || 'Failed to load applications');
        }
    } catch (error) {
        console.error('Error loading applications:', error);
        showToast('Failed to load applications: ' + error.message, 'error');
        
        const applicationsList = document.getElementById('applicationsList');
        if (applicationsList) {
            applicationsList.innerHTML = `
                <div class="no-results">
                    <div class="no-results-icon">⚠️</div>
                    <div class="no-results-text">Failed to load applications</div>
                    <div class="no-results-subtext">${escapeHtml(error.message)}</div>
                    <button class="btn btn-primary" onclick="loadApplications()">Retry</button>
                </div>
            `;
        }
    }
}

function displayApplications(applications) {
    const applicationsList = document.getElementById('applicationsList');
    
    if (!applicationsList) {
        console.error('applicationsList container not found');
        return;
    }
    
    if (!applications || applications.length === 0) {
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
        const initial = app.fullName ? app.fullName.charAt(0).toUpperCase() : '?';
        const formattedDate = formatDateTime(app.submitted_date);
        
        const docTotal = parseInt(app.document_count) || 0;
        const docApproved = parseInt(app.approved_docs) || 0;
        const docRejected = parseInt(app.rejected_docs) || 0;
        
        let docStatus = `${docApproved}/${docTotal} Approved`;
        if (docApproved === docTotal && docTotal > 0) {
            docStatus = 'All Approved';
        } else if (docRejected > 0) {
            docStatus = `${docRejected} Rejected`;
        }
        
        return `
            <div class="application-card" data-application-id="${app.id}">
                <div class="application-header">
                    <div class="application-info">
                        <div class="application-avatar">${initial}</div>
                        <div class="application-details">
                            <h3>${escapeHtml(app.fullName)}</h3>
                            <div class="application-nic">${formatNIC(app.nic)}</div>
                            <div class="application-date">Submitted ${formattedDate}</div>
                        </div>
                    </div>
                    <div class="application-status ${statusClass}">${statusText}</div>
                </div>
                <div class="application-content">
                    <div class="content-item">
                        <div class="content-label">Email</div>
                        <div class="content-value">${escapeHtml(app.email)}</div>
                    </div>
                    <div class="content-item">
                        <div class="content-label">Phone</div>
                        <div class="content-value">${escapeHtml(app.phone)}</div>
                    </div>
                    <div class="content-item">
                        <div class="content-label">Payment</div>
                        <div class="content-value">${app.payment ? 'Rs. ' + parseFloat(app.payment.amount).toLocaleString() : 'N/A'}</div>
                    </div>
                    <div class="content-item">
                        <div class="content-label">Documents</div>
                        <div class="content-value">${docStatus}</div>
                    </div>
                </div>
                <div class="application-actions">
                    <button class="btn btn-primary btn-sm" onclick="reviewApplication(${app.id})">Review</button>
                    ${app.status === 'pending_verification' ? `
                        <button class="btn btn-success btn-sm" onclick="quickApprove(${app.id})">Quick Approve</button>
                        <button class="btn btn-danger btn-sm" onclick="quickReject(${app.id})">Quick Reject</button>
                    ` : ''}
                </div>
            </div>
        `;
    }).join('');
}

function updateResultsCount(count) {
    const resultsCount = document.getElementById('resultsCount');
    if (resultsCount) {
        resultsCount.textContent = `${count} application${count !== 1 ? 's' : ''} found`;
    }
}


// REVIEW APPLICATION


async function reviewApplication(applicationId) {
    console.log('Loading application details for review:', applicationId);
    currentApplicationId = applicationId;
    
    try {
        showLoadingModal();
        
        const response = await fetch(`api_admin-review.php?action=getApplicationDetails&id=${applicationId}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('Application details response:', result);
        
        if (result.success) {
            currentApplicationData = result.data;
            populateReviewModal(result.data);
            showReviewModal();
        } else {
            throw new Error(result.message || 'Failed to load application details');
        }
    } catch (error) {
        console.error('Error loading application details:', error);
        showToast('Failed to load application details: ' + error.message, 'error');
    } finally {
        hideLoadingModal();
    }
}

function populateReviewModal(data) {
    const app = data.application;
    const docs = data.documents;
    const payment = data.payment;
    
    // Personal information
    document.getElementById('reviewFullName').textContent = app.full_name || 'N/A';
    document.getElementById('reviewNIC').textContent = formatNIC(app.nic);
    document.getElementById('reviewDOB').textContent = formatDate(app.date_of_birth);
    document.getElementById('reviewGender').textContent = app.gender || 'N/A';
    document.getElementById('reviewTransmission').textContent = app.transmission_type || 'N/A';
    document.getElementById('reviewDistrict').textContent = app.district || 'N/A';
    
    // Documents
    populateDocuments(docs);
    
    // Payment 
    if (payment) {
        const paymentDetails = document.querySelector('.payment-details');
        paymentDetails.innerHTML = `
            <div class="payment-item">
                <span class="payment-label">Amount:</span>
                <span class="payment-value">Rs. ${parseFloat(payment.amount).toLocaleString()}</span>
            </div>
            <div class="payment-item">
                <span class="payment-label">Method:</span>
                <span class="payment-value">${escapeHtml(payment.payment_method)}</span>
            </div>
            <div class="payment-item">
                <span class="payment-label">Status:</span>
                <span class="payment-value success">✓ ${payment.payment_status}</span>
            </div>
            <div class="payment-item">
                <span class="payment-label">Transaction ID:</span>
                <span class="payment-value">${escapeHtml(payment.transaction_id || 'N/A')}</span>
            </div>
        `;
    }
}

function populateDocuments(documents) {
    const documentsGrid = document.querySelector('.documents-grid');
    
    const docTypes = {
        'birth_certificate': { icon: '📄', label: 'Birth Certificate' },
        'nic_copy': { icon: '🪪', label: 'NIC Copy' },
        'medical_certificate': { icon: '🏥', label: 'Medical Certificate' },
        'passport_photo': { icon: '📸', label: 'Passport Photo' }
    };
    
    documentsGrid.innerHTML = Object.entries(docTypes).map(([type, info]) => {
        const doc = documents[type];
        
        if (!doc) {
            return `
                <div class="document-item">
                    <div class="document-preview">
                        <div class="preview-placeholder">${info.icon}</div>
                        <div class="document-name">${info.label}</div>
                    </div>
                    <div class="document-actions">
                        <div class="document-status missing">❌ Missing</div>
                    </div>
                </div>
            `;
        }
        
        const statusIcon = doc.status === 'approved' ? '✓' : 
                          doc.status === 'rejected' ? '❌' : '⏳';
        const statusClass = doc.status || 'pending';
        const statusText = doc.status === 'approved' ? 'Approved' :
                          doc.status === 'rejected' ? 'Rejected' : 'Pending';
        
        return `
            <div class="document-item" data-document-id="${doc.id}" data-document-type="${type}">
                <div class="document-preview">
                    <div class="preview-placeholder">${info.icon}</div>
                    <div class="document-name">${info.label}</div>
                </div>
                <div class="document-actions">
                    <button class="btn btn-secondary btn-sm" onclick="viewDocument(${doc.id})">View</button>
                    ${doc.status === 'pending' ? `
                        <button class="btn btn-success btn-sm" onclick="reviewDocumentQuick(${doc.id}, 'approved')" title="Approve">✓</button>
                        <button class="btn btn-danger btn-sm" onclick="reviewDocumentQuick(${doc.id}, 'rejected')" title="Reject">✗</button>
                    ` : `
                        <div class="document-status ${statusClass}">${statusIcon} ${statusText}</div>
                    `}
                </div>
                ${doc.rejection_reason ? `
                    <div class="document-rejection-reason">
                        <strong>Reason:</strong> ${escapeHtml(doc.rejection_reason)}
                    </div>
                ` : ''}
            </div>
        `;
    }).join('');
}


// DOCUMENT REVIEW

async function reviewDocumentQuick(documentId, status) {
    console.log(`Reviewing document ${documentId} with status: ${status}`);
    
    let rejectionReason = null;
    
    if (status === 'rejected') {
        rejectionReason = prompt('Please provide a reason for rejection:');
        if (!rejectionReason || rejectionReason.trim() === '') {
            showToast('Rejection reason is required', 'warning');
            return;
        }
    }
    
    try {
        const response = await fetch('api_admin-review.php?action=reviewDocument', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                documentId: documentId,
                status: status,
                rejectionReason: rejectionReason
            })
        });
        
        const result = await response.json();
        console.log('Review document response:', result);
        
        if (result.success) {
            showToast(`Document ${status} successfully!`, 'success');
            
            
            await reviewApplication(currentApplicationId);
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Error reviewing document:', error);
        showToast('Failed to review document: ' + error.message, 'error');
    }
}

async function viewDocument(documentId) {
    try {
        const response = await fetch(`api_admin-review.php?action=getDocumentFile&id=${documentId}`);
        const result = await response.json();
        
        if (result.success) {
            currentDocumentId = documentId;
            showDocumentModal(result.data);
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Error viewing document:', error);
        showToast('Failed to load document: ' + error.message, 'error');
    }
}

function showDocumentModal(documentData) {
    const modal = document.getElementById('documentModal');
    const documentTitle = document.getElementById('documentTitle');
    const documentImage = document.getElementById('documentImage');
    
    documentTitle.textContent = documentData.documentType.replace('_', ' ').toUpperCase();
    
    
    if (documentData.fileType.startsWith('image/')) {
        documentImage.innerHTML = `
            <img src="${documentData.downloadUrl}" alt="${documentData.fileName}" id="documentImg" style="max-width: 100%; height: auto;">
        `;
    } else if (documentData.fileType === 'application/pdf') {
        documentImage.innerHTML = `
            <iframe src="${documentData.downloadUrl}" width="100%" height="600px" style="border: none;"></iframe>
        `;
    } else {
        documentImage.innerHTML = `
            <div class="image-placeholder">
                <div class="placeholder-icon">📄</div>
                <div class="placeholder-text">${documentData.fileName}</div>
                <button class="btn btn-primary" onclick="window.open('${documentData.downloadUrl}', '_blank')">
                    Open File
                </button>
            </div>
        `;
    }
    
    modal.classList.remove('hidden');
}


// SUBMIT APPLICATION REVIEW 


async function submitApplicationReview() {
    const decisionElement = document.querySelector('input[name="decision"]:checked');
    
    if (!decisionElement) {
        showToast('Please select a decision (Approve or Reject)', 'warning');
        return;
    }
    
    const decision = decisionElement.value;
    const comments = document.getElementById('reviewComments').value;
    
    // Get rejection reasons
    let rejectionReasons = [];
    if (decision === 'reject') {
        const checkboxes = document.querySelectorAll('.rejection-reasons input[type="checkbox"]:checked');
        rejectionReasons = Array.from(checkboxes).map(cb => cb.nextElementSibling.textContent);
        
        if (rejectionReasons.length === 0 && !comments) {
            showToast('Please select rejection reasons or add comments', 'warning');
            return;
        }
    }
    
    // Confirm action
    const confirmMessage = decision === 'approve' 
        ? 'Are you sure you want to APPROVE this application? Status will be changed to VERIFIED.'
        : 'Are you sure you want to REJECT this application? Status will be changed to NOT_VERIFIED.';
    
    if (!confirm(confirmMessage)) {
        return;
    }
    
    try {
        showLoadingModal();
        
        const response = await fetch('api_admin-review.php?action=reviewApplication', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                applicationId: currentApplicationId,
                decision: decision,
                comments: comments,
                rejectionReasons: rejectionReasons
            })
        });
        
        const result = await response.json();
        console.log('Review application response:', result);
        
        if (result.success) {
            const statusMessage = decision === 'approve' 
                ? 'Application APPROVED! Status changed to VERIFIED ✓'
                : 'Application REJECTED! Status changed to NOT_VERIFIED ✗';
            
            showToast(statusMessage, 'success');
            hideReviewModal();
            
            
            await loadApplications();
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Error submitting review:', error);
        showToast('Failed to submit review: ' + error.message, 'error');
    } finally {
        hideLoadingModal();
    }
}


// QUICK ACTIONS


async function quickApprove(applicationId) {
    if (!confirm('Are you sure you want to APPROVE this application? Status will be changed to VERIFIED.')) {
        return;
    }
    
    try {
        showLoadingModal();
        
        const response = await fetch('../../api_admin-review.php?action=reviewApplication', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                applicationId: applicationId,
                decision: 'approve',
                comments: 'Quick approved by admin'
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('Application APPROVED! Status changed to VERIFIED ✓', 'success');
            await loadApplications();
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Error approving application:', error);
        showToast('Failed to approve application: ' + error.message, 'error');
    } finally {
        hideLoadingModal();
    }
}

async function quickReject(applicationId) {
    const reason = prompt('Please provide a reason for rejection:');
    if (!reason || reason.trim() === '') {
        showToast('Rejection reason is required', 'warning');
        return;
    }
    
    if (!confirm('Are you sure you want to REJECT this application? Status will be changed to NOT_VERIFIED.')) {
        return;
    }
    
    try {
        showLoadingModal();
        
        const response = await fetch('../../api_admin-review.php?action=reviewApplication', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                applicationId: applicationId,
                decision: 'reject',
                comments: reason
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('Application REJECTED! Status changed to NOT_VERIFIED ✗', 'success');
            await loadApplications();
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Error rejecting application:', error);
        showToast('Failed to reject application: ' + error.message, 'error');
    } finally {
        hideLoadingModal();
    }
}



function initializeFilters() {
    const statusFilter = document.getElementById('statusFilter');
    const dateFilter = document.getElementById('dateFilter');
    const searchInput = document.getElementById('searchInput');
    const clearFilters = document.getElementById('clearFilters');
    const refreshApplications = document.getElementById('refreshApplications');
    
    if (statusFilter) {
        statusFilter.addEventListener('change', loadApplications);
    }
    
    if (dateFilter) {
        dateFilter.addEventListener('change', loadApplications);
    }
    
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(loadApplications, 500);
        });
    }
    
    if (clearFilters) {
        clearFilters.addEventListener('click', function() {
            if (statusFilter) statusFilter.value = '';
            if (dateFilter) dateFilter.value = '';
            if (searchInput) searchInput.value = '';
            loadApplications();
        });
    }
    
    if (refreshApplications) {
        refreshApplications.addEventListener('click', loadApplications);
    }
}

function initializeNavigation() {
    const logoutBtn = document.getElementById('logoutBtn');
    
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'logout.php';
            }
        });
    }
}

function initializeModals() {
    const reviewModal = document.getElementById('reviewModal');
    const closeModal = document.getElementById('closeModal');
    const cancelReview = document.getElementById('cancelReview');
    const submitReview = document.getElementById('submitReview');
    
    const documentModal = document.getElementById('documentModal');
    const closeDocumentModal = document.getElementById('closeDocumentModal');
    
    if (closeModal) {
        closeModal.addEventListener('click', hideReviewModal);
    }
    
    if (cancelReview) {
        cancelReview.addEventListener('click', hideReviewModal);
    }
    
    if (submitReview) {
        submitReview.addEventListener('click', submitApplicationReview);
    }
    
    if (closeDocumentModal) {
        closeDocumentModal.addEventListener('click', hideDocumentModal);
    }
    
    
    const zoomIn = document.getElementById('zoomIn');
    const zoomOut = document.getElementById('zoomOut');
    const rotateDocument = document.getElementById('rotateDocument');
    const downloadDocument = document.getElementById('downloadDocument');
    
    let currentZoom = 1;
    let currentRotation = 0;
    
    if (zoomIn) {
        zoomIn.addEventListener('click', function() {
            currentZoom += 0.2;
            applyImageTransform();
        });
    }
    
    if (zoomOut) {
        zoomOut.addEventListener('click', function() {
            currentZoom = Math.max(0.2, currentZoom - 0.2);
            applyImageTransform();
        });
    }
    
    if (rotateDocument) {
        rotateDocument.addEventListener('click', function() {
            currentRotation = (currentRotation + 90) % 360;
            applyImageTransform();
        });
    }
    
    if (downloadDocument) {
        downloadDocument.addEventListener('click', async function() {
            if (currentDocumentId) {
                try {
                    const response = await fetch(`../../api_admin-review.php?action=getDocumentFile&id=${currentDocumentId}`);
                    const result = await response.json();
                    if (result.success) {
                        window.open(result.data.downloadUrl, '_blank');
                    }
                } catch (error) {
                    showToast('Failed to download document', 'error');
                }
            }
        });
    }
    
    function applyImageTransform() {
        const img = document.getElementById('documentImg');
        if (img) {
            img.style.transform = `scale(${currentZoom}) rotate(${currentRotation}deg)`;
            img.style.transition = 'transform 0.3s ease';
        }
    }
}



function showReviewModal() {
    const modal = document.getElementById('reviewModal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function hideReviewModal() {
    const modal = document.getElementById('reviewModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        
        // Reset form
        const decisionInputs = document.querySelectorAll('input[name="decision"]');
        decisionInputs.forEach(input => input.checked = false);
        
        const commentsField = document.getElementById('reviewComments');
        if (commentsField) commentsField.value = '';
        
        const checkboxes = document.querySelectorAll('.rejection-reasons input[type="checkbox"]');
        checkboxes.forEach(cb => cb.checked = false);
    }
}

function hideDocumentModal() {
    const modal = document.getElementById('documentModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

function showLoadingModal() {
    console.log('Loading...');
   
}

function hideLoadingModal() {
    console.log('Loading complete');
}


function getStatusClass(status) {
    const statusClasses = {
        'pending_verification': 'status-pending',
        'verified': 'status-verified',
        'not_verified': 'status-rejected',
        'rejected': 'status-rejected',
        'theory_scheduled': 'status-scheduled',
        'theory_passed': 'status-passed',
        'practical_scheduled': 'status-scheduled',
        'license_issued': 'status-completed'
    };
    return statusClasses[status] || 'status-default';
}

function getStatusText(status) {
    const statusTexts = {
        'pending_verification': 'Pending Verification',
        'verified': 'Verified',
        'not_verified': 'Not Verified',
        'rejected': 'Rejected',
        'theory_scheduled': 'Theory Scheduled',
        'theory_passed': 'Theory Passed',
        'practical_scheduled': 'Practical Scheduled',
        'license_issued': 'License Issued'
    };
    return statusTexts[status] || status;
}

function formatNIC(nic) {
    if (!nic) return 'N/A';
    if (nic.length === 10) {
        return nic.substring(0, 9) + 'V';
    }
    return nic;
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
}

function formatDateTime(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);
    
    if (diffMins < 60) {
        return `${diffMins} minute${diffMins !== 1 ? 's' : ''} ago`;
    } else if (diffHours < 24) {
        return `${diffHours} hour${diffHours !== 1 ? 's' : ''} ago`;
    } else if (diffDays < 7) {
        return `${diffDays} day${diffDays !== 1 ? 's' : ''} ago`;
    } else {
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.textContent = message;

    toast.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: ${type === 'success' ? '#10b981'
                    : type === 'error' ? '#ef4444'
                    : type === 'warning' ? '#f59e0b'
                    : '#3b82f6'};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 10000;
        font-size: 17px;
        font-weight: 500;
        line-height: 1.4;
        padding: 10px 20px;   /* balanced space around text */
        display: inline-block;
        white-space: nowrap;
        animation: fadeIn 0.25s ease, fadeOut 0.25s ease 3.75s;
        opacity: 0;
        transition: opacity 0.25s ease;
        text-align: center;
    `;

    document.body.appendChild(toast);

    
    requestAnimationFrame(() => {
        toast.style.opacity = '1';
    });

    
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 250);
    }, 4000);
}


const style = document.createElement('style');
style.textContent = `
@keyframes fadeIn {
    from { opacity: 0; transform: translate(-50%, -55%); }
    to { opacity: 1; transform: translate(-50%, -50%); }
}
@keyframes fadeOut {
    from { opacity: 1; transform: translate(-50%, -50%); }
    to { opacity: 0; transform: translate(-50%, -45%); }
}
`;
document.head.appendChild(style);
