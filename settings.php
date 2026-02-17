<?php
session_start();
require 'database/db.php';

// 1. Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Fetch User Data
$stmt = $pdo->prepare("SELECT email, api_key FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$email = $user['email'] ?? 'Error loading email';
// Use empty string if null so we can check it easily in HTML
$apiKey = $user['api_key'] ?? ''; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Settings | Smart Browser State Manager</title>

  <link rel="stylesheet" href="css/base.css" />
  <link rel="stylesheet" href="css/layout.css" />
  <link rel="stylesheet" href="css/pages/modal.css" />

  <link rel="stylesheet" href="css/pages/settings.css" />
  <link rel="stylesheet" href="css/pages/dashboard.css">
</head>
<body>

  <div class="mobile-top">
    <div class="burger" onclick="toggleMenu()">☰</div>
  </div>

  <div class="layout">
    <aside class="sidebar" id="sidebar">
      <div class="brand">Smart Browser State</div>
      <div class="nav-item" onclick="goTo('dashboard.php')">Dashboard</div>
      <div class="nav-item" onclick="goTo('saved-states.php')">Saved States</div>
      <div class="nav-item active">Settings</div>
      <div class="nav-item" onclick="window.location.href='logout.php'">Logout</div> 
    </aside>

    <main class="main">
      <h1 class="page-title">Settings</h1>

      <section class="settings-card">
        <h2>Extension Connection</h2>
        <div class="setting-row setting-row--column">
          <label>Your API Key</label>
          <div style="display:flex; gap:10px; width:100%;">
            <input type="text" value="<?= htmlspecialchars($apiKey) ?>" id="apiKeyField" readonly 
                   placeholder="No API Key generated yet" 
                   style="background:#f3f4f6; color:#555; flex:1; font-family:monospace;">
            
            <button class="secondary" id="copyKeyBtn" style="<?= empty($apiKey) ? 'display:none' : '' ?>">Copy</button>
            
            <button class="primary" id="generateKeyBtn" style="<?= !empty($apiKey) ? 'display:none' : '' ?>">Generate Key</button>
          </div>
          <p class="setting-description">Paste this key into the browser extension to enable saving.</p>
        </div>
      </section>

      <section class="settings-card security-card">
        <h2>Security</h2>
        <div class="security-description">
          <p>Update your account password to keep your account secure.</p>
          <button class="secondary" id="togglePasswordBtn">Change Password</button>
        </div>

        <div class="password-collapse" id="passwordCollapse">
          <form class="password-form" id="passwordForm" novalidate>
            <div class="form-group">
              <label for="currentPassword">Current password</label>
              <input type="password" id="currentPassword" required>
            </div>
            <div class="form-group">
              <label for="newPassword">New password</label>
              <input type="password" id="newPassword" required>
              <div class="strength-wrapper">
                <div class="strength-bar" id="strengthBar"></div>
              </div>
              <small id="strengthText"></small>
            </div>
            <div class="form-group">
              <label for="confirmPassword">Confirm new password</label>
              <input type="password" id="confirmPassword" required>
              <small class="error-message" id="matchError"></small>
            </div>
            <div class="form-actions">
              <button type="submit" class="primary">Update Password</button>
            </div>
          </form>
        </div>
      </section>

      <section class="settings-card">
        <h2>Account</h2>
        <div class="setting-row">
            <label>Email</label>
            <input type="email" value="<?= htmlspecialchars($email) ?>" disabled />
        </div>
        <div class="setting-row">
            <button class="danger" id="delete">Delete Account</button>
        </div>
      </section>
    </main>
  </div>

  <?php include 'includes/delete-confirmation.php'; ?>
  
  <script src="script/nav.js"></script>
  <script src="script/settings.js"></script>
  <script src="script/modal.js"></script> 

</body>
</html>