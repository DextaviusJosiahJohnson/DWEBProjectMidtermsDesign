<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset="UTF-8">
  <title>Smart Browser State Manager</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/pages/landing.css">  
</head>

<body>
  <!-- NAVIGATION BAR -->
 <header>
  <div class="logo">
  <img src="img/logo.png" alt="Logo" class="logo-img">
  <span>Smart Browser State Manager</span>
</div>

    <nav class="nav-links" id="navLinks">
        <button data-target="home">Home</button>
        <button data-target="features">Features</button>
        <button data-target="how-it-works">How It Works</button>
        <button><a href="login.php">Login</a></button>
    </nav>

  <div class="burger" onclick="toggleMenu()">
    <span></span>
    <span></span>
    <span></span>
  </div>
</header>

  <!-- HERO SECTION -->
  <section id="home" class="hero">
    <h1>Automatically Save Your Browser Sessions</h1>
    <p>
        Smart Browser State Manager runs in the background to capture your browser sessions,
        allowing you to restore your work anytime through a secure web dashboard.
    </p>
     <button onclick="window.location.href='login.php'">Get Started</button>
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

<!--Footer-->
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
      <p>Email: support@smartbrowser.com</p>
      <p>&copy; 2026 Smart Browser State Manager</p>
    </div>

  </div>
</footer>




<script>
  function toggleMenu() {//function to add a 'active' css in the id 'navLinks'. 
  // the onclick attribute on the div class will call this function, to activate the burger,
  //  to make the menu visible
    document.getElementById('navLinks').classList.toggle('active');
  }
  document.querySelectorAll('[data-target]').forEach(button => { 
    button.addEventListener('click', () => {
      const sectionId = button.getAttribute('data-target'); //when button is clicked.
      const section = document.getElementById(sectionId);//will get the section name via getting the ID

      section.scrollIntoView({ //fwill go to the section, after identifying rhe section,  for smooth scrolling
        behavior: 'smooth'
      });

      // Close burger menu after click (mobile UX)
      document.getElementById('navLinks').classList.remove('active');
    });
  });
</script>
