

function checkAuth() {
    const currentUser = localStorage.getItem('currentUser');
    const currentPath = window.location.pathname;
    
    
    const publicPages = ['index.php', 'register.php', 'login.php', 'admin-login.php', 'pages/'];
    const isPublicPage = publicPages.some(page => currentPath.includes(page));
    
    if (!currentUser && !isPublicPage) {
        window.location.href = 'login.php';
        return false;
    }
    
    return true;
}

function login(nic, password) {
    
    const users = JSON.parse(localStorage.getItem('users') || '[]');
    const user = users.find(u => u.nic === nic && u.password === password);
    
    if (user) {
        localStorage.setItem('isAuthenticated', 'true');
        localStorage.setItem('currentUserId', user.userId);
        return true;
    }
    return false;
}

function logout() {
    
    window.location.href = 'logout.php';
}

function getCurrentUser() {
    
    const currentUser = localStorage.getItem('currentUser');
    if (!currentUser) return null;
    
    try {
        return JSON.parse(currentUser);
    } catch (e) {
        console.error('Error parsing currentUser from localStorage:', e);
        return null;
    }
}



function initializeUserData() {
    const currentUser = getCurrentUser();
    if (!currentUser) return;
    
    
    if (!localStorage.getItem('userProfile')) {
        const userProfile = {
            fullName: currentUser.fullName || '',
            nic: currentUser.nic,
            dateOfBirth: '',
            gender: '',
            email: currentUser.email || '',
            phone: currentUser.phone || '',
            district: '',
            transmissionType: ''
        };
        localStorage.setItem('userProfile', JSON.stringify(userProfile));
    }
    
  
    if (!localStorage.getItem('applicationState')) {
        const applicationState = {
            status: 'not_started',
            progress: 0,
            submittedDate: null,
            verificationDueDate: null,
            verifiedDate: null,
            rejectionReason: null,
            rejectedDocuments: [],
            documents: {
                birthCertificate: { fileName: '', fileData: '', uploaded: false },
                nicCopy: { fileName: '', fileData: '', uploaded: false },
                medicalCertificate: { fileName: '', fileData: '', uploaded: false },
                photo: { fileName: '', fileData: '', uploaded: false }
            },
            payment: {
                amount: 3200,
                method: '',
                transactionId: '',
                paidDate: null,
                status: 'pending'
            }
        };
        localStorage.setItem('applicationState', JSON.stringify(applicationState));
    }
    
    
    if (!localStorage.getItem('tests')) {
        const tests = {
            theory: {
                scheduled: false,
                date: null,
                time: null,
                testLink: null,
                score: null,
                passed: false,
                passedDate: null,
                attempts: 0
            },
            practical: {
                scheduled: false,
                date: null,
                time: null,
                center: null,
                address: null,
                vehicle: null,
                examiner: null,
                passed: false,
                passedDate: null
            }
        };
        localStorage.setItem('tests', JSON.stringify(tests));
    }
    
    
    if (!localStorage.getItem('license')) {
        const license = {
            issued: false,
            number: null,
            issueDate: null,
            expiryDate: null,
            category: 'B1',
            downloadUrl: null
        };
        localStorage.setItem('license', JSON.stringify(license));
    }
}


function generateId() {
    return Math.random().toString(36).substr(2, 9);
}

function generateTransactionId() {
    return 'TXN-' + Date.now() + '-' + Math.random().toString(36).substr(2, 6).toUpperCase();
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function formatDateTime(date) {
    return new Date(date).toLocaleString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatNIC(nic) {
    if (!nic) return '';
    return nic.replace(/(\d{4})(\d{4})(\d{4})/, '$1 $2 $3');
}

function formatPhone(phone) {
    if (!phone) return '';
    return phone.replace(/(\+94)(\d{2})(\d{3})(\d{4})/, '$1 $2 $3 $4');
}



function showToast(message, type = 'info', duration = 3000) {
    
    const existingToasts = document.querySelectorAll('.toast');
    existingToasts.forEach(toast => toast.remove());
    
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    
   
    const icon = type === 'success' ? '✅' : 
                 type === 'error' ? '❌' : 
                 type === 'warning' ? '⚠️' : 'ℹ️';
    toast.innerHTML = `${icon} ${message}`;
    
    document.body.appendChild(toast);
    
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(-50%) translateY(20px)';
        setTimeout(() => toast.remove(), 300);
    }, duration);
}



function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validateNIC(nic) {
    
    const cleanNIC = nic.replace(/\s/g, '');
    if (cleanNIC.length === 0) return false;
    
   
    if (!/^\d{6,15}$/.test(cleanNIC)) return false;
    
    return true;
}

function validatePhone(phone) {
    const cleanPhone = phone.replace(/\s/g, '');
    return /^\+94\d{9}$/.test(cleanPhone);
}

function validatePassword(password) {
    const minLength = 8;
    const hasUpper = /[A-Z]/.test(password);
    const hasLower = /[a-z]/.test(password);
    const hasNumber = /\d/.test(password);
    const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);
    
    if (password.length < minLength) return { valid: false, strength: 'weak', message: 'Password must be at least 8 characters' };
    if (!hasUpper || !hasLower || !hasNumber) return { valid: false, strength: 'medium', message: 'Password must contain uppercase, lowercase, and number' };
    if (!hasSpecial) return { valid: true, strength: 'strong', message: 'Strong password' };
    return { valid: true, strength: 'very-strong', message: 'Very strong password' };
}



function validateFile(file, maxSize = 5 * 1024 * 1024, allowedTypes = ['image/jpeg', 'image/png', 'application/pdf']) {
    const errors = [];
    
    if (file.size > maxSize) {
        errors.push('File size must be less than 5MB');
    }
    
    if (!allowedTypes.includes(file.type)) {
        errors.push('File must be JPG, PNG, or PDF');
    }
    
    return {
        valid: errors.length === 0,
        errors: errors
    };
}

function convertToBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = error => reject(error);
    });
}



function updateProgress(progress) {
    const applicationState = JSON.parse(localStorage.getItem('applicationState') || '{}');
    applicationState.progress = progress;
    localStorage.setItem('applicationState', JSON.stringify(applicationState));
}

function getProgressStatus() {
    const applicationState = JSON.parse(localStorage.getItem('applicationState') || '{}');
    return {
        status: applicationState.status || 'not_started',
        progress: applicationState.progress || 0
    };
}



function sendEmailNotification(to, subject, message) {

    console.log(`Email sent to ${to}: ${subject} - ${message}`);
    return true;
}

function sendSMSNotification(phone, message) {
    
    console.log(`SMS sent to ${phone}: ${message}`);
    return true;
}



function animateProgressBar(element, targetPercent) {
    const progressBar = element.querySelector('.progress-fill');
    if (!progressBar) return;
    
    let currentPercent = 0;
    const increment = targetPercent / 100;
    
    const timer = setInterval(() => {
        currentPercent += increment;
        if (currentPercent >= targetPercent) {
            currentPercent = targetPercent;
            clearInterval(timer);
        }
        progressBar.style.width = currentPercent + '%';
    }, 20);
}

function createConfetti() {
    const colors = ['#005F73', '#0A9396', '#94D3A2', '#EE9B00', '#F9C74F'];
    const confettiCount = 100;
    
    for (let i = 0; i < confettiCount; i++) {
        const confetti = document.createElement('div');
        confetti.style.position = 'fixed';
        confetti.style.width = '10px';
        confetti.style.height = '10px';
        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.left = Math.random() * 100 + 'vw';
        confetti.style.top = '-10px';
        confetti.style.borderRadius = '50%';
        confetti.style.pointerEvents = 'none';
        confetti.style.zIndex = '10000';
        
        document.body.appendChild(confetti);
        
        
        const animation = confetti.animate([
            { transform: 'translateY(0px) rotate(0deg)', opacity: 1 },
            { transform: `translateY(100vh) rotate(360deg)`, opacity: 0 }
        ], {
            duration: 3000 + Math.random() * 2000,
            easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)'
        });
        
        animation.onfinish = () => confetti.remove();
    }
}



function showModal(title, content, actions = []) {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">${title}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                ${content}
            </div>
            <div class="modal-footer">
                ${actions.map(action => `
                    <button class="btn ${action.class || 'btn-secondary'}" data-action="${action.action}">
                        ${action.text}
                    </button>
                `).join('')}
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
   
    modal.querySelector('.modal-close').addEventListener('click', () => closeModal(modal));
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal(modal);
    });
    
    actions.forEach(action => {
        const button = modal.querySelector(`[data-action="${action.action}"]`);
        if (button) {
            button.addEventListener('click', () => {
                if (action.handler) action.handler();
                closeModal(modal);
            });
        }
    });
    
    
    setTimeout(() => modal.classList.add('modal-show'), 10);
}

function closeModal(modal) {
    modal.classList.remove('modal-show');
    setTimeout(() => modal.remove(), 300);
}



document.addEventListener('DOMContentLoaded', function() {
    
    checkAuth();
    
   
    initializeUserData();
   
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            const nav = document.querySelector('.nav');
            nav.classList.toggle('nav-open');
            
            
            const spans = this.querySelectorAll('span');
            spans.forEach((span, index) => {
                if (nav.classList.contains('nav-open')) {
                    if (index === 0) span.style.transform = 'rotate(45deg) translate(5px, 5px)';
                    if (index === 1) span.style.opacity = '0';
                    if (index === 2) span.style.transform = 'rotate(-45deg) translate(7px, -6px)';
                } else {
                    span.style.transform = '';
                    span.style.opacity = '';
                }
            });
        });
        
       
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                const nav = document.querySelector('.nav');
                if (nav.classList.contains('nav-open')) {
                    nav.classList.remove('nav-open');
                    const spans = mobileMenuToggle.querySelectorAll('span');
                    spans.forEach(span => {
                        span.style.transform = '';
                        span.style.opacity = '';
                    });
                }
            });
        });
    }
    
    
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});



window.LicenseXpress = {
    checkAuth,
    login,
    logout,
    getCurrentUser,
    initializeUserData,
    generateId,
    generateTransactionId,
    formatDate,
    formatDateTime,
    formatNIC,
    formatPhone,
    showToast,
    validateEmail,
    validateNIC,
    validatePhone,
    validatePassword,
    validateFile,
    convertToBase64,
    updateProgress,
    getProgressStatus,
    sendEmailNotification,
    sendSMSNotification,
    animateProgressBar,
    createConfetti,
    showModal,
    closeModal
};


