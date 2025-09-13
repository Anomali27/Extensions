// Script sederhana validasi Sign Up
document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector(".signup-form");

  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const username = document.querySelector("#username").value.trim();
    const email = document.querySelector("#email").value.trim();
    const password = document.querySelector("#password").value.trim();

    if (!username || !email || !password) {
      alert("Please fill all the fields!");
      return;
    }

    if (!email.includes("@")) {
      alert("Please enter a valid email address!");
      return;
    }

    // Jika semua valid → redirect ke index.php
    alert(`Account created successfully!\nWelcome, ${username}`);
    window.location.href = "../index.php";
  });
});

modalMsg.textContent = `Sign Up berhasil! Selamat datang, ${username}`;
