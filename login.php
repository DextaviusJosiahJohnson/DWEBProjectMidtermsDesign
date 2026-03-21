<?php
require 'includes/public_header.php';

$message = "";

// ── Rate Limiting ─────────────────────────────────────────
if (!isset($_SESSION['login_attempts']))     $_SESSION['login_attempts']     = 0;
if (!isset($_SESSION['login_lockout_time'])) $_SESSION['login_lockout_time'] = null;

$locked          = false;
$lockout_seconds = 900; // 15 minutes

if ($_SESSION['login_lockout_time']) {
    $elapsed = time() - $_SESSION['login_lockout_time'];
    if ($elapsed < $lockout_seconds) {
        $locked    = true;
        $remaining = ceil(($lockout_seconds - $elapsed) / 60);
        $message   = "Too many failed attempts. Try again in {$remaining} minute(s).";
    } else {
        $_SESSION['login_attempts']     = 0;
        $_SESSION['login_lockout_time'] = null;
    }
}

require_once 'database/db.php';

// ── REGISTER ──────────────────────────────────────────────
if (!$locked && isset($_POST['register_email'])) {
    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['register_email']);
    $password = $_POST['register_password'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        $message = "Email already exists.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt   = $pdo->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$fullname, $email, $hashed])) {
            $message = "Registration successful! Please login.";
        } else {
            $message = "Error registering user.";
        }
    }
}

// ── LOGIN ─────────────────────────────────────────────────
if (!$locked && isset($_POST['login_email'])) {
    $email    = trim($_POST['login_email']);
    $password = $_POST['login_password'];

    $stmt = $pdo->prepare("SELECT id, fullname, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['login_attempts']     = 0;
        $_SESSION['login_lockout_time'] = null;

        // Prevent session fixation
        session_regenerate_id(true);

        $_SESSION['user_id']  = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['email']    = $email;

        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['login_attempts']++;
        if ($_SESSION['login_attempts'] >= 5) {
            $_SESSION['login_lockout_time'] = time();
            $message = "Too many failed attempts. Account locked for 15 minutes.";
        } else {
            $remaining = 5 - $_SESSION['login_attempts'];
            $message   = "Invalid email or password. {$remaining} attempt(s) remaining.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Smart Browser State Manager</title>
  <script nonce="<?= $nonce ?>">document.documentElement.setAttribute("data-theme","minimal");</script>
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
      <p style="color:#d9534f;text-align:center;margin-bottom:15px;font-weight:500;">
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
        <input type="email" name="login_email" placeholder="you@example.com"
               required <?= $locked ? 'disabled' : '' ?> />
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="login_password" placeholder="••••••••"
               required <?= $locked ? 'disabled' : '' ?> />
      </div>
      <button class="btn" <?= $locked ? 'disabled' : '' ?>>Login</button>
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

    <div class="footer-text">© 2026 Smart Browser State Manager</div>
  </div>

  <script nonce="<?= $nonce ?>">
    const loginTab     = document.getElementById('loginTab');
    const registerTab  = document.getElementById('registerTab');
    const loginForm    = document.getElementById('loginForm');
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