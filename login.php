<?php
session_start();
require_once 'database/db.php'; // Uses your central PDO connection

$message = "";

// REGISTER
if (isset($_POST['register_email'])) {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['register_email']);
    $password = $_POST['register_password'];

    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        $message = "Email already exists.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
        
        if ($stmt->execute([$fullname, $email, $hashed])) {
            $message = "Registration successful! Please login.";
        } else {
            $message = "Error registering user.";
        }
    }
}

// LOGIN
if (isset($_POST['login_email'])) {
    $email = trim($_POST['login_email']);
    $password = $_POST['login_password'];

    $stmt = $pdo->prepare("SELECT id, fullname, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Save user session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['email'] = $email;

        header("Location: dashboard.php");
        exit();
    } else {
        $message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

    <?php if (!empty($message)): ?>
        <p style="color:#d9534f; text-align:center; margin-bottom:15px; font-weight:500;">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <div class="tabs">
      <div class="tab active" id="loginTab">Login</div>
      <div class="tab" id="registerTab">Register</div>
    </div>

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