// 🌐 Load Header
fetch('header.html')
  .then(res => res.text())
  .then(data => {
    document.getElementById('header-placeholder').innerHTML = data;
    setupMobileMenu();       // Setup mobile nav toggling
    setupAnchorScrolling();  // Enable anchor scrolling
  });

// 🔻 Load Footer
fetch('footer.html')
  .then(res => res.text())
  .then(data => {
    document.getElementById('footer-placeholder').innerHTML = data;
  });

// ✨ Section Scroll Animation
const observer = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('animate');
      observer.unobserve(entry.target);
    }
  });
}, { threshold: 0.1 });

document.querySelectorAll('section').forEach(section => observer.observe(section));

function toggleMenu() {
  const navbar = document.getElementById('navbar');
  const icon = document.getElementById('menu-icon');

  navbar.classList.toggle('active'); // Show/hide menu

  // Change icon text based on menu visibility
  if (navbar.classList.contains('active')) {
    icon.textContent = '✖'; // Cross icon
  } else {
    icon.textContent = '☰'; // Hamburger icon
  }
}

// 🧭 Smooth Scroll to Anchor on Page Load
function setupAnchorScrolling() {
  const hash = window.location.hash;
  if (hash) {
    setTimeout(() => {
      const target = document.querySelector(hash);
      if (target) {
        target.scrollIntoView({ behavior: 'smooth' });
      }
    }, 300);
  }
}

// 📄 (Optional) SPA Page Load Function
function loadPage(path) {
  fetch(path)
    .then(res => res.text())
    .then(html => {
      document.getElementById("main").innerHTML = html;
      setupAnchorScrolling(); // Re-scroll after dynamic load
    });
}
