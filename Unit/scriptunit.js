// scriptunit.js

// Global variables
let modal, closeBtn, roomCards, packages, today;
let selectedTimes = []; // Array to hold selected time slots

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Modal elements
    modal = document.getElementById('bookingModal');
    closeBtn = document.querySelector('.close');

    // Room cards
    roomCards = document.querySelectorAll('.room-card.available');

    // Packages
    packages = {
        'Reguler': { duration: 60, price: 10000 },
        'Hemat': { duration: 180, price: 25000 },
        'Full': { duration: 360, price: 45000 },
        'Malam': { duration: 480, price: 60000 },
        'VIP': { duration: 720, price: 90000 },
        'Custom': { duration: 0, price: 0 }
    };

    // Set minimum date to today
    today = new Date().toISOString().split('T')[0];
    const startDateInput = document.getElementById('startDate');
    if (startDateInput) {
        startDateInput.setAttribute('min', today);
        startDateInput.addEventListener('change', updateAvailableTimes);
    }

    // Initialize event listeners
    initializeEventListeners();

    // Update stopwatch every second
    setInterval(updateStopwatch, 1000);
    updateStopwatch(); // Initial call
});

// Initialize all event listeners
function initializeEventListeners() {
    // === 🔹 PILIH JAM MULAI ===
    const timeButtons = document.querySelectorAll('.time-btn');
    const startTimeInput = document.getElementById('startTime');

    timeButtons.forEach(btn => {
        btn.addEventListener('click', handleTimeSelection);
    });

    // Duration controls with interactive feedback
    const decreaseBtn = document.getElementById('decreaseDuration');
    const increaseBtn = document.getElementById('increaseDuration');
    const durationInput = document.getElementById('duration');

    if (decreaseBtn) {
        decreaseBtn.addEventListener('click', function() {
            const currentValue = parseInt(durationInput.value);
            if (currentValue > 1) {
                const newValue = currentValue - 1;
                durationInput.value = newValue;

                // Add visual feedback
                decreaseBtn.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    decreaseBtn.style.transform = 'scale(1)';
                }, 100);

                calculatePrice();
                updateAvailableTimes();
                adjustTimeSelection();
            } else {
                // Visual feedback for minimum reached
                decreaseBtn.style.background = '#ccc';
                setTimeout(() => {
                    decreaseBtn.style.background = '#007bff';
                }, 200);
            }
        });
    }

    if (increaseBtn) {
        increaseBtn.addEventListener('click', function() {
            const currentValue = parseInt(durationInput.value);
            const newValue = currentValue + 1;
            durationInput.value = newValue;

            // Add visual feedback
            increaseBtn.style.transform = 'scale(0.95)';
            setTimeout(() => {
                increaseBtn.style.transform = 'scale(1)';
            }, 100);

            calculatePrice();
            updateAvailableTimes();
            adjustTimeSelection();
        });
    }

    // Open modal when clicking available room
    if (roomCards) {
        roomCards.forEach(card => {
            card.addEventListener('click', () => {
                const roomId = card.dataset.roomId;
                const type = card.dataset.type;
                const roomName = card.dataset.roomName;

                document.getElementById('roomId').value = roomId;
                document.getElementById('modalRoomType').textContent = type;
                document.getElementById('modalRoomName').textContent = roomName;

                // Reset date/time constraints when opening modal
                document.getElementById('startDate').value = today;
                document.getElementById('startDate').dispatchEvent(new Event('change'));

                // Clear any previous time selections BEFORE updating
                document.querySelectorAll('.time-btn.active').forEach(btn => {
                    btn.classList.remove('active');
                });
                updateSelectedTimes();

                // Reset duration to 1 hour and update times
                document.getElementById('duration').value = 1;
                calculatePrice(); // Calculate initial price
                updateAvailableTimes();

                // Reset time selection to none
                document.querySelectorAll('.time-btn.active').forEach(btn => {
                    btn.classList.remove('active');
                });
                updateSelectedTimes();

                modal.style.display = 'block';
            });
        });
    }

    // Close modal
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    }

    // Close modal when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });

    // Calculate price when package changes
    document.querySelectorAll('input[name="package"]').forEach(radio => {
        radio.addEventListener('change', () => {
            calculatePrice();
            updateAvailableTimes();
        });
    });

    // Calculate price when duration changes
    if (durationInput) {
        durationInput.addEventListener('input', calculatePrice);
        durationInput.addEventListener('input', function() {
            const duration = parseFloat(this.value) || 0;
            const customRadio = document.querySelector('input[name="package"][value="Custom"]');

            // Auto-select Custom when duration is changed
            if (duration > 0 && customRadio) {
                customRadio.checked = true;
                calculatePrice();
                updateAvailableTimes();
                adjustTimeSelection();
            }
        });
    }

    // Booking form submit
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);

            // Validate that all required fields are filled
            if (!data.startDate || !data.startTime || !data.duration || !data.package) {
                Swal.fire('Error', 'Silakan isi semua field yang diperlukan.', 'error');
                return;
            }

            // Validate that at least one time is selected
            const selectedTimes = document.querySelectorAll('.time-btn.active');
            if (selectedTimes.length === 0) {
                Swal.fire('Error', 'Silakan pilih setidaknya satu jam mulai.', 'error');
                return;
            }

            // Check if price matches any package
            const totalPrice = parseInt(document.getElementById('totalPrice').textContent.replace(/\./g, ''));
            let packageName = data.package;
            if (data.package === 'Custom') {
                for (const [pkg, details] of Object.entries(packages)) {
                    if (pkg !== 'Custom' && details.price === totalPrice) {
                        packageName = pkg;
                        break;
                    }
                }
            }

            data.package = packageName;
            data.price = totalPrice;

            // Submit the booking
            try {
                const response = await fetch('process_booking.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    // Update room card to booked state immediately
                    const roomCard = document.querySelector(`.room-card[data-room-id="${data.roomId}"]`);
                    if (roomCard) {
                        roomCard.classList.remove('available');
                        roomCard.classList.add('booked');
                        const statusIndicator = roomCard.querySelector('.status-indicator');
                        const statusText = roomCard.querySelector('p');
                        if (statusIndicator) {
                            statusIndicator.classList.remove('available');
                            statusIndicator.classList.add('booked');
                        }
                        if (statusText) {
                            statusText.textContent = 'Dipesan';
                        }
                    }

                    // Close modal and show success, then redirect to orders page
                    modal.style.display = 'none';
                    Swal.fire('Berhasil!', 'Booking berhasil dibuat.', 'success').then(() => {
                        window.location.href = 'orders.php';
                    });
                } else {
                    Swal.fire('Error', result.message || 'Gagal membuat booking', 'error');
                }
            } catch (error) {
                console.error('Error submitting booking:', error);
                Swal.fire('Error', 'Terjadi kesalahan saat memproses booking', 'error');
            }
        });
    }
}

// Handle time selection with duration-based multi-selection
function handleTimeSelection(event) {
    const button = event.target;
    const duration = parseInt(document.getElementById('duration').value) || 0;

    // Check if button is disabled
    if (button.disabled || button.classList.contains('disabled') || button.classList.contains('booked')) {
        return;
    }

    // If already selected, deselect it
    if (button.classList.contains('active')) {
        button.classList.remove('active');
        updateSelectedTimes();
        return;
    }

    // Clear all previous selections
    document.querySelectorAll('.time-btn.active').forEach(btn => {
        btn.classList.remove('active');
    });

    // Calculate required time slots based on duration (each button = 1 hour)
    const startTime = button.dataset.time;
    const requiredSlots = duration; // Each slot is 1 hour
    const timeButtons = Array.from(document.querySelectorAll('.time-btn'));
    const startIndex = timeButtons.findIndex(btn => btn.dataset.time === startTime);

    if (startIndex === -1) return;

    // Check if we have enough consecutive slots
    let canSelect = true;
    for (let i = 0; i < requiredSlots; i++) {
        const slotIndex = startIndex + i;
        if (slotIndex >= timeButtons.length) {
            canSelect = false;
            break;
        }
        const slot = timeButtons[slotIndex];
        if (slot.disabled || slot.classList.contains('disabled') || slot.classList.contains('booked')) {
            canSelect = false;
            break;
        }
    }

    if (!canSelect) {
        Swal.fire('Waktu Tidak Tersedia', 'Durasi yang dipilih melebihi slot waktu yang tersedia atau bertabrakan dengan booking lain.', 'warning');
        return;
    }

    // Select the required consecutive slots
    for (let i = 0; i < requiredSlots; i++) {
        timeButtons[startIndex + i].classList.add('active');
    }

    updateSelectedTimes();
}

// Function to update selected times
function updateSelectedTimes() {
    const selectedButtons = document.querySelectorAll('.time-btn.active');
    const selectedTimes = Array.from(selectedButtons).map(btn => btn.dataset.time);
    const startTimeInput = document.getElementById('startTime');
    if (startTimeInput) {
        startTimeInput.value = selectedTimes.join(',');
    }
}

// Function to adjust time selection based on duration changes
function adjustTimeSelection() {
    const selectedButtons = document.querySelectorAll('.time-btn.active');
    if (selectedButtons.length === 0) return;

    const duration = parseInt(document.getElementById('duration').value) || 0;
    const requiredSlots = duration; // Each button = 1 hour
    const timeButtons = Array.from(document.querySelectorAll('.time-btn'));
    const startTime = selectedButtons[0].dataset.time;
    const startIndex = timeButtons.findIndex(btn => btn.dataset.time === startTime);

    if (startIndex === -1) return;

    // Clear current selection
    selectedButtons.forEach(btn => btn.classList.remove('active'));

    // Check if we can select the required slots
    let canSelect = true;
    for (let i = 0; i < requiredSlots; i++) {
        const slotIndex = startIndex + i;
        if (slotIndex >= timeButtons.length) {
            canSelect = false;
            break;
        }
        const slot = timeButtons[slotIndex];
        if (slot.disabled || slot.classList.contains('disabled') || slot.classList.contains('booked')) {
            canSelect = false;
            break;
        }
    }

    if (canSelect) {
        // Select the required consecutive slots
        for (let i = 0; i < requiredSlots; i++) {
            timeButtons[startIndex + i].classList.add('active');
        }
    } else {
        // If can't select, clear all selections
        Swal.fire('Durasi Berubah', 'Durasi yang dipilih tidak sesuai dengan slot waktu yang tersedia. Silakan pilih waktu mulai lagi.', 'info');
    }

    updateSelectedTimes();
}

// Calculate price
function calculatePrice() {
    const selectedPackage = document.querySelector('input[name="package"]:checked');
    const durationInput = document.getElementById('duration');
    const totalPriceEl = document.getElementById('totalPrice');

    if (!selectedPackage || !durationInput || !totalPriceEl) return;

    const customDuration = parseFloat(durationInput.value) || 0;

    let price = 0;
    if (selectedPackage.value === 'Custom') {
        price = customDuration * 10000; // Rp 10,000 per hour
    } else {
        price = packages[selectedPackage.value].price;
        durationInput.value = packages[selectedPackage.value].duration / 60; // Convert minutes to hours
    }

    totalPriceEl.textContent = price.toLocaleString('id-ID');
}

// Function to update available times based on duration and existing bookings
function updateAvailableTimes() {
    const durationInput = document.getElementById('duration');
    const startDateInput = document.getElementById('startDate');
    const roomIdInput = document.getElementById('roomId');

    if (!durationInput || !startDateInput || !roomIdInput) {
        console.log('Missing input elements for updateAvailableTimes');
        return;
    }

    const duration = parseFloat(durationInput.value) || 0;
    const selectedDate = startDateInput.value;
    const roomId = roomIdInput.value;
    const timeButtons = document.querySelectorAll('.time-btn');

    console.log('updateAvailableTimes called:', { duration, selectedDate, roomId, timeButtonsLength: timeButtons.length });

    if (!selectedDate || duration <= 0 || !roomId) {
        console.log('Showing all times as available (missing params)');
        // Show all times if no date, duration, or room selected
        timeButtons.forEach(btn => {
            btn.classList.remove('disabled', 'booked');
            btn.disabled = false;
        });
        return;
    }

    // Fetch existing bookings for this room and date
    console.log('Fetching bookings for room:', roomId, 'date:', selectedDate);
    fetch(`get_bookings.php?room_id=${roomId}&date=${selectedDate}`)
        .then(response => {
            console.log('Fetch response status:', response.status);
            return response.json();
        })
        .then(bookings => {
            console.log('Bookings received:', bookings);
            const now = new Date();
            const selectedDateObj = new Date(selectedDate);

            timeButtons.forEach(btn => {
                const startTime = btn.dataset.time;
                const [hours, minutes] = startTime.split(':').map(Number);
                const startDateTime = new Date(selectedDateObj);
                startDateTime.setHours(hours, minutes, 0, 0);

                const endDateTime = new Date(startDateTime.getTime() + duration * 60 * 60 * 1000);

                // Check if this time slot conflicts with existing bookings
                let isBooked = false;
                bookings.forEach(booking => {
                    const bookingStart = new Date(`${selectedDate}T${booking.start_time}`);
                    const bookingEnd = new Date(bookingStart.getTime() + booking.duration * 60 * 1000);

                    // Check for overlap
                    if ((startDateTime < bookingEnd && endDateTime > bookingStart)) {
                        isBooked = true;
                    }
                });

                // Disable if start time is in the past for today
                const isToday = selectedDate === today;
                const isPastTime = isToday && startDateTime < now;

                // Disable if end time is after 24:00 (next day)
                const isOvernight = endDateTime.getDate() !== startDateTime.getDate();

                console.log(`Time ${startTime}: isPast=${isPastTime}, isOvernight=${isOvernight}, isBooked=${isBooked}`);

                if (isPastTime || isOvernight) {
                    btn.classList.add('disabled');
                    btn.classList.remove('booked');
                    btn.disabled = true;
                    // If this button was selected, unselect it
                    if (btn.classList.contains('active')) {
                        btn.classList.remove('active');
                        updateSelectedTimes();
                    }
                } else if (isBooked) {
                    btn.classList.add('booked');
                    btn.classList.remove('disabled');
                    btn.disabled = true;
                    // If this button was selected, unselect it
                    if (btn.classList.contains('active')) {
                        btn.classList.remove('active');
                        updateSelectedTimes();
                    }
                } else {
                    btn.classList.remove('disabled', 'booked');
                    btn.disabled = false;
                }
            });
        })
        .catch(error => {
            console.error('Error fetching bookings:', error);
            // If error, show all times as available
            timeButtons.forEach(btn => {
                btn.classList.remove('disabled', 'booked');
                btn.disabled = false;
            });
        });
}

// Stopwatch for active bookings
function updateStopwatch() {
    document.querySelectorAll('.stopwatch').forEach(stopwatch => {
        const bookingId = stopwatch.dataset.bookingId;
        const duration = parseInt(stopwatch.dataset.duration);
        const timeRemainingEl = stopwatch.querySelector('.time-remaining');

        // Get start time from local storage (only if user has started)
        let startTime = localStorage.getItem(`start_${bookingId}`);
        if (!startTime) {
            // If not started, show full duration
            const hours = Math.floor(duration / 60);
            const minutes = duration % 60;
            timeRemainingEl.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:00`;
            return;
        }

        const elapsed = Math.floor((Date.now() - startTime) / 1000);
        const remaining = duration * 60 - elapsed;

        if (remaining <= 0) {
            timeRemainingEl.textContent = '00:00:00';
            // Auto complete booking
            completeBooking(bookingId);
        } else {
            const hours = Math.floor(remaining / 3600);
            const minutes = Math.floor((remaining % 3600) / 60);
            const seconds = remaining % 60;
            timeRemainingEl.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }
    });
}

function startSession(bookingId) {
    // Send request to server to record start time
    fetch('start_session.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ bookingId: bookingId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Store start time locally for countdown
            localStorage.setItem(`start_${bookingId}`, Date.now());

            // Hide start button and show stop button
            const stopwatch = document.querySelector(`.stopwatch[data-booking-id="${bookingId}"]`);
            stopwatch.querySelector('.start-btn').style.display = 'none';

            // Add stop button if not exists
            if (!stopwatch.querySelector('.stop-btn')) {
                const stopBtn = document.createElement('button');
                stopBtn.className = 'stop-btn';
                stopBtn.textContent = 'Stop';
                stopBtn.onclick = () => stopSession(bookingId);
                stopwatch.appendChild(stopBtn);
            }

            // Calculate and show end time
            const duration = parseInt(stopwatch.dataset.duration);
            const endTime = new Date(Date.now() + duration * 60 * 1000);
            const endTimeStr = endTime.toLocaleString('id-ID', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });

            // Show notification
            Swal.fire({
                title: 'Sesi Dimulai!',
                text: `Sesi akan selesai pada: ${endTimeStr}`,
                icon: 'success',
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            Swal.fire('Error', data.message || 'Failed to start session', 'error');
        }
    })
    .catch(error => {
        console.error('Error starting session:', error);
        Swal.fire('Error', 'Failed to start session', 'error');
    });
}

function stopSession(bookingId) {
    Swal.fire({
        title: 'Hentikan Sesi?',
        text: 'Apakah Anda yakin ingin menghentikan sesi sekarang?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hentikan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            completeBooking(bookingId);
        }
    });
}

async function completeBooking(bookingId) {
    try {
        const response = await fetch('complete_booking.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ bookingId })
        });

        const result = await response.json();

        if (result.success) {
            localStorage.removeItem(`start_${bookingId}`);
            location.reload();
        } else {
            alert('Error completing booking');
        }
    } catch (error) {
        alert('Error completing booking');
    }
}
