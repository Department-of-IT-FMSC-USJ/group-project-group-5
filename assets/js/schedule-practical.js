

document.addEventListener('DOMContentLoaded', function() {
    
    if (!LicenseXpress.checkAuth()) {
        window.location.href = 'login.php';
        return;
    }

    
    initializePracticalScheduling();
});

function initializePracticalScheduling() {
    const currentUser = LicenseXpress.getCurrentUser();
    
    
    updateUserInfo(currentUser);

    
    loadTestCenters();

    
    initializeCalendar();

    
    initializeTimeSlots();

    
    initializeVehicleSelection();

    
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

function loadTestCenters() {
    const testCentersGrid = document.getElementById('testCentersGrid');
    
    const testCenters = [
        {
            id: 'colombo-werahera',
            name: 'Colombo - Werahera Test Center',
            location: 'Werahera, Colombo',
            address: 'No. 123, Baseline Road, Colombo 09',
            status: 'available',
            features: ['Manual', 'Automatic', 'Parking', 'Waiting Area'],
            examiner: 'Mr. K. Perera'
        },
        {
            id: 'kandy-test-center',
            name: 'Kandy Test Center',
            location: 'Kandy',
            address: 'No. 456, Peradeniya Road, Kandy',
            status: 'available',
            features: ['Manual', 'Automatic', 'Parking'],
            examiner: 'Ms. S. Fernando'
        },
        {
            id: 'galle-test-center',
            name: 'Galle Test Center',
            location: 'Galle',
            address: 'No. 789, Galle Road, Galle',
            status: 'full',
            features: ['Manual', 'Parking'],
            examiner: 'Mr. R. Silva'
        },
        {
            id: 'kurunegala-test-center',
            name: 'Kurunegala Test Center',
            location: 'Kurunegala',
            address: 'No. 321, Kurunegala Road, Kurunegala',
            status: 'available',
            features: ['Manual', 'Automatic', 'Parking', 'Waiting Area', 'Cafeteria'],
            examiner: 'Mr. A. Rajapaksa'
        }
    ];

    testCentersGrid.innerHTML = testCenters.map(center => {
        const statusClass = center.status === 'available' ? 'available' : 'full';
        const statusText = center.status === 'available' ? 'Available' : 'Fully Booked';
        
        return `
            <div class="test-center" data-center-id="${center.id}">
                <div class="center-radio">
                    <input type="radio" name="testCenter" value="${center.id}" id="${center.id}" ${center.status === 'full' ? 'disabled' : ''}>
                    <label for="${center.id}"></label>
                </div>
                <div class="center-header">
                    <div>
                        <div class="center-name">${center.name}</div>
                        <div class="center-location">${center.location}</div>
                    </div>
                    <div class="center-status ${statusClass}">${statusText}</div>
                </div>
                <div class="center-details">
                    <div class="center-address">${center.address}</div>
                    <div class="center-features">
                        ${center.features.map(feature => `<span class="feature-tag">${feature}</span>`).join('')}
                    </div>
                </div>
            </div>
        `;
    }).join('');

    
    document.querySelectorAll('.test-center').forEach(center => {
        center.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            if (!radio.disabled) {
                radio.checked = true;
                selectTestCenter(this);
            }
        });
    });
}

function selectTestCenter(centerElement) {
    
    document.querySelectorAll('.test-center').forEach(center => {
        center.classList.remove('selected');
    });

    
    centerElement.classList.add('selected');

    
    showCalendarSection();

    LicenseXpress.showToast('✅ Test center selected. Choose your date.', 'success');
}

function showCalendarSection() {
    const calendarSection = document.querySelector('.calendar-section');
    calendarSection.classList.remove('hidden');
    
    
    calendarSection.scrollIntoView({ behavior: 'smooth' });
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
            minBookingDate.setDate(minBookingDate.getDate() + 7);
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

        
        updateBookingSummary(null, date, null, null);

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

    
    showVehicleSection();

    
    const selectedDateElement = document.getElementById('selectedDate');
    const selectedDate = new Date(selectedDateElement.textContent);
    updateBookingSummary(null, selectedDate, time, null);

    LicenseXpress.showToast('✅ Time slot selected. Choose vehicle option.', 'success');
}

function showVehicleSection() {
    const vehicleSection = document.getElementById('vehicleSection');
    vehicleSection.classList.remove('hidden');
    
    
    vehicleSection.scrollIntoView({ behavior: 'smooth' });
}

function initializeVehicleSelection() {
    const vehicleOptions = document.querySelectorAll('.vehicle-option');
    
    vehicleOptions.forEach(option => {
        option.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
            selectVehicle(this);
        });
    });
}

function selectVehicle(vehicleElement) {
    
    document.querySelectorAll('.vehicle-option').forEach(option => {
        option.classList.remove('selected');
    });

    
    vehicleElement.classList.add('selected');

    
    showBookingSummary();

    
    const selectedDateElement = document.getElementById('selectedDate');
    const selectedDate = new Date(selectedDateElement.textContent);
    const selectedTime = document.querySelector('.time-slot.selected .time-slot-time').textContent;
    const selectedVehicle = vehicleElement.querySelector('.option-title').textContent;
    
    updateBookingSummary(null, selectedDate, selectedTime, selectedVehicle);

    LicenseXpress.showToast('✅ Vehicle selected. Review your booking.', 'success');
}

function showBookingSummary() {
    const bookingSummary = document.getElementById('bookingSummary');
    bookingSummary.classList.remove('hidden');
    
    
    const confirmButton = document.getElementById('confirmBooking');
    confirmButton.disabled = false;
    
    
    bookingSummary.scrollIntoView({ behavior: 'smooth' });
}

function updateBookingSummary(center, date, time, vehicle) {
    const summaryCenter = document.getElementById('summaryCenter');
    const summaryDate = document.getElementById('summaryDate');
    const summaryTime = document.getElementById('summaryTime');
    const summaryVehicle = document.getElementById('summaryVehicle');

    if (center) {
        summaryCenter.textContent = center;
    }

    if (date) {
        const dateOptions = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        };
        summaryDate.textContent = date.toLocaleDateString('en-US', dateOptions);
    }

    if (time) {
        summaryTime.textContent = time;
    }

    if (vehicle) {
        summaryVehicle.textContent = vehicle;
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

    
    const selectedCenter = document.querySelector('input[name="testCenter"]:checked');
    const selectedDate = document.getElementById('selectedDate').textContent;
    const selectedTime = document.querySelector('.time-slot.selected .time-slot-time').textContent;
    const selectedVehicle = document.querySelector('input[name="vehicleType"]:checked');
    
    if (!selectedCenter || !selectedDate || !selectedTime || !selectedVehicle) {
        LicenseXpress.showToast('Please complete all selections', 'error');
        return;
    }

    
    confirmButton.disabled = true;
    btnText.textContent = 'Processing Booking...';
    btnSpinner.classList.remove('hidden');

    
    setTimeout(() => {
        processBooking(selectedCenter.value, selectedDate, selectedTime, selectedVehicle.value);
    }, 2500);
}

function processBooking(centerId, date, time, vehicleType) {
    
    const tests = JSON.parse(localStorage.getItem('tests') || '{}');
    const applicationState = JSON.parse(localStorage.getItem('applicationState') || '{}');

    
    tests.practical = {
        scheduled: true,
        date: new Date(date).toISOString(),
        time: time,
        center: getCenterName(centerId),
        address: getCenterAddress(centerId),
        vehicle: vehicleType === 'own' ? 'Own Vehicle' : 'Test Center Vehicle',
        examiner: getCenterExaminer(centerId),
        passed: false,
        passedDate: null
    };


    applicationState.status = 'practical_scheduled';
    applicationState.progress = 85;

    localStorage.setItem('tests', JSON.stringify(tests));
    localStorage.setItem('applicationState', JSON.stringify(applicationState));

    
    const currentUser = LicenseXpress.getCurrentUser();
    LicenseXpress.sendEmailNotification(
        currentUser?.email || 'user@example.com',
        'Practical Test Scheduled',
        `Your practical driving test has been scheduled for ${date} at ${time}. Please arrive 30 minutes before your scheduled time.`
    );

    LicenseXpress.sendSMSNotification(
        currentUser?.phone || '+94771234567',
        `Your practical test is scheduled for ${date} at ${time}. Arrive 30 minutes early.`
    );

    
    showSuccessModal();
}

function getCenterName(centerId) {
    const centers = {
        'colombo-werahera': 'Colombo - Werahera Test Center',
        'kandy-test-center': 'Kandy Test Center',
        'galle-test-center': 'Galle Test Center',
        'kurunegala-test-center': 'Kurunegala Test Center'
    };
    return centers[centerId] || 'Test Center';
}

function getCenterAddress(centerId) {
    const addresses = {
        'colombo-werahera': 'No. 123, Baseline Road, Colombo 09',
        'kandy-test-center': 'No. 456, Peradeniya Road, Kandy',
        'galle-test-center': 'No. 789, Galle Road, Galle',
        'kurunegala-test-center': 'No. 321, Kurunegala Road, Kurunegala'
    };
    return addresses[centerId] || 'Test Center Address';
}

function getCenterExaminer(centerId) {
    const examiners = {
        'colombo-werahera': 'Mr. K. Perera',
        'kandy-test-center': 'Ms. S. Fernando',
        'galle-test-center': 'Mr. R. Silva',
        'kurunegala-test-center': 'Mr. A. Rajapaksa'
    };
    return examiners[centerId] || 'Test Examiner';
}

function showSuccessModal() {
    const successModal = document.getElementById('successModal');
    successModal.classList.remove('hidden');

    
    const confirmButton = document.getElementById('confirmBooking');
    const btnText = confirmButton.querySelector('.btn-text');
    const btnSpinner = confirmButton.querySelector('.btn-spinner');

    confirmButton.disabled = false;
    btnText.textContent = 'Confirm Practical Test Booking';
    btnSpinner.classList.add('hidden');

    LicenseXpress.showToast('✅ Practical test booked successfully!', 'success');
}
