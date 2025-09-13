// Script untuk demo interaksi form
document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector(".form");

  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const username = document.querySelector("#username").value;
    const password = document.querySelector("#password").value;

    if (username && password) {
      // Redirect ke index.php
      window.location.href = "../index.php";
    } else {
      alert("Please fill in both Username and Password.");
    }
  });
});


document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector(".login-form");
  const modal = document.getElementById("successModal");
  const modalMsg = document.getElementById("successMessage");
  const closeBtn = modal.querySelector(".close");

  form.addEventListener("submit", (e) => {
    e.preventDefault();

    const username = document.querySelector("#username").value.trim();
    const password = document.querySelector("#password").value.trim();

    if (!username || !password) {
      alert("Harap isi semua field!");
      return;
    }

    // Tampilkan modal sukses
    modalMsg.textContent = `Login berhasil! Selamat datang, ${username}`;
    modal.style.display = "flex";

    // Simulasi redirect setelah 2 detik
    setTimeout(() => {
      window.location.href = "../index.php";
    }, 2000);
  });

  // Tutup modal manual
  closeBtn.addEventListener("click", () => {
    modal.style.display = "none";
  });

  // Klik luar modal
  window.addEventListener("click", (e) => {
    if (e.target === modal) {
      modal.style.display = "none";
    }
  });
});
