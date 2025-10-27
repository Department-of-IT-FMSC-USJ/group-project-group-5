
async function fetchQuestions() {
    try {
        const res = await fetch('exam-window-backend.php?action=getQuestions', {
            method: 'GET',
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
        });
        if (res.status === 401) { 
            alert('Your session has expired. Please login again.');
            window.location.href = 'login.php'; 
            return []; 
        }
        const json = await res.json();
        if (!json.success) {
            console.error('Failed to load questions:', json.error);
            return [];
        }
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


window.suppressSecurityEvents = window.suppressSecurityEvents || false;


try { window.onbeforeunload = null; } catch (e) {}

//  POST with session cookie
async function submitAnswers(payload) {
    try {
        const res = await fetch('exam-window-backend.php?action=submitAnswers', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        });

        // handle errors in response
        if (res.status === 401) {
           
            return { success: false, error: 'Not authenticated. Please login and try again.' , status:401};
        }
        if (res.status === 403) {
            const txt = await res.text().catch(()=>null);
            let body = null;
            try { body = JSON.parse(txt); } catch(e){}
            return { success: false, error: (body && body.error) ? body.error : 'Not permitted to submit exam.', status:403 };
        }

        const json = await res.json().catch(() => null);
        return json;
    } catch (err) {
        console.error('submitAnswers network error', err);
        return { success: false, error: 'Network error while submitting exam' };
    }
}

async function submitExam() {
    if (typeof examTerminated !== 'undefined' && examTerminated) return;

    
    if (!Array.isArray(answers)) answers = new Array(totalQuestions).fill(null);
    const unanswered = answers.filter(a => a === null || typeof a === 'undefined').length;
    if (unanswered > 0) {
        alert(`Please answer all questions before submitting (${unanswered} unanswered).`);
        return;
    }

   
    suppressSecurityEvents = true;
    try {
        
        const confirmed = confirm('Are you sure you want to submit your exam? You cannot change your answers after submission.');
        if (!confirmed) return;

        showSubmissionOverlay && showSubmissionOverlay();

        const payload = { answers: answers };
        console.log('Submitting exam payload', payload);

        const res = await submitAnswers(payload);
        console.log('Submission response', res);

        if (!res || !res.success) {
            const message = res && res.error ? res.error : 'Unknown error submitting exam';
            alert(message);
            if (res && res.status === 401) {
                
                window.location.href = 'login.php';
            } else if (res && res.status === 403) {
                
                window.location.href = 'application-status.php';
            }
            return;
        }

        
        const score = Number(res.score) || 0;
        const total = Number(res.total) || totalQuestions;
        const percentage = Number(res.percentage) || Math.round((score / total) * 100);
        const passed = !!res.passed;
        const testId = res.test_id || null;

        try { localStorage.setItem('lastExamResult', JSON.stringify({ score, total, percentage, passed, testId, timestamp: new Date().toISOString() })); } catch(e){}

        
        if (typeof showResultsModal === 'function') {
            showResultsModal(score, total, percentage, passed, testId);
        } else {
            alert(`Result: ${score}/${total} (${percentage}%) - ${passed ? 'PASSED' : 'FAILED'}`);
        }

        
        examTerminated = true;
    } catch (err) {
        console.error('submitExam error', err);
        alert('Unexpected error while submitting exam. Please contact support.');
    } finally {
        suppressSecurityEvents = false;
        hideSubmissionOverlay && hideSubmissionOverlay();
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

                
                const questions = await fetchQuestions();
                if (!Array.isArray(questions) || questions.length === 0) {
                    hideLoadingOverlay();
                    alert('Unable to load exam questions. Please contact support.');
                    window.location.href = 'dashboard.php';
                    return;
                }
                examQuestions = questions;
                totalQuestions = examQuestions.length;
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
    // guards
    if (typeof currentQuestion !== 'number' || currentQuestion < 0 || currentQuestion >= totalQuestions) {
        console.warn('selectAnswer: invalid currentQuestion', currentQuestion);
        return;
    }

  
    answers[currentQuestion] = Number(optionIndex);

    
    const optionEls = document.querySelectorAll('#answerOptions .answer-option');
    optionEls.forEach((el, idx) => {
        el.classList.toggle('selected', idx === optionIndex);
    });

    
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


async function processExamSubmission() {
    try {
        if (timerInterval) clearInterval(timerInterval);

        const payload = { answers: answers };
        console.log('Submitting payload:', payload);
        showSubmissionOverlay();

        const res = await submitAnswers(payload);
        console.log('Backend response:', res);

        hideSubmissionOverlay();

        if (!res || !res.success) {
            alert('Error submitting exam: ' + (res && res.error ? res.error : 'Unknown error') + '\nPlease contact support.');
            return;
        }

        const score = Number.isFinite(Number(res.score)) ? Number(res.score) : 0;
        const total = Number.isFinite(Number(res.total)) ? Number(res.total) : totalQuestions;
        const percentage = total > 0 ? Math.round((score / total) * 100) : 0;
        const passed = (typeof res.passed === 'boolean') ? res.passed : (percentage >= 40);

        try { 
            localStorage.setItem('lastExamResult', JSON.stringify({ 
                score, total, percentage, passed, timestamp: new Date().toISOString() 
            })); 
        } catch (e) {
            console.error('Failed to save to localStorage:', e);
        }

        window.__lastExamServerResponse = res;
        showAnswerReview(score, percentage, passed);
    } catch (err) {
        console.error('processExamSubmission error:', err);
        alert('An unexpected error occurred while submitting the exam. Please contact support.');
    } finally {
        suppressSecurityEvents = false;
        hideSubmissionOverlay();
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

// Prevent PrintScreen 
function preventScreenshots() {
    document.addEventListener('keydown', function(e) {
        // PrintScreen
        if (e.key === 'PrintScreen') {
            e.preventDefault();
            showSecurityWarning('Screenshot attempt detected!');
            return false;
        }
        // Windows Screenshots
        if (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === 's') {
            e.preventDefault();
            showSecurityWarning('Screenshot attempt detected!');
            return false;
        }
        // Mac screenshot 
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

// Block copy/paste/cut 
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

// Block right click menu
function preventRightClick() {
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        showSecurityWarning('Right-click detected!');
        return false;
    });
}

//Block tab switching
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

// Block common shortcuts
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
        } catch (err) { /* ignore */ }
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
        } catch (err) {  }
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
 
