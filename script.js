// ======================
// 🏗️ INITIALIZATION CODE
// ======================

// 🌐 Load Header and Footer
function loadTemplates() {
  fetch('header.html')
    .then(res => res.text())
    .then(data => {
      document.getElementById('header-placeholder').innerHTML = data;
      setupMobileMenu();
      setupAnchorScrolling();
    });

  fetch('footer.html')
    .then(res => res.text())
    .then(data => {
      document.getElementById('footer-placeholder').innerHTML = data;
    });
}

// ======================
// 🧭 NAVIGATION FUNCTIONS
// ======================

// 📜 Setup mobile menu toggle
function toggleMenu() {
  const navbar = document.getElementById('navbar');
  const menuIcon = document.getElementById('menu-icon');
  
  // Toggle the 'active' class on the navbar
  navbar.classList.toggle('active');
  
  // Change the menu icon based on state
  if (navbar.classList.contains('active')) {
    menuIcon.innerHTML = '✕'; // Close icon when menu is open
  } else {
    menuIcon.innerHTML = '☰'; // Hamburger icon when menu is closed
  }
}


// ⚓ Smooth scroll to anchor
function setupAnchorScrolling() {
  // Handle initial page load with hash
  const hash = window.location.hash;
  if (hash) {
    setTimeout(() => {
      const target = document.querySelector(hash);
      if (target) {
        target.scrollIntoView({ behavior: 'smooth' });
      }
    }, 300);
  }

  // Setup all anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({ behavior: 'smooth' });
      }
    });
  });
}

// ======================
// ✨ ANIMATION FUNCTIONS
// ======================

// 👀 Intersection Observer for scroll animations
function setupScrollAnimations() {
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('animate');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('section').forEach(section => {
    observer.observe(section);
  });
}

// ======================
// 🎯 REGISTRATION BUTTON
// ======================

// 🖱️ Setup floating register buttonfunction setupRegisterButton() {
  const btn = document.querySelector('.floating-register-btn');
    
    if (btn) {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        
        const registerSection = document.getElementById('register');
        if (registerSection) {
          // Modern smooth scroll with options
          registerSection.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
            inline: 'nearest'
          });
          
          // Update URL without jumping
          history.pushState(null, null, '#register');
          
          // Click animation feedback
          this.style.transform = 'scale(0.9)';
          const icon = this.querySelector('.btn-icon');
          if (icon) {
            icon.style.transform = 'rotate(10deg)';
          }
          
          setTimeout(() => {
            this.style.transform = '';
            if (icon) icon.style.transform = '';
          }, 200);
        }
      });
    }

    // Bonus: Hide button when already at registration section
    window.addEventListener('scroll', function() {
      const registerSection = document.getElementById('register');
      const btn = document.querySelector('.floating-register-btn');
      
      if (registerSection && btn) {
        const rect = registerSection.getBoundingClientRect();
        if (rect.top <= 100 && rect.bottom >= 100) {
          btn.style.opacity = '0';
          btn.style.pointerEvents = 'none';
        } else {
          btn.style.opacity = '1';
          btn.style.pointerEvents = 'auto';
        }
      }
    });


// ======================
// 📄 (OPTIONAL) SPA LOADER
// ======================

function loadPage(path) {
  fetch(path)
    .then(res => res.text())
    .then(html => {
      document.getElementById("main").innerHTML = html;
      setupAnchorScrolling();
    });
}

// ======================
// 🚀 INITIALIZE EVERYTHING
// ======================

document.addEventListener('DOMContentLoaded', function() {
  loadTemplates();
  setupScrollAnimations();
  setupRegisterButton();
  
  // Initialize other components as needed
});