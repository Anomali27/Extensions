// Smooth scroll effect
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener("click", function(e) {
    e.preventDefault();
    document.querySelector(this.getAttribute("href")).scrollIntoView({
      behavior: "smooth"
    });
  });
});

// Ripple effect for buttons
document.querySelectorAll('.btn-primary, .btn-secondary, .btn-dark').forEach(btn => {
  btn.addEventListener("click", function(e) {
    let circle = document.createElement("span");
    circle.classList.add("ripple");
    this.appendChild(circle);

    let d = Math.max(this.clientWidth, this.clientHeight);
    circle.style.width = circle.style.height = d + "px";
    circle.style.left = e.clientX - this.getBoundingClientRect().left - d/2 + "px";
    circle.style.top = e.clientY - this.getBoundingClientRect().top - d/2 + "px";

    setTimeout(() => circle.remove(), 600);
  });
});

// === Modal Popup ===
const modal = document.getElementById("authModal");
const closeBtn = document.querySelector(".modal .close");

// Semua tombol reservasi / pilih paket diarahkan ke modal
document.querySelectorAll(".reservasi, .paket").forEach(btn => {
  btn.addEventListener("click", function(e) {
    e.preventDefault();
    modal.style.display = "flex";
  });
});

// Tombol close
closeBtn.addEventListener("click", () => {
  modal.style.display = "none";
});

// Klik di luar modal -> close
window.addEventListener("click", (e) => {
  if (e.target === modal) {
    modal.style.display = "none";
  }
});