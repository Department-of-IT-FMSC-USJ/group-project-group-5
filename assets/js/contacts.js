

document.addEventListener('DOMContentLoaded', function() {
    
    initializeContact();
});

function initializeContact() {
    
    initializeFormValidation();

    
    initializeFormSubmission();

    
    initializeLiveChat();
}

function initializeFormValidation() {
    const form = document.getElementById('contactForm');
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');

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
    let isValid = true;
    let errorMessage = '';

    switch (fieldName) {
        case 'firstName':
        case 'lastName':
            if (value.length < 2) {
                isValid = false;
                errorMessage = 'Name must be at least 2 characters';
            }
            break;
        case 'email':
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid email address';
            }
            break;
        case 'phone':
            if (value && !/^[\+]?[0-9\s\-\(\)]{10,}$/.test(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid phone number';
            }
            break;
        case 'subject':
            if (!value) {
                isValid = false;
                errorMessage = 'Please select a subject';
            }
            break;
        case 'message':
            if (value.length < 10) {
                isValid = false;
                errorMessage = 'Message must be at least 10 characters';
            }
            break;
    }

    
    if (isValid) {
        field.classList.remove('error');
        field.classList.add('valid');
    } else {
        field.classList.remove('valid');
        field.classList.add('error');
    }

    
    let errorElement = field.parentElement.querySelector('.error-message');
    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'error-message';
        field.parentElement.appendChild(errorElement);
    }

    errorElement.textContent = errorMessage;
    return isValid;
}

function initializeFormSubmission() {
    const form = document.getElementById('contactForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnSpinner = submitBtn.querySelector('.btn-spinner');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
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

        
        const formData = new FormData(form);
        const data = {
            firstName: formData.get('firstName'),
            lastName: formData.get('lastName'),
            email: formData.get('email'),
            phone: formData.get('phone'),
            subject: formData.get('subject'),
            message: formData.get('message'),
            newsletter: formData.get('newsletter') === 'on'
        };

       
        submitBtn.disabled = true;
        btnText.textContent = 'Sending...';
        btnSpinner.classList.remove('hidden');

        
        setTimeout(() => {
            processContactForm(data);
            
           
            submitBtn.disabled = false;
            btnText.textContent = 'Send Message';
            btnSpinner.classList.add('hidden');
        }, 2000);
    });
}

function processContactForm(data) {
    
    console.log('Contact form data:', data);
    
    
    LicenseXpress.showToast('✅ Message sent successfully! We\'ll get back to you within 24 hours.', 'success');
    
   
    document.getElementById('contactForm').reset();
    
    
    logContactSubmission(data);
}

function logContactSubmission(data) {
    
    const submissions = JSON.parse(localStorage.getItem('contactSubmissions') || '[]');
    
    submissions.push({
        ...data,
        timestamp: new Date().toISOString(),
        id: 'contact_' + Date.now()
    });

    
    if (submissions.length > 100) {
        submissions.splice(0, submissions.length - 100);
    }

    localStorage.setItem('contactSubmissions', JSON.stringify(submissions));
}

function initializeLiveChat() {
  
    window.openLiveChat = function() {
        
        LicenseXpress.showToast('💬 Live chat is coming soon! Please use email or phone support for now.', 'info');
    };
}


const style = document.createElement('style');
style.textContent = `
    .form-input.error,
    .form-select.error,
    .form-textarea.error {
        border-color: var(--error);
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
    
    .form-input.valid,
    .form-select.valid,
    .form-textarea.valid {
        border-color: var(--success);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
    
    .error-message {
        color: var(--error);
        font-size: 14px;
        margin-top: 8px;
        min-height: 20px;
    }
`;
document.head.appendChild(style);


document.addEventListener('keydown', function(e) {
    
    if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
        const form = document.getElementById('contactForm');
        if (form.contains(e.target)) {
            e.preventDefault();
            form.dispatchEvent(new Event('submit'));
        }
    }
});


function trackFormInteraction(action, field = null) {
    
    console.log(`Contact form ${action}${field ? ` on ${field}` : ''}`);
}


document.getElementById('contactForm').addEventListener('input', function(e) {
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT' || e.target.tagName === 'TEXTAREA') {
        trackFormInteraction('input', e.target.name);
    }
});

document.getElementById('contactForm').addEventListener('submit', function() {
    trackFormInteraction('submit');
});
