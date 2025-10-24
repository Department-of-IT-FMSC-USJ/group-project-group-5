<?php
session_start();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        require_once 'database/database_connection.php';
        
        
        $nic = $_POST['nic'] ?? '';
        $password = $_POST['password'] ?? '';
        $rememberMe = isset($_POST['rememberMe']) && $_POST['rememberMe'] === 'true';
        
        
        if (empty($nic) || empty($password)) {
            echo json_encode([
                'success' => false,
                'message' => 'NIC and password are required'
            ]);
            exit;
        }
        
        
        $nic = preg_replace('/[^0-9Vv]/', '', $nic);
        
        
        $db = new Database();
        
        
        $sql = "SELECT user_id, nic, full_name, email, phone, password_hash 
                FROM users WHERE nic = :nic LIMIT 1";
        $user = $db->fetch($sql, ['nic' => $nic]);
        
        if (!$user) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid NIC or password'
            ]);
            exit;
        }
        
        
        if (!password_verify($password, $user['password_hash'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid NIC or password'
            ]);
            exit;
        }
        
        
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['nic'] = $user['nic'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];
        
        
        if ($rememberMe) {
            
            setcookie('remember_user', $user['user_id'], time() + (7 * 24 * 60 * 60), '/');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'userId' => $user['user_id'],
                'fullName' => $user['full_name'],
                'nic' => $user['nic'],
                'email' => $user['email'],
                'phone' => $user['phone']
            ],
            'session_id' => session_id(), 
            'session_data' => $_SESSION 
        ]);
        exit;
        
    } catch (Exception $e) {
        
        error_log("Login error: " . $e->getMessage());
        
        echo json_encode([
            'success' => false,
            'message' => 'Login failed. Please try again.',
            'error' => $e->getMessage() 
        ]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - LicenseXpress</title>
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
                    <a href="register.php" class="btn btn-secondary">Register</a>
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
                        <h1 class="form-title">Welcome Back</h1>
                        <p class="form-subtitle">Sign in to continue your journey</p>
                    </div>

                    <form id="loginForm" class="login-form">
                        
                        <div class="form-group">
                            <label for="nic" class="form-label">NIC Number</label>
                            <div class="input-container">
                                <input 
                                    type="text" 
                                    id="nic" 
                                    name="nic" 
                                    class="form-input" 
                                    placeholder="Enter your NIC number"
                                    maxlength="14"
                                    required
                                >
                                <div class="input-icon">🆔</div>
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
                                    placeholder="Enter your password"
                                    required
                                >
                                <div class="input-icon">🔒</div>
                                <button type="button" class="password-toggle" id="passwordToggle">
                                    <span class="toggle-icon">👁️</span>
                                </button>
                                <div class="validation-icon"></div>
                            </div>
                            <div class="error-message"></div>
                        </div>

                        
                        <div class="form-options">
                            <label class="checkbox-container">
                                <input type="checkbox" id="rememberMe" name="rememberMe">
                                <span class="checkmark"></span>
                                <span class="checkbox-text">Remember Me</span>
                            </label>
                            <a href="#" class="forgot-password" id="forgotPassword">Forgot Password?</a>
                        </div>

                        
                        <button type="submit" class="btn btn-primary btn-large btn-full" id="submitBtn">
                            <span class="btn-text">Sign In</span>
                            <span class="btn-icon">→</span>
                            <div class="btn-spinner hidden">
                                <div class="spinner"></div>
                            </div>
                        </button>
                    </form>

                    
                    <div class="form-footer">
                        <p class="form-footer-text">
                            Don't have an account? 
                            <a href="register.php" class="link">Register</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="assets/js/app.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = submitBtn.querySelector('.btn-text');
            const btnIcon = submitBtn.querySelector('.btn-icon');
            const btnSpinner = submitBtn.querySelector('.btn-spinner');
            const forgotPasswordLink = document.getElementById('forgotPassword');

            
            let loginAttempts = parseInt(localStorage.getItem('loginAttempts') || '0');
            const maxAttempts = 5;
            const lockoutTime = 15 * 60 * 1000; // 15 minutes
            const lastAttemptTime = localStorage.getItem('lastLoginAttempt');

            
            if (loginAttempts >= maxAttempts && lastAttemptTime) {
                const timeSinceLastAttempt = Date.now() - parseInt(lastAttemptTime);
                if (timeSinceLastAttempt < lockoutTime) {
                    const remainingTime = Math.ceil((lockoutTime - timeSinceLastAttempt) / 60000);
                    LicenseXpress.showToast(`Account locked. Try again in ${remainingTime} minutes.`, 'error');
                    submitBtn.disabled = true;
                    return;
                } else {
                    
                    loginAttempts = 0;
                    localStorage.removeItem('loginAttempts');
                    localStorage.removeItem('lastLoginAttempt');
                }
            }

            
            const nicInput = document.getElementById('nic');
            nicInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\s/g, '');
                if (value.length > 12) value = value.substr(0, 12);
                e.target.value = value.replace(/(\d{4})(\d{4})(\d{4})/, '$1 $2 $3');
            });

            
            const passwordToggle = document.getElementById('passwordToggle');
            const passwordInput = document.getElementById('password');

            passwordToggle.addEventListener('click', function() {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                this.querySelector('.toggle-icon').textContent = isPassword ? '🙈' : '👁️';
            });

            
            forgotPasswordLink.addEventListener('click', function(e) {
                e.preventDefault();
                LicenseXpress.showToast('Contact support at support@licensexpress.lk', 'info');
            });

            
            function validateField(fieldName, value) {
                const field = document.getElementById(fieldName);
                const errorElement = field.closest('.form-group').querySelector('.error-message');
                const validationIcon = field.closest('.input-container').querySelector('.validation-icon');
                let isValid = true;
                let errorMessage = '';

                switch (fieldName) {
                    case 'nic':
                        if (!LicenseXpress.validateNIC(value)) {
                            isValid = false;
                            errorMessage = 'Please enter a valid NIC number';
                        }
                        break;
                    case 'password':
                        if (value.length < 6) {
                            isValid = false;
                            errorMessage = 'Password must be at least 6 characters';
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

            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const nic = document.getElementById('nic').value.replace(/\s/g, '');
                const password = document.getElementById('password').value;
                const rememberMe = document.getElementById('rememberMe').checked;

                
                const nicValid = validateField('nic', nic);
                const passwordValid = validateField('password', password);

                if (!nicValid || !passwordValid) {
                    LicenseXpress.showToast('Please fix the errors above', 'error');
                    return;
                }

                
                submitBtn.disabled = true;
                btnText.textContent = 'Signing In...';
                btnIcon.classList.add('hidden');
                btnSpinner.classList.remove('hidden');

                
                const formData = new FormData();
                formData.append('nic', nic);
                formData.append('password', password);
                formData.append('rememberMe', rememberMe.toString());

                fetch('login.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin' 
                })
                .then(response => {
                    
                    return response.text().then(text => {
                        console.log('PHP Response:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('Failed to parse JSON:', e);
                            console.error('Raw response:', text);
                            throw new Error('Invalid JSON response from server');
                        }
                    });
                })
                .then(data => {
                    if (data.success) {
                        
                        localStorage.setItem('isAuthenticated', 'true');
                        localStorage.setItem('currentUser', JSON.stringify(data.user));
                        
                        
                        localStorage.removeItem('loginAttempts');
                        localStorage.removeItem('lastLoginAttempt');

                        
                        LicenseXpress.showToast('✅ Login successful!', 'success');

                        
                        setTimeout(() => {
                            window.location.replace('dashboard.php');
                        }, 500);
                    } else {
                        
                        loginAttempts++;
                        localStorage.setItem('loginAttempts', loginAttempts.toString());
                        localStorage.setItem('lastLoginAttempt', Date.now().toString());

                        
                        submitBtn.disabled = false;
                        btnText.textContent = 'Sign In';
                        btnIcon.classList.remove('hidden');
                        btnSpinner.classList.add('hidden');

                        
                        if (loginAttempts >= maxAttempts) {
                            LicenseXpress.showToast('Too many failed attempts. Try again in 15 minutes.', 'error');
                            submitBtn.disabled = true;
                        } else {
                            const remainingAttempts = maxAttempts - loginAttempts;
                            LicenseXpress.showToast(data.message || `Invalid NIC or password. ${remainingAttempts} attempts remaining.`, 'error');
                            
                            
                            form.classList.add('shake');
                            setTimeout(() => form.classList.remove('shake'), 500);
                        }
                    }
                })
                .catch(error => {
                    console.error('Login error:', error);
                    
                    
                    submitBtn.disabled = false;
                    btnText.textContent = 'Sign In';
                    btnIcon.classList.remove('hidden');
                    btnSpinner.classList.add('hidden');
                    
                    LicenseXpress.showToast('Login failed. Please try again.', 'error');
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
            max-width: 500px;
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

        .login-form {
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

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: -8px 0;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .checkbox-container input[type="checkbox"] {
            display: none;
        }

        .checkmark {
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 4px;
            position: relative;
            transition: all 0.3s ease;
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
            font-size: 10px;
            font-weight: bold;
        }

        .checkbox-text {
            color: var(--text-muted);
            font-size: 14px;
        }

        .forgot-password {
            color: var(--primary-light);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .forgot-password:hover {
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

        .link {
            color: var(--primary-light);
            text-decoration: none;
        }

        .link:hover {
            text-decoration: underline;
        }

        .shake {
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        @media (max-width: 768px) {
            .form-card {
                padding: 32px 24px;
            }
            
            .form-title {
                font-size: 28px;
            }

            .form-options {
                flex-direction: column;
                gap: 16px;
                align-items: flex-start;
            }
        }
    </style>
</body>
</html>


