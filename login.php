<?php
  require_once 'database/db.php';
    session_start();

    $conn = new mysqli("localhost", "root", "", "smart_browser_state");

    // REGISTER
    if (isset($_POST['register_email'])) {

        $fullname = $_POST['fullname'];
        $email = $_POST['register_email'];
        $password = password_hash($_POST['register_password'], PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sss", $fullname, $email, $password);

        if ($stmt->execute()) {
            $message = "Registration successful!";
        } else {
            $message = "Email already exists.";
        }
    }

    // LOGIN
    if (isset($_POST['login_email'])) {

        $email = $_POST['login_email'];
        $password = $_POST['login_password'];

        $stmt = $conn->prepare(
            "SELECT id, fullname, password FROM users WHERE email = ?"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {

            // Save user session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['email'] = $email;

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();

        } else {
            $message = "Invalid email or password.";
        }
    }
?>


<head>
  <title>Login</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/pages/login.css">
</head>

<body>
  <div class="auth-container">
    <div class="auth-header">
      <h1>Smart Browser State Manager</h1>
      <p>Access your saved browser sessions</p>
    </div>

    <?php if (isset($message)): ?>
        <p style="color:#444; text-align:center; margin-bottom:10px;">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <!-- Tabs -->
    <div class="tabs">
      <div class="tab active" id="loginTab">Login</div>
      <div class="tab" id="registerTab">Register</div>
    </div>

    <!-- Login Form -->
    <form id="loginForm" class="active" method="POST">
      <div class="form-group">
        <label>Email</label>
       <input type="email" name="login_email" placeholder="you@example.com" required />
      </div>

      <div class="form-group">
        <label>Password</label>
        <input type="password" name="login_password" placeholder="••••••••" required />
      </div>

      <button class="btn">Login</button>
    </form>

    <!-- Register Form -->
    <form id="registerForm" method="POST">
      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="fullname" placeholder="John Doe" required />
      </div>

      <div class="form-group">
        <label>Email</label>
       <input type="email" name="register_email" placeholder="you@example.com" required />
      </div>

      <div class="form-group">
        <label>Password</label>
        <input type="password" name="register_password" placeholder="Create a password" required />
      </div>

      <button class="btn">Create Account</button>
    </form>

    <div class="footer-text">
      © 2026 Smart Browser State Manager
    </div>
  </div>

  <script>
    const loginTab = document.getElementById('loginTab');
    const registerTab = document.getElementById('registerTab');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    loginTab.addEventListener('click', () => {
      loginTab.classList.add('active');
      registerTab.classList.remove('active');
      loginForm.classList.add('active');
      registerForm.classList.remove('active');
    });

    registerTab.addEventListener('click', () => {
      registerTab.classList.add('active');
      loginTab.classList.remove('active');
      registerForm.classList.add('active');
      loginForm.classList.remove('active');
    });
  </script>

</body>
</html>
