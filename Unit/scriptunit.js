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
                    btn.setAttribute('tabindex', '-1');
                });
                updateSelectedTimes();

                // Reset duration to 1 hour and update times
                document.getElementById('duration').value = 1;
                calculatePrice(); // Calculate initial price
                updateAvailableTimes();

                // Reset time selection to none
                document.querySelectorAll('.time-btn.active').forEach(btn => {
                    btn.classList.remove('active');
                    btn.setAttribute('tabindex', '-1');
                });
                updateSelectedTimes();

                modalFadeIn();
                modal.setAttribute('aria-hidden', 'false');

                // Focus first active or first time-btn for accessibility
                const firstBtn = document.querySelector('.time-btn');
                if (firstBtn) firstBtn.focus();
            });
        });
    }

    // Close modal
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            modalFadeOut();
        });
    }

    // Close modal when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modalFadeOut();
        }
    });

    // Keyboard accessibility: close modal on Escape key
    window.addEventListener('keydown', (e) => {
        if (e.key === "Escape" && modal.style.display === 'block') {
            modalFadeOut();
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
        durationInput.addEventListener('input', () => {
            const duration = parseFloat(durationInput.value) || 0;
            const customRadio = document.querySelector('input[name="package"][value="Custom"]');

            // Auto-select Custom when duration is changed
            if (duration > 0 && customRadio) {
                customRadio.checked = true;
                calculatePrice();
                updateAvailableTimes();
                adjustTimeSelection();
            }
        });
        durationInput.addEventListener('input', calculatePrice);
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
                    modalFadeOut();
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

// Modal fade in/out animation functions
function modalFadeIn() {
    modal.style.opacity = 0;
    modal.style.display = 'block';

    let op = 0;
    const timer = setInterval(() => {
        if (op >= 1) clearInterval(timer);
        modal.style.opacity = op;
        op += 0.1;
    }, 20);
}

function modalFadeOut() {
    let op = 1;
    const timer = setInterval(() => {
        if (op <= 0) {
            clearInterval(timer);
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
        }
        modal.style.opacity = op;
        op -= 0.1;
    }, 20);
}

// Handle time selection with duration-based multi-selection
function handleTimeSelection(event) {
    const button = event.target;
    if (button.classList.contains("disabled") || button.classList.contains("booked")) return;

    const timeButtons = [...document.querySelectorAll(".time-btn")];
    const duration = parseInt(document.getElementById("duration").value);

    const startIndex = timeButtons.indexOf(button);
    if (startIndex === -1) return;

    // Clear old selections
    timeButtons.forEach(btn => btn.classList.remove("active"));

    for (let i = 0; i < duration; i++) {
        const nextBtn = timeButtons[startIndex + i];
        if (!nextBtn || nextBtn.classList.contains("booked")) {
            Swal.fire("Slot tidak tersedia", "Silahkan pilih jam lain.", "warning");
            return;
        }
    }

    for (let i = 0; i < duration; i++) {
        timeButtons[startIndex + i].classList.add("active");
    }

    const startTimeInput = document.getElementById("startTime");
    startTimeInput.value = button.dataset.time; // ONLY FIRST TIME SELECTED
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
    selectedButtons.forEach(btn => {
        btn.classList.remove('active');
        btn.setAttribute('tabindex', '-1');
    });

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
            timeButtons[startIndex + i].setAttribute('tabindex', '0');
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
    const roomId = document.getElementById("roomId").value;
    const selectedDate = document.getElementById("startDate").value;
    const duration = parseInt(document.getElementById("duration").value);

    if (!roomId || !selectedDate) return;

    fetch(`../php/get_bookings.php?room_id=${roomId}&date=${selectedDate}`)
        .then(res => res.json())
        .then(bookings => {
            const timeButtons = document.querySelectorAll(".time-btn");

            timeButtons.forEach(btn => {
                btn.classList.remove("disabled", "booked", "active");
            });

            bookings.forEach(b => {
                const start = parseInt(b.start_time.split(":")[0]);
                const dur = parseInt(b.duration);
                const end = (start + dur) % 24;

                for (let i = 0; i < dur; i++) {
                    const blockIndex = (start + i) % 24;
                    const targetBtn = timeButtons[blockIndex];
                    if (targetBtn) targetBtn.classList.add("booked");
                }
            });

            adjustTimeSelection(); // Keep selected slot valid
        });
}


function updateStopwatch() {
    document.querySelectorAll('.stopwatch').forEach(stopwatch => {
        const bookingId = stopwatch.dataset.bookingId;
        const duration = parseInt(stopwatch.dataset.duration);
        const timeRemainingEl = stopwatch.querySelector('.time-remaining');
        const startDate = stopwatch.dataset.startDate;
        const startTime = stopwatch.dataset.startTime;

        console.log(`BookingId: ${bookingId}, startDate: ${startDate}, startTime: ${startTime}, duration: ${duration}`);

        if (!startDate || !startTime) {
            console.log('Missing startDate or startTime');
            timeRemainingEl.textContent = '--:--:--';
            return;
        }

        const startDateTimeStr = `${startDate}T${startTime}`;
        const startDateTime = new Date(startDateTimeStr);
        const now = new Date();

        console.log(`startDateTimeStr: ${startDateTimeStr}, startDateTime: ${startDateTime.toString()}, now: ${now.toString()}`);

        if (isNaN(startDateTime.getTime())) {
            console.log('Invalid startDateTime format');
            timeRemainingEl.textContent = '--:--:--';
            return;
        }

        const endTime = new Date(startDateTime.getTime() + duration * 60000);

        if (now < startDateTime) {
            // Countdown to session start
            const diff = Math.floor((startDateTime - now) / 1000);
            const hours = Math.floor(diff / 3600);
            const minutes = Math.floor((diff % 3600) / 60);
            const seconds = diff % 60;
            timeRemainingEl.textContent = `Mulai: ${hours.toString().padStart(2, '0')}:${minutes
                .toString()
                .padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        } else if (now >= startDateTime && now <= endTime) {
            // Countdown to session end
            const diff = Math.floor((endTime - now) / 1000);
            const hours = Math.floor(diff / 3600);
            const minutes = Math.floor((diff % 3600) / 60);
            const seconds = diff % 60;
            timeRemainingEl.textContent = `${hours.toString().padStart(2, '0')}:${minutes
                .toString()
                .padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        } else {
            // Session ended
            timeRemainingEl.textContent = 'Sesi selesai';
        }
    });
}

function startSession(bookingId) {
    // Only admin can start session
    if (typeof USER_ROLE === 'undefined' || USER_ROLE !== 'admin') {
        Swal.fire('Error', 'Hanya admin yang dapat memulai sesi permainan.', 'error');
        return;
    }

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
            if (!stopwatch) return;

            const startBtn = stopwatch.querySelector('.start-btn');
            if (startBtn) startBtn.style.display = 'none';

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

            // Immediately update stopwatch display after session start
            updateStopwatch();
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
