<!DOCTYPE html>
<html lang="or">
<head>
  <!-- 🔷 Metadata and External Resources -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ଚକ୍ର ହିଲିଂ କୋର୍ସ | SK Mishra</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="home.css">
</head>

<style>
.floating-register-btn {
  position: fixed;
  bottom: 30px;
  right: 30px;
  background: linear-gradient(135deg, #FF5722, #FF9800);
  color: white;
  border-radius: 50px;
  padding: 15px 20px;
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  box-shadow: 0 4px 20px rgba(255, 87, 34, 0.3);
  z-index: 999;
  overflow: hidden;
  width: 60px;
  height: 60px;
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.floating-register-btn:hover {
  width: 200px;
  background: linear-gradient(135deg, #FF9800, #FF5722);
  box-shadow: 0 6px 25px rgba(255, 87, 34, 0.4);
}

.btn-icon {
  font-size: 24px;
  min-width: 30px;
  text-align: center;
  transition: all 0.3s ease;
}

.btn-text {
  white-space: nowrap;
  opacity: 0;
  transform: translateX(-10px);
  transition: all 0.3s ease;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
}

.floating-register-btn:hover .btn-text {
  opacity: 1;
  transform: translateX(0);
  transition-delay: 0.1s;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
  .floating-register-btn {
    bottom: 20px;
    right: 20px;
    width: 50px;
    height: 50px;
    padding: 12px 15px;
  }
  
  .floating-register-btn:hover {
    width: 160px;
  }
  
  .btn-icon {
    font-size: 20px;
  }
}
</style>

<body>
   
     <!-- 🔶 Header & Navigation -->

  <div id="header-placeholder"></div>
      

  <!-- 🟣 Hero Section -->
  <section class="hero-section" id="home">
    <div class="hero-content">
    <h1>Chakra Healing Course</h1>
    <p class="subtitle">9-Day Intensive Training | Balance Your Mind and Body</p>
      <ul class="hero-highlights">
        <li>✅ ଦୈନିକ ଧ୍ୟାନ ଓ ବ୍ରିଥିଂ ବ୍ୟାୟାମ</li>
        <li>✅ ଚକ୍ର ବ୍ଲକେଜ୍ ଉପରେ ଲାଇଭ୍ ହିଲିଂ</li>
        <li>✅  ମନ୍ତ୍ର ଚିକିତ୍ସା</li>
        <li>✅ ଇ-ସର୍ଟିଫିକେଟ୍ ସହିତ ପ୍ରମାଣପତ୍ର</li>
      </ul>
      <button class="cta-button" onclick="window.location.href='https://wa.me/919692193870'">✨ ଏବେ ଯୋଗଦିଅନ୍ତୁ</button>
      <p class="note">ସୀମିତ ସ୍ଥାନ | ଅଗ୍ରିମ ରେଜିଷ୍ଟ୍ରେସନ ଆବଶ୍ୟକ</p>
    </div>
  </section>

  <!-- 🔵 About Section -->
  <section class="about-section animate" id="about">
    <div class="about-container">
      <h2>👤 SK Mishra ବିଷୟରେ</h2>
      <div class="about-flex">
        <div class="about-left">
          <img src="https://coachskmishra.in/public/images/about-us-img-1.jpg" alt="SK Mishra" class="about-img" />
          <div class="about-stats">
            <div><strong>20+</strong><br>ବର୍ଷ ଅନୁଭବ</div>
            <div><strong>10000+</strong><br>ଜୀବନ ଉନ୍ନତ</div>
            <div><strong>300+</strong><br>କୋର୍ସ ବାସ୍ତବାୟନ</div>
          </div>
        </div>
        <div class="about-right">
          <p><strong> Coach SK Mishra</strong> ଏକ ଜଣେ ଆନ୍ତର୍ଜାତୀୟ ଚକ୍ର ହିଲିଂ ଗୁରୁ...</p>
          <ul class="about-highlights">
            <li>🌿 ଚକ୍ର ବ୍ଲକ ବିମୋଚନ ପାଇଁ ଉତ୍ତମ ଧ୍ୟାନ ବିଧି</li>
            
            <li>🧘 ଶ୍ୱାସ ନିୟନ୍ତ୍ରଣ ଓ ଶରୀର ଜାଗୃତି ବ୍ୟାୟାମ</li>
            <li>📜 ଅନ୍ତର୍ଜାତୀୟ ସ୍ତରର ସର୍ଟିଫିକେଟ୍ ପ୍ରଦାନ</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

 
  <!-- 🟡 Program Overview Section -->
<section class="program-section animate" id="program">
  <div class="program-container">
    <h2>🕉️ 9-Day Chakra Healing Course</h2>
    <p class="program-intro">Daily guided journey through your energy centers...</p>
    <ul class="program-list">
      <li><span class="chakra-dot gray"></span><strong>Day 1:</strong> Introduction to Chakra System</li>
      <li><span class="chakra-dot red"></span><strong>Day 2:</strong> Root Chakra - Grounding & Stability</li>
      <li><span class="chakra-dot orange"></span><strong>Day 3:</strong> Sacral Chakra - Creativity & Flow</li>
      <li><span class="chakra-dot yellow"></span><strong>Day 4:</strong> Solar Plexus - Personal Power</li>
      <li><span class="chakra-dot green"></span><strong>Day 5:</strong> Heart Chakra - Love & Compassion</li>
      <li><span class="chakra-dot blue"></span><strong>Day 6:</strong> Throat Chakra - Authentic Expression</li>
      <li><span class="chakra-dot indigo"></span><strong>Day 7:</strong> Third Eye - Intuition & Insight</li>
      <li><span class="chakra-dot violet"></span><strong>Day 8:</strong> Crown Chakra - Spiritual Connection</li>
      <li><span class="chakra-dot gold"></span><strong>Day 9:</strong> Integration & Certification</li>
    </ul>
    <div class="program-bonus">
      🎁 <strong>Bonus Includes:</strong> 
      Guided Meditations, 
      Chakra Balancing Techniques,
      Self-Healing Workbook
    </div>
  </div>
</section>

  <!-- 🟠 Testimonials Section -->
  <section class="testimonials animate" id="testimonials">
    <div class="testimonials-container">
      <h2>💬 ପ୍ରତିକ୍ରିୟା</h2>
      <div class="testimonial">
        <p>“ମୋ ଆତ୍ମବିଶ୍ୱାସ ବଢ଼ିଛି...”</p>
        <span>- ରିନା ନାୟକ</span>
      </div>
      <div class="testimonial">
        <p>“ଏହି କୋର୍ସ ମୋ ପାଇଁ ଜୀବନ ପରିବର୍ତ୍ତନ କରିଦେଲା...”</p>
        <span>- ରାହୁଲ ଦାସ</span>
      </div>
      <div class="testimonial">
        <p>“ତାଙ୍କ ଧ୍ୟାନ ଗାଇଡ୍ ବେଶ ପ୍ରଭାବଶାଳୀ...”</p>
        <span>- ସୁମିତ୍ରା ଦେବୀ</span>
      </div>
    </div>
  </section>

  <!-- 🟢 Contact Section -->
  <section class="contact-section animate" id="contact">
    <div class="contact-container">
      <h2>📞 ଯୋଗାଯୋଗ କରନ୍ତୁ</h2>
      <p><strong>For mental peace and inner strength, begin a new journey from today.</strong></p>
      <div class="contact-details">
        <p>📱 <strong>ଫୋନ୍:</strong> <a href="tel:+919692193870">96921 93870</a></p>
        <p>💬 <strong>WhatsApp:</strong> <a href="https://wa.me/919692193870" target="_blank">ଚାଟ୍ କରନ୍ତୁ</a></p>
        <p>✉️ <strong>ଇମେଲ୍:</strong> <a href="mailto:coachskmishra@gmail.com">coachskmishra@gmail.com</a></p>
      </div>
      <div class="contact-cta">
        <a href="#program" class="cta-button">📌 କୋର୍ସ ଯୋଗଦିଅନ୍ତୁ</a>
      </div>
    </div>
  </section>

 <!-- 📝 Registration Form Section -->
<section class="register-section" id="register">
  <div class="form-card">
    <h2>REGISTER FORM</h2>
    <p class="sub-heading">BOOK YOUR SEAT NOW</p>

    <img src="https://coachskmishra.com/wp-content/uploads/2025/02/madhyam_gurukul-removebg-preview.png" alt="Logo" class="from-logo" />

    <!-- Error message container -->
    <div id="error-message" class="error-message" style="display:none;"></div>
    
    <!-- Success message container -->
    <div id="success-message" class="success-message" style="display:none;">
      <div class="success-icon">✓</div>
      <h3>Registration Successful!</h3>
      <p>Thank you for registering. We've sent a confirmation to your email.</p>
      <button onclick="resetForm()">Submit Another Response</button>
    </div>

    <form id="registrationForm" action="submit.php" method="POST" novalidate>
      <div class="form-group">
        <input type="text" id="name" name="name" required minlength="3" placeholder=" " pattern="[A-Za-z ]+" />
        <label for="name">Full Name</label>
        <span class="error-text"></span>
      </div>

      <div class="form-group">
        <input type="email" id="email" name="email" required placeholder=" " />
        <label for="email">Email Address</label>
        <span class="error-text"></span>
      </div>

      <div class="form-group phone-group">
        <span class="flag">🇮🇳</span>
        <input type="tel" id="phone" name="phone" maxlength="10" minlength="10" required placeholder="Whatsapp Number" pattern="[0-9]{10}" />
        <span class="error-text"></span>
      </div>

      <div class="form-group">
        <input type="text" id="city" name="city" required placeholder=" " />
        <label for="city">City</label>
        <span class="error-text"></span>
      </div>

      <div class="form-group">
        <input type="text" id="job" name="job" required placeholder=" " />
        <label for="job">Job Title</label>
        <span class="error-text"></span>
      </div>

      <!-- CSRF Protection -->
      <input type="hidden" name="csrf_token" id="csrf_token" value="">

      <button type="submit" id="submitBtn">Yes...! I Want To Join</button>
      
      <!-- Loading indicator -->
      <div id="loading" style="display:none;">
        <div class="spinner"></div>
        <p>Processing your registration...</p>
      </div>
    </form>
  </div>
</section>

<div class="floating-register-btn"  aria-label="Register Now">
  <div class="btn-icon">📝</div>
  <div class="btn-text">Register Now</div>
</div>

  <!-- ⚫ Footer -->
  <div id="footer-placeholder"></div>


  <script>
// Generate and set CSRF token when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Generate a random token
    const token = generateToken();
    
    // Set it in the form
    document.getElementById('csrf_token').value = token;
    
    // Also store it in sessionStorage for validation
    sessionStorage.setItem('csrf_token', token);
});

function generateToken() {
    const array = new Uint32Array(10);
    window.crypto.getRandomValues(array);
    return Array.from(array, dec => ('0' + dec.toString(16)).join(''));
}

</script>

  <!-- 🔽 Section Scroll Animation Script -->
   <script src="script.js"></script>
  

</body>
</html>
