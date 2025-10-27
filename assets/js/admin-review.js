

let currentApplicationId = null;
let currentApplicationData = null;
let currentDocumentId = null;

document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin Review initializing...');
    initializeAdminReview();
});

function initializeAdminReview() {
    loadAllSections();
    initializeFilters();
    initializeNavigation();
    initializeModals();
}



async function loadAllSections() {
    console.log('Loading all sections...');
    
    try {
        const searchInput = document.getElementById('searchInput')?.value || '';
        
        const params = new URLSearchParams();
        if (searchInput) params.append('search', searchInput);
        
        
        const [pendingResponse, practicalResponse, approvedResponse] = await Promise.all([
            fetch(`api_admin-review.php?action=getApplicationsBySection&section=pending&${params.toString()}`),
            fetch(`api_admin-review.php?action=getApplicationsBySection&section=practical&${params.toString()}`),
            fetch(`api_admin-review.php?action=getApplicationsBySection&section=approved&${params.toString()}`)
        ]);
        
        const [pendingResult, practicalResult, approvedResult] = await Promise.all([
            pendingResponse.json(),
            practicalResponse.json(),
            approvedResponse.json()
        ]);
        
        if (pendingResult.success) {
            displayApplications(pendingResult.data, 'pendingApplicationsList');
            updateSectionCount('pendingCount', pendingResult.data.length);
        }
        
        if (practicalResult.success) {
            displayApplications(practicalResult.data, 'practicalApplicationsList');
            updateSectionCount('practicalCount', practicalResult.data.length);
        }
        
        if (approvedResult.success) {
            displayApplications(approvedResult.data, 'approvedApplicationsList');
            updateSectionCount('approvedCount', approvedResult.data.length);
        }
        
    } catch (error) {
        console.error('Error loading sections:', error);
        showToast('Failed to load applications: ' + error.message, 'error');
    }
}

async function loadApplications() {
    
    await loadAllSections();
}

function displayApplications(applications, containerId = 'applicationsList') {
    const applicationsList = document.getElementById(containerId);
    
    if (!applicationsList) {
        console.error(`Container ${containerId} not found`);
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
            <div class="application-card" data-application-id="${app.id}" data-priority="${app.status === 'pending_verification' ? 'pending' : 'normal'}">
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
                    ${getApplicationActions(app)}
                </div>
                ${getPriorityIndicator(app)}
            </div>
        `;
    }).join('');
}

function updateSectionCount(countId, count) {
    const countElement = document.getElementById(countId);
    if (countElement) {
        countElement.textContent = `${count} application${count !== 1 ? 's' : ''} found`;
    }
}

function getApplicationActions(app) {
    switch (app.status) {
        case 'pending_verification':
        case 'rejected':
            return `
                <button class="btn btn-primary btn-sm" onclick="reviewApplication(${app.id})">Review</button>
                <button class="btn btn-success btn-sm" onclick="quickApprove(${app.id})">Quick Approve</button>
                <button class="btn btn-danger btn-sm" onclick="quickReject(${app.id})">Quick Reject</button>
            `;
        case 'practical_scheduled':
            return `
                <button class="btn btn-primary btn-sm" onclick="reviewApplication(${app.id})">Review</button>
                <button class="btn btn-warning btn-sm" onclick="openPracticalResultModal(${app.id})">Submit Result</button>
            `;
        case 'verified':
        case 'theory_scheduled':
        case 'theory_passed':
        case 'license_issued':
            return `
                <button class="btn btn-secondary btn-sm" onclick="reviewApplication(${app.id})">View Details</button>
            `;
        default:
            return `
                <button class="btn btn-primary btn-sm" onclick="reviewApplication(${app.id})">Review</button>
            `;
    }
}

function getPriorityIndicator(app) {
    if (app.status === 'pending_verification') {
        return `
            <div class="priority-indicator">
                <span class="priority-badge">⚠️ Needs Review</span>
            </div>
        `;
    } else if (app.status === 'practical_scheduled') {
        return `
            <div class="priority-indicator">
                <span class="priority-badge practical">🚗 Practical Scheduled</span>
            </div>
        `;
    }
    return '';
}



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
    
    
    document.getElementById('reviewFullName').textContent = app.full_name || 'N/A';
    document.getElementById('reviewNIC').textContent = formatNIC(app.nic);
    document.getElementById('reviewDOB').textContent = formatDate(app.date_of_birth);
    document.getElementById('reviewGender').textContent = app.gender || 'N/A';
    document.getElementById('reviewTransmission').textContent = app.transmission_type || 'N/A';
    document.getElementById('reviewDistrict').textContent = app.district || 'N/A';
    
    
    populateDocuments(docs);
    
    
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
        'photo': { icon: '📸', label: 'Passport Photo' }
    };
    
    function findPhotoDocFallback() {
        
        for (const [key, value] of Object.entries(documents || {})) {
            if (typeof key === 'string' && key.toLowerCase().includes('photo')) {
                return value;
            }
        }
        return null;
    }
    
    documentsGrid.innerHTML = Object.entries(docTypes).map(([type, info]) => {
        
        let doc = documents ? documents[type] : null;
        if (!doc && type === 'photo') {
            
            doc = documents ? (documents['passport_photo'] || findPhotoDocFallback()) : null;
        }
        
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
        
        const isPending = !doc.status || doc.status === 'pending';
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
                    ${isPending ? `
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



async function submitApplicationReview() {
    const decisionElement = document.querySelector('input[name="decision"]:checked');
    
    if (!decisionElement) {
        showToast('Please select a decision (Approve or Reject)', 'warning');
        return;
    }
    
    const decision = decisionElement.value;
    const comments = document.getElementById('reviewComments').value;
    
    
    let rejectionReasons = [];
    if (decision === 'reject') {
        const checkboxes = document.querySelectorAll('.rejection-reasons input[type="checkbox"]:checked');
        rejectionReasons = Array.from(checkboxes).map(cb => cb.nextElementSibling.textContent);
        
        if (rejectionReasons.length === 0 && !comments) {
            showToast('Please select rejection reasons or add comments', 'warning');
            return;
        }
    }
    
    
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



async function openPracticalResultModal(applicationId) {
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
            populatePracticalModal(result.data);
            showPracticalModal();
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

function populatePracticalModal(data) {
    const app = data.application;
    const practicalTest = data.practicalTest;
    
    
    document.getElementById('practicalFullName').textContent = app.full_name || 'N/A';
    document.getElementById('practicalNIC').textContent = formatNIC(app.nic);
    document.getElementById('practicalTestCenter').textContent = practicalTest ? practicalTest.center_name || 'N/A' : 'N/A';
    document.getElementById('practicalScheduledDate').textContent = practicalTest ? formatDate(practicalTest.scheduled_date) : 'N/A';
}

async function submitPracticalExamResult() {
    const resultElement = document.querySelector('input[name="practicalResult"]:checked');
    
    if (!resultElement) {
        showToast('Please select a result (Passed or Failed)', 'warning');
        return;
    }
    
    const result = resultElement.value;
    const comments = document.getElementById('practicalComments').value;
    
    
    const confirmMessage = result === 'passed' 
        ? 'Are you sure this candidate PASSED the practical exam? This will issue their license.'
        : 'Are you sure this candidate FAILED the practical exam? They will need to retake the test.';
    
    if (!confirm(confirmMessage)) {
        return;
    }
    
    try {
        showLoadingModal();
        
        const response = await fetch('api_admin-review.php?action=submitPracticalResult', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                applicationId: currentApplicationId,
                result: result,
                comments: comments
            })
        });
        
        const result = await response.json();
        console.log('Practical result response:', result);
        
        if (result.success) {
            const statusMessage = result.data.result === 'passed' 
                ? 'Practical exam PASSED! License issued successfully 🎉'
                : 'Practical exam FAILED. Candidate can retake the test.';
            
            showToast(statusMessage, 'success');
            hidePracticalModal();
            
            
            await loadAllSections();
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Error submitting practical result:', error);
        showToast('Failed to submit practical result: ' + error.message, 'error');
    } finally {
        hideLoadingModal();
    }
}



async function quickApprove(applicationId) {
    if (!confirm('Are you sure you want to APPROVE this application? Status will be changed to VERIFIED.')) {
        return;
    }
    
    try {
        showLoadingModal();
        
        const response = await fetch('api_admin-review.php?action=reviewApplication', {
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
        
        const response = await fetch('api_admin-review.php?action=reviewApplication', {
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
    const searchInput = document.getElementById('searchInput');
    const clearFilters = document.getElementById('clearFilters');
    const refreshPending = document.getElementById('refreshPending');
    const refreshPractical = document.getElementById('refreshPractical');
    const refreshApproved = document.getElementById('refreshApproved');
    
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(loadAllSections, 500);
        });
    }
    
    if (clearFilters) {
        clearFilters.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            loadAllSections();
        });
    }
    
    if (refreshPending) {
        refreshPending.addEventListener('click', loadAllSections);
    }
    
    if (refreshPractical) {
        refreshPractical.addEventListener('click', loadAllSections);
    }
    
    if (refreshApproved) {
        refreshApproved.addEventListener('click', loadAllSections);
    }
}

function initializeNavigation() {
    const logoutBtn = document.getElementById('logoutBtn');
    
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to logout?')) {
                // Clear admin authentication
                localStorage.removeItem('isAdminAuthenticated');
                localStorage.removeItem('adminUsername');
                localStorage.removeItem('adminAuthExpiry');
                localStorage.removeItem('adminLoginTime');
                
                // Redirect to admin login
                window.location.href = 'admin-login.php';
            }
        });
    }
}

function initializeModals() {
    const reviewModal = document.getElementById('reviewModal');
    const closeModal = document.getElementById('closeModal');
    const cancelReview = document.getElementById('cancelReview');
    const submitReview = document.getElementById('submitReview');
    
    const practicalModal = document.getElementById('practicalModal');
    const closePracticalModal = document.getElementById('closePracticalModal');
    const cancelPractical = document.getElementById('cancelPractical');
    const submitPracticalResult = document.getElementById('submitPracticalResult');
    
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
    
    if (closePracticalModal) {
        closePracticalModal.addEventListener('click', hidePracticalModal);
    }
    
    if (cancelPractical) {
        cancelPractical.addEventListener('click', hidePracticalModal);
    }
    
    if (submitPracticalResult) {
        submitPracticalResult.addEventListener('click', submitPracticalExamResult);
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
                    const response = await fetch(`api_admin-review.php?action=getDocumentFile&id=${currentDocumentId}`);
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
        
        
        const decisionInputs = document.querySelectorAll('input[name="decision"]');
        decisionInputs.forEach(input => input.checked = false);
        
        const commentsField = document.getElementById('reviewComments');
        if (commentsField) commentsField.value = '';
        
        const checkboxes = document.querySelectorAll('.rejection-reasons input[type="checkbox"]');
        checkboxes.forEach(cb => cb.checked = false);
    }
}

function showPracticalModal() {
    const modal = document.getElementById('practicalModal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else {
        console.error('Practical modal element not found!');
    }
}

function hidePracticalModal() {
    const modal = document.getElementById('practicalModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        
        // Reset form
        const resultInputs = document.querySelectorAll('input[name="practicalResult"]');
        resultInputs.forEach(input => input.checked = false);
        
        const commentsField = document.getElementById('practicalComments');
        if (commentsField) commentsField.value = '';
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
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : type === 'warning' ? '#f59e0b' : '#3b82f6'};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 10000;
        animation: slideIn 0.3s ease;
        max-width: 400px;
        font-weight: 500;
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}


const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);