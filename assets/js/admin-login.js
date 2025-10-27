// ═══════════════════════════════════════════════════════════════════════════════
// ADMIN LOGIN JAVASCRIPT
// ═══════════════════════════════════════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', function() {
    // Don't redirect - allow viewing login page even if authenticated
    // This allows logout to work properly
    
    // Initialize admin login
    initializeAdminLogin();
});

function initializeAdminLogin() {
    // Initialize form validation
    initializeFormValidation();

    // Initialize password toggle
    initializePasswordToggle();

    // Initialize form submission
    initializeFormSubmission();
}

function initializeFormValidation() {
    const form = document.getElementById('adminLoginForm');
    const inputs = form.querySelectorAll('input[required]');

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
}

function validateField(field) {
    const value = field.value.trim();
    const fieldName = field.name;
    const errorElement = field.closest('.form-group').querySelector('.error-message');
    const validationIcon = field.closest('.input-container').querySelector('.validation-icon');
    let isValid = true;
    let errorMessage = '';

    switch (fieldName) {
        case 'username':
            if (value.length < 3) {
                isValid = false;
                errorMessage = 'Username must be at least 3 characters';
            }
            break;
        case 'password':
            if (value.length < 6) {
                isValid = false;
                errorMessage = 'Password must be at least 6 characters';
            }
            break;
    }

    // Update UI
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

function initializePasswordToggle() {
    const passwordToggle = document.getElementById('passwordToggle');
    const passwordInput = document.getElementById('password');
    const toggleIcon = passwordToggle.querySelector('.toggle-icon');

    passwordToggle.addEventListener('click', function() {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        toggleIcon.textContent = isPassword ? '🙈' : '👁️';
    });
}

function initializeFormSubmission() {
    const form = document.getElementById('adminLoginForm');
    const loginBtn = document.getElementById('loginBtn');
    const btnText = loginBtn.querySelector('.btn-text');
    const btnSpinner = loginBtn.querySelector('.btn-spinner');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validate all fields
        const inputs = form.querySelectorAll('input[required]');
        let allValid = true;

        inputs.forEach(input => {
            if (!validateField(input)) {
                allValid = false;
            }
        });

        if (!allValid) {
            LicenseXpress.showToast('Please fix the errors above', 'error');
            return;
        }

        // Get form data
        const formData = new FormData(form);
        const username = formData.get('username');
        const password = formData.get('password');
        const rememberMe = formData.get('rememberMe') === 'on';

        // Show loading state
        loginBtn.disabled = true;
        btnText.textContent = 'Signing In...';
        btnSpinner.classList.remove('hidden');

        // Simulate login attempt
        setTimeout(() => {
            attemptAdminLogin(username, password, rememberMe);
        }, 1500);
    });
}

function attemptAdminLogin(username, password, rememberMe) {
    // Check admin credentials
    const adminData = JSON.parse(localStorage.getItem('adminData') || '{}');
    
    // Default admin credentials (in production, this would be server-side)
    const defaultAdmin = {
        username: 'admin',
        password: 'admin123'
    };

    const isValidCredentials = 
        (username === defaultAdmin.username && password === defaultAdmin.password) ||
        (adminData.username === username && adminData.password === password);

    if (isValidCredentials) {
        // Successful login
        handleSuccessfulLogin(username, rememberMe);
    } else {
        // Failed login
        handleFailedLogin();
    }
}

function handleSuccessfulLogin(username, rememberMe) {
    // Set admin authentication
    localStorage.setItem('isAdminAuthenticated', 'true');
    localStorage.setItem('adminUsername', username);
    
    if (rememberMe) {
        const expiryDate = new Date();
        expiryDate.setDate(expiryDate.getDate() + 7);
        localStorage.setItem('adminAuthExpiry', expiryDate.toISOString());
    }

    // Log admin login
    logAdminActivity('login', username);

    // Show success message
    LicenseXpress.showToast('✅ Admin login successful!', 'success');

    // Redirect to admin dashboard
    setTimeout(() => {
        window.location.href = 'admin-dashboard.php';
    }, 1000);
}

function handleFailedLogin() {
    const loginBtn = document.getElementById('loginBtn');
    const btnText = loginBtn.querySelector('.btn-text');
    const btnSpinner = loginBtn.querySelector('.btn-spinner');
    const form = document.getElementById('adminLoginForm');

    // Reset button state
    loginBtn.disabled = false;
    btnText.textContent = 'Sign In to Admin Panel';
    btnSpinner.classList.add('hidden');

    // Add shake animation
    form.classList.add('shake');
    setTimeout(() => {
        form.classList.remove('shake');
    }, 500);

    // Show error message
    LicenseXpress.showToast('❌ Invalid username or password', 'error');

    // Clear form
    form.reset();
    
    // Log failed attempt
    logAdminActivity('failed_login_attempt', 'unknown');
}

function logAdminActivity(action, username) {
    const activityLog = JSON.parse(localStorage.getItem('adminActivityLog') || '[]');
    
    activityLog.push({
        action: action,
        username: username,
        timestamp: new Date().toISOString(),
        ip: '127.0.0.1', // In production, get real IP
        userAgent: navigator.userAgent
    });

    // Keep only last 100 activities
    if (activityLog.length > 100) {
        activityLog.splice(0, activityLog.length - 100);
    }

    localStorage.setItem('adminActivityLog', JSON.stringify(activityLog));
}

// Extend LicenseXpress with admin authentication
if (typeof LicenseXpress === 'undefined') {
    window.LicenseXpress = {};
}

LicenseXpress.checkAdminAuth = function() {
    const isAuthenticated = localStorage.getItem('isAdminAuthenticated');
    const expiryDate = localStorage.getItem('adminAuthExpiry');
    
    if (!isAuthenticated) {
        return false;
    }

    // Check if session has expired
    if (expiryDate) {
        const now = new Date();
        const expiry = new Date(expiryDate);
        
        if (now > expiry) {
            localStorage.removeItem('isAdminAuthenticated');
            localStorage.removeItem('adminUsername');
            localStorage.removeItem('adminAuthExpiry');
            return false;
        }
    }

    return true;
};

LicenseXpress.getCurrentAdmin = function() {
    const username = localStorage.getItem('adminUsername');
    if (!username) return null;
    
    return {
        username: username,
        role: 'admin',
        loginTime: localStorage.getItem('adminLoginTime')
    };
};

LicenseXpress.adminLogout = function() {
    localStorage.removeItem('isAdminAuthenticated');
    localStorage.removeItem('adminUsername');
    localStorage.removeItem('adminAuthExpiry');
    localStorage.removeItem('adminLoginTime');
    
    // Log logout
    logAdminActivity('logout', 'admin');
    
    window.location.href = 'admin-login.php';
};
