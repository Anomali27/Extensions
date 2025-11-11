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
});

