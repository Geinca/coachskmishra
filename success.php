<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thank You! | Chakra Healing Course</title>
  <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #8E44AD;
      --secondary: #9B59B6;
      --accent: #E74C3C;
      --light: #F5EEF8;
      --dark: #2C3E50;
    }
    
    body {
      font-family: 'Hind Siliguri', sans-serif;
      background-color: var(--light);
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      color: var(--dark);
      background-image: radial-gradient(circle at 10% 20%, rgba(142, 68, 173, 0.1) 0%, rgba(142, 68, 173, 0.05) 90%);
    }
    
    .success-container {
      background: white;
      border-radius: 16px;
      padding: 40px;
      width: 90%;
      max-width: 600px;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
      position: relative;
      overflow: hidden;
      animation: fadeIn 0.8s ease;
      border: 1px solid rgba(142, 68, 173, 0.2);
    }
    
    .success-container::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 8px;
      background: linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));
    }
    
    .chakra-animation {
      width: 150px;
      height: 150px;
      margin: 0 auto 30px;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .chakra {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      background: conic-gradient(
        from 0deg,
        #E74C3C 0deg 60deg,
        #F39C12 60deg 120deg,
        #F1C40F 120deg 180deg,
        #2ECC71 180deg 240deg,
        #3498DB 240deg 300deg,
        #8E44AD 300deg 360deg
      );
      position: relative;
      animation: spin 8s linear infinite;
      box-shadow: 0 0 30px rgba(142, 68, 173, 0.3);
    }
    
    .chakra::after {
      content: "✓";
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 60px;
      height: 60px;
      background: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 30px;
      color: var(--primary);
      box-shadow: inset 0 0 10px rgba(0,0,0,0.1);
    }
    
    h1 {
      color: var(--primary);
      font-size: 28px;
      margin-bottom: 20px;
      font-weight: 700;
      text-shadow: 0 2px 4px rgba(142, 68, 173, 0.1);
    }
    
    p {
      font-size: 16px;
      line-height: 1.8;
      margin-bottom: 30px;
      color: #555;
    }
    
    .details-box {
      background: rgba(142, 68, 173, 0.05);
      border-radius: 12px;
      padding: 20px;
      margin: 25px 0;
      text-align: left;
      border: 1px dashed rgba(142, 68, 173, 0.3);
      position: relative;
    }
    
    .details-box::before {
      content: "Your Details";
      position: absolute;
      top: -12px;
      left: 20px;
      background: white;
      padding: 0 10px;
      font-size: 14px;
      color: var(--primary);
      font-weight: 600;
    }
    
    .detail-item {
      margin-bottom: 12px;
      display: flex;
      align-items: center;
    }
    
    .detail-item strong {
      width: 100px;
      display: inline-block;
      color: var(--primary);
      font-weight: 600;
    }
    
    .detail-item span {
      flex: 1;
      padding: 8px 12px;
      background: white;
      border-radius: 6px;
      border: 1px solid rgba(142, 68, 173, 0.2);
    }
    
    .action-buttons {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-top: 30px;
      flex-wrap: wrap;
    }
    
    .btn {
      padding: 12px 25px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 15px;
    }
    
    .btn-primary {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: white;
      box-shadow: 0 4px 15px rgba(142, 68, 173, 0.3);
    }
    
    .btn-secondary {
      background: white;
      color: var(--primary);
      border: 1px solid var(--primary);
      box-shadow: 0 2px 10px rgba(142, 68, 173, 0.1);
    }
    
    .btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(142, 68, 173, 0.3);
    }
    
    .energy-dots {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      pointer-events: none;
      z-index: -1;
      overflow: hidden;
    }
    
    .energy-dot {
      position: absolute;
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: rgba(142, 68, 173, 0.4);
      animation: float 15s linear infinite;
    }
    
    @keyframes spin {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.95); }
      to { opacity: 1; transform: scale(1); }
    }
    
    @keyframes float {
      0% { transform: translateY(0) translateX(0); opacity: 0; }
      10% { opacity: 1; }
      90% { opacity: 1; }
      100% { transform: translateY(-100vh) translateX(20px); opacity: 0; }
    }
    
    @media (max-width: 600px) {
      .success-container {
        padding: 30px 20px;
        width: 95%;
      }
      
      .chakra-animation {
        width: 120px;
        height: 120px;
      }
      
      h1 {
        font-size: 22px;
      }
      
      .action-buttons {
        flex-direction: column;
        gap: 10px;
      }
      
      .detail-item {
        flex-direction: column;
        align-items: flex-start;
      }
      
      .detail-item strong {
        width: auto;
        margin-bottom: 5px;
      }
      
      .detail-item span {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="success-container">
    <div class="chakra-animation">
      <div class="chakra"></div>
    </div>
    <h1>Your Energy Journey Begins!</h1>
    <p>Your registration has been successfully completed. We've sent a confirmation to your email, please check your inbox.</p>
    
    <div class="details-box">
      <div class="detail-item">
        <strong>Name:</strong>
        <span><?php echo htmlspecialchars($_GET['name'] ?? ''); ?></span>
      </div>
      <div class="detail-item">
        <strong>Email:</strong>
        <span><?php echo htmlspecialchars($_GET['email'] ?? ''); ?></span>
      </div>
      <div class="detail-item">
        <strong>Phone:</strong>
        <span><?php echo htmlspecialchars($_GET['phone'] ?? ''); ?></span>
      </div>
      <div class="detail-item">
        <strong>City:</strong>
        <span><?php echo htmlspecialchars($_GET['city'] ?? ''); ?></span>
      </div>
      <div class="detail-item">
        <strong>Course:</strong>
        <span>9-Day Chakra Balancing Course</span>
      </div>
    </div>
    
    <p>Our team member will contact you shortly. For any questions, please call us at 96921 93870 or connect with us on WhatsApp using the button below.</p>
    
    <div class="action-buttons">
      <a href="https://wa.me/919692193870" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        Chat on WhatsApp
      </a>
      <a href="index.php" class="btn btn-secondary">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
          <polyline points="9 22 9 12 15 12 15 22"></polyline>
        </svg>
        Return to Home
      </a>
    </div>
    
    <div class="energy-dots" id="energyDots"></div>
  </div>

  <script>
    // Create energy dots
    function createEnergyDots() {
      const container = document.getElementById('energyDots');
      const dotCount = 20;
      
      for (let i = 0; i < dotCount; i++) {
        const dot = document.createElement('div');
        dot.className = 'energy-dot';
        dot.style.left = Math.random() * 100 + '%';
        dot.style.top = Math.random() * 100 + 100 + '%';
        dot.style.animationDelay = Math.random() * 5 + 's';
        dot.style.animationDuration = 10 + Math.random() * 10 + 's';
        dot.style.opacity = Math.random() * 0.5 + 0.1;
        dot.style.width = dot.style.height = (3 + Math.random() * 5) + 'px';
        container.appendChild(dot);
      }
    }
    
    // Run when page loads
    window.onload = function() {
      createEnergyDots();
      
      // Add slight delay to chakra animation
      setTimeout(() => {
        document.querySelector('.chakra').style.animation = 'spin 8s linear infinite';
      }, 500);
    };
  </script>
</body>
</html>