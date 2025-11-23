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

    // Initialize Bootstrap modal instances
    const addUserModalInstance = modalAddUser ? new bootstrap.Modal(modalAddUser) : null;
    const editUserModalInstance = modalEditUser ? new bootstrap.Modal(modalEditUser) : null;

    // Utility to open modal
    function openModal(modal) {
        if (!modal) return;
        modal.removeAttribute('hidden');
        // Focus first focusable element
        const focusable = modal.querySelector('input, select, textarea, button');
        if (focusable) focusable.focus();
        document.body.style.overflow = 'hidden'; // Disable background scroll
    }

    // Utility to close modal
    function closeModal(modal) {
        if (!modal) return;
        modal.setAttribute('hidden', '');
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

    // Remove AJAX submit handler for Add User form, allow normal form submit for page reload and backend handling

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
    
    // Other unrelated AJAX or booking, inventory, rooms management code left untouched for now

    // SweetAlert2 usage for centralized alerts from #alertData
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
});
