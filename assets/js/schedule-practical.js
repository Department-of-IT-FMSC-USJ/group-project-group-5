

document.addEventListener('DOMContentLoaded', function() {
    
    initializePracticalScheduling();
});

let selectedTestCenter = null;
let selectedDate = null;
let selectedTime = null;
let isRescheduling = false;

function initializePracticalScheduling() {
    console.log('Initializing practical scheduling...');
    const appData = window.applicationData;
    
    
    if (appData.rescheduleCount >= 1) {
        disableRescheduleButton();
    }
    
    
    loadTestCenters();

    
    if (appData && appData.testCenterId) {
        setTimeout(() => {
            const centerElement = document.querySelector(`[data-center-id="${appData.testCenterId}"]`);
            if (centerElement) {
                centerElement.querySelector('input[type="radio"]').checked = true;
                selectTestCenter(centerElement);
            }
        }, 100);
    }

    
    initializeCalendar();

    
    console.log('Initializing reschedule...');
    initializeReschedule();

    
    initializeNavigation();
    
    console.log('Practical scheduling initialized successfully');
}

function disableRescheduleButton() {
    const rescheduleBtn = document.getElementById('rescheduleBtn');
    if (rescheduleBtn) {
        rescheduleBtn.disabled = true;
        rescheduleBtn.textContent = 'Reschedule Used (1/1)';
        rescheduleBtn.style.opacity = '0.6';
        rescheduleBtn.style.cursor = 'not-allowed';
        rescheduleBtn.title = 'You have already used your one reschedule opportunity';
    }
}

function loadTestCenters() {
    const testCentersGrid = document.getElementById('testCentersGrid');
    
    
    testCentersGrid.innerHTML = '<div style="text-align: center; padding: 40px;">Loading test centers...</div>';
    
    
    fetch('api_booking.php?action=get_test_centers', {
        credentials: 'same-origin'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                renderTestCenters(data.data);
            } else {
                testCentersGrid.innerHTML = '<div style="text-align: center; padding: 40px; color: #EF4444;">Failed to load test centers</div>';
                console.error('Error loading test centers:', data.message);
            }
        })
        .catch(error => {
            testCentersGrid.innerHTML = '<div style="text-align: center; padding: 40px; color: #EF4444;">Error loading test centers</div>';
            console.error('Error fetching test centers:', error);
        });
}

function renderTestCenters(testCenters) {
    const testCentersGrid = document.getElementById('testCentersGrid');
    
    testCentersGrid.innerHTML = testCenters.map(center => {
        const statusClass = 'available'; 
        const statusText = 'Available';
        const features = center.facilities || [];
        const slug = center.name.toLowerCase().replace(/\s+/g, '-');
        
        return `
            <div class="test-center" data-center-id="${center.id}" data-center-slug="${slug}" data-center-name="${center.name}" data-center-address="${center.address}">
                <div class="center-radio">
                    <input type="radio" name="testCenter" value="${center.id}" id="center-${center.id}">
                    <label for="center-${center.id}"></label>
                </div>
                <div class="center-header">
                    <div>
                        <div class="center-name">${center.name}</div>
                        <div class="center-location">${center.district}</div>
                    </div>
                    <div class="center-status ${statusClass}">${statusText}</div>
                </div>
                <div class="center-details">
                    <div class="center-address">${center.address}</div>
                    <div class="center-features">
                        ${features.map(feature => `<span class="feature-tag">${feature}</span>`).join('')}
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
    
    selectedTestCenter = {
        id: centerElement.dataset.centerId,
        name: centerElement.dataset.centerName,
        address: centerElement.dataset.centerAddress
    };

    checkBookingCompletion();
    LicenseXpress.showToast('✅ Test center selected.', 'success');
}

function initializeReschedule() {
    const rescheduleBtn = document.getElementById('rescheduleBtn');
    const cancelReschedule = document.getElementById('cancelReschedule');
    
    if (!rescheduleBtn) {
        console.error('Reschedule button not found!');
        return;
    }
    
    console.log('Reschedule button found, attaching event listener');
    
    rescheduleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        console.log('Reschedule button clicked!');
        isRescheduling = true;
        
        
        const testCenterSection = document.getElementById('testCenterSection');
        const calendarSection = document.getElementById('calendarSection');
        
        console.log('Sections found:', {
            testCenterSection: !!testCenterSection,
            calendarSection: !!calendarSection
        });
        
        if (!testCenterSection || !calendarSection) {
            console.error('One or more sections not found!');
            alert('Error: Some sections not found. Please check console for details.');
            return;
        }
        
        testCenterSection.classList.remove('hidden');
        calendarSection.classList.remove('hidden');
        
        rescheduleBtn.classList.add('hidden');
        testCenterSection.scrollIntoView({ behavior: 'smooth' });
        
        if (typeof LicenseXpress !== 'undefined' && LicenseXpress.showToast) {
            LicenseXpress.showToast('📅 Select your preferred test center, date, and time', 'info');
        } else {
            console.log('Toast notification not available');
        }
    });
    
    if (cancelReschedule) {
        cancelReschedule.addEventListener('click', function() {
            isRescheduling = false;
            const testCenterSection = document.getElementById('testCenterSection');
            const calendarSection = document.getElementById('calendarSection');
            const timeSlotsSection = document.getElementById('timeSlotsSection');
            const rescheduleBtn = document.getElementById('rescheduleBtn');
            
            testCenterSection.classList.add('hidden');
            calendarSection.classList.add('hidden');
            timeSlotsSection.classList.add('hidden');
            rescheduleBtn.classList.remove('hidden');
            

            document.querySelectorAll('.calendar-day.selected').forEach(day => {
                day.classList.remove('selected');
            });
            document.querySelectorAll('.time-slot.selected').forEach(slot => {
                slot.classList.remove('selected');
            });
            document.querySelectorAll('.test-center.selected').forEach(center => {
                center.classList.remove('selected');
            });
            selectedDate = null;
            selectedTime = null;
            selectedTestCenter = null;
            
            LicenseXpress.showToast('Reschedule cancelled', 'info');
        });
    }
}

function initializeCalendar() {
    const calendarMonth = document.getElementById('calendarMonth');
    const calendarDays = document.getElementById('calendarDays');
    const prevMonth = document.getElementById('prevMonth');
    const nextMonth = document.getElementById('nextMonth');

    
    const appData = window.applicationData;
    const originalScheduledDate = new Date(appData.practicalDate);
    
    
    let currentDate = new Date(originalScheduledDate);
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

            
            if (date.toDateString() === originalScheduledDate.toDateString()) {
                dayElement.classList.add('original-date');
                dayElement.title = 'Original scheduled date';
            }

            
            if (date < originalScheduledDate) {
                dayElement.classList.add('disabled');
            }

            
            if (date >= originalScheduledDate && date.getMonth() === currentMonth) {
                dayElement.addEventListener('click', () => selectCalendarDate(date, dayElement));
            }

            calendarDays.appendChild(dayElement);
        }
    }

    
    function selectCalendarDate(date, element) {
        
        document.querySelectorAll('.calendar-day.selected').forEach(day => {
            day.classList.remove('selected');
        });

        
        element.classList.add('selected');
        
        selectedDate = date;

        
        showTimeSlotsSection(date);

        
        const selectedDateElement = document.getElementById('selectedDate');
        const dateOptions = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        };
        selectedDateElement.textContent = date.toLocaleDateString('en-US', dateOptions);

        LicenseXpress.showToast('✅ Date selected. Choose a time slot.', 'success');
    }

    
    prevMonth.addEventListener('click', () => {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        
        
        const newDate = new Date(currentYear, currentMonth, 1);
        if (newDate < new Date(originalScheduledDate.getFullYear(), originalScheduledDate.getMonth(), 1)) {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            return;
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

    
    timeSlotsSection.scrollIntoView({ behavior: 'smooth' });

    
    setTimeout(() => {
        loadTimeSlots(selectedDate);
    }, 1200);
}

function loadTimeSlots(selectedDate) {
    const timeSlotsLoading = document.getElementById('timeSlotsLoading');
    const timeSlotsGrid = document.getElementById('timeSlotsGrid');

    
    timeSlotsLoading.classList.remove('hidden');
    timeSlotsGrid.classList.add('hidden');

    if (!selectedTestCenter) {
        LicenseXpress.showToast('Please select a test center first', 'error');
        return;
    }

    const formattedDate = selectedDate.toISOString().split('T')[0];

    
    fetch(`api_booking.php?action=get_time_slots&type=practical&date=${formattedDate}&test_center_id=${selectedTestCenter.id}`, {
        credentials: 'same-origin'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayTimeSlots(data.data);
            } else {
                console.error('Failed to load time slots:', data.message);
                displayFallbackTimeSlots();
            }
        })
        .catch(error => {
            console.error('Error loading time slots:', error);
            displayFallbackTimeSlots();
        });
}

function displayTimeSlots(timeSlots) {
    const timeSlotsLoading = document.getElementById('timeSlotsLoading');
    const timeSlotsGrid = document.getElementById('timeSlotsGrid');

    timeSlotsLoading.classList.add('hidden');
    timeSlotsGrid.classList.remove('hidden');

    timeSlotsGrid.innerHTML = '';

    timeSlots.forEach(slot => {
        const slotElement = document.createElement('div');
        slotElement.className = `time-slot ${slot.available ? 'available' : 'booked'}`;
        slotElement.dataset.time = slot.time_24h;
        
        slotElement.innerHTML = `
            <div class="time-slot-icon">${slot.icon}</div>
            <div class="time-slot-time">${slot.time}</div>
            <div class="time-slot-status">${slot.available ? '✓ Available' : '✗ Booked'}</div>
        `;

        if (slot.available) {
            slotElement.addEventListener('click', () => selectTimeSlot(slot.time, slot.time_24h, slotElement));
        }

        timeSlotsGrid.appendChild(slotElement);
    });
}

function displayFallbackTimeSlots() {
    const timeSlotsLoading = document.getElementById('timeSlotsLoading');
    const timeSlotsGrid = document.getElementById('timeSlotsGrid');

    timeSlotsLoading.classList.add('hidden');
    timeSlotsGrid.classList.remove('hidden');

    
    const timeSlots = [
        { time: '09:00 AM', time_24h: '09:00:00', icon: '🌅', available: true },
        { time: '10:00 AM', time_24h: '10:00:00', icon: '☀️', available: true },
        { time: '11:00 AM', time_24h: '11:00:00', icon: '⏰', available: false },
        { time: '12:00 PM', time_24h: '12:00:00', icon: '🕛', available: true },
        { time: '01:00 PM', time_24h: '13:00:00', icon: '🌤️', available: false },
        { time: '02:00 PM', time_24h: '14:00:00', icon: '☀️', available: true },
        { time: '03:00 PM', time_24h: '15:00:00', icon: '🌤️', available: true },
        { time: '04:00 PM', time_24h: '16:00:00', icon: '🌆', available: true }
    ];

    timeSlotsGrid.innerHTML = '';

    timeSlots.forEach(slot => {
        const slotElement = document.createElement('div');
        slotElement.className = `time-slot ${slot.available ? 'available' : 'booked'}`;
        slotElement.dataset.time = slot.time_24h;
        
        slotElement.innerHTML = `
            <div class="time-slot-icon">${slot.icon}</div>
            <div class="time-slot-time">${slot.time}</div>
            <div class="time-slot-status">${slot.available ? '✓ Available' : '✗ Booked'}</div>
        `;

        if (slot.available) {
            slotElement.addEventListener('click', () => selectTimeSlot(slot.time, slot.time_24h, slotElement));
        }

        timeSlotsGrid.appendChild(slotElement);
    });
}

function selectTimeSlot(time, time24h, element) {
    
    document.querySelectorAll('.time-slot.selected').forEach(slot => {
        slot.classList.remove('selected');
    });

    
    element.classList.add('selected');
    
    selectedTime = {
        display: time,
        value: time24h
    };

    
    if (isRescheduling) {
        document.getElementById('confirmReschedule').disabled = false;
    }

    LicenseXpress.showToast('✅ Time slot selected.', 'success');
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
    
    const vehicleType = vehicleElement.dataset.vehicle;
    const vehicleTitle = vehicleElement.querySelector('.option-title').textContent;
    
    selectedVehicle = {
        type: vehicleType,
        title: vehicleTitle
    };

    checkBookingCompletion();
    LicenseXpress.showToast('✅ Vehicle selected.', 'success');
}



function initializeNavigation() {
    
    const confirmReschedule = document.getElementById('confirmReschedule');
    if (confirmReschedule) {
        confirmReschedule.addEventListener('click', handleReschedule);
    }

    
    const goToDashboard = document.getElementById('goToDashboard');
    goToDashboard.addEventListener('click', function() {
        window.location.href = 'dashboard.php';
    });

    
    const logoutBtn = document.getElementById('logoutBtn');
    logoutBtn.addEventListener('click', function() {
        LicenseXpress.logout();
    });
}

function handleReschedule() {
    if (!selectedDate || !selectedTime) {
        LicenseXpress.showToast('Please select both date and time', 'error');
        return;
    }

    if (!selectedTestCenter) {
        LicenseXpress.showToast('Please select a test center first', 'error');
        return;
    }

    const appData = window.applicationData;
    
    
    if (!appData.debugInfo.hasApplication || !appData.applicationId || appData.applicationId === 'APP-DEMO-001') {
        LicenseXpress.showToast('You need to submit an application first before scheduling a practical test. Please go to the Application Form.', 'error');
        
        setTimeout(() => {
            window.location.href = 'application-form.php';
        }, 2000);
        return;
    }

    const confirmBtn = document.getElementById('confirmReschedule');
    confirmBtn.disabled = true;
    confirmBtn.textContent = 'Rescheduling...';

    const formattedDate = selectedDate.toISOString().split('T')[0];

    
    console.log('Reschedule Debug Info:', {
        appData: appData,
        debugInfo: appData.debugInfo,
        applicationId: appData.applicationId,
        selectedTestCenter: selectedTestCenter,
        selectedDate: formattedDate,
        selectedTime: selectedTime.value
    });

    
    const rescheduleData = {
        application_id: appData.applicationId,
        test_center_id: selectedTestCenter.id,
        scheduled_date: formattedDate,
        scheduled_time: selectedTime.value
    };

    fetch('api_booking.php?action=reschedule_practical_test', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        credentials: 'same-origin',
        body: JSON.stringify(rescheduleData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            
            document.getElementById('scheduledDate').textContent = selectedDate.toLocaleDateString('en-US', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            document.getElementById('scheduledTime').textContent = selectedTime.display;
            
            
            document.getElementById('scheduledCenter').textContent = selectedTestCenter.name;

            
            document.getElementById('testCenterSection').classList.add('hidden');
            document.getElementById('calendarSection').classList.add('hidden');
            document.getElementById('timeSlotsSection').classList.add('hidden');
            document.getElementById('confirmReschedule').disabled = true;
            
            
            disableRescheduleButton();
            
            isRescheduling = false;
            
            LicenseXpress.showToast('✅ Practical test rescheduled successfully! Redirecting to dashboard...', 'success');
            
            
            setTimeout(() => {
                window.location.href = 'dashboard.php';
            }, 2000);
        } else {
            LicenseXpress.showToast(data.message || 'Failed to reschedule test', 'error');
        }
    })
    .catch(error => {
        console.error('Error rescheduling:', error);
        LicenseXpress.showToast('Error rescheduling test. Please try again.', 'error');
    })
    .finally(() => {
        confirmBtn.disabled = false;
        confirmBtn.textContent = 'Confirm Reschedule';
    });
}


function formatDateForAPI(date) {
    if (!date) return null;
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function formatTimeForAPI(time) {
    if (!time) return null;
    
    const [timePart, ampm] = time.split(' ');
    const [hours, minutes] = timePart.split(':');
    let hours24 = parseInt(hours);
    
    if (ampm === 'PM' && hours24 !== 12) {
        hours24 += 12;
    } else if (ampm === 'AM' && hours24 === 12) {
        hours24 = 0;
    }
    
    return `${String(hours24).padStart(2, '0')}:${minutes}:00`;
}