
document.addEventListener('DOMContentLoaded', function() {
   
    if (!LicenseXpress.checkAuth()) {
        window.location.href = 'login.php';
        return;
    }

    
    initializeRescheduleExam();
});

function initializeRescheduleExam() {
    const currentUser = LicenseXpress.getCurrentUser();
    const tests = JSON.parse(localStorage.getItem('tests') || '{}');
    
    
    updateUserInfo(currentUser);

   
    loadPreviousAttempt(tests);

    
    initializeCalendar();

    
    initializeTimeSlots();

    
    initializeNavigation();
}

function updateUserInfo(user) {
    const userName = document.getElementById('userName');
    const userNIC = document.getElementById('userNIC');
    const userAvatar = document.getElementById('userAvatar');

    if (user) {
        const name = user.fullName || 'User';
        const initial = name.charAt(0).toUpperCase();
        
        userName.textContent = name;
        userNIC.textContent = LicenseXpress.formatNIC(user.nic);
        userAvatar.textContent = initial;
    }
}

function loadPreviousAttempt(tests) {
    const previousScore = document.getElementById('previousScore');
    const previousDate = document.getElementById('previousDate');
    const previousTime = document.getElementById('previousTime');
    const attemptCount = document.getElementById('attemptCount');

    if (tests.theory) {
        const score = tests.theory.score || 0;
        const total = 50;
        const percentage = Math.round((score / total) * 100);
        
        previousScore.textContent = `${score}/${total} (${percentage}%)`;
        previousDate.textContent = LicenseXpress.formatDate(tests.theory.passedDate);
        previousTime.textContent = '45 minutes';
        attemptCount.textContent = tests.theory.attempts || 1;
    }
}

function initializeCalendar() {
    const calendarMonth = document.getElementById('calendarMonth');
    const calendarDays = document.getElementById('calendarDays');
    const prevMonth = document.getElementById('prevMonth');
    const nextMonth = document.getElementById('nextMonth');

    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();

    
    function renderCalendar() {
        const firstDay = new Date(currentYear, currentMonth, 1);
        const lastDay = new Date(currentYear, currentMonth + 1, 0);
        const startDate = new Date(firstDay);
        startDate.setDate(startDate.getDate() - firstDay.getDay());

        const monthNames = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        calendarMonth.textContent = `${monthNames[currentMonth]} ${currentYear}`;

        calendarDays.innerHTML = '';

        
        for (let i = 0; i < 42; i++) {
            const date = new Date(startDate);
            date.setDate(startDate.getDate() + i);

            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day';
            dayElement.textContent = date.getDate();

            
            if (date.getMonth() !== currentMonth) {
                dayElement.style.opacity = '0.3';
            }

            
            const today = new Date();
            if (date.toDateString() === today.toDateString()) {
                dayElement.classList.add('today');
            }

            
            if (date < today) {
                dayElement.classList.add('disabled');
            }

            
            const minBookingDate = new Date();
            minBookingDate.setDate(minBookingDate.getDate() + 1);
            if (date < minBookingDate) {
                dayElement.classList.add('disabled');
            }

            
            if (date >= minBookingDate && date.getMonth() === currentMonth) {
                dayElement.addEventListener('click', () => selectDate(date, dayElement));
            }

            calendarDays.appendChild(dayElement);
        }
    }


    function selectDate(date, element) {
        
        document.querySelectorAll('.calendar-day.selected').forEach(day => {
            day.classList.remove('selected');
        });

        
        element.classList.add('selected');

        
        showTimeSlotsSection(date);

        
        const selectedDateElement = document.getElementById('selectedDate');
        const dateOptions = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        };
        selectedDateElement.textContent = date.toLocaleDateString('en-US', dateOptions);

        
        updateRescheduleSummary(date, null);

        LicenseXpress.showToast('✅ Date selected. Choose a time slot.', 'success');
    }

    
    prevMonth.addEventListener('click', () => {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        renderCalendar();
    });

    nextMonth.addEventListener('click', () => {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        renderCalendar();
    });

    renderCalendar();
}

function showTimeSlotsSection(selectedDate) {
    const timeSlotsSection = document.getElementById('timeSlotsSection');
    const timeSlotsLoading = document.getElementById('timeSlotsLoading');
    const timeSlotsGrid = document.getElementById('timeSlotsGrid');

    timeSlotsSection.classList.remove('hidden');
    timeSlotsLoading.classList.remove('hidden');
    timeSlotsGrid.classList.add('hidden');

    
    setTimeout(() => {
        loadTimeSlots(selectedDate);
    }, 1200);
}

function loadTimeSlots(selectedDate) {
    const timeSlotsLoading = document.getElementById('timeSlotsLoading');
    const timeSlotsGrid = document.getElementById('timeSlotsGrid');

    
    timeSlotsLoading.classList.add('hidden');
    timeSlotsGrid.classList.remove('hidden');

    
    const timeSlots = [
        { time: '09:00 AM', icon: '🌅', available: true },
        { time: '10:00 AM', icon: '☀️', available: true },
        { time: '11:00 AM', icon: '⏰', available: false },
        { time: '12:00 PM', icon: '🕛', available: true },
        { time: '01:00 PM', icon: '🌤️', available: false },
        { time: '02:00 PM', icon: '☀️', available: true },
        { time: '03:00 PM', icon: '🌤️', available: true },
        { time: '04:00 PM', icon: '🌆', available: true },
        { time: '05:00 PM', icon: '🌇', available: false }
    ];

    timeSlotsGrid.innerHTML = '';

    timeSlots.forEach(slot => {
        const slotElement = document.createElement('div');
        slotElement.className = `time-slot ${slot.available ? 'available' : 'booked'}`;
        
        slotElement.innerHTML = `
            <div class="time-slot-icon">${slot.icon}</div>
            <div class="time-slot-time">${slot.time}</div>
            <div class="time-slot-status">${slot.available ? '✓ Available' : '✗ Booked'}</div>
        `;

        if (slot.available) {
            slotElement.addEventListener('click', () => selectTimeSlot(slot.time, slotElement));
        }

        timeSlotsGrid.appendChild(slotElement);
    });
}

function selectTimeSlot(time, element) {
   
    document.querySelectorAll('.time-slot.selected').forEach(slot => {
        slot.classList.remove('selected');
    });

    
    element.classList.add('selected');

    
    showRescheduleSummary();

    
    const selectedDateElement = document.getElementById('selectedDate');
    const selectedDate = new Date(selectedDateElement.textContent);
    updateRescheduleSummary(selectedDate, time);

    LicenseXpress.showToast('✅ Time slot selected. Review your reschedule.', 'success');
}

function showRescheduleSummary() {
    const rescheduleSummary = document.getElementById('rescheduleSummary');
    rescheduleSummary.classList.remove('hidden');

    
    const confirmButton = document.getElementById('confirmReschedule');
    confirmButton.disabled = false;
}

function updateRescheduleSummary(selectedDate, selectedTime) {
    const summaryDate = document.getElementById('summaryDate');
    const summaryTime = document.getElementById('summaryTime');

    if (selectedDate) {
        const dateOptions = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        };
        summaryDate.textContent = selectedDate.toLocaleDateString('en-US', dateOptions);
    }

    if (selectedTime) {
        summaryTime.textContent = selectedTime;
    }
}

function initializeTimeSlots() {
    
}

function initializeNavigation() {
    
    const confirmButton = document.getElementById('confirmReschedule');
    confirmButton.addEventListener('click', confirmReschedule);

    
    const goToDashboard = document.getElementById('goToDashboard');
    goToDashboard.addEventListener('click', function() {
        window.location.href = 'dashboard.php';
    });

    
    const logoutBtn = document.getElementById('logoutBtn');
    logoutBtn.addEventListener('click', function() {
        LicenseXpress.logout();
    });
}

function confirmReschedule() {
    const confirmButton = document.getElementById('confirmReschedule');
    const btnText = confirmButton.querySelector('.btn-text');
    const btnSpinner = confirmButton.querySelector('.btn-spinner');

    
    const selectedDateElement = document.getElementById('selectedDate');
    const selectedTimeElement = document.getElementById('summaryTime');
    
    if (!selectedDateElement.textContent || !selectedTimeElement.textContent) {
        LicenseXpress.showToast('Please select both date and time', 'error');
        return;
    }

    
    confirmButton.disabled = true;
    btnText.textContent = 'Processing Reschedule...';
    btnSpinner.classList.remove('hidden');

    
    setTimeout(() => {
        processReschedule(selectedDateElement.textContent, selectedTimeElement.textContent);
    }, 2500);
}

function processReschedule(selectedDate, selectedTime) {
    
    const tests = JSON.parse(localStorage.getItem('tests') || '{}');
    const applicationState = JSON.parse(localStorage.getItem('applicationState') || '{}');

    
    tests.theory.date = new Date(selectedDate).toISOString();
    tests.theory.time = selectedTime;
    tests.theory.scheduled = true;
    tests.theory.attempts = (tests.theory.attempts || 0) + 1;

    
    tests.theory.score = null;
    tests.theory.passed = false;
    tests.theory.passedDate = null;

    
    applicationState.status = 'theory_scheduled';
    applicationState.progress = 60;

    localStorage.setItem('tests', JSON.stringify(tests));
    localStorage.setItem('applicationState', JSON.stringify(applicationState));

    
    const currentUser = LicenseXpress.getCurrentUser();
    LicenseXpress.sendEmailNotification(
        currentUser?.email || 'user@example.com',
        'Theory Exam Rescheduled',
        `Your theory exam has been rescheduled to ${selectedDate} at ${selectedTime}. You can now take your exam on the new scheduled date.`
    );

    LicenseXpress.sendSMSNotification(
        currentUser?.phone || '+94771234567',
        `Your theory exam has been rescheduled to ${selectedDate} at ${selectedTime}.`
    );

   
    showSuccessModal();
}

function showSuccessModal() {
    const successModal = document.getElementById('successModal');
    successModal.classList.remove('hidden');

    
    const confirmButton = document.getElementById('confirmReschedule');
    const btnText = confirmButton.querySelector('.btn-text');
    const btnSpinner = confirmButton.querySelector('.btn-spinner');

    confirmButton.disabled = false;
    btnText.textContent = 'Confirm Reschedule (Rs. 500)';
    btnSpinner.classList.add('hidden');

    LicenseXpress.showToast('✅ Exam rescheduled successfully!', 'success');
}
