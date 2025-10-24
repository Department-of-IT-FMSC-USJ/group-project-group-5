

document.addEventListener('DOMContentLoaded', function() {
  
    if (!LicenseXpress.checkAuth()) {
        window.location.href = 'login.php';
        return;
    }

    
    initializeScheduleTheory();
});

function initializeScheduleTheory() {
    const currentUser = LicenseXpress.getCurrentUser();
    
  
    updateUserInfo(currentUser);

    
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
            minBookingDate.setDate(minBookingDate.getDate() + 2);
            if (date < minBookingDate) {
                dayElement.classList.add('too-soon');
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

        
        updateBookingSummary(date, null);

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

    
    showBookingSummary();

    
    const selectedDateElement = document.getElementById('selectedDate');
    const selectedDate = new Date(selectedDateElement.textContent);
    updateBookingSummary(selectedDate, time);

    LicenseXpress.showToast('✅ Time slot selected. Review your booking.', 'success');
}

function showBookingSummary() {
    const bookingSummary = document.getElementById('bookingSummary');
    bookingSummary.classList.remove('hidden');

    
    const confirmButton = document.getElementById('confirmBooking');
    confirmButton.disabled = false;
}

function updateBookingSummary(selectedDate, selectedTime) {
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
    
    const confirmButton = document.getElementById('confirmBooking');
    confirmButton.addEventListener('click', confirmBooking);

    
    const goToDashboard = document.getElementById('goToDashboard');
    goToDashboard.addEventListener('click', function() {
        window.location.href = 'dashboard.php';
    });

    
    const logoutBtn = document.getElementById('logoutBtn');
    logoutBtn.addEventListener('click', function() {
        LicenseXpress.logout();
    });
}

function confirmBooking() {
    const confirmButton = document.getElementById('confirmBooking');
    const btnText = confirmButton.querySelector('.btn-text');
    const btnSpinner = confirmButton.querySelector('.btn-spinner');

    
    const selectedDateElement = document.getElementById('selectedDate');
    const selectedTimeElement = document.getElementById('summaryTime');
    
    if (!selectedDateElement.textContent || !selectedTimeElement.textContent) {
        LicenseXpress.showToast('Please select both date and time', 'error');
        return;
    }

    
    confirmButton.disabled = true;
    btnText.textContent = 'Processing Booking...';
    btnSpinner.classList.remove('hidden');

    
    setTimeout(() => {
        processBooking(selectedDateElement.textContent, selectedTimeElement.textContent);
    }, 2500);
}

function processBooking(selectedDate, selectedTime) {
    
    const tests = JSON.parse(localStorage.getItem('tests') || '{}');
    const applicationState = JSON.parse(localStorage.getItem('applicationState') || '{}');

    tests.theory = {
        scheduled: true,
        date: selectedDate,
        time: selectedTime,
        testLink: null, 
        score: null,
        passed: false,
        passedDate: null,
        attempts: 0
    };

    applicationState.status = 'theory_scheduled';
    applicationState.progress = 60;

    localStorage.setItem('tests', JSON.stringify(tests));
    localStorage.setItem('applicationState', JSON.stringify(applicationState));

    
    const currentUser = LicenseXpress.getCurrentUser();
    LicenseXpress.sendEmailNotification(
        currentUser?.email || 'user@example.com',
        'Theory Test Scheduled',
        `Your theory test has been scheduled for ${selectedDate} at ${selectedTime}. You will receive the test link 1 hour before your scheduled time.`
    );

    LicenseXpress.sendSMSNotification(
        currentUser?.phone || '+94771234567',
        `Your theory test is scheduled for ${selectedDate} at ${selectedTime}. Test link will be sent 1 hour before.`
    );


    showSuccessModal();
}

function showSuccessModal() {
    const successModal = document.getElementById('successModal');
    successModal.classList.remove('hidden');

    
    const confirmButton = document.getElementById('confirmBooking');
    const btnText = confirmButton.querySelector('.btn-text');
    const btnSpinner = confirmButton.querySelector('.btn-spinner');

    confirmButton.disabled = false;
    btnText.textContent = 'Confirm Booking →';
    btnSpinner.classList.add('hidden');

    LicenseXpress.showToast('✅ Booking confirmed successfully!', 'success');
}


document.addEventListener('click', function(e) {
    if (e.target.classList.contains('too-soon')) {
        LicenseXpress.showToast('⚠️ Tests must be scheduled at least 2 days in advance', 'warning');
    }
});


document.addEventListener('click', function(e) {
    if (e.target.classList.contains('disabled')) {
        LicenseXpress.showToast('⚠️ Cannot select past dates', 'warning');
    }
});
