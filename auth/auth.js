const signUpButton = document.getElementById('signUp');
const signInButton = document.getElementById('signIn');
const container = document.getElementById('authContainer');
const switchLinks = document.querySelectorAll('.switch');

// Tombol di panel kanan & kiri
signUpButton.addEventListener('click', () => {
  container.classList.add('right-panel-active');
});

signInButton.addEventListener('click', () => {
  container.classList.remove('right-panel-active');
});

// Link di bawah form ("Belum punya akun? Sign Up")
switchLinks.forEach(link => {
  link.addEventListener('click', e => {
    e.preventDefault();
    container.classList.toggle('right-panel-active');
  });
});
