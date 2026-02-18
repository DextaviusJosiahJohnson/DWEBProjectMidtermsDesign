<head>
    <title>Smart Browser State Manager</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/pages/landing.css">  
</head>

<body>
  <!-- NAVIGATION BAR -->
 <header>
  <div class="logo">Smart Browser State Manager</div>
    <nav class="nav-links" id="navLinks">
        <button data-target="home">Home</button>
        <button data-target="features">Features</button>
        <button data-target="how-it-works">How It Works</button>
        <button><a href="login.php">Login</a></button>
        <button><a href="downloads/extension.zip" download>Download Extension Source</a></button>
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
     <button><a href="login.php">Get Started</a></button>
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


<script>
  function toggleMenu() {
    document.getElementById('navLinks').classList.toggle('active');
  }
  document.querySelectorAll('[data-target]').forEach(button => {
    button.addEventListener('click', () => {
      const sectionId = button.getAttribute('data-target');
      const section = document.getElementById(sectionId);

      section.scrollIntoView({
        behavior: 'smooth'
      });

      // Close burger menu after click (mobile UX)
      document.getElementById('navLinks').classList.remove('active');
    });
  });
</script>
