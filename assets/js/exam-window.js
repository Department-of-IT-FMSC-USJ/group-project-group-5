

let examStarted = false;
let examTerminated = false;
let currentQuestion = 0;
let answers = new Array(50).fill(null);
let timerInterval = null;
let timeLeft = 3600;
let cameraStream = null;
let screenshotBlocked = false;


const examQuestions = [
    {
        id: 1,
        question: "What is the maximum speed limit for light motor vehicles in urban areas?",
        options: ["40 km/h", "50 km/h", "60 km/h", "70 km/h"],
        correct: 1,
        image: null
    },
    {
        id: 2,
        question: "When should you use your headlights?",
        options: ["Only at night", "In foggy conditions", "During heavy rain", "All of the above"],
        correct: 3,
        image: null
    },
    {
        id: 3,
        question: "What does this traffic sign mean?",
        options: ["No entry", "Stop", "Give way", "No parking"],
        correct: 2,
        image: "assets/images/traffic-signs/give-way.png"
    }
    
];


document.addEventListener('DOMContentLoaded', function() {
    
    if (!LicenseXpress.checkAuth()) {
        window.location.href = 'login.php';
        return;
    }


    initializeExam();
});

function initializeExam() {
    
    showLoadingOverlay('Initializing exam...');


    requestCameraPermission();
}

function requestCameraPermission() {
    const permissionOverlay = document.getElementById('cameraPermissionOverlay');
    const permissionStatus = document.getElementById('permissionStatus');
    
    permissionOverlay.classList.remove('hidden');
    permissionStatus.textContent = 'Requesting camera permission...';


    navigator.mediaDevices.getUserMedia({ video: true, audio: false })
        .then(stream => {
            cameraStream = stream;
            permissionStatus.textContent = 'Camera access granted!';
            

            setTimeout(() => {
                permissionOverlay.classList.add('hidden');
                startExam();
            }, 1000);
        })
        .catch(error => {
            console.error('Camera access denied:', error);
            permissionStatus.textContent = 'Camera access denied!';
            

            setTimeout(() => {
                alert('Webcam access is required for this exam. Please enable your webcam and refresh the page.');
                window.location.href = 'dashboard.php';
            }, 2000);
        });
}

function startExam() {

    hideLoadingOverlay();

   
    document.getElementById('examHeader').style.display = 'block';
    document.getElementById('examMain').style.display = 'block';


    initializeQuestionNavigator();
    loadQuestion(0);
    startTimer();
    initializeSecurityMeasures();

    
    setTimeout(() => {
        examStarted = true;
    }, 3000);


    LicenseXpress.showToast('✅ Exam started successfully! Good luck!', 'success');
}

function initializeQuestionNavigator() {
    const questionGrid = document.getElementById('questionGrid');
    

    for (let i = 0; i < 50; i++) {
        const questionItem = document.createElement('div');
        questionItem.className = 'question-item unanswered';
        questionItem.textContent = i + 1;
        questionItem.dataset.question = i;
        questionGrid.appendChild(questionItem);
    }


    updateQuestionNavigator();
}

function loadQuestion(questionIndex) {
    currentQuestion = questionIndex;
    
    
    document.getElementById('questionNumber').textContent = questionIndex + 1;
    
    
    const question = examQuestions[questionIndex] || {
        question: `Sample question ${questionIndex + 1}`,
        options: ['Option A', 'Option B', 'Option C', 'Option D'],
        correct: 0,
        image: null
    };

    
    document.getElementById('questionText').textContent = question.question;

    
    const questionImage = document.getElementById('questionImage');
    const questionImg = document.getElementById('questionImg');
    if (question.image) {
        questionImg.src = question.image;
        questionImage.classList.remove('hidden');
    } else {
        questionImage.classList.add('hidden');
    }

   
    const answerOptions = document.getElementById('answerOptions');
    answerOptions.innerHTML = '';

    question.options.forEach((option, index) => {
        const optionElement = document.createElement('div');
        optionElement.className = 'answer-option';
        optionElement.dataset.option = index;
        
        optionElement.innerHTML = `
            <div class="option-radio"></div>
            <div class="option-label">${String.fromCharCode(65 + index)}</div>
            <div class="option-text">${option}</div>
        `;


        optionElement.addEventListener('click', () => selectAnswer(index));

        answerOptions.appendChild(optionElement);
    });


    updateNavigationButton();


    updateQuestionNavigator();


    updateProgress();
}

function selectAnswer(optionIndex) {

    answers[currentQuestion] = optionIndex;


    const answerOptions = document.querySelectorAll('.answer-option');
    answerOptions.forEach((option, index) => {
        option.classList.remove('selected');
        if (index === optionIndex) {
            option.classList.add('selected');
        }
    });

    
    updateQuestionNavigator();


    updateNavigationButton();
}

function updateNavigationButton() {
    const nextBtn = document.getElementById('nextBtn');
    const btnText = nextBtn.querySelector('.btn-text');

    
    const isAnswered = answers[currentQuestion] !== null;
    nextBtn.disabled = !isAnswered;


    if (currentQuestion === 49) {
        btnText.textContent = 'Submit Exam';
    } else {
        btnText.textContent = 'Next Question →';
    }
}

function nextQuestion() {

    if (answers[currentQuestion] === null) {
        alert('Please select an answer before proceeding');
        return;
    }


    if (currentQuestion === 49) {
        submitExam();
        return;
    }

    
    loadQuestion(currentQuestion + 1);
}

function updateQuestionNavigator() {
    const questionItems = document.querySelectorAll('.question-item');
    
    questionItems.forEach((item, index) => {
        item.classList.remove('current', 'answered', 'unanswered');
        
        if (index === currentQuestion) {
            item.classList.add('current');
        } else if (answers[index] !== null) {
            item.classList.add('answered');
        } else {
            item.classList.add('unanswered');
        }
    });
}

function updateProgress() {
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');
    
    const progress = ((currentQuestion + 1) / 50) * 100;
    progressFill.style.width = progress + '%';
    progressText.textContent = `Question ${currentQuestion + 1} of 50`;
}

function startTimer() {
    timerInterval = setInterval(() => {
        timeLeft--;
        

        updateTimerDisplay();
        

        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            submitExam();
        }
    }, 1000);
}

function updateTimerDisplay() {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    const timeString = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    
    document.getElementById('timerDisplay').textContent = timeString;
    document.getElementById('questionTimer').textContent = timeString;

    
    const timerDisplay = document.getElementById('timerDisplay');
    if (timeLeft < 300) { 
        timerDisplay.classList.add('danger');
    } else if (timeLeft < 600) {
        timerDisplay.classList.add('warning');
    }
}

function submitExam() {
    if (examTerminated) return;


    const unanswered = answers.filter(a => a === null).length;
    if (unanswered > 0) {
        alert('Please answer all questions before submitting');
        return;
    }

    
    const confirmed = confirm('Are you sure you want to submit your exam? You cannot change your answers after submission.');
    if (!confirmed) return;


    showSubmissionOverlay();


    setTimeout(() => {
        processExamSubmission();
    }, 2000);
}

function processExamSubmission() {
    
    if (timerInterval) {
        clearInterval(timerInterval);
    }


    const score = calculateScore();
    const percentage = Math.round((score / 50) * 100);
    const passed = score >= 40;


    hideSubmissionOverlay();

   
    showAnswerReview(score, percentage, passed);
}

function calculateScore() {
    let score = 0;
    for (let i = 0; i < 50; i++) {
       
        const correctAnswer = Math.floor(Math.random() * 4); 
        if (answers[i] === correctAnswer) {
            score++;
        }
    }
    return score;
}

function showAnswerReview(score, percentage, passed) {
    const modal = document.getElementById('answerReviewModal');
    const reviewList = document.getElementById('reviewList');
    const reviewSummary = document.getElementById('reviewSummary');


    reviewSummary.textContent = `Reviewing ${score} correct answers out of 50 questions`;

    
    reviewList.innerHTML = '';
    for (let i = 0; i < 50; i++) {
        const reviewItem = document.createElement('div');
        reviewItem.className = 'review-item';
        
        const status = answers[i] !== null ? '✅' : '⚪';
        const answerText = answers[i] !== null ? 
            String.fromCharCode(65 + answers[i]) : 'Not Answered';
        
        reviewItem.innerHTML = `
            <div class="review-status">${status}</div>
            <div class="review-question">
                <div class="review-question-number">Question ${i + 1}</div>
                <div class="review-answer">Answer: ${answerText}</div>
            </div>
        `;
        
        reviewList.appendChild(reviewItem);
    }


    modal.classList.remove('hidden');

    
    document.getElementById('proceedToResults').addEventListener('click', () => {
        modal.classList.add('hidden');
        showResults(score, percentage, passed);
    });
}

function showResults(score, percentage, passed) {
    const modal = document.getElementById('resultsModal');
    const resultsIcon = document.getElementById('resultsIcon');
    const resultsTitle = document.getElementById('resultsTitle');
    const resultsSubtitle = document.getElementById('resultsSubtitle');
    const scoreValue = document.getElementById('scoreValue');
    const scorePercentage = document.getElementById('scorePercentage');
    const resultsMessage = document.getElementById('resultsMessage');
    const proceedBtn = document.getElementById('proceedToDocuments');
    const rescheduleBtn = document.getElementById('rescheduleExam');

    
    if (passed) {
        resultsIcon.textContent = '🎉';
        resultsTitle.textContent = 'Exam Passed!';
        resultsSubtitle.textContent = 'Congratulations on passing your theory exam';
        resultsMessage.innerHTML = `
            <p>Excellent work! You've successfully passed the theory exam.</p>
            <p>Your practical test will be automatically scheduled for 3 months from today.</p>
        `;
        proceedBtn.style.display = 'block';
        rescheduleBtn.style.display = 'none';
    } else {
        resultsIcon.textContent = '❌';
        resultsTitle.textContent = 'Exam Failed';
        resultsSubtitle.textContent = 'You need to score at least 40 out of 50 to pass';
        resultsMessage.innerHTML = `
            <p>Unfortunately, you did not pass the theory exam.</p>
            <p>You need to score at least 40 out of 50 (80%) to pass. You scored ${score} out of 50.</p>
            <p>You can reschedule and retake the exam.</p>
        `;
        proceedBtn.style.display = 'none';
        rescheduleBtn.style.display = 'block';
    }


    scoreValue.textContent = score;
    scorePercentage.textContent = percentage + '%';

    
    modal.classList.remove('hidden');

    
    proceedBtn.addEventListener('click', () => {
        window.location.href = 'document-submission.php';
    });

    rescheduleBtn.addEventListener('click', () => {
        window.location.href = 'reschedule-exam.php';
    });

    
    updateExamResults(score, percentage, passed);
}

function updateExamResults(score, percentage, passed) {
    const tests = JSON.parse(localStorage.getItem('tests') || '{}');
    const applicationState = JSON.parse(localStorage.getItem('applicationState') || '{}');

    tests.theory.score = score;
    tests.theory.passed = passed;
    tests.theory.passedDate = new Date().toISOString();

    if (passed) {
        applicationState.status = 'theory_passed';
        applicationState.progress = 75;
        
        
        const practicalDate = new Date();
        practicalDate.setMonth(practicalDate.getMonth() + 3);
        
        tests.practical = {
            scheduled: true,
            date: practicalDate.toISOString(),
            time: '10:00 AM',
            center: 'Colombo - Werahera Test Center',
            address: 'No. 123, Baseline Road, Colombo 09',
            vehicle: 'Own Vehicle (Manual)',
            examiner: 'Mr. K. Perera',
            passed: false,
            passedDate: null
        };
        
       
        applicationState.status = 'practical_scheduled';
        applicationState.progress = 85;
    } else {
        applicationState.status = 'theory_failed';
        applicationState.progress = 60; 
    }

    localStorage.setItem('tests', JSON.stringify(tests));
    localStorage.setItem('applicationState', JSON.stringify(applicationState));
}


function initializeSecurityMeasures() {
    
    preventScreenshots();
    
    
    preventCopyPasteCut();
    

    preventRightClick();
    
    
    preventTabSwitching();
    
    
    preventDeveloperTools();
    
    
    detectWindowResize();
    

    clearClipboard();
    

    addUnloadWarning();
}

function preventScreenshots() {
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'PrintScreen') {
            e.preventDefault();
            showSecurityWarning('Screenshot attempt detected!');
            return false;
        }
    });

    document.addEventListener('keyup', function(e) {
        if (e.key === 'PrintScreen') {
            e.preventDefault();
            showSecurityWarning('Screenshot attempt detected!');
            return false;
        }
    });

    
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.shiftKey && e.key === 'S') {
            e.preventDefault();
            showSecurityWarning('Screenshot attempt detected!');
            return false;
        }
    });

    
    document.addEventListener('keydown', function(e) {
        if (e.metaKey && e.shiftKey && (e.key === '3' || e.key === '4' || e.key === '5')) {
            e.preventDefault();
            showSecurityWarning('Screenshot attempt detected!');
            return false;
        }
    });
}

function preventCopyPasteCut() {
    
    document.addEventListener('copy', function(e) {
        e.preventDefault();
        showSecurityWarning('Copying content detected!');
        return false;
    });

    
    document.addEventListener('paste', function(e) {
        e.preventDefault();
        showSecurityWarning('Pasting content detected!');
        return false;
    });

    
    document.addEventListener('cut', function(e) {
        e.preventDefault();
        showSecurityWarning('Cutting content detected!');
        return false;
    });

    
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && (e.key === 'c' || e.key === 'v' || e.key === 'x')) {
            e.preventDefault();
            showSecurityWarning('Copy/Paste/Cut attempt detected!');
            return false;
        }
    });
}

function preventRightClick() {
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        showSecurityWarning('Right-click detected!');
        return false;
    });
}

function preventTabSwitching() {
    
    document.addEventListener('visibilitychange', function() {
        if (examStarted && document.hidden) {
            showSecurityWarning('Tab switching detected!');
        }
    });

    
    window.addEventListener('blur', function() {
        if (examStarted) {
            showSecurityWarning('Window switching detected!');
        }
    });
}

function preventDeveloperTools() {
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F12') {
            e.preventDefault();
            showSecurityWarning('Developer tools access detected!');
            return false;
        }
    });

   
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'I') {
            e.preventDefault();
            showSecurityWarning('Developer tools access detected!');
            return false;
        }
    });


    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'J') {
            e.preventDefault();
            showSecurityWarning('Developer tools access detected!');
            return false;
        }
    });

    
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'u') {
            e.preventDefault();
            showSecurityWarning('Developer tools access detected!');
            return false;
        }
    });
}

function detectWindowResize() {
    let resizeCount = 0;
    window.addEventListener('resize', function() {
        resizeCount++;
        if (resizeCount > 3) {
            showSecurityWarning('Suspicious window resize detected!');
        }
    });
}

function clearClipboard() {
    setInterval(() => {
        try {
            navigator.clipboard.writeText('');
        } catch (error) {
            
        }
    }, 50);
}

function addUnloadWarning() {
    window.addEventListener('beforeunload', function(e) {
        if (!examTerminated) {
            e.preventDefault();
            e.returnValue = 'Are you sure you want to leave? Your exam progress will be lost.';
            return e.returnValue;
        }
    });
}

function showSecurityWarning(reason) {
    if (examTerminated) return;

    examTerminated = true;
    
    
    if (timerInterval) {
        clearInterval(timerInterval);
    }


    if (cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop());
    }


    const modal = document.getElementById('securityWarningModal');
    const warningReason = document.getElementById('warningReason');
    const countdownTimer = document.getElementById('countdownTimer');

    warningReason.textContent = reason;
    modal.classList.remove('hidden');


    let countdown = 3;
    countdownTimer.textContent = countdown;

    const countdownInterval = setInterval(() => {
        countdown--;
        countdownTimer.textContent = countdown;
        
        if (countdown <= 0) {
            clearInterval(countdownInterval);
            window.location.href = 'reschedule-exam.php';
        }
    }, 1000);
}



function showLoadingOverlay(message) {
    const overlay = document.getElementById('loadingOverlay');
    const loadingText = document.getElementById('loadingText');
    loadingText.textContent = message;
    overlay.classList.remove('hidden');
}

function hideLoadingOverlay() {
    const overlay = document.getElementById('loadingOverlay');
    overlay.classList.add('hidden');
}

function showSubmissionOverlay() {
    const overlay = document.getElementById('submissionOverlay');
    overlay.classList.remove('hidden');
}

function hideSubmissionOverlay() {
    const overlay = document.getElementById('submissionOverlay');
    overlay.classList.add('hidden');
}


document.addEventListener('click', function(e) {
    if (e.target.id === 'nextBtn' || e.target.closest('#nextBtn')) {
        nextQuestion();
    }
});


document.addEventListener('keydown', function(e) {

    if (e.altKey && e.key === 'Tab') {
        e.preventDefault();
        showSecurityWarning('Alt+Tab detected!');
        return false;
    }

    
    if (e.ctrlKey && e.key === 'Tab') {
        e.preventDefault();
        showSecurityWarning('Ctrl+Tab detected!');
        return false;
    }

    
    if (e.altKey && e.key === 'F4') {
        e.preventDefault();
        showSecurityWarning('Alt+F4 detected!');
        return false;
    }


    if (e.key === 'Meta' || e.key === 'Win') {
        e.preventDefault();
        showSecurityWarning('Windows key detected!');
        return false;
    }
});


document.addEventListener('dragstart', function(e) {
    e.preventDefault();
    return false;
});

document.addEventListener('drop', function(e) {
    e.preventDefault();
    return false;
});


document.addEventListener('selectstart', function(e) {
    e.preventDefault();
    return false;
});


document.addEventListener('focusin', function(e) {
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
        e.target.blur();
    }
});


window.addEventListener('load', function() {
    
    document.body.style.userSelect = 'none';
    document.body.style.webkitUserSelect = 'none';
    document.body.style.mozUserSelect = 'none';
    document.body.style.msUserSelect = 'none';
});
