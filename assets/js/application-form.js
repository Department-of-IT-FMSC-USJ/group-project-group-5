
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Starting initialization');
    
    
    initializeApplicationForm();
});

function initializeApplicationForm() {
    console.log('=== Initializing Application Form ===');
    const currentUser = LicenseXpress.getCurrentUser();
    console.log('Current User:', currentUser);
    const applicationState = JSON.parse(localStorage.getItem('applicationState') || '{}');
    const userProfile = JSON.parse(localStorage.getItem('userProfile') || '{}');

    console.log('Updating user info...');
    updateUserInfo(currentUser);

    console.log('Prefilling form...');
    prefillForm(currentUser, userProfile, applicationState);

    console.log('Initializing form steps...');
    initializeFormSteps();

    console.log('Initializing upload zones...');
    initializeUploadZones();

    console.log('Initializing payment methods...');
    initializePaymentMethods();

    console.log('Initializing form validation...');
    
    initializeFormValidation();

    console.log('Initializing navigation...');
    
    initializeNavigation();
}

function updateUserInfo(user) {
    const userName = document.getElementById('userName');
    const userNIC = document.getElementById('userNIC');
    const userAvatar = document.getElementById('userAvatar');

    if (user) {
        const name = user.fullName || 'User';
        const initial = name.charAt(0).toUpperCase();
        
        userName.textContent = name;
        userNIC.textContent = LicenseXpress.formatNIC(user.nic);
        userAvatar.textContent = initial;
    }
}

function prefillForm(currentUser, userProfile, applicationState) {
    
    if (currentUser) {
        document.getElementById('fullName').value = currentUser.fullName || '';
        document.getElementById('nic').value = LicenseXpress.formatNIC(currentUser.nic);
    }

    
    if (userProfile.fullName) {
        document.getElementById('fullName').value = userProfile.fullName;
    }
    if (userProfile.dateOfBirth) {
        document.getElementById('dateOfBirth').value = userProfile.dateOfBirth;
    }
    if (userProfile.gender) {
        document.getElementById('gender').value = userProfile.gender;
    }
    if (userProfile.transmissionType) {
        const transmissionRadio = document.querySelector(`input[name="transmissionType"][value="${userProfile.transmissionType}"]`);
        if (transmissionRadio) {
            transmissionRadio.checked = true;
            updateTransmissionCard(userProfile.transmissionType);
        }
    }
    if (userProfile.district) {
        document.getElementById('district').value = userProfile.district;
    }

    
    if (applicationState.status === 'rejected') {
        prefillDocuments(applicationState);
    }
}

function prefillDocuments(applicationState) {
    const documents = applicationState.documents || {};
    
    Object.keys(documents).forEach(docType => {
        const doc = documents[docType];
        if (doc.uploaded && doc.fileData) {
            
            restoreUploadedDocuments();
        }
    });
}


function restoreUploadedDocuments() {
    const applicationState = JSON.parse(localStorage.getItem('applicationState') || '{}');
    const documents = applicationState.documents || {};
    
    Object.keys(documents).forEach(docType => {
        const doc = documents[docType];
        if (doc && doc.uploaded && doc.fileData) {
            
            const zone = document.getElementById(docType + 'Zone');
            if (zone) {
                const progress = zone.querySelector('.upload-progress');
                const preview = zone.querySelector('.upload-preview');
                const filename = zone.querySelector('.preview-filename');
                const size = zone.querySelector('.preview-size');
                const thumbnail = zone.querySelector('.preview-thumbnail');
                
                if (progress && preview) {
                    
                    progress.classList.add('hidden');
                    preview.classList.remove('hidden');
                    
                    
                    if (filename) filename.textContent = doc.fileName;
                    if (size) size.textContent = formatFileSize(doc.fileSize);
                    
                    
                    if (thumbnail && doc.fileType && doc.fileType.startsWith('image/')) {
                        const img = document.createElement('img');
                        img.src = doc.fileData;
                        img.style.width = '100%';
                        img.style.height = '100%';
                        img.style.objectFit = 'cover';
                        img.style.borderRadius = '6px';
                        thumbnail.innerHTML = '';
                        thumbnail.appendChild(img);
                    } else if (thumbnail) {
                        thumbnail.innerHTML = '📄';
                    }
                }
            }
        }
    });
    
    
    updateUploadCount();
}

function initializeFormSteps() {
    const steps = document.querySelectorAll('.step');
    const formSteps = document.querySelectorAll('.form-step');
    let currentStep = 1;

    
    function updateSteps(step) {
        steps.forEach((stepEl, index) => {
            const stepNumber = index + 1;
            stepEl.classList.remove('active', 'completed');
            
            if (stepNumber < step) {
                stepEl.classList.add('completed');
            } else if (stepNumber === step) {
                stepEl.classList.add('active');
            }
        });

        
        const progressFill = document.getElementById('progressFill');
        const progressPercent = ((step - 1) / (steps.length - 1)) * 100;
        progressFill.style.width = progressPercent + '%';

        
        formSteps.forEach((formStep, index) => {
            formStep.classList.remove('active');
            if (index + 1 === step) {
                formStep.classList.add('active');
            }
        });
        
        if (step === 2) {
            restoreUploadedDocuments();
        }
    }

    
    window.nextStep = function() {
        if (validateCurrentStep(currentStep)) {
            if (currentStep < steps.length) {
                currentStep++;
                updateSteps(currentStep);
                saveCurrentStepData(currentStep - 1);
            }
        } else {
            alert('Please fill in all required fields before proceeding.');
        }
    };

    window.backStep = function() {
        if (currentStep > 1) {
            currentStep--;
            updateSteps(currentStep);
        }
    };

    window.goToStep = function(step) {
        if (step >= 1 && step <= steps.length) {
            currentStep = step;
            updateSteps(currentStep);
        }
    };

    
    updateSteps(1);
}

function initializeUploadZones() {
    const uploadZones = [
        { id: 'birthCertificate', zone: 'birthCertificateZone', area: 'birthCertificateArea' },
        { id: 'nicCopy', zone: 'nicCopyZone', area: 'nicCopyArea' },
        { id: 'medicalCertificate', zone: 'medicalCertificateZone', area: 'medicalCertificateArea' },
        { id: 'photo', zone: 'photoZone', area: 'photoArea' }
    ];

    uploadZones.forEach(upload => {
        const zone = document.getElementById(upload.zone);
        const area = document.getElementById(upload.area);
        const input = document.getElementById(upload.id);

        console.log(`Initializing upload zone: ${upload.id}`, { zone, area, input });

        if (!area || !input) {
            console.error(`Missing elements for ${upload.id}`);
            return;
        }

       
        const triggerFileSelect = (e) => {
            
            console.log(`Triggered file select for: ${upload.id}`);
            
            
            try {
                input.click();
                console.log('Click executed successfully');
            } catch (err) {
                console.error('Error clicking input:', err);
            }
        };

        
        area.addEventListener('click', triggerFileSelect);

        
        area.addEventListener('dragover', handleDragOver);
        area.addEventListener('dragleave', handleDragLeave);
        area.addEventListener('drop', (e) => handleDrop(e, input));

        
        input.addEventListener('change', (e) => handleFileSelect(e, upload.id));

        
        const previewBtn = zone.querySelector('.btn-preview');
        const deleteBtn = zone.querySelector('.btn-delete');

        if (previewBtn) {
            previewBtn.addEventListener('click', () => showImagePreview(upload.id));
        }

        if (deleteBtn) {
            deleteBtn.addEventListener('click', () => deleteDocument(upload.id));
        }
    });
}

function handleDragOver(e) {
    e.preventDefault();
    e.currentTarget.classList.add('dragover');
}

function handleDragLeave(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('dragover');
}

function handleDrop(e, input) {
    e.preventDefault();
    e.currentTarget.classList.remove('dragover');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        input.files = files;
        handleFileSelect({ target: input }, input.id);
    }
}

function handleFileSelect(e, docType) {
    const file = e.target.files[0];
    if (!file) return;

    
    const validation = LicenseXpress.validateFile(file);
    if (!validation.valid) {
        LicenseXpress.showToast(validation.errors.join(', '), 'error');
        return;
    }

    
    showUploadProgress(docType);

    
    LicenseXpress.convertToBase64(file).then(base64 => {
        completeUpload(docType, file, base64);
    });
}

function showUploadProgress(docType) {
    const zone = document.getElementById(docType + 'Zone');
    const progress = zone.querySelector('.upload-progress');
    const progressFill = zone.querySelector('.progress-fill');
    const progressText = zone.querySelector('.progress-text');

    progress.classList.remove('hidden');
    
    let progressValue = 0;
    const interval = setInterval(() => {
        progressValue += Math.random() * 20;
        if (progressValue >= 100) {
            progressValue = 100;
            clearInterval(interval);
        }
        progressFill.style.width = progressValue + '%';
        progressText.textContent = Math.round(progressValue) + '%';
    }, 100);
}

function completeUpload(docType, file, base64) {
    const zone = document.getElementById(docType + 'Zone');
    const progress = zone.querySelector('.upload-progress');
    const preview = zone.querySelector('.upload-preview');
    const filename = zone.querySelector('.preview-filename');
    const size = zone.querySelector('.preview-size');
    const thumbnail = zone.querySelector('.preview-thumbnail');

    
    progress.classList.add('hidden');
    preview.classList.remove('hidden');

    
    filename.textContent = file.name;
    size.textContent = formatFileSize(file.size);
    
   
    if (file.type.startsWith('image/')) {
        const img = document.createElement('img');
        img.src = base64;
        img.style.width = '100%';
        img.style.height = '100%';
        img.style.objectFit = 'cover';
        img.style.borderRadius = '6px';
        thumbnail.innerHTML = '';
        thumbnail.appendChild(img);
    } else {
        thumbnail.innerHTML = '📄';
    }

    
    saveDocumentToLocalStorage(docType, file.name, base64, file);

    
    updateUploadCount();

    
    LicenseXpress.showToast('✅ Document ready!', 'success');
}

function saveDocumentToLocalStorage(docType, fileName, fileData, file) {
    const applicationState = JSON.parse(localStorage.getItem('applicationState') || '{}');
    if (!applicationState.documents) {
        applicationState.documents = {};
    }

    
    applicationState.documents[docType] = {
        fileName: fileName,
        fileData: fileData, 
        fileSize: file ? file.size : 0,
        fileType: file ? file.type : 'application/octet-stream',
        uploaded: true,
        uploadDate: new Date().toISOString()
    };

    localStorage.setItem('applicationState', JSON.stringify(applicationState));
}


function updateUserStatus(status, applicationId) {
    const currentUser = LicenseXpress.getCurrentUser();
    if (currentUser) {
        currentUser.status = status;
        currentUser.applicationId = applicationId;
        currentUser.lastUpdated = new Date().toISOString();
        localStorage.setItem('currentUser', JSON.stringify(currentUser));
    }
    
    
    const applicationState = JSON.parse(localStorage.getItem('applicationState') || '{}');
    applicationState.status = status;
    applicationState.applicationId = applicationId;
    applicationState.lastUpdated = new Date().toISOString();
    localStorage.setItem('applicationState', JSON.stringify(applicationState));
}

function updateUploadCount() {
    const applicationState = JSON.parse(localStorage.getItem('applicationState') || '{}');
    const documents = applicationState.documents || {};
    const uploadedCount = Object.values(documents).filter(doc => doc.uploaded).length;
    
    const uploadCount = document.getElementById('uploadCount');
    const uploadProgressFill = document.getElementById('uploadProgressFill');
    
    uploadCount.textContent = `${uploadedCount}/4`;
    uploadProgressFill.style.width = (uploadedCount / 4 * 100) + '%';
}

function deleteDocument(docType) {
    const zone = document.getElementById(docType + 'Zone');
    if (!zone) return;
    
    const preview = zone.querySelector('.upload-preview');
    const progress = zone.querySelector('.upload-progress');
    const input = document.getElementById(docType);

    
    if (preview) preview.classList.add('hidden');
    if (progress) progress.classList.add('hidden');
    if (input) input.value = '';

   
    const applicationState = JSON.parse(localStorage.getItem('applicationState') || '{}');
    if (applicationState.documents && applicationState.documents[docType]) {
        delete applicationState.documents[docType];
        localStorage.setItem('applicationState', JSON.stringify(applicationState));
    }

    
    updateUploadCount();

    LicenseXpress.showToast('Document removed', 'info');
}

function showImagePreview(docType) {
    const applicationState = JSON.parse(localStorage.getItem('applicationState') || '{}');
    const doc = applicationState.documents?.[docType];
    
    if (doc && doc.fileData) {
        const modal = document.getElementById('imageModal');
        const previewImage = document.getElementById('previewImage');
        
        
        previewImage.src = doc.fileData;
        modal.classList.remove('hidden');
    } else {
        LicenseXpress.showToast('⚠️ Document preview not available', 'warning');
    }
}

function initializePaymentMethods() {
    const paymentOptions = document.querySelectorAll('.payment-option');
    
    paymentOptions.forEach(option => {
        const radio = option.querySelector('input[type="radio"]');
        const label = option.querySelector('.option-label');
        
        
        radio.addEventListener('change', function() {
            
            paymentOptions.forEach(opt => opt.classList.remove('selected'));
            
            
            if (this.checked) {
                option.classList.add('selected');
            }
        });
        
        
        label.addEventListener('click', function() {
            radio.checked = true;
            radio.dispatchEvent(new Event('change'));
        });
    });
}

function initializeFormValidation() {
    
    const personalForm = document.getElementById('personalInfoForm');
    const inputs = personalForm.querySelectorAll('input, select');

    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });

        input.addEventListener('input', function() {
            if (this.classList.contains('error')) {
                validateField(this);
            }
        });
    });

    
    const transmissionCards = document.querySelectorAll('.transmission-card');
    transmissionCards.forEach(card => {
        card.addEventListener('click', function() {
            const value = this.dataset.value;
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
            updateTransmissionCard(value);
        });
    });
}

function validateField(field) {
    const value = field.value.trim();
    const fieldName = field.name;
    const errorElement = field.closest('.form-group').querySelector('.error-message');
    const validationIcon = field.closest('.input-container').querySelector('.validation-icon');
    let isValid = true;
    let errorMessage = '';

    switch (fieldName) {
        case 'fullName':
            if (value.length < 3) {
                isValid = false;
                errorMessage = 'Please enter a valid full name';
            }
            break;
        case 'dateOfBirth':
            if (!value) {
                isValid = false;
                errorMessage = 'Please select your date of birth';
            } else {
                const age = calculateAge(value);
                if (age < 18) {
                    isValid = false;
                    errorMessage = 'You must be at least 18 years old';
                }
            }
            break;
        case 'gender':
            if (!value) {
                isValid = false;
                errorMessage = 'Please select your gender';
            }
            break;
        case 'district':
            if (!value) {
                isValid = false;
                errorMessage = 'Please select your district';
            }
            break;
    }

    
    if (isValid) {
        field.classList.remove('error');
        field.classList.add('valid');
        validationIcon.innerHTML = '✓';
        validationIcon.className = 'validation-icon valid';
    } else {
        field.classList.remove('valid');
        field.classList.add('error');
        validationIcon.innerHTML = '✗';
        validationIcon.className = 'validation-icon error';
    }

    errorElement.textContent = errorMessage;
    return isValid;
}

function updateTransmissionCard(value) {
    const cards = document.querySelectorAll('.transmission-card');
    cards.forEach(card => {
        card.classList.remove('selected');
        if (card.dataset.value === value) {
            card.classList.add('selected');
        }
    });
}

function calculateAge(dateString) {
    const today = new Date();
    const birthDate = new Date(dateString);
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    
    return age;
}

function validateCurrentStep(step) {
    switch (step) {
        case 1:
            return validatePersonalInfo();
        case 2:
            return validateDocuments();
        case 3:
            return validatePayment();
        default:
            return true;
    }
}

function validatePersonalInfo() {
    const form = document.getElementById('personalInfoForm');
    const inputs = form.querySelectorAll('input[required], select[required]');
    let allValid = true;

    inputs.forEach(input => {
        if (!validateField(input)) {
            allValid = false;
        }
    });

    
    const transmissionSelected = form.querySelector('input[name="transmissionType"]:checked');
    if (!transmissionSelected) {
        LicenseXpress.showToast('Please select transmission type', 'error');
        allValid = false;
    }

    return allValid;
}

function validateDocuments() {
    const applicationState = JSON.parse(localStorage.getItem('applicationState') || '{}');
    const documents = applicationState.documents || {};
    const requiredDocs = ['birthCertificate', 'nicCopy', 'medicalCertificate', 'photo'];
    
    const uploadedDocs = requiredDocs.filter(doc => documents[doc]?.uploaded);
    
    if (uploadedDocs.length !== requiredDocs.length) {
        LicenseXpress.showToast('Please upload all required documents', 'error');
        return false;
    }

    return true;
}

function validatePayment() {
    const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked');
    const termsAccepted = document.getElementById('terms').checked;

    if (!paymentMethod) {
        LicenseXpress.showToast('Please select a payment method', 'error');
        return false;
    }

    if (!termsAccepted) {
        LicenseXpress.showToast('Please accept the terms and conditions', 'error');
        return false;
    }

    return true;
}

function saveCurrentStepData(step) {
    switch (step) {
        case 1:
            savePersonalInfo();
            break;
        case 2:
            
            break;
        case 3:
            savePaymentInfo();
            break;
    }
}

function savePersonalInfo() {
    const form = document.getElementById('personalInfoForm');
    const formData = new FormData(form);
    
    const userProfile = {
        fullName: formData.get('fullName'),
        nic: document.getElementById('nic').value.replace(/\s/g, ''),
        dateOfBirth: formData.get('dateOfBirth'),
        gender: formData.get('gender'),
        transmissionType: formData.get('transmissionType'),
        district: formData.get('district')
    };

    localStorage.setItem('userProfile', JSON.stringify(userProfile));
}

function savePaymentInfo() {
    const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked');
    
    if (paymentMethod) {
        const applicationState = JSON.parse(localStorage.getItem('applicationState') || '{}');
        
        
        if (!applicationState.payment) {
            applicationState.payment = {};
        }
        
        applicationState.payment.method = paymentMethod.value;
        localStorage.setItem('applicationState', JSON.stringify(applicationState));
    }
}


function submitApplication() {
    console.log('Submitting application...');
    
    
    const currentUser = LicenseXpress.getCurrentUser();
    const userProfile = JSON.parse(localStorage.getItem('userProfile') || '{}');
    const applicationState = JSON.parse(localStorage.getItem('applicationState') || '{}');
    
    
    const applicationData = {
        userId: currentUser.userId,
        fullName: userProfile.fullName,
        nic: userProfile.nic,
        dateOfBirth: userProfile.dateOfBirth,
        gender: userProfile.gender,
        transmissionType: userProfile.transmissionType,
        district: userProfile.district,
        documents: applicationState.documents || {},
        payment: applicationState.payment || {},
        status: 'pending_verification',
        submittedDate: new Date().toISOString(),
        applicationId: 'APP' + Date.now()
    };
    
    
    const applications = JSON.parse(localStorage.getItem('applications') || '[]');
    applications.push(applicationData);
    localStorage.setItem('applications', JSON.stringify(applications));
    
    
    applicationState.status = 'pending_verification';
    applicationState.submittedDate = new Date().toISOString();
    applicationState.applicationId = applicationData.applicationId;
    localStorage.setItem('applicationState', JSON.stringify(applicationState));
    
    
    alert('Application submitted successfully! Application ID: ' + applicationData.applicationId);
    
    
    window.location.href = 'dashboard.php';
}

function initializeNavigation() {
    
    const nextStep1 = document.getElementById('nextStep1');
    const backStep2 = document.getElementById('backStep2');
    const nextStep2 = document.getElementById('nextStep2');
    const backStep3 = document.getElementById('backStep3');

    if (nextStep1) nextStep1.addEventListener('click', nextStep);
    if (backStep2) backStep2.addEventListener('click', backStep);
    if (nextStep2) nextStep2.addEventListener('click', nextStep);
    if (backStep3) backStep3.addEventListener('click', backStep);

    
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const step = parseInt(this.dataset.step);
            goToStep(step);
        });
    });

    
    const submitBtn = document.getElementById('submitApplication');
    if (submitBtn) submitBtn.addEventListener('click', submitApplication);

    
    const closeModal = document.getElementById('closeModal');
    const modalClose = document.querySelector('.modal-close');
    const goToDashboard = document.getElementById('goToDashboard');
    
    if (closeModal) closeModal.addEventListener('click', closeImageModal);
    if (modalClose) modalClose.addEventListener('click', closeImageModal);
    if (goToDashboard) {
        goToDashboard.addEventListener('click', function() {
            window.location.href = 'dashboard.php';
        });
    }

    
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function() {
            LicenseXpress.logout();
        });
    }
}

function submitApplication() {
    console.log('Submit button clicked!');
    
    
    const currentUser = LicenseXpress.getCurrentUser();
    const userProfile = JSON.parse(localStorage.getItem('userProfile') || '{}');
    const applicationState = JSON.parse(localStorage.getItem('applicationState') || '{}');
    
    
    const applicationData = {
        userId: currentUser.userId,
        fullName: userProfile.fullName,
        nic: userProfile.nic,
        dateOfBirth: userProfile.dateOfBirth,
        gender: userProfile.gender,
        transmissionType: userProfile.transmissionType,
        district: userProfile.district,
        documents: applicationState.documents || {},
        payment: applicationState.payment || {},
        status: 'pending_verification',
        submittedDate: new Date().toISOString(),
        applicationId: 'APP' + Date.now()
    };
    
    
    const applications = JSON.parse(localStorage.getItem('applications') || '[]');
    applications.push(applicationData);
    localStorage.setItem('applications', JSON.stringify(applications));
    
    
    applicationState.status = 'pending_verification';
    applicationState.submittedDate = new Date().toISOString();
    applicationState.applicationId = applicationData.applicationId;
    localStorage.setItem('applicationState', JSON.stringify(applicationState));
    
    
    alert('Application submitted successfully! Application ID: ' + applicationData.applicationId);
    
    
    window.location.href = 'dashboard.php';
}

function simulateProcessing() {
    const progressFill = document.getElementById('loadingProgressFill');
    const progressText = document.getElementById('loadingProgressText');
    const statusText = document.getElementById('loadingStatus');

    const steps = [
        { progress: 20, status: 'Validating personal information...' },
        { progress: 40, status: 'Checking document uploads...' },
        { progress: 60, status: 'Processing payment...' },
        { progress: 80, status: 'Creating application record...' },
        { progress: 90, status: 'Sending confirmations...' },
        { progress: 100, status: 'Complete!' }
    ];

    let currentStep = 0;
    const interval = setInterval(() => {
        if (currentStep < steps.length) {
            const step = steps[currentStep];
            progressFill.style.width = step.progress + '%';
            progressText.textContent = step.progress + '%';
            statusText.textContent = step.status;
            currentStep++;
        } else {
            clearInterval(interval);
            completeSubmission();
        }
    }, 800);
}

function completeSubmission() {
    
    const applicationState = JSON.parse(localStorage.getItem('applicationState') || '{}');
    applicationState.status = 'pending_verification';
    applicationState.progress = 25;
    applicationState.submittedDate = new Date().toISOString();
    applicationState.verificationDueDate = new Date(Date.now() + 48*60*60*1000).toISOString();
    applicationState.applicationId = 'LX-2025-' + LicenseXpress.generateId();
    
    
    if (!applicationState.payment) {
        applicationState.payment = {};
    }
    
    applicationState.payment.status = 'completed';
    applicationState.payment.transactionId = LicenseXpress.generateTransactionId();
    applicationState.payment.paidDate = new Date().toISOString();

    localStorage.setItem('applicationState', JSON.stringify(applicationState));

    
    const loadingOverlay = document.getElementById('loadingOverlay');
    loadingOverlay.classList.add('hidden');

    
    const successModal = document.getElementById('successModal');
    const applicationId = document.getElementById('applicationId');
    const confirmationEmail = document.getElementById('confirmationEmail');
    const confirmationPhone = document.getElementById('confirmationPhone');

    applicationId.textContent = applicationState.applicationId;
    confirmationEmail.textContent = LicenseXpress.getCurrentUser()?.email || 'user@example.com';
    confirmationPhone.textContent = LicenseXpress.getCurrentUser()?.phone || '+94 77 123 4567';

    successModal.classList.remove('hidden');

    
    LicenseXpress.sendEmailNotification(
        LicenseXpress.getCurrentUser()?.email || 'user@example.com',
        'Application Submitted Successfully',
        'Your license application has been received and is being processed.'
    );

    LicenseXpress.sendSMSNotification(
        LicenseXpress.getCurrentUser()?.phone || '+94771234567',
        'Your license application has been submitted successfully. Application ID: ' + applicationState.applicationId
    );
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}


function updateSummaryContent() {
    const userProfile = JSON.parse(localStorage.getItem('userProfile') || '{}');
    const applicationState = JSON.parse(localStorage.getItem('applicationState') || '{}');
    const documents = applicationState.documents || {};

    
    const personalInfoSummary = document.getElementById('personalInfoSummary');
    personalInfoSummary.innerHTML = `
        <div class="summary-item">
            <div class="summary-label">Full Name</div>
            <div class="summary-value">${userProfile.fullName || ''}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">NIC Number</div>
            <div class="summary-value">${LicenseXpress.formatNIC(userProfile.nic || '')}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Date of Birth</div>
            <div class="summary-value">${LicenseXpress.formatDate(userProfile.dateOfBirth || '')}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Gender</div>
            <div class="summary-value">${userProfile.gender || ''}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Transmission</div>
            <div class="summary-value">${userProfile.transmissionType || ''}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">District</div>
            <div class="summary-value">${userProfile.district || ''}</div>
        </div>
    `;

    
    const documentsSummary = document.getElementById('documentsSummary');
    const documentNames = {
        birthCertificate: 'Birth Certificate',
        nicCopy: 'NIC Copy',
        medicalCertificate: 'Medical Certificate',
        photo: 'Passport Photo'
    };

    documentsSummary.innerHTML = Object.keys(documents).map(docType => {
        const doc = documents[docType];
        if (doc.uploaded) {
            return `
                <div class="summary-item">
                    <div class="summary-label">${documentNames[docType]}</div>
                    <div class="summary-value">✓ ${doc.fileName}</div>
                </div>
            `;
        }
        return '';
    }).join('');
}


const originalNextStep = window.nextStep;
window.nextStep = function() {
    if (validateCurrentStep(currentStep)) {
        if (currentStep < 3) {
            currentStep++;
            updateSteps(currentStep);
            saveCurrentStepData(currentStep - 1);
            
            
            if (currentStep === 3) {
                updateSummaryContent();
            }
        }
    }
};


function submitApplicationToDatabase() {
    console.log('Submitting application to database...');
    
    
    const currentUser = LicenseXpress.getCurrentUser();
    console.log('Current user:', currentUser);
    
    
    const userProfile = JSON.parse(localStorage.getItem('userProfile') || '{}');
    const applicationState = JSON.parse(localStorage.getItem('applicationState') || '{}');
    
    console.log('User profile:', userProfile);
    console.log('Application state:', applicationState);
    
    
    const applicationData = {
        fullName: userProfile.fullName || currentUser.fullName || 'Test User',
        nic: userProfile.nic || currentUser.nic || '200012345678',
        dateOfBirth: userProfile.dateOfBirth || '2000-01-01',
        gender: userProfile.gender || 'Male',
        transmissionType: userProfile.transmissionType || 'Manual',
        district: userProfile.district || 'Colombo',
        tempApplicationId: applicationState.tempApplicationId || null,
        documents: applicationState.documents || {}
    };
    
    
    const requiredDocs = ['birthCertificate', 'nicCopy', 'medicalCertificate', 'photo'];
    const uploadedDocs = requiredDocs.filter(docType => 
        applicationState.documents && 
        applicationState.documents[docType] && 
        applicationState.documents[docType].uploaded
    );
    
    if (uploadedDocs.length !== requiredDocs.length) {
        alert('Please upload all required documents before submitting.');
        return;
    }
    
    
    const missingDocs = requiredDocs.filter(docType => 
        !applicationState.documents || 
        !applicationState.documents[docType] || 
        !applicationState.documents[docType].uploaded
    );
    
    if (missingDocs.length > 0) {
        alert(`Please upload the following documents: ${missingDocs.join(', ')}`);
        return;
    }
    
    console.log('Submitting to database:', applicationData);
    console.log('Documents being sent:', applicationData.documents);
    
    
    const submitBtn = document.querySelector('.btn-submit');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnSpinner = submitBtn.querySelector('.btn-spinner');
    
    btnText.textContent = 'Submitting...';
    btnSpinner.classList.remove('hidden');
    submitBtn.disabled = true;
    
    
    fetch('submit_application.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(applicationData)
    })
    .then(response => {
        
        return response.text().then(text => {
            console.log('Raw PHP response:', text);
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('JSON parse error:', e);
                console.error('Response text:', text);
                throw new Error('Invalid JSON response from server');
            }
        });
    })
    .then(data => {
        console.log('Database response:', data);
        
        if (data.success) {
            
            const updatedApplicationState = {
                ...applicationState,
                status: 'pending_verification',
                submittedDate: new Date().toISOString(),
                applicationId: data.applicationId,
                applicationDbId: data.applicationDbId,
                userId: data.userId
            };
            localStorage.setItem('applicationState', JSON.stringify(updatedApplicationState));
            
            
            updateUserStatus('pending_verification', data.applicationId);
            
            
            if (updatedApplicationState.documents) {
                Object.keys(updatedApplicationState.documents).forEach(docType => {
                    if (updatedApplicationState.documents[docType].fileData) {
                        delete updatedApplicationState.documents[docType].fileData;
                    }
                });
                localStorage.setItem('applicationState', JSON.stringify(updatedApplicationState));
            }
            
            
            alert('Application submitted successfully! Application ID: ' + data.applicationId);
            window.location.href = 'dashboard.php';
        } else {
            throw new Error(data.error || 'Submission failed');
        }
    })
    .catch(error => {
        console.error('Submission error:', error);
        
        
        const submitBtn = document.querySelector('.btn-submit');
        const btnText = submitBtn.querySelector('.btn-text');
        const btnSpinner = submitBtn.querySelector('.btn-spinner');
        
        btnText.textContent = '💳 Submit Application & Pay Rs. 3,200';
        btnSpinner.classList.add('hidden');
        submitBtn.disabled = false;
        
        alert('Error submitting application: ' + error.message + '\n\nCheck browser console for details (F12)');
    });
}

