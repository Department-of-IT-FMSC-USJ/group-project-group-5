<?php
session_start();


require_once 'database/database_connection.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        $db = new Database();
        
        
        $fullName = trim($_POST['fullName'] ?? '');
        $nic = trim(str_replace(' ', '', $_POST['nic'] ?? ''));
        $email = trim($_POST['email'] ?? '');
        $phone = trim(str_replace(' ', '', $_POST['phone'] ?? ''));
        $password = $_POST['password'] ?? '';
        
        
        if (empty($fullName) || empty($nic) || empty($email) || empty($phone) || empty($password)) {
            echo json_encode(['success' => false, 'error' => 'All fields are required']);
            exit;
        }
        
        
        $sql = "SELECT id FROM users WHERE nic = :nic OR email = :email LIMIT 1";
        $existingUser = $db->fetch($sql, ['nic' => $nic, 'email' => $email]);
        
        if ($existingUser) {
            echo json_encode(['success' => false, 'error' => 'User with this NIC or email already exists']);
            exit;
        }
        
        
        $userData = [
            'user_id' => 'USER' . time() . rand(1000, 9999),
            'nic' => $nic,
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'date_of_birth' => '2000-01-01', 
            'gender' => 'Male', 
            'district' => 'Colombo', 
            'transmission_type' => 'Manual', 
            'registration_date' => date('Y-m-d H:i:s')
        ];
        
        $userId = $db->insert('users', $userData);
        
        
        $_SESSION['user_id'] = $userId;
        $_SESSION['nic'] = $nic;
        $_SESSION['full_name'] = $fullName;
        $_SESSION['email'] = $email;
        
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful',
            'user' => [
                'userId' => $userData['user_id'],
                'fullName' => $fullName,
                'nic' => $nic,
                'email' => $email,
                'phone' => $phone
            ]
        ]);
        exit;
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Registration failed: ' . $e->getMessage()]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - LicenseXpress</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <div class="logo-icon">LX</div>
                    <span class="logo-text">LicenseXpress</span>
                </div>
                <nav class="nav">
                    <a href="index.php" class="nav-link">Home</a>
                    <a href="pages/learners.php" class="nav-link">Find Schools</a>
                    <a href="pages/about.php" class="nav-link">About Us</a>
                    <a href="pages/contactus.php" class="nav-link">Contact</a>
                    <a href="pages/faq.php" class="nav-link">FAQ</a>
                    <a href="pages/guidelines.php" class="nav-link">Guidelines</a>
                </nav>
                <div class="header-actions">
                    <a href="login.php" class="btn btn-primary">Sign In</a>
                    <button class="mobile-menu-toggle" id="mobile-menu-toggle">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
            </div>
        </div>
    </header>

  
    <main class="main-content">
        <div class="container">
            <div class="form-container">
                <div class="form-card glass-card">
                    <div class="form-header">
                        <h1 class="form-title">Create Your Account</h1>
                        <p class="form-subtitle">Join thousands of drivers on their journey</p>
                    </div>

                    <form id="registrationForm" class="registration-form">
                        
                        <div class="form-group">
                            <label for="fullName" class="form-label">Full Name</label>
                            <div class="input-container">
                                <input 
                                    type="text" 
                                    id="fullName" 
                                    name="fullName" 
                                    class="form-input" 
                                    placeholder="Enter your full name as per NIC"
                                    required
                                >
                                <div class="input-icon">👤</div>
                                <div class="validation-icon"></div>
                            </div>
                            <div class="error-message"></div>
                        </div>

                        
                        <div class="form-group">
                            <label for="nic" class="form-label">NIC Number</label>
                            <div class="input-container">
                                <input 
                                    type="text" 
                                    id="nic" 
                                    name="nic" 
                                    class="form-input" 
                                    placeholder="2000 1234 5678"
                                    maxlength="14"
                                    required
                                >
                                <div class="input-icon">🆔</div>
                                <div class="validation-icon"></div>
                            </div>
                            <div class="error-message"></div>
                        </div>

                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-container">
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    class="form-input" 
                                    placeholder="Enter your email address"
                                    required
                                >
                                <div class="input-icon">📧</div>
                                <div class="validation-icon"></div>
                            </div>
                            <div class="error-message"></div>
                        </div>

                    
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone Number</label>
                            <div class="input-container">
                                <input 
                                    type="tel" 
                                    id="phone" 
                                    name="phone" 
                                    class="form-input" 
                                    placeholder="+94 77 123 4567"
                                    required
                                >
                                <div class="input-icon">📱</div>
                                <div class="validation-icon"></div>
                            </div>
                            <div class="error-message"></div>
                        </div>

                        
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-container">
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    class="form-input" 
                                    placeholder="Create a strong password"
                                    required
                                >
                                <div class="input-icon">🔒</div>
                                <button type="button" class="password-toggle" id="passwordToggle">
                                    <span class="toggle-icon">👁️</span>
                                </button>
                                <div class="validation-icon"></div>
                            </div>
                            <div class="password-strength">
                                <div class="strength-bar">
                                    <div class="strength-fill"></div>
                                </div>
                                <div class="strength-text"></div>
                            </div>
                            <div class="error-message"></div>
                        </div>

                        <div class="form-group">
                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                            <div class="input-container">
                                <input 
                                    type="password" 
                                    id="confirmPassword" 
                                    name="confirmPassword" 
                                    class="form-input" 
                                    placeholder="Confirm your password"
                                    required
                                >
                                <div class="input-icon">🔒</div>
                                <button type="button" class="password-toggle" id="confirmPasswordToggle">
                                    <span class="toggle-icon">👁️</span>
                                </button>
                                <div class="validation-icon"></div>
                            </div>
                            <div class="error-message"></div>
                        </div>

                        
                        <div class="form-group">
                            <label class="checkbox-container">
                                <input type="checkbox" id="terms" name="terms" required>
                                <span class="checkmark"></span>
                                <span class="checkbox-text">
                                    I agree to the <a href="#" class="link">Terms & Conditions</a> and <a href="#" class="link">Privacy Policy</a>
                                </span>
                            </label>
                            <div class="error-message"></div>
                        </div>

                        
                        <button type="submit" class="btn btn-primary btn-large btn-full" id="submitBtn">
                            <span class="btn-text">Create Account</span>
                            <span class="btn-icon">→</span>
                            <div class="btn-spinner hidden">
                                <div class="spinner"></div>
                            </div>
                        </button>
                    </form>

                
                    <div class="form-footer">
                        <p class="form-footer-text">
                            Already have an account? 
                            <a href="login.php" class="link">Sign In</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="assets/js/app.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registrationForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = submitBtn.querySelector('.btn-text');
            const btnIcon = submitBtn.querySelector('.btn-icon');
            const btnSpinner = submitBtn.querySelector('.btn-spinner');

           
            const nicInput = document.getElementById('nic');
            nicInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\s/g, '');
                if (value.length > 12) value = value.substr(0, 12);
                e.target.value = value.replace(/(\d{4})(\d{4})(\d{4})/, '$1 $2 $3');
                validateField('nic', e.target.value);
            });

            
            const phoneInput = document.getElementById('phone');
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\s/g, '');
                if (value.startsWith('+94')) {
                    if (value.length > 13) value = value.substr(0, 13);
                    e.target.value = value.replace(/(\+94)(\d{2})(\d{3})(\d{4})/, '$1 $2 $3 $4');
                } else if (value.startsWith('94')) {
                    value = '+' + value;
                    if (value.length > 13) value = value.substr(0, 13);
                    e.target.value = value.replace(/(\+94)(\d{2})(\d{3})(\d{4})/, '$1 $2 $3 $4');
                } else if (value.startsWith('0')) {
                    value = '+94' + value.substr(1);
                    if (value.length > 13) value = value.substr(0, 13);
                    e.target.value = value.replace(/(\+94)(\d{2})(\d{3})(\d{4})/, '$1 $2 $3 $4');
                }
                validateField('phone', e.target.value);
            });

            
            const passwordInput = document.getElementById('password');
            const strengthBar = document.querySelector('.strength-fill');
            const strengthText = document.querySelector('.strength-text');

            passwordInput.addEventListener('input', function(e) {
                const validation = LicenseXpress.validatePassword(e.target.value);
                const strength = validation.strength;
                
                strengthBar.className = `strength-fill strength-${strength}`;
                strengthText.textContent = validation.message;
                strengthText.className = `strength-text strength-${strength}`;
                
                validateField('password', e.target.value);
            });

            
            const confirmPasswordInput = document.getElementById('confirmPassword');
            confirmPasswordInput.addEventListener('input', function(e) {
                validateField('confirmPassword', e.target.value);
            });

            
            const passwordToggle = document.getElementById('passwordToggle');
            const confirmPasswordToggle = document.getElementById('confirmPasswordToggle');

            passwordToggle.addEventListener('click', function() {
                togglePasswordVisibility(passwordInput, this);
            });

            confirmPasswordToggle.addEventListener('click', function() {
                togglePasswordVisibility(confirmPasswordInput, this);
            });

            
            function validateField(fieldName, value) {
                const field = document.getElementById(fieldName);
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
                    case 'nic':
                        if (!LicenseXpress.validateNIC(value)) {
                            isValid = false;
                            errorMessage = 'Please enter a valid NIC number';
                        }
                        break;
                    case 'email':
                        if (!LicenseXpress.validateEmail(value)) {
                            isValid = false;
                            errorMessage = 'Please enter a valid email address';
                        }
                        break;
                    case 'phone':
                        if (!LicenseXpress.validatePhone(value)) {
                            isValid = false;
                            errorMessage = 'Please enter a valid Sri Lankan phone number';
                        }
                        break;
                    case 'password':
                        const passwordValidation = LicenseXpress.validatePassword(value);
                        if (!passwordValidation.valid) {
                            isValid = false;
                            errorMessage = passwordValidation.message;
                        }
                        break;
                    case 'confirmPassword':
                        const password = document.getElementById('password').value;
                        if (value !== password) {
                            isValid = false;
                            errorMessage = 'Passwords do not match';
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

            function togglePasswordVisibility(input, button) {
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                button.querySelector('.toggle-icon').textContent = isPassword ? '🙈' : '👁️';
            }

            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                
                const fields = ['fullName', 'nic', 'email', 'phone', 'password', 'confirmPassword'];
                let allValid = true;
                
                fields.forEach(fieldName => {
                    const field = document.getElementById(fieldName);
                    if (!validateField(fieldName, field.value)) {
                        allValid = false;
                    }
                });

                
                const termsChecked = document.getElementById('terms').checked;
                if (!termsChecked) {
                    LicenseXpress.showToast('Please accept the terms and conditions', 'error');
                    allValid = false;
                }

                if (!allValid) {
                    LicenseXpress.showToast('Please fix the errors above', 'error');
                    return;
                }

                
                submitBtn.disabled = true;
                btnText.textContent = 'Creating Account...';
                btnIcon.classList.add('hidden');
                btnSpinner.classList.remove('hidden');

                
                const formData = new FormData();
                formData.append('fullName', document.getElementById('fullName').value);
                formData.append('nic', document.getElementById('nic').value.replace(/\s/g, ''));
                formData.append('email', document.getElementById('email').value);
                formData.append('phone', document.getElementById('phone').value.replace(/\s/g, ''));
                formData.append('password', document.getElementById('password').value);

                
                fetch('register.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        
                        const userData = {
                            userId: data.user.userId,
                            fullName: data.user.fullName,
                            nic: data.user.nic,
                            email: data.user.email,
                            phone: data.user.phone,
                            registeredDate: new Date().toISOString()
                        };
                        
                        localStorage.setItem('currentUser', JSON.stringify(userData));
                        
                        
                        LicenseXpress.showToast('✅ Account created successfully!', 'success');

                       
                        setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 1500);
                    } else {
                        
                        LicenseXpress.showToast('❌ ' + (data.error || 'Registration failed'), 'error');
                        
                        n
                        submitBtn.disabled = false;
                        btnText.textContent = 'Create Account';
                        btnIcon.classList.remove('hidden');
                        btnSpinner.classList.add('hidden');
                    }
                })
                .catch(error => {
                    console.error('Registration error:', error);
                    LicenseXpress.showToast('❌ Registration failed. Please try again.', 'error');
                    
                    
                    submitBtn.disabled = false;
                    btnText.textContent = 'Create Account';
                    btnIcon.classList.remove('hidden');
                    btnSpinner.classList.add('hidden');
                });
            });

            
            const inputs = form.querySelectorAll('input[required]');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    validateField(this.name, this.value);
                });
            });
        });
    </script>

    <style>
        .main-content {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 120px 0 80px;
        }

        .form-container {
            width: 100%;
            max-width: 550px;
        }

        .form-card {
            padding: 40px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .form-title {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 12px;
            color: var(--text);
        }

        .form-subtitle {
            color: var(--text-muted);
            font-size: 16px;
        }

        .registration-form {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .input-container {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            pointer-events: none;
        }

        .form-input {
            padding-left: 50px;
            padding-right: 50px;
            height: 56px;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            color: var(--text-muted);
        }

        .validation-icon {
            position: absolute;
            right: 50px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            font-weight: bold;
        }

        .validation-icon.valid {
            color: var(--success);
        }

        .validation-icon.error {
            color: var(--error);
        }

        .form-input.error {
            border-color: var(--error);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .form-input.valid {
            border-color: var(--success);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .error-message {
            color: var(--error);
            font-size: 14px;
            margin-top: 8px;
            min-height: 20px;
        }

        .password-strength {
            margin-top: 8px;
        }

        .strength-bar {
            width: 100%;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-fill.weak {
            width: 25%;
            background: var(--error);
        }

        .strength-fill.medium {
            width: 50%;
            background: var(--warning);
        }

        .strength-fill.strong {
            width: 75%;
            background: var(--success);
        }

        .strength-fill.very-strong {
            width: 100%;
            background: var(--success);
        }

        .strength-text {
            font-size: 12px;
            margin-top: 4px;
        }

        .strength-text.weak {
            color: var(--error);
        }

        .strength-text.medium {
            color: var(--warning);
        }

        .strength-text.strong,
        .strength-text.very-strong {
            color: var(--success);
        }

        .checkbox-container {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            cursor: pointer;
        }

        .checkbox-container input[type="checkbox"] {
            display: none;
        }

        .checkmark {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 4px;
            position: relative;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .checkbox-container input[type="checkbox"]:checked + .checkmark {
            background: var(--gradient-1);
            border-color: var(--primary);
        }

        .checkbox-container input[type="checkbox"]:checked + .checkmark::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        .checkbox-text {
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.5;
        }

        .link {
            color: var(--primary-light);
            text-decoration: none;
        }

        .link:hover {
            text-decoration: underline;
        }

        .btn-full {
            width: 100%;
        }

        .btn-spinner {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
        }

        .form-footer {
            text-align: center;
            margin-top: 32px;
            padding-top: 32px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-footer-text {
            color: var(--text-muted);
        }

        @media (max-width: 768px) {
            .form-card {
                padding: 32px 24px;
            }
            
            .form-title {
                font-size: 28px;
            }
        }
    </style>
</body>
</html>


