
 async function fetchQuestions() {
    try {
        console.log('Fetching questions from: exam-window-backend.php?action=getQuestions');
        const res = await fetch('exam-window-backend.php?action=getQuestions', {
            method: 'GET',
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
        });
        console.log('Response status:', res.status);
        console.log('Response headers:', res.headers);
        
        if (res.status === 401) { 
            alert('Your session has expired. Please login again.');
            window.location.href = 'login.php'; 
            return []; 
        }
        const json = await res.json();
        console.log('Response JSON:', json);
        
        if (!json.success) {
            console.error('Failed to load questions:', json.error);
            return [];
        }
        console.log('Successfully loaded questions:', json.data);
        return json.data;
    } catch (err) {
        console.error('Network or parse error fetching questions', err);
        return [];
    }
}

async function fetchQuestionById(id) {
    try {
        const res = await fetch(`exam-window-backend.php?action=getQuestion&id=${encodeURIComponent(id)}`, {
            method: 'GET',
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
        });
        if (res.status === 401) { 
            alert('Your session has expired. Please login again.');
            window.location.href = 'login.php'; 
            return null; 
        }
        const json = await res.json();
        if (!json.success) {
            console.error('Failed to fetch question', json.error);
            return null;
        }
        return json.data;
    } catch (err) {
        console.error('Error fetching question', err);
        return null;
    }
}

async function submitAnswers(payload) {
    try {
        console.log('Submitting to: exam-window-backend.php?action=submitAnswers');
        console.log('Payload being sent:', payload);
        
        const res = await fetch('exam-window-backend.php?action=submitAnswers', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        });
        
        console.log('Response status:', res.status);
        console.log('Response headers:', res.headers);
        
        // Handle different response 
        if (res.status === 401) {
            console.error('Authentication failed - User ID might be missing or invalid');
            return { success: false, error: 'Authentication failed. Please ensure you are logged in.' };
        }
        
        if (res.status === 400) {
            console.error('Bad request - Invalid data sent');
            return { success: false, error: 'Invalid exam data. Please try again.' };
        }
        
        if (res.status === 500) {
            console.error('Server error');
            return { success: false, error: 'Server error. Please contact support.' };
        }
        
        const json = await res.json();
        console.log('Response JSON:', json);
        return json;
    } catch (err) {
        console.error('Network error submitting answers:', err);
        return { success: false, error: 'Network error. Please check your connection and try again.' };
    }
}


let examStarted = false;
let examTerminated = false;
let currentQuestion = 0;
let answers = [];              
let timerInterval = null;
let timeLeft = 3600; 
let cameraStream = null;
let screenshotBlocked = false;

let examQuestions = [];        
let totalQuestions = 0;        


let suppressSecurityEvents = false;


document.addEventListener('DOMContentLoaded', async function() {
    if (!LicenseXpress.checkAuth()) {
        window.location.href = 'login.php';
        return;
    }
    showLoadingOverlay('Initializing exam...');
    requestCameraPermission();
});

function requestCameraPermission() {
    const permissionOverlay = document.getElementById('cameraPermissionOverlay');
    const permissionStatus = document.getElementById('permissionStatus');
    
    permissionOverlay.classList.remove('hidden');
    permissionStatus.textContent = 'Requesting camera permission...';

    navigator.mediaDevices.getUserMedia({ video: true, audio: false })
        .then(stream => {
           
            permissionStatus.textContent = 'Camera access granted'
            setTimeout(async () => {
                permissionOverlay.classList.add('hidden');

                
                console.log('Starting to fetch questions...');
                const questions = await fetchQuestions();
                console.log('Questions received:', questions);
                console.log('Questions length:', questions ? questions.length : 'null/undefined');
                
                if (!Array.isArray(questions) || questions.length === 0) {
                    console.error('No questions loaded, redirecting to dashboard');
                    hideLoadingOverlay();
                    alert('Unable to load exam questions. Please contact support.');
                    window.location.href = 'dashboard.php';
                    return;
                }
                examQuestions = questions;
                totalQuestions = examQuestions.length;
                console.log('Exam questions set:', examQuestions);
                console.log('Total questions:', totalQuestions);
                answers = new Array(totalQuestions).fill(null); // <-- ensure this runs after fetching questions

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
    
    if (typeof attachNextButtonHandler === 'function') attachNextButtonHandler();
    startTimer();
    initializeSecurityMeasures();
    enforceFullScreen(); 
    setTimeout(() => { examStarted = true; }, 3000);
    LicenseXpress.showToast('✅ Exam started successfully! Good luck!', 'success');
}


function initializeQuestionNavigator() {
    const questionGrid = document.getElementById('questionGrid');
    if (!questionGrid) return;
    questionGrid.innerHTML = '';

    
    for (let i = 0; i < totalQuestions; i++) {
        const questionItem = document.createElement('div');
        questionItem.className = 'question-item unanswered';
        questionItem.textContent = i + 1;
        questionItem.dataset.question = i;
       
        questionGrid.appendChild(questionItem);
    }

    updateQuestionNavigator();
}

function loadQuestion(questionIndex) {
    if (questionIndex < 0 || questionIndex >= totalQuestions) return;
    currentQuestion = questionIndex;
    const questionNumberEl = document.getElementById('questionNumber');
    if (questionNumberEl) questionNumberEl.textContent = questionIndex + 1;

    
    const question = examQuestions[questionIndex] || {
        text: `Sample question ${questionIndex + 1}`,
        options: ['Option A', 'Option B', 'Option C', 'Option D'],
        image: null
    };

    const questionTextEl = document.getElementById('questionText');
    if (questionTextEl) questionTextEl.textContent = question.text || '';

    const questionImage = document.getElementById('questionImage');
    const questionImg = document.getElementById('questionImg');
    if (question.image) {
        if (questionImg) questionImg.src = question.image;
        questionImage.classList.remove('hidden');
    } else {
        questionImage.classList.add('hidden');
    }

    
    const answerOptions = document.getElementById('answerOptions');
    if (!answerOptions) return;
    answerOptions.innerHTML = '';

    const opts = Array.isArray(question.options) ? question.options : (question.opts || []);
    
    for (let i = 0; i < opts.length; i++) {
        const option = opts[i] || '';
        const optionElement = document.createElement('div');
        optionElement.className = 'answer-option';
        optionElement.dataset.option = i;
        optionElement.innerHTML = `
            <div class="option-radio"></div>
            <div class="option-label">${String.fromCharCode(65 + i)}</div>
            <div class="option-text">${escapeHtml(option)}</div>
        `;
        optionElement.addEventListener('click', () => selectAnswer(i));
        if (answers[currentQuestion] === i) optionElement.classList.add('selected');
        answerOptions.appendChild(optionElement);
    }

    updateNavigationButton();
    updateQuestionNavigator();
    updateProgress();
}


function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}


function ensureAnswersInitialized() {
    if (!Array.isArray(answers) || answers.length !== totalQuestions) {
        answers = new Array(totalQuestions).fill(null);
    }
}


function selectAnswer(optionIndex) {
    
    if (typeof currentQuestion !== 'number' || currentQuestion < 0 || currentQuestion >= totalQuestions) {
        console.warn('selectAnswer: invalid currentQuestion', currentQuestion);
        return;
    }


    answers[currentQuestion] = Number(optionIndex);


    const optionEls = document.querySelectorAll('#answerOptions .answer-option');
    optionEls.forEach((el, idx) => {
        el.classList.toggle('selected', idx === optionIndex);
    });

    t
    updateQuestionNavigator(); 
    updateProgress();
    updateNavigationButton();

    
    console.log('Answer set', { currentQuestion, selected: optionIndex, answers_snapshot: answers.slice(0, 10) });
}


function updateNavigationButton() {
     const nextBtn = document.getElementById('nextBtn');
    if (!nextBtn) return;
    const btnText = nextBtn.querySelector('.btn-text');

    const answered = (answers[currentQuestion] !== null && answers[currentQuestion] !== undefined);
    nextBtn.disabled = !answered;

    if (currentQuestion === totalQuestions - 1) {
        if (btnText) btnText.textContent = 'Submit Exam';
   } else {
        if (btnText) btnText.textContent = 'Next Question →';
    }
}


function attachNextButtonHandler() {
    const nextBtn = document.getElementById('nextBtn');
    if (!nextBtn) return;


    nextBtn.onclick = null;

    nextBtn.addEventListener('click', function (e) {
        e.preventDefault();

        if (nextBtn.disabled) {
            alert('Please select an answer before proceeding');
            return;
        }
        
        if (!(answers[currentQuestion] !== null && answers[currentQuestion] !== undefined)) {
            alert('Please select an answer before proceeding');
            return;
        }
        
        if (currentQuestion === totalQuestions - 1) {
            submitExam();
            return;
        }
        
        loadQuestion(currentQuestion + 1);
    }, { passive: false });
}





function updateQuestionNavigator() {
    const questionItems = document.querySelectorAll('.question-item');
    questionItems.forEach((item, index) => {
        
        item.classList.remove('current', 'answered', 'unanswered', 'locked');

        if (index === currentQuestion) {
            item.classList.add('current');
           
            if (answers[index] !== null) item.classList.add('answered');
        } else {
            
            if (answers[index] !== null) {
                item.classList.add('answered');
            } else {
                item.classList.add('unanswered');
            }
        }
    });
}


window.nextQuestion = function() {
    if (answers[currentQuestion] === null) {
        alert('Please select an answer before proceeding');
        return;
    }
    if (currentQuestion === totalQuestions - 1) {
        submitExam();
        return;
    }

    
    const nextIndex = currentQuestion + 1;
    currentQuestion = nextIndex;
    
    loadQuestion(currentQuestion);
    updateQuestionNavigator();
    updateProgress();
    updateNavigationButton();
};


function updateProgress() {
    const progressText = document.getElementById('progressText');
    const progressFill = document.getElementById('progressFill');
    
    if (progressText) {
        progressText.textContent = `Question ${currentQuestion + 1} of ${totalQuestions}`;
    }
    
    if (progressFill) {
        const percentage = ((currentQuestion + 1) / totalQuestions) * 100;
        progressFill.style.width = percentage + '%';
    }
}


function startTimer() {
    if (timerInterval) clearInterval(timerInterval);
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
    const timerDisplay = document.getElementById('timerDisplay');
    const questionTimer = document.getElementById('questionTimer');
    if (timerDisplay) timerDisplay.textContent = timeString;
    if (questionTimer) questionTimer.textContent = timeString;
    if (timeLeft < 300 && timerDisplay) {
        timerDisplay.classList.add('danger');
    } else if (timeLeft < 600 && timerDisplay) {
        timerDisplay.classList.add('warning');
    }
}

async function submitExam() {
    if (examTerminated) {
        console.log('Exam already terminated, ignoring submit');
        return;
    }
    
    console.log('submitExam called', { answers, totalQuestions });
    
    
    const unanswered = answers.filter(a => a === null || a === undefined).length;
    if (unanswered > 0) {
        alert(`Please answer all questions before submitting (${unanswered} unanswered).`);
        return;
    }

    
    isSubmitting = true;
    suppressSecurityEvents = true;
    
    try {
        const confirmed = confirm('Are you sure you want to submit your exam? You cannot change your answers after submission.');
        if (!confirmed) {
            isSubmitting = false;
            suppressSecurityEvents = false;
            return;
        }

        showSubmissionOverlay();
        console.log('Calling processExamSubmission...');
        await processExamSubmission();
    } finally {
        isSubmitting = false;
        suppressSecurityEvents = false;
        hideSubmissionOverlay();
    }
}


async function processExamSubmission() {
    try {
        if (timerInterval) clearInterval(timerInterval);
        
        suppressSecurityEvents = true;
        examTerminated = false;

        const currentUser = JSON.parse(localStorage.getItem('currentUser') || '{}');
        const userId = currentUser.id || currentUser.userId;
        
        console.log('Current user from localStorage:', currentUser);
        console.log('Extracted userId:', userId);
        console.log('Available fields:', Object.keys(currentUser));
        
        
        const payload = { 
            answers: answers,
            userId: userId,
            userIdString: currentUser.userId,  
            userIdInt: currentUser.id           
        };
        console.log('Submitting payload:', payload);
        showSubmissionOverlay();

        const res = await submitAnswers(payload);
        console.log('Backend response:', res);

        hideSubmissionOverlay();

        if (!res || !res.success) {
            console.error('Submission failed:', res);
            
            const errorMsg = res && res.error ? res.error : 'Unknown error occurred';
            LicenseXpress.showToast(`❌ Exam submission failed: ${errorMsg}`, 'error');
            
            
            setTimeout(() => {
                
                suppressSecurityEvents = false;
            }, 2000);
            return;
        }

        
        console.log('Exam submitted successfully:', res);
        const score = Number.isFinite(Number(res.score)) ? Number(res.score) : 0;
        const total = Number.isFinite(Number(res.total)) ? Number(res.total) : 50;
        const passed = res.passed === true;
        
        
        localStorage.setItem('lastExamResult', JSON.stringify({
            score: score,
            total: total,
            passed: passed,
            testId: res.test_id,
            timestamp: new Date().toISOString()
        }));
        
       
        LicenseXpress.showToast(`✅ Exam submitted successfully! Score: ${score}/${total}`, 'success');
        
        
        setTimeout(() => {
            window.location.href = 'dashboard.php';
        }, 2000);
        
    } catch (error) {
        console.error('Error in exam submission process:', error);
        hideSubmissionOverlay();
        LicenseXpress.showToast('❌ An error occurred during submission. Please try again.', 'error');
        
       
        setTimeout(() => {
            suppressSecurityEvents = false;
        }, 2000);
    }
}


function showAnswerReview(score, percentage, passed) {
    const modal = document.getElementById('answerReviewModal');
    const reviewList = document.getElementById('reviewList');
    const reviewSummary = document.getElementById('reviewSummary');

    if (!modal) {
        console.warn('showAnswerReview: answerReviewModal not found, directly showing results');
        showResults(score, percentage, passed);
        return;
    }

    if (reviewSummary) reviewSummary.textContent = `You scored ${score} out of ${ (typeof window.__lastExamServerResponse !== 'undefined' && window.__lastExamServerResponse.total) ? window.__lastExamServerResponse.total : totalQuestions }. Proceed to final results.`;

    if (reviewList) {
        reviewList.innerHTML = '';
        for (let i = 0; i < totalQuestions; i++) {
            const reviewItem = document.createElement('div');
            reviewItem.className = 'review-item';
            const answerText = (answers[i] !== null && answers[i] !== undefined) ? String.fromCharCode(65 + answers[i]) : 'Not Answered';
            reviewItem.innerHTML = `
                <div class="review-question">
                    <div class="review-question-number">Question ${i + 1}</div>
                    <div class="review-answer">Answer: ${answerText}</div>
                </div>
            `;
            reviewList.appendChild(reviewItem);
        }
    }

    
    modal.classList.remove('hidden');

    
    const proceedBtnOld = document.getElementById('proceedToResults');
    if (proceedBtnOld) {
        const newBtn = proceedBtnOld.cloneNode(true);
        proceedBtnOld.parentNode.replaceChild(newBtn, proceedBtnOld);
        newBtn.addEventListener('click', () => {
            modal.classList.add('hidden');
            showResults(score, percentage, passed);
        });
    } else {
        
        setTimeout(() => {
            modal.classList.add('hidden');
            showResults(score, percentage, passed);
        }, 1200);
    }
}


function showResults(score, percentage, passed) {
    const modal = document.getElementById('resultsModal');
    const resultsIcon = document.getElementById('resultsIcon');
    const resultsTitle = document.getElementById('resultsTitle');
    const resultsSubtitle = document.getElementById('resultsSubtitle');
    const scoreValue = document.getElementById('scoreValue');
    const scorePercentage = document.getElementById('scorePercentage');
    const resultsMessage = document.getElementById('resultsMessage');
    const viewResultsBtn = document.getElementById('viewResultsPage');
    const proceedBtn = document.getElementById('proceedToDocuments');
    const rescheduleBtn = document.getElementById('rescheduleExam');

   
    if (passed) {
        if (resultsIcon) resultsIcon.textContent = '🎉';
        if (resultsTitle) resultsTitle.textContent = 'Exam Passed!';
        if (resultsSubtitle) resultsSubtitle.textContent = 'Congratulations — you passed the theory exam.';
        if (resultsMessage) resultsMessage.innerHTML = `<p>You scored ${score} out of ${totalQuestions} (${percentage}%).</p>`;
    } else {
        if (resultsIcon) resultsIcon.textContent = '❌';
        if (resultsTitle) resultsTitle.textContent = 'Exam Failed';
        if (resultsSubtitle) resultsSubtitle.textContent = 'You did not achieve the required pass mark.';
        if (resultsMessage) resultsMessage.innerHTML = `<p>You scored ${score} out of ${totalQuestions} (${percentage}%).</p>`;
    }

    if (scoreValue) scoreValue.textContent = score;
    if (scorePercentage) scorePercentage.textContent = percentage + '%';

    
    if (viewResultsBtn) {
        viewResultsBtn.style.display = 'inline-block';
        viewResultsBtn.replaceWith(viewResultsBtn.cloneNode(true));
        document.getElementById('viewResultsPage').addEventListener('click', () => {
            
            window.location.href = 'exam-results.php';
        });
    }

    if (proceedBtn) {
        proceedBtn.style.display = passed ? 'inline-block' : 'none';
        proceedBtn.addEventListener('click', () => { window.location.href = 'document-submission.php'; });
    }

    if (rescheduleBtn) {
        rescheduleBtn.style.display = passed ? 'none' : 'inline-block';
        rescheduleBtn.addEventListener('click', () => { window.location.href = 'reschedule-exam.php'; });
    }

    modal.classList.remove('hidden');

    
    updateExamResults(score, percentage, passed);
}

function updateExamResults(score, percentage, passed) {
    const tests = JSON.parse(localStorage.getItem('tests') || '{}');
    const applicationState = JSON.parse(localStorage.getItem('applicationState') || '{}');
    if (!tests.theory) tests.theory = {};
    tests.theory.score = score;
    tests.theory.passed = passed;
    tests.theory.passedDate = new Date().toISOString();
    if (passed) {
        applicationState.status = 'theory_passed';
        applicationState.progress = 75;
        const practicalDate = new Date();
        practicalDate.setMonth(practicalDate.getMonth() + 3);
        tests.practical = tests.practical || {};
        tests.practical.scheduled = true;
        tests.practical.date = practicalDate.toISOString();
        tests.practical.time = '10:00 AM';
        tests.practical.center = 'Colombo - Werahera Test Center';
        tests.practical.address = 'No. 123, Baseline Road, Colombo 09';
        tests.practical.vehicle = 'Own Vehicle (Manual)';
        tests.practical.examiner = 'Mr. K. Perera';
        tests.practical.passed = false;
        tests.practical.passedDate = null;
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
    disableTextSelection();
    blockDragAndDrop();
    monitorVisibilityAndFocus();
    
}


function preventScreenshots() {
    document.addEventListener('keydown', function(e) {
        
        if (e.key === 'PrintScreen') {
            e.preventDefault();
            showSecurityWarning('Screenshot attempt detected!');
            return false;
        }
       
        if (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === 's') {
            e.preventDefault();
            showSecurityWarning('Screenshot attempt detected!');
            return false;
        }
        
        if ((e.metaKey || e.ctrlKey) && e.shiftKey && (e.key === '3' || e.key === '4' || e.key === '5')) {
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
}


function preventCopyPasteCut() {
    document.addEventListener('copy', function(e) { e.preventDefault(); showSecurityWarning('Copying content detected!'); return false; });
    document.addEventListener('paste', function(e) { e.preventDefault(); showSecurityWarning('Pasting content detected!'); return false; });
    document.addEventListener('cut', function(e) { e.preventDefault(); showSecurityWarning('Cutting content detected!'); return false; });

    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && ['c','v','x','C','V','X'].includes(e.key)) {
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
        if (suppressSecurityEvents) return;
        if (examStarted && document.hidden) {
            showSecurityWarning('Tab switching detected!');
        }
    });

    window.addEventListener('blur', function() {
        if (suppressSecurityEvents) return;
        if (examStarted) {
            showSecurityWarning('Window switching detected!');
        }
    });
}


function preventDeveloperTools() {
    document.addEventListener('keydown', function(e) {
        
        if (e.key === 'F12') { e.preventDefault(); showSecurityWarning('Developer tools access detected!'); return false; }
        
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key.toLowerCase() === 'i' || e.key.toLowerCase() === 'j')) {
            e.preventDefault(); showSecurityWarning('Developer tools access detected!'); return false;
        }
        
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'u') {
            e.preventDefault(); showSecurityWarning('Developer tools access detected!'); return false;
        }
    });
}


function detectWindowResize() {
    let resizeCount = 0;
    window.addEventListener('resize', function() {
        resizeCount++;
        if (resizeCount > 3 && examStarted) {
            showSecurityWarning('Suspicious window resize detected!');
        }
    });
}


function clearClipboard() {
    setInterval(() => {
        try {
            if (navigator.clipboard && navigator.clipboard.writeText) navigator.clipboard.writeText('');
        } catch (err) {  }
    }, 1000);
}


let isSubmitting = false;

function addUnloadWarning() {
    window.addEventListener('beforeunload', function(e) {
       
        if (isSubmitting || examTerminated) {
            return;
        }
        
        if (!examTerminated && examStarted) {
            e.preventDefault();
            e.returnValue = 'Are you sure you want to leave? Your exam progress will be lost.';
            return e.returnValue;
        }
    });
}


function disableTextSelection() {
    document.body.style.userSelect = 'none';
    document.body.style.webkitUserSelect = 'none';
    document.body.style.mozUserSelect = 'none';
    document.body.style.msUserSelect = 'none';
    document.addEventListener('selectstart', function(e) { e.preventDefault(); return false; });
}


function blockDragAndDrop() {
    document.addEventListener('dragstart', function(e) { e.preventDefault(); return false; });
    document.addEventListener('drop', function(e) { e.preventDefault(); return false; });
}

function monitorVisibilityAndFocus() {
    document.addEventListener('focusin', function(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
            e.target.blur();
        }
    });
}


function enforceFullScreen() {
    const el = document.documentElement;
    if (el.requestFullscreen) {
        el.requestFullscreen().catch(() => {});
    } else if (el.webkitRequestFullscreen) {
        el.webkitRequestFullscreen().catch(() => {});
    }

    document.addEventListener('fullscreenchange', onFullScreenChange);
    document.addEventListener('webkitfullscreenchange', onFullScreenChange);
    document.addEventListener('mozfullscreenchange', onFullScreenChange);
    document.addEventListener('MSFullscreenChange', onFullScreenChange);
}

function onFullScreenChange() {
    const isFs = document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement;
    if (examStarted && !isFs) {
        showSecurityWarning('Exited fullscreen mode detected!');
    }
}


function showSecurityWarning(reason) {
    if (suppressSecurityEvents) return;
    if (examTerminated) return;

    
    if (timerInterval) { clearInterval(timerInterval); timerInterval = null; }


    if (cameraStream) {
        try {
            cameraStream.getTracks().forEach(track => track.stop());
        } catch (err) { }
    }

    examTerminated = true;
    const modal = document.getElementById('securityWarningModal');
    const warningReason = document.getElementById('warningReason');
    const countdownTimer = document.getElementById('countdownTimer');
    if (warningReason) warningReason.textContent = reason || 'Security violation detected!';
    if (countdownTimer) countdownTimer.textContent = '3';
    if (modal) modal.classList.remove('hidden');

    
    let countdown = 3;
    const countdownInterval = setInterval(() => {
        countdown--;
        if (countdownTimer) countdownTimer.textContent = String(countdown);
        if (countdown <= 0) {
            clearInterval(countdownInterval);
            window.location.href = 'reschedule-exam.php';
        }
    }, 1000);
}


function showLoadingOverlay(message) {
    const overlay = document.getElementById('loadingOverlay');
    const loadingText = document.getElementById('loadingText');
    if (loadingText) loadingText.textContent = message;
    if (overlay) overlay.classList.remove('hidden');
}
function hideLoadingOverlay() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) overlay.classList.add('hidden');
}
function showSubmissionOverlay() {
    const overlay = document.getElementById('submissionOverlay');
    if (overlay) overlay.classList.remove('hidden');
}
function hideSubmissionOverlay() {
    const overlay = document.getElementById('submissionOverlay');
    if (overlay) overlay.classList.add('hidden');
}


document.addEventListener('click', function(e) {
    const btn = e.target.id === 'nextBtn' ? e.target : (e.target.closest && e.target.closest('#nextBtn'));
    if (!btn) return;
    
    if (btn.disabled) {
        e.preventDefault();
        return;
    }
    
    nextQuestion();
});


async function testSubmitSuccess() {
    const currentUser = JSON.parse(localStorage.getItem('currentUser') || '{}');
    const userId = currentUser.id || currentUser.userId;
    
    if (!userId) {
        alert('❌ No user ID available for test submission');
        return;
    }
    
    try {
        
        const confirmed = confirm('🎯 TEST SUBMIT: This will simulate a successful exam submission and update your status to "theory_passed". Continue?');
        if (!confirmed) {
            return;
        }
        
        
        const backendResponse = await fetch('exam-window-backend.php?action=testSubmit', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ userId: userId })
        });
        
        const backendResult = await backendResponse.text();
        console.log('Backend response:', backendResult);
        
        let backendSuccess = false;
        let mockExamResult = {
            success: true,
            score: 45,
            total: 50,
            passed: true,
            test_id: 'TEST_' + Date.now(),
            message: 'Exam submitted successfully!'
        };
        
        if (backendResponse.ok) {
            try {
                const backendData = JSON.parse(backendResult);
                if (backendData.success) {
                    backendSuccess = true;
                    mockExamResult = {
                        success: true,
                        score: backendData.score,
                        total: backendData.total,
                        passed: backendData.passed,
                        test_id: backendData.test_id,
                        message: backendData.message
                    };
                }
            } catch (e) {
                console.log('Backend response parsing failed, using mock data');
            }
        }
        
        console.log('Simulating successful exam submission:', mockExamResult);
        
        
        localStorage.setItem('lastExamResult', JSON.stringify({
            score: mockExamResult.score,
            total: mockExamResult.total,
            passed: mockExamResult.passed,
            testId: mockExamResult.test_id,
            timestamp: new Date().toISOString()
        }));
        
        
        const updatedUser = {
            ...currentUser,
            status: 'theory_passed',
            lastUpdated: new Date().toISOString()
        };
        localStorage.setItem('currentUser', JSON.stringify(updatedUser));
        
        
        const successMessage = `🎉 TEST SUBMIT SUCCESSFUL!
        
Score: ${mockExamResult.score}/${mockExamResult.total} (${Math.round((mockExamResult.score/mockExamResult.total)*100)}%)
Status: PASSED ✅
Backend Update: ${backendSuccess ? '✅ Database Updated' : '⚠️ Frontend Only'}
User Status: theory_passed

Next Steps:
✅ Exam results stored in localStorage
✅ User status updated to "theory_passed"
${backendSuccess ? '✅ Database status updated to "theory_passed"\n✅ Theory test record created in database' : ''}
✅ Dashboard will show "Theory Test Results" section
✅ Dashboard will show "What's Next?" section

Click OK to go to the dashboard and see the changes!`;
        
        alert(successMessage);
        
       
        window.location.href = 'dashboard.php';
        
    } catch (error) {
        console.error('Test submission failed:', error);
        alert(`❌ Test submission failed: ${error.message}`);
    }
}


window.testSubmitSuccess = testSubmitSuccess;
 
