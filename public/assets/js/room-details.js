/**
 * Room Details Calendar Logic
 */

document.addEventListener("DOMContentLoaded", function () {
  const calendarGrid = document.querySelector(".calendar-grid");
  const monthDisplay = document.querySelector(".calendar-header span");
  const prevMonthBtn = document.querySelector(
    ".calendar-header .nav-btn:first-child",
  );
  const nextMonthBtn = document.querySelector(
    ".calendar-header .nav-btn:last-child",
  );
  const scheduleContainer = document.querySelector(".schedule-container"); // Need to add this class to view
  const selectedDateDisplay = document.querySelector(".selected-date-display"); // Optional header for schedule

  // Load bookings from global variable set in PHP
  const bookings = window.roomBookings || [];

  let currentDate = new Date(); // Internal state for calendar view
  let selectedDate = new Date(); // Currently selected day

  renderCalendar(currentDate);
  renderSchedule(selectedDate);

  // Event Listeners
  prevMonthBtn.addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar(currentDate);
  });

  nextMonthBtn.addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar(currentDate);
  });

  function renderCalendar(date) {
    const year = date.getFullYear();
    const month = date.getMonth();

    const monthNames = [
      "January",
      "February",
      "March",
      "April",
      "May",
      "June",
      "July",
      "August",
      "September",
      "October",
      "November",
      "December",
    ];
    monthDisplay.textContent = `${monthNames[month]} ${year}`;

    // Clear existing days
    const existingDays = calendarGrid.querySelectorAll(".day-cell");
    existingDays.forEach((el) => el.remove());

    const firstDayOfMonth = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysInPrevMonth = new Date(year, month, 0).getDate();

    // Prev month filler
    for (let i = 0; i < firstDayOfMonth; i++) {
      const dayNum = daysInPrevMonth - firstDayOfMonth + 1 + i;
      const cell = document.createElement("div");
      cell.className = "day-cell prev-month";
      cell.textContent = dayNum;
      cell.style.color = "#cbd5e1";
      calendarGrid.appendChild(cell);
    }

    // Current month days
    for (let i = 1; i <= daysInMonth; i++) {
      const cell = document.createElement("div");
      cell.className = "day-cell";
      cell.textContent = i;

      const cellDateStr = formatDateKey(year, month, i);

      // Check if day has bookings
      if (hasBookingsOnDate(cellDateStr)) {
        cell.classList.add("has-bookings");
        // Optional: visual indicator like a dot
        const dot = document.createElement("div");
        dot.style.cssText =
          "width: 4px; height: 4px; background: #EF4444; border-radius: 50%; margin: 2px auto 0;";
        cell.appendChild(dot);
      }

      // Check if selected
      if (
        year === selectedDate.getFullYear() &&
        month === selectedDate.getMonth() &&
        i === selectedDate.getDate()
      ) {
        cell.classList.add("active");
      }

      cell.addEventListener("click", () => {
        document
          .querySelectorAll(".day-cell.active")
          .forEach((el) => el.classList.remove("active"));
        cell.classList.add("active");

        selectedDate = new Date(year, month, i);
        renderSchedule(selectedDate);
      });

      calendarGrid.appendChild(cell);
    }
  }

  function formatDateKey(year, month, day) {
    // Returns YYYY-MM-DD local
    const m = (month + 1).toString().padStart(2, "0");
    const d = day.toString().padStart(2, "0");
    return `${year}-${m}-${d}`;
  }

  function hasBookingsOnDate(dateStr) {
    return bookings.some((b) => b.date === dateStr);
  }

  function renderSchedule(date) {
    const year = date.getFullYear();
    const month = date.getMonth();
    const day = date.getDate();
    const dateStr = formatDateKey(year, month, day);

    // Filter bookings for this day
    const dayBookings = bookings.filter((b) => b.date === dateStr);
    dayBookings.sort((a, b) => a.start_time.localeCompare(b.start_time));

    // Update Header
    const options = {
      weekday: "long",
      year: "numeric",
      month: "long",
      day: "numeric",
    };
    if (selectedDateDisplay) {
      selectedDateDisplay.textContent = date.toLocaleDateString(
        "en-US",
        options,
      );
    }

    // Clear schedule
    scheduleContainer.innerHTML = "";

    if (dayBookings.length === 0) {
      scheduleContainer.innerHTML = `
                <div class="schedule-item available">
                    <div class="schedule-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" color="#059669">
                            <circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div class="timeline">
                        <div class="time-range">All Day</div>
                        <div class="event-name">Available</div>
                    </div>
                </div>
            `;
      return;
    }

    // Render bookings
    dayBookings.forEach((b) => {
      const start = b.start_time.substring(0, 5); // HH:MM
      const end = b.end_time.substring(0, 5);

      const item = document.createElement("div");
      item.className = "schedule-item";
      item.innerHTML = `
                <div class="schedule-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" color="#64748b">
                        <circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div class="timeline">
                    <div class="time-range">${start} - ${end}</div>
                    <div class="event-name">Reserved</div>
                </div>
            `;
      scheduleContainer.appendChild(item);
    });
  }
});
