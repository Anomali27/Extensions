// === ANIMASI CARD ===
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
});





// === POPUP MODAL TAMBAH USER ===
document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("modalAddUser");
  const openBtn = document.getElementById("btnAddUser");
  const closeBtn = document.getElementById("closeModal");

  if (openBtn) {
    openBtn.addEventListener("click", () => {
      modal.style.display = "flex";
    });
  }

  if (closeBtn) {
    closeBtn.addEventListener("click", () => {
      modal.style.display = "none";
    });
  }

  window.addEventListener("click", (e) => {
    if (e.target === modal) modal.style.display = "none";
  });

  // Tangani submit form tambah user via AJAX
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

        const data = await res.text();
        console.log("Response:", data);

        Swal.fire({
          title: "Berhasil!",
          text: "User berhasil ditambahkan ✅",
          icon: "success",
          timer: 1500,
          showConfirmButton: false,
        });

        modal.style.display = "none";
        setTimeout(() => location.reload(), 1600);
      } catch (err) {
        Swal.fire("Error", "Gagal menambahkan user!", "error");
      }
    });
  }
});



// === POPUP EDIT USER ===
document.addEventListener("DOMContentLoaded", () => {
  const modalEdit = document.getElementById("modalEditUser");
  const closeEditBtn = document.getElementById("closeEditModal");
  const editForm = document.getElementById("editUserForm");

  // Tombol edit di tabel
  document.querySelectorAll(".btnEditUser").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();

      // Ambil data dari atribut tombol
      const id = btn.dataset.id;
      const username = btn.dataset.username;
      const email = btn.dataset.email;
      const status = btn.dataset.status;

      // Isi form modal
      document.getElementById("editId").value = id;
      document.getElementById("editUsername").value = username;
      document.getElementById("editEmail").value = email;
      document.getElementById("editStatus").value = status;

      // Tampilkan modal edit
      modalEdit.style.display = "flex";
    });
  });

  // Tutup modal jika tombol batal ditekan
  closeEditBtn.addEventListener("click", () => {
    modalEdit.style.display = "none";
  });

  // Tutup modal jika klik di luar form
  window.addEventListener("click", (e) => {
    if (e.target === modalEdit) modalEdit.style.display = "none";
  });
});

// === BOOKING MANAGEMENT ===
document.addEventListener("DOMContentLoaded", () => {
  // Edit Booking Time
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
          });
        }
      });
    });
  });

  // Cancel Booking
  document.querySelectorAll('.btnCancelBooking').forEach(btn => {
    btn.addEventListener('click', function() {
      const bookingId = this.dataset.id;

      Swal.fire({
        title: 'Cancel Booking?',
        text: 'This will cancel the booking permanently.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Cancel',
        cancelButtonText: 'No'
      }).then(result => {
        if (result.isConfirmed) {
          fetch('cancel_booking.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ bookingId: bookingId })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire('Success', 'Booking cancelled successfully', 'success').then(() => location.reload());
            } else {
              Swal.fire('Error', data.message, 'error');
            }
          });
        }
      });
    });
  });

  // Delete Booking
  document.querySelectorAll('.btnDeleteBooking').forEach(btn => {
    btn.addEventListener('click', function() {
      const bookingId = this.dataset.id;

      Swal.fire({
        title: 'Delete Booking?',
        text: 'This will permanently delete the booking from the database.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'No'
      }).then(result => {
        if (result.isConfirmed) {
          fetch('delete_booking.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ bookingId: bookingId })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire('Success', 'Booking deleted successfully', 'success').then(() => location.reload());
            } else {
              Swal.fire('Error', data.message, 'error');
            }
          });
        }
      });
    });
  });

  // === ROOM MANAGEMENT ===
  // Toggle Room Status
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

  // Room Card Click to Open Modal
  document.querySelectorAll('#rooms .room-card').forEach(card => {
    card.addEventListener('click', function() {
      const roomId = this.dataset.roomId;
      if (!roomId) return;

      // Fetch room data
      fetch(`get_room_modal.php?room_id=${roomId}`)
        .then(response => response.json())
        .then(data => {
          if (data.error) {
            Swal.fire('Error', data.error, 'error');
            return;
          }

          // Populate modal
          document.getElementById('modalRoomName').textContent = data.room.name;
          document.getElementById('modalRoomType').textContent = data.room.type;
          const statusBadge = document.getElementById('modalRoomStatus');
          statusBadge.textContent = data.room.status.charAt(0).toUpperCase() + data.room.status.slice(1);
          statusBadge.style.backgroundColor = data.room.status === 'available' ? '#28a745' : '#dc3545';
          statusBadge.style.color = 'white';

          // Bookings
          const bookingsDiv = document.getElementById('modalBookings');
          if (data.bookings.length > 0) {
            bookingsDiv.innerHTML = data.bookings.map(b => `
              <p><strong>${b.username}</strong> - ${b.start_time} (${b.duration} min) - ${b.status}</p>
            `).join('');
          } else {
            bookingsDiv.innerHTML = '<p>No bookings today.</p>';
          }

          // Available Slots
          const slotsDiv = document.getElementById('modalSlots');
          if (data.available_slots.length > 0) {
            slotsDiv.innerHTML = data.available_slots.map(slot => `<span class="badge bg-success me-1">${slot}</span>`).join('');
          } else {
            slotsDiv.innerHTML = '<p>No available slots.</p>';
          }

          // Set room ID for actions
          document.getElementById('editRoomId').value = data.room.id;
          document.getElementById('editRoomName').value = data.room.name;
          document.getElementById('editRoomType').value = data.room.type;

          // Show modal
          const modal = new bootstrap.Modal(document.getElementById('roomModal'));
          modal.show();
        })
        .catch(err => {
          console.error('Error fetching room data:', err);
          Swal.fire('Error', 'Failed to load room data', 'error');
        });
    });
  });

  // Modal Actions
  // Set Available
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

  // Set Booked
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

  // View Booking History
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

  // Helper function to update UI
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
    document.getElementById('editRoomForm').style.display = 'block';
  });

  document.getElementById('cancelEdit').addEventListener('click', function() {
    document.getElementById('editRoomForm').style.display = 'none';
  });

  document.getElementById('formEditRoom').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('edit_room.php', {
      method: 'POST',
      body: formData
    })
    .then(response => {
      if (response.redirected) {
        window.location.href = response.url;
      } else {
        return response.text();
      }
    })
    .then(data => {
      if (data) {
        Swal.fire('Error', 'Failed to update room', 'error');
      }
    })
    .catch(err => {
      console.error('Error updating room:', err);
      Swal.fire('Error', 'Failed to update room', 'error');
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
        .then(response => {
          if (response.redirected) {
            window.location.href = response.url;
          } else {
            return response.text();
          }
        })
        .then(data => {
          if (data) {
            Swal.fire('Error', 'Failed to delete room', 'error');
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

