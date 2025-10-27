<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theory Exam - LicenseXpress</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/exam-window.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
   
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner">
                <div class="spinner"></div>
            </div>
            <div class="loading-text" id="loadingText">Initializing exam...</div>
        </div>
    </div>

    
    <div class="camera-permission-overlay hidden" id="cameraPermissionOverlay">
        <div class="permission-content">
            <div class="permission-icon">📹</div>
            <div class="permission-title">Camera Permission Required</div>
            <div class="permission-message">
                This exam requires camera access for monitoring purposes. 
                Please allow camera access to continue.
            </div>
            <div class="permission-status" id="permissionStatus">Requesting camera permission...</div>
        </div>
    </div>

    
    <header class="exam-header" id="examHeader" style="display: none;">
        <div class="header-content">
            <div class="exam-info">
                <div class="exam-title">Theory Exam - LicenseXpress</div>
                <div class="exam-subtitle">Sri Lankan Driving License Theory Test</div>
            </div>
            <div class="exam-controls">
                <div class="timer-container">
                    <div class="timer-icon">⏰</div>
                    <div class="timer-display" id="timerDisplay">60:00</div>
                </div>
                <div class="monitoring-status">
                    <div class="monitoring-icon">🔴</div>
                    <div class="monitoring-text">Invigilator Monitoring Active</div>
                </div>
               
                <div class="test-submit-container" style="margin-left: 20px;">
                    <button class="btn btn-test" id="testSubmitBtn" onclick="testSubmitSuccess()" style="background: #28a745; color: white; padding: 8px 16px; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                        🎯 TEST SUBMIT
                    </button>
                </div>
            </div>
        </div>
    </header>

    
    <main class="exam-main" id="examMain" style="display: none;">
        <div class="exam-container">
            
            <aside class="question-navigator" id="questionNavigator">
                <div class="navigator-header">
                    <h3>Questions</h3>
                    <div class="progress-info">
                        <span id="progressText">Question 1 of 50</span>
                    </div>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
                <div class="question-grid" id="questionGrid">
                     
                </div>
            </aside>

            
            <section class="question-content">
                <div class="question-container">
                    
                    <div class="question-header">
                        <div class="question-number">
                            <span class="number-label">Question</span>
                            <span class="number-value" id="questionNumber">1</span>
                        </div>
                        <div class="question-timer">
                            <span class="timer-label">Time Remaining</span>
                            <span class="timer-value" id="questionTimer">60:00</span>
                        </div>
                    </div>

                    
                    <div class="question-text" id="questionText">
                        
                    </div>

                    <div class="question-image hidden" id="questionImage">
                        <img src="" alt="Question Image" id="questionImg">
                    </div>

                    <div class="answer-options" id="answerOptions">
                        
                    </div>

                    <div class="question-navigation">
                        <button class="btn btn-secondary" id="nextBtn" disabled>
                            <span class="btn-text">Next Question →</span>
                            <div class="btn-spinner hidden">
                                <div class="spinner"></div>
                            </div>
                        </button>
                    </div>
                </div>
            </section>
        </div>
    </main>

    
    <div class="answer-review-modal hidden" id="answerReviewModal">
        <div class="modal-backdrop"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h2>Answer Review</h2>
                <div class="review-summary">
                    <span id="reviewSummary">Reviewing your answers...</span>
                </div>
            </div>
            <div class="modal-body">
                <div class="review-list" id="reviewList">
                    
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="proceedToResults">Proceed to Results</button>
            </div>
        </div>
    </div>

    
    <div class="results-modal hidden" id="resultsModal">
        <div class="modal-backdrop"></div>
        <div class="modal-content">
            <div class="results-header">
                <div class="results-icon" id="resultsIcon">🎉</div>
                <div class="results-title" id="resultsTitle">Exam Passed!</div>
                <div class="results-subtitle" id="resultsSubtitle">Congratulations on passing your theory exam</div>
            </div>
            <div class="results-content">
                <div class="score-display">
                    <div class="score-circle">
                        <div class="score-value" id="scoreValue">45</div>
                        <div class="score-label">out of 50</div>
                    </div>
                    <div class="score-details">
                        <div class="score-percentage" id="scorePercentage">90%</div>
                        <div class="pass-mark">Pass Mark: 40/50 (80%)</div>
                    </div>
                </div>
                <div class="results-message" id="resultsMessage">
                    <p>Excellent work! You've successfully passed the theory exam.</p>
                    <p>Your practical test will be automatically scheduled for 3 months from today.</p>
                </div>
            </div>
            <div class="results-actions">
                <button class="btn btn-primary" id="proceedToDocuments">Proceed to Document Submission</button>
                <button class="btn btn-secondary" id="rescheduleExam">Reschedule Exam</button>
            </div>
        </div>
    </div>

    
    <div class="security-warning-modal hidden" id="securityWarningModal">
        <div class="modal-backdrop"></div>
        <div class="modal-content">
            <div class="warning-header">
                <div class="warning-icon">⚠️</div>
                <div class="warning-title">Security Violation Detected</div>
            </div>
            <div class="warning-content">
                <div class="warning-reason" id="warningReason">Screenshot attempt detected!</div>
                <div class="warning-message">
                    <strong>Your exam is now being terminated.</strong>
                </div>
                <div class="warning-countdown">
                    <span>Redirecting in </span>
                    <span id="countdownTimer">3</span>
                    <span> seconds...</span>
                </div>
            </div>
        </div>
    </div>

    
    <div class="submission-overlay hidden" id="submissionOverlay">
        <div class="submission-content">
            <div class="submission-spinner">
                <div class="spinner"></div>
            </div>
            <div class="submission-text">Submitting exam...</div>
            <div class="submission-progress">
                <div class="progress-bar">
                    <div class="progress-fill" id="submissionProgress"></div>
                </div>
            </div>
        </div>
    </div>

    
    <video id="cameraFeed" autoplay muted style="display: none;"></video>

    <script src="assets/js/app.js?v=<?php echo time(); ?>"></script>
    <script src="assets/js/exam-window.js?v=<?php echo time(); ?>"></script>
</body>
</html>
