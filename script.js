// Smooth page transitions
document.addEventListener('DOMContentLoaded', function() {
  const links = document.querySelectorAll('a[href]');
  links.forEach(link => {
    link.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      if (href && !href.startsWith('#') && !href.startsWith('http')) {
        e.preventDefault();
        document.body.style.opacity = '0';
        setTimeout(() => {
          window.location.href = href;
        }, 300);
      }
    });
  });
});

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
