function showModal(message, success) {
  const modal = document.getElementById("modal");
  const msg = document.getElementById("modal-message");

  msg.textContent = message;
  msg.style.color = success ? "#00bfa5" : "red";
  modal.style.display = "flex";

  setTimeout(() => {
    modal.style.display = "none";
  }, 2000);
}

// Efek transisi antar halaman (slide ke kiri dari signup ke login)
document.querySelectorAll('.switch').forEach(link => {
  link.addEventListener('click', (e) => {
    e.preventDefault();
    const container = document.querySelector('.container');
    container.classList.add('slide-left');
    setTimeout(() => {
      window.location.href = e.target.getAttribute('href');
    }, 500);
  });
});
