/**
 * Booking Form Interaction Logic
 */

document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.querySelector('input[name="date"]');
    const calendarGrid = document.querySelector('.calendar-grid');
    const monthDisplay = document.querySelector('.calendar-header span');
    const prevMonthBtn = document.querySelector('.calendar-header .nav-btn:first-child');
    const nextMonthBtn = document.querySelector('.calendar-header .nav-btn:last-child');
    
    // Attendees Validation
    const attendeesInput = document.querySelector('input[name="attendees"]');
    if (attendeesInput) {
        attendeesInput.addEventListener('change', function() {
            const min = parseInt(this.getAttribute('min'));
            const max = parseInt(this.getAttribute('max'));
            let val = parseInt(this.value);
            
            if (val < min) this.value = min;
            if (val > max) this.value = max;
        });
    }

    // --- Calendar Logic ---
    let currentDate = new Date(); // Internal state for calendar view
    
    // Initialize
    if (dateInput.value) {
        currentDate = new Date(dateInput.value);
    }
    
    // If the input is empty or invalid, default to today
    if (isNaN(currentDate.getTime())) {
        currentDate = new Date();
        // Set input to today YYYY-MM-DD
        dateInput.value = formatDate(currentDate);
    }

    renderCalendar(currentDate);

    // Event Listeners
    prevMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar(currentDate);
    });

    nextMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar(currentDate);
    });
    
    // Sync input change to calendar (if user types/picks connection from browser picker)
    dateInput.addEventListener('change', (e) => {
        const d = new Date(e.target.value);
        if (!isNaN(d.getTime())) {
            currentDate = d;
            renderCalendar(currentDate);
            updateSummary();
        }
    });

    function renderCalendar(date) {
        const year = date.getFullYear();
        const month = date.getMonth();
        
        // Update Header
        const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
        monthDisplay.textContent = `${monthNames[month]} ${year}`;

        // Clear grid (keep headers)
        // Note: The HTML structure has headers inside .calendar-grid. 
        // We should probably separate them or being careful not to remove them.
        // Let's assume we rebuild the day cells.
        
        // Remove existing .day-cell elements
        const existingDays = calendarGrid.querySelectorAll('.day-cell');
        existingDays.forEach(el => el.remove());

        // Calculate days
        const firstDayOfMonth = new Date(year, month, 1).getDay(); // 0 (Sun) - 6 (Sat)
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const daysInPrevMonth = new Date(year, month, 0).getDate();

        // Previous month filler days
        for (let i = 0; i < firstDayOfMonth; i++) {
            const dayNum = daysInPrevMonth - firstDayOfMonth + 1 + i;
            const cell = document.createElement('div');
            cell.className = 'day-cell prev-month';
            cell.textContent = dayNum;
            cell.style.color = '#cbd5e1'; // Visual style for inactive
            calendarGrid.appendChild(cell);
        }

        // Current month days
        const today = new Date();
        const selectedDate = new Date(dateInput.value);

        for (let i = 1; i <= daysInMonth; i++) {
            const cell = document.createElement('div');
            cell.className = 'day-cell';
            cell.textContent = i;
            
            // Check if this is the selected day
            if (year === selectedDate.getFullYear() && 
                month === selectedDate.getMonth() && 
                i === selectedDate.getDate()) {
                cell.classList.add('active');
            }

            // Click handler
            cell.addEventListener('click', () => {
                // Remove active class from others
                document.querySelectorAll('.day-cell.active').forEach(el => el.classList.remove('active'));
                cell.classList.add('active');
                
                // Update input
                const newDate = new Date(year, month, i);
                // Adjust for timezone offset so YYYY-MM-DD corresponds to local time
                const offset = newDate.getTimezoneOffset();
                const localDate = new Date(newDate.getTime() - (offset * 60 * 1000));
                dateInput.value = localDate.toISOString().split('T')[0];
                
                updateSummary();
            });

            calendarGrid.appendChild(cell);
        }
    }

    function formatDate(date) {
         const offset = date.getTimezoneOffset();
         const localDate = new Date(date.getTime() - (offset * 60 * 1000));
         return localDate.toISOString().split('T')[0];
    }
    
    function updateSummary() {
        const summaryValue = document.querySelector('.summary-value.date-display');
        if (summaryValue) {
            summaryValue.textContent = dateInput.value;
        }
        
        // Fetch availability for selected date
        fetchAvailability(dateInput.value);
    }
    
    // Initial summary update
    updateSummary();
    
    // --- Real-time Availability Checking ---
    let currentBookings = [];
    
    async function fetchAvailability(date) {
        if (!window.ROOM_ID || !date) return;
        
        const availabilitySection = document.getElementById('availability-section');
        const occupiedSlots = document.getElementById('occupied-slots');
        
        try {
            const response = await fetch(`/api/rooms/${window.ROOM_ID}/bookings?date=${date}`);
            if (!response.ok) throw new Error('Failed to fetch availability');
            
            const data = await response.json();
            currentBookings = data.bookings || [];
            
            // Display occupied slots
            if (currentBookings.length === 0) {
                occupiedSlots.innerHTML = '<div style="text-align: center; padding: 12px; color: #10b981;">✓ No bookings for this date</div>';
            } else {
                let html = '';
                currentBookings.forEach(booking => {
                    html += `
                        <div style="background: #FEF2F2; padding: 8px 12px; border-radius: 4px; margin-bottom: 8px; border-left: 3px solid #EF4444;">
                            <strong>${booking.start_time}</strong> - <strong>${booking.end_time}</strong>
                        </div>
                    `;
                });
                occupiedSlots.innerHTML = html;
            }
            
            availabilitySection.style.display = 'block';
            
            // Check for conflicts with current selection
            checkTimeConflict();
            
        } catch (error) {
            console.error('Error fetching availability:', error);
            occupiedSlots.innerHTML = '<div style="text-align: center; padding: 12px; color: #ef4444;">Failed to load availability</div>';
        }
    }
    
    // Time inputs
    const startTimeInput = document.querySelector('input[name="start_time"]');
    const endTimeInput = document.querySelector('input[name="end_time"]');
    const submitButton = document.querySelector('button[type="submit"]');
    const conflictWarning = document.getElementById('conflict-warning');
    const conflictMessage = document.getElementById('conflict-message');
    
    // Check for conflicts whenever time changes
    if (startTimeInput) {
        startTimeInput.addEventListener('change', checkTimeConflict);
    }
    if (endTimeInput) {
        endTimeInput.addEventListener('change', checkTimeConflict);
    }
    
    function checkTimeConflict() {
        if (!startTimeInput || !endTimeInput || currentBookings.length === 0) {
            hideConflictWarning();
            return;
        }
        
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;
        
        if (!startTime || !endTime) {
            hideConflictWarning();
            return;
        }
        
        // Validate end time is after start time
        if (endTime <= startTime) {
            showConflictWarning('End time must be after start time');
            return;
        }
        
        // Check for overlaps with existing bookings
        for (const booking of currentBookings) {
            const bookingStart = booking.start_time;
            const bookingEnd = booking.end_time;
            
            // Check if times overlap
            // Overlap occurs if: (start < bookingEnd) AND (end > bookingStart)
            if (startTime < bookingEnd && endTime > bookingStart) {
                showConflictWarning(`This time conflicts with an existing booking (${bookingStart} - ${bookingEnd})`);
                return;
            }
        }
        
        // No conflicts
        hideConflictWarning();
    }
    
    function showConflictWarning(message) {
        if (conflictWarning && conflictMessage) {
            conflictMessage.textContent = message;
            conflictWarning.style.display = 'block';
            
            // Disable submit button
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.style.opacity = '0.5';
                submitButton.style.cursor = 'not-allowed';
            }
        }
    }
    
    function hideConflictWarning() {
        if (conflictWarning) {
            conflictWarning.style.display = 'none';
            
            // Enable submit button
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.style.opacity = '1';
                submitButton.style.cursor = 'pointer';
            }
        }
    }
});

