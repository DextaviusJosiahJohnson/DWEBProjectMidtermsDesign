<?php
require 'includes/public_header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Smart Browser State Manager</title>
  <script nonce="<?= $nonce ?>">document.documentElement.setAttribute("data-theme","minimal");</script>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/pages/landing.css">
</head>
<body>

  <!-- NAVIGATION -->
  <header>
    <div class="logo">
      <img src="img/logo.png" alt="Logo" class="logo-img">
      <span>Smart Browser State Manager</span>
    </div>

    <nav class="nav-links" id="navLinks">
      <button data-target="home">Home</button>
      <button data-target="features">Features</button>
      <button data-target="how-it-works">How It Works</button>
      <button id="navLoginBtn">Login</button>
    </nav>

    <div class="burger" id="landingBurger">
      <span></span><span></span><span></span>
    </div>
  </header>

  <!-- HERO -->
  <section id="home" class="hero">
    <h1>Automatically Save Your Browser Sessions</h1>
    <p>
      Smart Browser State Manager runs in the background to capture your browser
      sessions, allowing you to restore your work anytime through a secure web dashboard.
    </p>
    <button id="heroGetStarted">Get Started</button>
  </section>

  <!-- FEATURES -->
  <section id="features" class="features">
    <h2>Key Features</h2>
    <div class="feature-grid">
      <div class="feature-card">
        <h3>Automatic Detection</h3>
        <p>Browser sessions are captured automatically without manual saving.</p>
      </div>
      <div class="feature-card">
        <h3>Secure Storage</h3>
        <p>All session data is securely stored and tied to your account.</p>
      </div>
      <div class="feature-card">
        <h3>One-Click Restore</h3>
        <p>Reopen your saved browser sessions whenever you need them.</p>
      </div>
      <div class="feature-card">
        <h3>Cross-Device Access</h3>
        <p>Manage your saved sessions from any device using the web app.</p>
      </div>
    </div>
  </section>

  <!-- HOW IT WORKS -->
  <section id="how-it-works" class="how">
    <h2>How It Works</h2>
    <div class="steps">
      <div class="step">
        <strong>1</strong>
        Install the browser extension
        <br><br>
        <a href="downloads/extension.zip" download class="download-icon-link">
          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
               viewBox="0 0 24 24" fill="none" stroke="currentColor"
               stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
          </svg>
        </a>
      </div>
      <div class="step">
        <strong>2</strong>
        Sessions are saved automatically in the background
      </div>
      <div class="step">
        <strong>3</strong>
        View and restore sessions through the web app
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-brand">
        <h3>Smart Browser State Manager</h3>
        <p>Automatically save and restore your browser sessions securely from anywhere.</p>
      </div>
      <div class="footer-links">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="#home">Home</a></li>
          <li><a href="#features">Features</a></li>
          <li><a href="#how-it-works">How It Works</a></li>
          <li><a href="login.php">Login</a></li>
        </ul>
      </div>
      <div class="footer-contact">
        <h4>Contact</h4>
        <p>Email: <a href="mailto:support@surtr.app">support@surtr.app</a></p>
        <p>&copy; 2026 Smart Browser State Manager</p>
      </div>
    </div>
  </footer>

  <script nonce="<?= $nonce ?>">
    // Login buttons
    document.getElementById('navLoginBtn').addEventListener('click', function () {
        window.location.href = 'login.php';
    });
    document.getElementById('heroGetStarted').addEventListener('click', function () {
        window.location.href = 'login.php';
    });

    // Burger menu
    document.getElementById('landingBurger').addEventListener('click', function () {
        document.getElementById('navLinks').classList.toggle('active');
    });

    // Smooth scroll nav buttons
    document.querySelectorAll('[data-target]').forEach(function (button) {
        button.addEventListener('click', function () {
            var section = document.getElementById(button.getAttribute('data-target'));
            if (section) {
                section.scrollIntoView({ behavior: 'smooth' });
                document.getElementById('navLinks').classList.remove('active');
            }
        });
    });
  </script>
</body>
</html>