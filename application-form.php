<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Form - LicenseXpress</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/application-form.css">
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
                    <a href="dashboard.php" class="nav-link">Dashboard</a>
                    <a href="application-form.php" class="nav-link active">Application</a>
                    <a href="pages/about.php" class="nav-link">About</a>
                    <a href="pages/contactus.php" class="nav-link">Contact</a>
                </nav>
                <div class="header-actions">
                    <div class="user-info">
                        <div class="user-avatar" id="userAvatar">J</div>
                        <div class="user-details">
                            <div class="user-name" id="userName">John Doe</div>
                            <div class="user-nic" id="userNIC">200012345678</div>
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
            <span class="breadcrumb-item">Dashboard</span>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">Application Form</span>
        </div>
    </div>

   
    <main class="application-main">
        <div class="container">
           
            <div class="progress-indicator">
                <div class="progress-steps">
                    <div class="step" id="step1" data-step="1">
                        <div class="step-number">1</div>
                        <div class="step-label">Personal Information</div>
                    </div>
                    <div class="step" id="step2" data-step="2">
                        <div class="step-number">2</div>
                        <div class="step-label">Document Upload</div>
                    </div>
                    <div class="step" id="step3" data-step="3">
                        <div class="step-number">3</div>
                        <div class="step-label">Payment & Review</div>
                    </div>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
            </div>

            
            <div class="form-container">
                
                <div class="form-step active" id="formStep1">
                    <div class="step-header">
                        <h1 class="step-title">📝 Personal Information</h1>
                        <p class="step-subtitle">Please provide accurate information as per your government documents</p>
                    </div>

                    <form class="application-form" id="personalInfoForm">
                        <div class="form-grid">

                            <div class="form-group">
                                <label for="fullName" class="form-label">Full Name</label>
                                <div class="input-container">
                                    <input type="text" id="fullName" name="fullName" class="form-input" placeholder="Full name as per NIC" required>
                                    <div class="input-icon">👤</div>
                                    <div class="validation-icon"></div>
                                </div>
                                <div class="error-message"></div>
                            </div>

                           
                            <div class="form-group">
                                <label for="nic" class="form-label">NIC Number</label>
                                <div class="input-container">
                                    <input type="text" id="nic" name="nic" class="form-input" placeholder="2000 1234 5678" required>
                                    <div class="input-icon">🆔</div>
                                    <div class="validation-icon valid">✓</div>
                                </div>
                                <div class="error-message"></div>
                            </div>

                            
                            <div class="form-group">
                                <label for="dateOfBirth" class="form-label">Date of Birth</label>
                                <div class="input-container">
                                    <input type="date" id="dateOfBirth" name="dateOfBirth" class="form-input" required>
                                    <div class="input-icon">📅</div>
                                    <div class="validation-icon"></div>
                                </div>
                                <div class="error-message"></div>
                            </div>


                            <div class="form-group">
                                <label for="gender" class="form-label">Gender</label>
                                <div class="input-container">
                                    <select id="gender" name="gender" class="form-input" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male">👨 Male</option>
                                        <option value="Female">👩 Female</option>
                                        <option value="Other">👤 Other</option>
                                    </select>
                                    <div class="input-icon">⚧</div>
                                    <div class="validation-icon"></div>
                                </div>
                                <div class="error-message"></div>
                            </div>


                            <div class="form-group transmission-group">
                                <label class="form-label">Transmission Type</label>
                                <div class="transmission-options">
                                    <div class="transmission-card" data-value="manual">
                                        <div class="card-icon">🚗</div>
                                        <div class="card-title">Manual Transmission</div>
                                        <div class="card-description">Allows you to drive both manual and automatic transmission vehicles</div>
                                        <div class="radio-button">
                                            <input type="radio" name="transmissionType" value="manual" id="manual" required>
                                            <label for="manual"></label>
                                        </div>
                                    </div>
                                    <div class="transmission-card" data-value="automatic">
                                        <div class="card-icon">⚙️</div>
                                        <div class="card-title">Automatic Transmission</div>
                                        <div class="card-description">Restricted to automatic transmission vehicles only</div>
                                        <div class="radio-button">
                                            <input type="radio" name="transmissionType" value="automatic" id="automatic" required>
                                            <label for="automatic"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="error-message"></div>
                            </div>

                           
                            <div class="form-group">
                                <label for="district" class="form-label">District</label>
                                <div class="input-container">
                                    <select id="district" name="district" class="form-input" required>
                                        <option value="">Select District</option>
                                        <option value="Colombo">Colombo</option>
                                        <option value="Gampaha">Gampaha</option>
                                        <option value="Kalutara">Kalutara</option>
                                        <option value="Kandy">Kandy</option>
                                        <option value="Matale">Matale</option>
                                        <option value="Nuwara Eliya">Nuwara Eliya</option>
                                        <option value="Galle">Galle</option>
                                        <option value="Matara">Matara</option>
                                        <option value="Hambantota">Hambantota</option>
                                        <option value="Jaffna">Jaffna</option>
                                        <option value="Kilinochchi">Kilinochchi</option>
                                        <option value="Mannar">Mannar</option>
                                        <option value="Vavuniya">Vavuniya</option>
                                        <option value="Mullaitivu">Mullaitivu</option>
                                        <option value="Batticaloa">Batticaloa</option>
                                        <option value="Ampara">Ampara</option>
                                        <option value="Trincomalee">Trincomalee</option>
                                        <option value="Kurunegala">Kurunegala</option>
                                        <option value="Puttalam">Puttalam</option>
                                        <option value="Anuradhapura">Anuradhapura</option>
                                        <option value="Polonnaruwa">Polonnaruwa</option>
                                        <option value="Badulla">Badulla</option>
                                        <option value="Monaragala">Monaragala</option>
                                        <option value="Ratnapura">Ratnapura</option>
                                        <option value="Kegalle">Kegalle</option>
                                    </select>
                                    <div class="input-icon">📍</div>
                                    <div class="validation-icon"></div>
                                </div>
                                <div class="error-message"></div>
                            </div>
                        </div>


                        <div class="info-alert">
                            <div class="alert-icon">ℹ️</div>
                            <div class="alert-content">
                                <strong>Important Notice</strong>
                                <p>All information must match your government-issued documents. Any discrepancies may result in application rejection.</p>
                            </div>
                        </div>

                     
                        <div class="form-navigation">
                            <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
                            <button type="button" class="btn btn-primary" onclick="document.getElementById('formStep1').style.display='none'; document.getElementById('formStep2').style.display='block';">Next: Upload Documents →</button>
                        </div>
                    </form>
                </div>

                
                <div class="form-step" id="formStep2">
                    <div class="step-header">
                        <h1 class="step-title">📁 Upload Required Documents</h1>
                        <p class="step-subtitle">Please upload clear, high-quality scans or photos of your documents</p>
                    </div>

                    
                    <div class="warning-alert">
                        <div class="alert-icon">⚠️</div>
                        <div class="alert-content">
                            <strong>Document Upload Requirements</strong>
                            <ul>
                                <li>✓ All documents must be clear and readable</li>
                                <li>✓ File size should not exceed 5MB per file</li>
                                <li>✓ Accepted formats: JPG, PNG, PDF</li>
                                <li>✓ Documents should be in color</li>
                                <li>✓ High resolution (minimum 1200x900 pixels)</li>
                                <li>✓ All text must be clearly visible</li>
                                <li>✓ No screenshots or photocopies of screens</li>
                                <li>✓ Original documents only</li>
                            </ul>
                        </div>
                    </div>

                   
                    <div class="upload-zones">
                       
                        <div class="upload-zone" id="birthCertificateZone">
                            <div class="upload-content">
                                <div class="upload-icon">📄</div>
                                <div class="upload-title">Birth Certificate</div>
                                <div class="upload-description">Upload your official birth certificate issued by the Registrar General</div>
                                <div class="upload-area" id="birthCertificateArea">
                                    <div class="upload-placeholder">
                                        <div class="upload-text">Click to upload or drag here</div>
                                        <div class="upload-info">Max 5MB • JPG, PNG, PDF</div>
                                    </div>
                                    <input type="file" id="birthCertificate" name="birthCertificate" accept=".jpg,.jpeg,.png,.pdf" style="display: none;">
                                </div>
                                <div class="upload-progress hidden">
                                    <div class="progress-bar">
                                        <div class="progress-fill"></div>
                                    </div>
                                    <div class="progress-text">0%</div>
                                </div>
                                <div class="upload-preview hidden">
                                    <div class="preview-content">
                                        <div class="preview-thumbnail"></div>
                                        <div class="preview-info">
                                            <div class="preview-filename"></div>
                                            <div class="preview-size"></div>
                                            <div class="preview-status">Uploaded ✓</div>
                                        </div>
                                        <div class="preview-actions">
                                            <button type="button" class="btn-preview">👁️ Preview</button>
                                            <button type="button" class="btn-delete">🗑️ Delete</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="upload-zone" id="nicCopyZone">
                            <div class="upload-content">
                                <div class="upload-icon">🪪</div>
                                <div class="upload-title">National Identity Card</div>
                                <div class="upload-description">Upload clear photos of front and back of your NIC (Can upload 2 images or 1 PDF)</div>
                                <div class="upload-area" id="nicCopyArea">
                                    <div class="upload-placeholder">
                                        <div class="upload-text">Click to upload or drag here</div>
                                        <div class="upload-info">Max 5MB • JPG, PNG, PDF</div>
                                    </div>
                                    <input type="file" id="nicCopy" name="nicCopy" accept=".jpg,.jpeg,.png,.pdf" multiple style="display: none;">
                                </div>
                                <div class="upload-progress hidden">
                                    <div class="progress-bar">
                                        <div class="progress-fill"></div>
                                    </div>
                                    <div class="progress-text">0%</div>
                                </div>
                                <div class="upload-preview hidden">
                                    <div class="preview-content">
                                        <div class="preview-thumbnail"></div>
                                        <div class="preview-info">
                                            <div class="preview-filename"></div>
                                            <div class="preview-size"></div>
                                            <div class="preview-status">Uploaded ✓</div>
                                        </div>
                                        <div class="preview-actions">
                                            <button type="button" class="btn-preview">👁️ Preview</button>
                                            <button type="button" class="btn-delete">🗑️ Delete</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="upload-zone" id="medicalCertificateZone">
                            <div class="upload-content">
                                <div class="upload-icon">🏥</div>
                                <div class="upload-title">Medical Certificate</div>
                                <div class="upload-description">Valid medical fitness certificate issued by a registered medical officer</div>
                                <div class="upload-note">Must be issued within the last 6 months</div>
                                <div class="upload-area" id="medicalCertificateArea">
                                    <div class="upload-placeholder">
                                        <div class="upload-text">Click to upload or drag here</div>
                                        <div class="upload-info">Max 5MB • PDF preferred</div>
                                    </div>
                                    <input type="file" id="medicalCertificate" name="medicalCertificate" accept=".jpg,.jpeg,.png,.pdf" style="display: none;">
                                </div>
                                <div class="upload-progress hidden">
                                    <div class="progress-bar">
                                        <div class="progress-fill"></div>
                                    </div>
                                    <div class="progress-text">0%</div>
                                </div>
                                <div class="upload-preview hidden">
                                    <div class="preview-content">
                                        <div class="preview-thumbnail"></div>
                                        <div class="preview-info">
                                            <div class="preview-filename"></div>
                                            <div class="preview-size"></div>
                                            <div class="preview-status">Uploaded ✓</div>
                                        </div>
                                        <div class="preview-actions">
                                            <button type="button" class="btn-preview">👁️ Preview</button>
                                            <button type="button" class="btn-delete">🗑️ Delete</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="upload-zone" id="photoZone">
                            <div class="upload-content">
                                <div class="upload-icon">📸</div>
                                <div class="upload-title">Passport Size Photo</div>
                                <div class="upload-description">Recent photograph with white background, face clearly visible</div>
                                <div class="upload-area" id="photoArea">
                                    <div class="upload-placeholder">
                                        <div class="upload-text">Click to upload or drag here</div>
                                        <div class="upload-info">Max 5MB • JPG, PNG only</div>
                                    </div>
                                    <input type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png" style="display: none;">
                                </div>
                                <div class="upload-progress hidden">
                                    <div class="progress-bar">
                                        <div class="progress-fill"></div>
                                    </div>
                                    <div class="progress-text">0%</div>
                                </div>
                                <div class="upload-preview hidden">
                                    <div class="preview-content">
                                        <div class="preview-thumbnail"></div>
                                        <div class="preview-info">
                                            <div class="preview-filename"></div>
                                            <div class="preview-size"></div>
                                            <div class="preview-status">Uploaded ✓</div>
                                        </div>
                                        <div class="preview-actions">
                                            <button type="button" class="btn-preview">👁️ Preview</button>
                                            <button type="button" class="btn-delete">🗑️ Delete</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                   
                    <div class="upload-progress-indicator">
                        <div class="progress-info">
                            <span class="progress-label">Documents Uploaded:</span>
                            <span class="progress-count" id="uploadCount">0/4</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" id="uploadProgressFill"></div>
                        </div>
                    </div>

                    
                    <div class="form-navigation">
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('formStep2').style.display='none'; document.getElementById('formStep1').style.display='block';">← Back</button>
                        <button type="button" class="btn btn-primary" onclick="document.getElementById('formStep2').style.display='none'; document.getElementById('formStep3').style.display='block';">Next: Payment & Review →</button>
                    </div>
                </div>

                
                <div class="form-step" id="formStep3">
                    <div class="step-header">
                        <h1 class="step-title">💳 Review & Payment</h1>
                        <p class="step-subtitle">Review your application and complete the payment to submit</p>
                    </div>

                   
                    <div class="summary-section">
                       
                        <div class="summary-card glass-card">
                            <div class="summary-header">
                                <h3>👤 Personal Information</h3>
                                <button type="button" class="btn-edit" data-step="1">Edit Step 1</button>
                            </div>
                            <div class="summary-content" id="personalInfoSummary">
                              
                            </div>
                        </div>

                       
                        <div class="summary-card glass-card">
                            <div class="summary-header">
                                <h3>📁 Uploaded Documents</h3>
                                <button type="button" class="btn-edit" data-step="2">Edit Step 2</button>
                            </div>
                            <div class="summary-content" id="documentsSummary">
                                
                            </div>
                        </div>
                    </div>

                   
                    <div class="payment-section">
                        <div class="payment-card glass-card">
                            <h3>💰 Payment Summary</h3>
                            <div class="payment-breakdown">
                                <div class="payment-item">
                                    <span class="payment-label">Application Fee</span>
                                    <span class="payment-amount">Rs. 1,500.00</span>
                                </div>
                                <div class="payment-item">
                                    <span class="payment-label">Document Processing Fee</span>
                                    <span class="payment-amount">Rs. 500.00</span>
                                </div>
                                <div class="payment-item">
                                    <span class="payment-label">Theory Test Fee</span>
                                    <span class="payment-amount">Rs. 1,000.00</span>
                                </div>
                                <div class="payment-item">
                                    <span class="payment-label">Service Charge</span>
                                    <span class="payment-amount">Rs. 200.00</span>
                                </div>
                                <div class="payment-divider"></div>
                                <div class="payment-total">
                                    <span class="payment-label">Total Amount</span>
                                    <span class="payment-amount">Rs. 3,200.00</span>
                                </div>
                            </div>
                            <div class="payment-note">All fees in Sri Lankan Rupees (LKR)</div>
                        </div>

                        
                        <div class="payment-methods">
                            <h3>💳 Payment Method</h3>
                            <div class="payment-options">
                                <div class="payment-option" data-method="card">
                                    <div class="option-header">
                                        <input type="radio" name="paymentMethod" value="card" id="cardPayment" required>
                                        <label for="cardPayment" class="option-label">
                                            <span class="option-icon">💳</span>
                                            <span class="option-title">Credit / Debit Card</span>
                                        </label>
                                    </div>
                                    <div class="option-content">
                                        <p>Accepted cards: Visa, Mastercard, Amex</p>
                                        <div class="card-form">
                                            <div class="form-group">
                                                <input type="text" class="form-input" placeholder="Card Number" maxlength="19">
                                            </div>
                                            <div class="form-row">
                                                <input type="text" class="form-input" placeholder="MM / YY" maxlength="5">
                                                <input type="text" class="form-input" placeholder="CVV" maxlength="3">
                                            </div>
                                            <div class="form-group">
                                                <input type="text" class="form-input" placeholder="Cardholder Name">
                                            </div>
                                        </div>
                                        <div class="security-note">🔒 Secured by SSL encryption</div>
                                    </div>
                                </div>

                                <div class="payment-option" data-method="bank">
                                    <div class="option-header">
                                        <input type="radio" name="paymentMethod" value="bank" id="bankPayment" required>
                                        <label for="bankPayment" class="option-label">
                                            <span class="option-icon">🏦</span>
                                            <span class="option-title">Bank Transfer / Direct Deposit</span>
                                        </label>
                                    </div>
                                    <div class="option-content">
                                        <p>Transfer to our bank account:</p>
                                        <div class="bank-details">
                                            <p><strong>Bank:</strong> Bank of Ceylon</p>
                                            <p><strong>Account Name:</strong> LicenseXpress Pvt Ltd</p>
                                            <p><strong>Account Number:</strong> 1234567890</p>
                                            <p><strong>Branch:</strong> Colombo Main</p>
                                        </div>
                                        <div class="form-group">
                                            <input type="file" class="form-input" accept=".jpg,.jpeg,.png,.pdf">
                                            <label class="file-label">📎 Upload Payment Receipt</label>
                                        </div>
                                        <div class="bank-note">Note: Verification may take 24 hours</div>
                                    </div>
                                </div>

                                <div class="payment-option" data-method="mobile">
                                    <div class="option-header">
                                        <input type="radio" name="paymentMethod" value="mobile" id="mobilePayment" required>
                                        <label for="mobilePayment" class="option-label">
                                            <span class="option-icon">📱</span>
                                            <span class="option-title">Mobile Payment</span>
                                        </label>
                                    </div>
                                    <div class="option-content">
                                        <p>Pay using mobile wallet:</p>
                                        <div class="form-group">
                                            <select class="form-input">
                                                <option value="">Select Provider</option>
                                                <option value="ezcash">eZ Cash</option>
                                                <option value="mcash">mCash</option>
                                                <option value="genie">Genie</option>
                                                <option value="frimi">FriMi</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input type="tel" class="form-input" placeholder="+94 __ ___ ____">
                                        </div>
                                        <div class="mobile-note">You'll receive a payment request</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                      
                        <div class="terms-section">
                            <label class="checkbox-container">
                                <input type="checkbox" id="terms" name="terms" required>
                                <span class="checkmark"></span>
                                <span class="checkbox-text">
                                    I agree to the <a href="#" class="link">Terms & Conditions</a> and <a href="#" class="link">Privacy Policy</a>
                                </span>
                            </label>
                        </div>

                       
                        <div class="important-notes glass-card">
                            <h3>📋 Before You Submit</h3>
                            <ul>
                                <li>Double-check all information for accuracy</li>
                                <li>Ensure all documents are clear and readable</li>
                                <li>Payment is non-refundable once processed</li>
                                <li>You'll receive confirmation via email and SMS</li>
                                <li>Verification takes up to 48 hours</li>
                                <li>Keep your application ID for reference</li>
                            </ul>
                        </div>

                       
                        <div class="submit-section">
                            <button type="button" class="btn btn-secondary" onclick="

                                const testData = {
                                    userId: 'test123',
                                    fullName: 'Test User',
                                    nic: '200012345678',
                                    status: 'pending_verification',
                                    submittedDate: new Date().toISOString(),
                                    applicationId: 'APP' + Date.now()
                                };
                                
                                const applications = JSON.parse(localStorage.getItem('applications') || '[]');
                                applications.push(testData);
                                localStorage.setItem('applications', JSON.stringify(applications));
                                
                                const applicationState = {
                                    status: 'pending_verification',
                                    submittedDate: new Date().toISOString(),
                                    applicationId: testData.applicationId
                                };
                                localStorage.setItem('applicationState', JSON.stringify(applicationState));
                                
                                alert('Test data saved! Application ID: ' + testData.applicationId);
                                window.location.href = 'dashboard.php';
                            " style="margin-right: 10px;">Test Submit</button>
                            <button type="button" class="btn btn-primary btn-large btn-submit" onclick="submitApplicationToDatabase()">
                                <span class="btn-text">💳 Submit Application & Pay Rs. 3,200</span>
                                <div class="btn-spinner hidden">
                                    <div class="spinner"></div>
                                </div>
                            </button>
                        </div>

                        
                        <div class="form-navigation">
                            <button type="button" class="btn btn-secondary" onclick="document.getElementById('formStep3').style.display='none'; document.getElementById('formStep2').style.display='block';">← Back</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>


    <div class="loading-overlay hidden" id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner">
                <div class="spinner"></div>
            </div>
            <div class="loading-text">Processing Your Application...</div>
            <div class="loading-progress">
                <div class="progress-bar">
                    <div class="progress-fill" id="loadingProgressFill"></div>
                </div>
                <div class="progress-text" id="loadingProgressText">0%</div>
            </div>
            <div class="loading-status" id="loadingStatus">Starting application...</div>
        </div>
    </main>


    <div class="success-modal hidden" id="successModal">
        <div class="modal-content">
            <div class="success-icon">✅</div>
            <div class="success-title">Application Submitted Successfully!</div>
            <div class="success-message">
                Your application has been received and is being processed.
            </div>
            <div class="application-id">
                Application ID: <span id="applicationId">LX-2025-001234</span>
            </div>
            <div class="success-details">
                <h4>What happens next?</h4>
                <ul>
                    <li>Verification begins immediately</li>
                    <li>You'll be notified within 48 hours</li>
                    <li>Check your email for updates</li>
                </ul>
            </div>
            <div class="confirmation-info">
                <div class="email-confirmation">
                    <strong>📧 Confirmation email sent to:</strong>
                    <span id="confirmationEmail">john@example.com</span>
                </div>
                <div class="sms-confirmation">
                    <strong>📱 SMS confirmation sent to:</strong>
                    <span id="confirmationPhone">+94 77 123 4567</span>
                </div>
            </div>
            <div class="success-actions">
                <button class="btn btn-primary" id="goToDashboard">Go to Dashboard</button>
            </div>
        </div>
    </div>


    <div class="image-modal hidden" id="imageModal">
        <div class="modal-backdrop"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Document Preview</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <img id="previewImage" src="" alt="Document Preview">
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="rotateImage">🔄 Rotate</button>
                <button class="btn btn-primary" id="downloadImage">⬇️ Download</button>
                <button class="btn btn-secondary" id="closeModal">Close</button>
            </div>
        </div>
    </div>

    <script src="assets/js/app.js"></script>
    <script src="assets/js/application-form.js"></script>
</body>
</html>


