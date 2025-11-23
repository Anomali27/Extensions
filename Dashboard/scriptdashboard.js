document.addEventListener("DOMContentLoaded", () => {
    console.log("Dashboard loaded ✅");

    // Animasi kecil untuk kartu statistik
    const cards = document.querySelectorAll(".info-card");
    cards.forEach((card, i) => {
        card.style.opacity = 0;
        card.style.transform = "translateY(20px)";
        setTimeout(() => {
            card.style.transition = "all 0.5s ease";
            card.style.opacity = 1;
            card.style.transform = "translateY(0)";
        }, 150 * i);
    });

    // --- CUSTOM MODAL IMPLEMENTATION ---
    // Get modals
    const modalAddUser = document.getElementById("modalAddUser");
    const modalEditUser = document.getElementById("modalEditUser");
    const modalDeleteUser = document.getElementById("modalDeleteUser");


    // Utility to open modal
    function openModal(modal) {
        if (!modal) return;
        modal.removeAttribute('hidden');
        // Removed inert attribute setting for quick fix as per user request
        // Focus first focusable element
        const focusable = modal.querySelector('input, select, textarea, button');
        if (focusable) focusable.focus();
        document.body.style.overflow = 'hidden'; // Disable background scroll
    }

    // Utility to close modal
    function closeModal(modal) {
        if (!modal) return;
        modal.setAttribute('hidden', '');
        // Removed inert attribute clearing for quick fix as per user request
        document.body.style.overflow = ''; // Restore scroll
    }

    // Attach close handlers to elements with data-modal-close attribute
    document.querySelectorAll('[data-modal-close]').forEach(el => {
        el.addEventListener('click', () => {
            const modal = el.closest('.custom-modal');
            closeModal(modal);
        });
    });

    // Open Add User modal
    const openAddBtn = document.getElementById("btnAddUser");
    if (openAddBtn && modalAddUser) {
        openAddBtn.addEventListener("click", () => {
            openModal(modalAddUser);
        });
    }

    // New alert container inside Add User modal
    const modalAddUserAlert = document.getElementById("modalAddUserAlert");
    const addUserForm = document.getElementById("addUserForm");

    // Handle add user form submit via AJAX with alert display and table update
    if (addUserForm) {
        addUserForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            const formData = new FormData(addUserForm);

            try {
                const res = await fetch("add_user.php", {
                    method: "POST",
                    body: formData,
                });

                const data = await res.json();

                if (data.success) {
                    // Show alert
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message || 'User berhasil ditambahkan ✅',
                        timer: 1500,
                        showConfirmButton: false,
                    });

                    // Close modal immediately
                    closeModal(modalAddUser);

                    // Reset form
                    addUserForm.reset();

                    // Optimistic UI: add new user row to user table without reloading page
                    const userTableBody = document.querySelector("#usersTable tbody");
                    if (userTableBody) {
                        // Create new row element
                        const tr = document.createElement('tr');
                        // Assuming the data.response includes new user id and fields for display
                        // If backend does not return these, you may need to reload page instead
                        tr.innerHTML = `
                            <td>new</td> <!-- Ideally new user ID -->
                            <td>${formData.get('username')}</td>
                            <td>${formData.get('email')}</td>
                            <td>0</td> <!-- Default saldo -->
                            <td>offline</td> <!-- Default status -->
                            <td>
                                <!-- Action buttons like Edit/Delete if needed -->
                                <button class="btn btn-sm btn-primary btnEditUser" data-username="${formData.get('username')}" data-email="${formData.get('email')}" data-status="offline">Edit</button>
                                <button class="btn btn-sm btn-danger btnDeleteUser" data-username="${formData.get('username')}">Delete</button>
                            </td>
                        `;
                        userTableBody.appendChild(tr);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Gagal menambahkan user!',
                    });
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan jaringan atau server.',
                });
            }
        });
    }

    // New code added for centralized alerts from #alertData
    const alertData = document.getElementById("alertData");
    if (alertData) {
        const successMsg = alertData.getAttribute("data-success-message");
        const errorMsg = alertData.getAttribute("data-error-message");

        if (successMsg) {
            Swal.fire({
                icon: "success",
                title: "Success",
                text: successMsg,
                timer: 2000,
                showConfirmButton: false,
            });
        }

        if (errorMsg) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: errorMsg,
                timer: 2000,
                showConfirmButton: false,
            });
        }
    }

    // Add delete confirmation for all buttons/links with class .btnHapus
    document.querySelectorAll(".btnHapus").forEach((btn) => {
        btn.addEventListener("click", function(e) {
            e.preventDefault();

            const href = btn.getAttribute("href") || btn.dataset.href;
            if (!href) return;

            Swal.fire({
                title: "Confirm Delete",
                text: "Are you sure you want to delete this item? This action cannot be undone.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "Cancel",
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with redirect
                    window.location.href = href;
                }
            });
        });
    });

    // Handle add user form submit via AJAX
    const formAdd = document.getElementById("addUserForm");
    if (formAdd) {
        formAdd.addEventListener("submit", async (e) => {
            e.preventDefault();

            const formData = new FormData(formAdd);

            try {
                const res = await fetch("add_user.php", {
                    method: "POST",
                    body: formData,
                });

                // Clone response for safe read
                const resClone = res.clone();
                let data;
                try {
                    data = await res.json();
                } catch (jsonErr) {
                    // Response is not valid JSON
                    const text = await resClone.text();
                    console.error("Non-JSON response from add_user.php:", text);
                    Swal.fire("Error", "Response from server is not valid JSON.", "error");
                    return;
                }

                console.log("Response:", data);

                if (addUserModalInstance) {
                    addUserModalInstance.hide();
                    // Explicitly focus on add button after modal hides
                    const openAddBtn = document.getElementById("btnAddUser");
                    if (openAddBtn) openAddBtn.focus();
                }

                if (data.success) {
                    Swal.fire({
                        title: "Berhasil!",
                        text: data.message || "User berhasil ditambahkan ✅",
                        icon: "success",
                        timer: 1500,
                        showConfirmButton: false,
                    }).then(() => {
                        setTimeout(() => location.reload(), 100); // Reload setelah SweetAlert hilang
                    });
                } else {
                    Swal.fire("Error", data.message || "Gagal menambahkan user!", "error");
                }
            } catch (err) {
                console.error('Error adding user:', err);
                Swal.fire("Error", "Gagal koneksi atau respons tidak valid!", "error");
            }
        });
    }

    // User edit modal open on edit buttons
    document.querySelectorAll(".btnEditUser").forEach((btn) => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();

            // Mengisi data ke form
            document.getElementById("editId").value = btn.dataset.id;
            document.getElementById("editUsername").value = btn.dataset.username;
            document.getElementById("editEmail").value = btn.dataset.email;
            document.getElementById("editStatus").value = btn.dataset.status;

            // Reset password field
            document.getElementById("editPassword").value = '';

            if (editUserModalInstance) {
                editUserModalInstance.show(); // Menggunakan Bootstrap API
            }
        });
    });
    
    // Handle edit user form submit (biarkan POST biasa jika logic edit_user.php kompleks/redirect)
    // Jika Anda ingin menggunakan AJAX di sini, Anda harus menambahkan listener serupa dengan addUserForm

    // Delete user modal setup
    let userIdToDelete = null;
    let userRowToDelete = null;

    document.querySelectorAll(".btnDeleteUser").forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            userIdToDelete = btn.dataset.id;
            userRowToDelete = btn.closest("tr");
            const username = btn.dataset.username || "";
            document.getElementById("deleteUsername").textContent = username;

            if (deleteUserModalInstance) {
                deleteUserModalInstance.show();
            }
        });
    });

    document.getElementById("confirmDeleteUserBtn").addEventListener("click", () => {
        if (!userIdToDelete) return;

        fetch("delete_user.php?id=" + encodeURIComponent(userIdToDelete), {
            method: "GET", // Menggunakan GET sesuai kode Anda sebelumnya
            headers: {
                "Content-Type": "application/json",
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (deleteUserModalInstance) {
                    deleteUserModalInstance.hide();
                }
                Swal.fire({
                    icon: "success",
                    title: "Deleted!",
                    text: data.message || `User ${document.getElementById("deleteUsername").textContent} berhasil dihapus.`,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    // Pilihan: Reload setelah delete
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Gagal!",
                    text: data.message || "Gagal menghapus user.",
                });
            }
        })
        .catch(err => {
            Swal.fire({
                icon: "error",
                title: "Error!",
                text: "Terjadi kesalahan: " + err.message,
            });
        });
    });

    // Inventory edit modal open on edit buttons
    document.querySelectorAll(".btnEditInventory").forEach((btn) => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();

            const id = btn.dataset.id;
            const type = btn.dataset.type || '';
            const quantity = btn.dataset.quantity || ''; // Harus ada di data-* tombol PHP

            document.getElementById("editInventoryId").value = id;
            document.getElementById("editInventoryType").value = type;
            document.getElementById("editInventoryQuantity").value = quantity;

            if (editInventoryModalInstance) {
                editInventoryModalInstance.show(); // Menggunakan Bootstrap API
            }
        });
    });
    
    // === Handle form edit Inventory submit via AJAX (NEW) ===
    const formEditInventory = document.getElementById("editInventoryForm");
    if (formEditInventory) {
        formEditInventory.addEventListener("submit", async (e) => {
            e.preventDefault();

            const formData = new FormData(formEditInventory);
            
            try {
                const res = await fetch(formEditInventory.action, {
                    method: "POST",
                    body: formData,
                });

                // Asumsi edit_inventory.php mengembalikan JSON
                const data = await res.json(); 

                // Tutup modal
                if (editInventoryModalInstance) {
                    editInventoryModalInstance.hide();
                }

                if (data.success) {
                    Swal.fire({
                        title: "Berhasil!",
                        text: data.message || "Quantity inventaris berhasil diupdate.",
                        icon: "success",
                        timer: 1500,
                        showConfirmButton: false,
                    }).then(() => {
                        setTimeout(() => location.reload(), 100); // Reload untuk update tabel
                    });
                } else {
                    Swal.fire("Error", data.message || "Gagal mengupdate inventaris!", "error");
                }
            } catch (err) {
                console.error('Error updating inventory:', err);
                Swal.fire("Error", "Gagal koneksi ke server atau respons tidak valid.", "error");
            }
        });
    }

    // --- Booking management helper function (dari saran sebelumnya) ---
    function handleBookingAction(bookingId, actionUrl, actionText, successMessage) {
        Swal.fire({
            title: `${actionText} Booking?`,
            text: `This will ${actionText.toLowerCase()} the booking permanently.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: `Yes, ${actionText}`,
            cancelButtonText: 'No'
        }).then(result => {
            if (result.isConfirmed) {
                fetch(actionUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ bookingId: bookingId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success', successMessage, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message || `Failed to ${actionText.toLowerCase()} booking.`, 'error');
                    }
                })
                .catch(err => Swal.fire('Error', 'Connection error: ' + err.message, 'error'));
            }
        });
    }

    // Booking management event listeners (menggunakan helper function)
    document.querySelectorAll('.btnEditBooking').forEach(btn => {
        btn.addEventListener('click', function() {
            const bookingId = this.dataset.id;
            const currentDate = this.dataset.date;
            const currentTime = this.dataset.time;
            const currentDuration = this.dataset.duration;

            Swal.fire({
                title: 'Edit Booking Time',
                html: `
                    <input type="date" id="newDate" class="swal2-input" value="${currentDate}" min="${new Date().toISOString().split('T')[0]}">
                    <input type="time" id="newTime" class="swal2-input" value="${currentTime}">
                    <input type="number" id="newDuration" class="swal2-input" value="${currentDuration / 60}" min="0.5" step="0.5" placeholder="Duration in hours">
                `,
                showCancelButton: true,
                confirmButtonText: 'Update',
                preConfirm: () => {
                    const newDate = document.getElementById('newDate').value;
                    const newTime = document.getElementById('newTime').value;
                    const newDuration = document.getElementById('newDuration').value;

                    if (!newDate || !newTime || !newDuration) {
                        Swal.showValidationMessage('All fields are required');
                        return false;
                    }

                    return { newDate, newTime, newDuration };
                }
            }).then(result => {
                if (result.isConfirmed) {
                    fetch('update_booking.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            bookingId: bookingId,
                            newDate: result.value.newDate,
                            newTime: result.value.newTime,
                            newDuration: result.value.newDuration * 60
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success', 'Booking updated successfully', 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(err => Swal.fire('Error', 'Connection error: ' + err.message, 'error'));
                }
            });
        });
    });

    document.querySelectorAll('.btnCancelBooking').forEach(btn => {
        btn.addEventListener('click', function() {
            handleBookingAction(this.dataset.id, 'cancel_booking.php', 'Cancel', 'Booking cancelled successfully');
        });
    });

    document.querySelectorAll('.btnDeleteBooking').forEach(btn => {
        btn.addEventListener('click', function() {
            handleBookingAction(this.dataset.id, 'delete_booking.php', 'Delete', 'Booking deleted successfully');
        });
    });

    // Room management unchanged
    document.querySelectorAll('.btnToggleRoom').forEach(btn => {
        btn.addEventListener('click', function() {
            const roomId = this.dataset.id;
            const currentStatus = this.dataset.status;
            const newStatus = currentStatus === 'available' ? 'booked' : 'available';

            Swal.fire({
                title: `Set Room ${newStatus}?`,
                text: `This will change the room status to ${newStatus}.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then(result => {
                if (result.isConfirmed) {
                    fetch('toggle_room.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ roomId: roomId, newStatus: newStatus })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success', `Room status updated to ${newStatus}`, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    });
                }
            });
        });
    });

    // Room card click to open modal (menggunakan roomModalInstance yang sudah diinisialisasi)
    document.querySelectorAll('#rooms .room-card').forEach(card => {
        card.addEventListener('click', function() {
            const roomId = this.dataset.roomId;
            if (!roomId) return;
            
            // Sembunyikan form edit saat modal dibuka
            document.getElementById('editRoomForm').style.display = 'none';

            fetch(`get_room_modal.php?room_id=${roomId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    Swal.fire('Error', data.error, 'error');
                    return;
                }

                document.getElementById('modalRoomName').textContent = data.room.name;
                document.getElementById('modalRoomType').textContent = data.room.type;
                const statusBadge = document.getElementById('modalRoomStatus');
                statusBadge.textContent = data.room.status.charAt(0).toUpperCase() + data.room.status.slice(1);
                statusBadge.style.backgroundColor = data.room.status === 'available' ? '#28a745' : '#dc3545';
                statusBadge.style.color = 'white';

                const bookingsDiv = document.getElementById('modalBookings');
                if (data.bookings.length > 0) {
                    bookingsDiv.innerHTML = data.bookings.map(b => `
                        <p><strong>${b.username}</strong> - ${b.start_time} (${b.duration} min) - ${b.status}</p>
                    `).join('');
                } else {
                    bookingsDiv.innerHTML = '<p>No bookings today.</p>';
                }

                const slotsDiv = document.getElementById('modalSlots');
                if (data.available_slots.length > 0) {
                    slotsDiv.innerHTML = data.available_slots.map(slot => `<span class="badge bg-success me-1">${slot}</span>`).join('');
                } else {
                    slotsDiv.innerHTML = '<p>No available slots.</p>';
                }
                
                // Isi form Edit Room
                document.getElementById('editRoomId').value = data.room.id;
                document.getElementById('editRoomName').value = data.room.name;
                document.getElementById('editRoomType').value = data.room.type;

                // Tampilkan modal menggunakan instance global
                if (roomModalInstance) {
                    roomModalInstance.show();
                }
            })
            .catch(err => {
                console.error('Error fetching room data:', err);
                Swal.fire('Error', 'Failed to load room data', 'error');
            });
        });
    });

    document.getElementById('btnSetAvailable').addEventListener('click', function() {
        const roomId = document.getElementById('editRoomId').value;
        const newStatus = 'available';

        Swal.fire({
            title: `Set Room Available?`,
            text: `This will change the room status to available.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then(result => {
            if (result.isConfirmed) {
                fetch('toggle_room.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ roomId: roomId, newStatus: newStatus })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateRoomStatusUI(roomId, newStatus);
                        Swal.fire('Success', `Room status updated to ${newStatus}`, 'success');
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                });
            }
        });
    });

    document.getElementById('btnSetBooked').addEventListener('click', function() {
        const roomId = document.getElementById('editRoomId').value;
        const newStatus = 'booked';

        Swal.fire({
            title: `Set Room Booked?`,
            text: `This will change the room status to booked.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then(result => {
            if (result.isConfirmed) {
                fetch('toggle_room.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ roomId: roomId, newStatus: newStatus })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateRoomStatusUI(roomId, newStatus);
                        Swal.fire('Success', `Room status updated to ${newStatus}`, 'success');
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                });
            }
        });
    });

    document.getElementById('btnViewHistory').addEventListener('click', function() {
        const roomId = document.getElementById('editRoomId').value;

        fetch(`get_room_history.php?room_id=${roomId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    Swal.fire('Error', data.error, 'error');
                    return;
                }

                const tbody = document.getElementById('historyTableBody');
                if (data.bookings.length > 0) {
                    tbody.innerHTML = data.bookings.map(b => `
                        <tr>
                            <td>${b.username}</td>
                            <td>${b.start_date}</td>
                            <td>${b.start_time}</td>
                            <td>${b.duration} min</td>
                            <td><span class="badge bg-${b.status === 'completed' ? 'secondary' : (b.status === 'active' ? 'success' : 'warning')}">${b.status}</span></td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center">No booking history found.</td></tr>';
                }

                const historyModal = new bootstrap.Modal(document.getElementById('roomHistoryModal'));
                historyModal.show();
            })
            .catch(err => {
                console.error('Error fetching history:', err);
                Swal.fire('Error', 'Failed to load booking history', 'error');
            });
    });

    function updateRoomStatusUI(roomId, newStatus) {
        // Update modal
        document.getElementById('modalRoomStatus').textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
        document.getElementById('modalRoomStatus').style.backgroundColor = newStatus === 'available' ? '#28a745' : '#dc3545';

        // Update card
        const card = document.querySelector(`#rooms .room-card[data-room-id="${roomId}"]`);
        if (card) {
            const badge = card.querySelector('.badge');
            badge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
            badge.style.backgroundColor = newStatus === 'available' ? '#28a745' : '#dc3545';
        }
    }

    document.getElementById('btnEditRoom').addEventListener('click', function() {
        // Use Bootstrap Collapse or class toggle instead of display:block for accessibility
        const editRoomForm = document.getElementById('editRoomForm');
        if (editRoomForm.classList.contains('d-none')) {
            editRoomForm.classList.remove('d-none');
            // Focus first input for better accessibility
            const firstInput = editRoomForm.querySelector('input, select, textarea, button');
            if (firstInput) firstInput.focus();
        }
    });

    document.getElementById('cancelEdit').addEventListener('click', function() {
        const editRoomForm = document.getElementById('editRoomForm');
        if (!editRoomForm.classList.contains('d-none')) {
            editRoomForm.classList.add('d-none');
            // Set focus back to edit button
            const btnEditRoom = document.getElementById('btnEditRoom');
            if (btnEditRoom) btnEditRoom.focus();
        }
    });

    // Handle form Edit Room submit via AJAX (FIXED: Expecting JSON response)
    document.getElementById('formEditRoom').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        // Sembunyikan form edit saat submit
        document.getElementById('editRoomForm').style.display = 'none';

        fetch('edit_room.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Harapkan JSON dari server
        .then(data => {
            // Tutup Room Modal
            if (roomModalInstance) {
                roomModalInstance.hide();
            }

            if (data.success) {
                Swal.fire('Success', data.message || 'Room updated successfully', 'success')
                .then(() => location.reload()); // Reload untuk update data di dashboard
            } else {
                Swal.fire('Error', data.message || 'Failed to update room', 'error');
            }
        })
        .catch(err => {
            console.error('Error updating room:', err);
            Swal.fire('Error', 'Failed to communicate with server.', 'error');
        });
    });

    document.getElementById('btnDeleteRoom').addEventListener('click', function() {
        const roomId = document.getElementById('editRoomId').value;

        Swal.fire({
            title: 'Delete Room?',
            text: 'This will permanently delete the room if no active bookings.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'No'
        }).then(result => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('id', roomId);

                fetch('delete_room.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json()) // Harapkan JSON dari server
                .then(data => {
                    // Tutup Room Modal
                    if (roomModalInstance) {
                        roomModalInstance.hide();
                    }
                    
                    if (data.success) {
                        Swal.fire('Success', data.message || 'Room deleted successfully', 'success')
                        .then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message || 'Failed to delete room', 'error');
                    }
                })
                .catch(err => {
                    console.error('Error deleting room:', err);
                    Swal.fire('Error', 'Failed to delete room', 'error');
                });
            }
        });
    });
});