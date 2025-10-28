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
  // Buat modal edit dinamis (tidak perlu di HTML)
  const modalEdit = document.createElement("div");
  modalEdit.classList.add("modal");
  modalEdit.id = "modalEditUser";
  modalEdit.innerHTML = `
    <div class="modal-content">
      <h3>Edit User</h3>
      <form id="editUserForm">
        <input type="hidden" name="id" id="editUserId">
        <input type="text" name="username" id="editUsername" placeholder="Username" required>
        <input type="email" name="email" id="editEmail" placeholder="Email" required>
        <input type="password" name="password" id="editPassword" placeholder="Password (kosongkan jika tidak diubah)">
        <div class="btn-group">
          <button type="submit" class="btn-primary">Simpan</button>
          <button type="button" class="btn-secondary" id="closeEditModal">Batal</button>
        </div>
      </form>
    </div>
  `;
  document.body.appendChild(modalEdit);

  // Fungsi buka modal edit
  document.querySelectorAll(".btnEdit").forEach((btn) => {
    btn.addEventListener("click", async (e) => {
      e.preventDefault();
      const id = btn.dataset.id;

      try {
        const res = await fetch(`get_user.php?id=${id}`);
        const data = await res.json();

        if (data) {
          document.getElementById("editUserId").value = data.id;
          document.getElementById("editUsername").value = data.username;
          document.getElementById("editEmail").value = data.email;
          modalEdit.style.display = "flex";
        }
      } catch (err) {
        Swal.fire("Gagal!", "Tidak dapat mengambil data user.", "error");
      }
    });
  });

  // Submit form edit user via AJAX
  document.addEventListener("submit", async (e) => {
    if (e.target.id === "editUserForm") {
      e.preventDefault();

      const formData = new FormData(e.target);

      try {
        const res = await fetch("edit_user.php", {
          method: "POST",
          body: formData,
        });

        const result = await res.text();
        console.log("Edit Response:", result);

        Swal.fire({
          title: "Sukses!",
          text: "Data user berhasil diperbarui ✅",
          icon: "success",
          timer: 1500,
          showConfirmButton: false,
        });

        modalEdit.style.display = "none";
        setTimeout(() => location.reload(), 1600);
      } catch (err) {
        Swal.fire("Error", "Gagal mengedit user!", "error");
      }
    }
  });

  // Tutup modal edit
  document.addEventListener("click", (e) => {
    if (e.target.id === "closeEditModal" || e.target === modalEdit) {
      modalEdit.style.display = "none";
    }
  });
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

