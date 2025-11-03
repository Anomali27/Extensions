// Scroll Reveal animation
ScrollReveal({
  reset: true,
  distance: '60px',
  duration: 1500,
  delay: 100
});

ScrollReveal().reveal('.hero-text, .section h2', { delay: 200, origin: 'top' });
ScrollReveal().reveal('.unit-card', { delay: 300, origin: 'bottom', interval: 150 });
ScrollReveal().reveal('.paket-card', { delay: 300, origin: 'bottom', interval: 100 });
