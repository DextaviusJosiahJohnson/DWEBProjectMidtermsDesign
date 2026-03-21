<?php
require 'includes/auth.php';
require 'database/db.php';

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT email, api_key FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$email  = $user['email']   ?? 'Error loading email';
$apiKey = $user['api_key'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Settings | Smart Browser State Manager</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Albert+Sans:wght@300;400;500;600;700;800&family=Rajdhani:wght@400;500;600;700&family=Share+Tech+Mono&family=Exo+2:wght@300;400;500;600&display=swap">
  <script nonce="<?= $nonce ?>">(function(){var t=localStorage.getItem("surtr_theme")||"minimal";document.documentElement.setAttribute("data-theme",t);})();</script>
  <link rel="stylesheet" href="css/base.css" />
  <link rel="stylesheet" href="css/layout.css" />
  <link rel="stylesheet" href="css/pages/settings.css" />
</head>
<body>

  <div class="mobile-top">
    <div class="burger" id="burgerBtn">☰</div>
  </div>

  <div class="layout">
    <aside class="sidebar" id="sidebar">
      <div class="brand">Smart Browser State</div>
      <a class="nav-item" href="dashboard.php">Dashboard</a>
      <a class="nav-item" href="saved-states.php">Saved States</a>
      <a class="nav-item" href="bookmarks.php">Bookmarks</a>
      <a class="nav-item" href="search-history.php">Search History</a>
      <div class="nav-item active">Settings</div>
      <a class="nav-item" href="logout.php">Logout</a>
    </aside>

    <main class="main">
      <h1 class="page-title">Settings</h1>

      <!-- THEME SWITCHER -->
      <section class="settings-card theme-switcher-card" id="themeSwitcherCard">
        <h2 class="settings-card-heading">Appearance</h2>
        <p class="theme-switcher-subtitle">Choose how the dashboard looks. Hover to preview, click to apply.</p>
        <div class="theme-options">

          <button class="theme-option" data-theme-target="division"
                  id="themeOptDivision" aria-label="Switch to Division Dark theme">
            <span class="active-badge">✓</span>
            <div class="theme-preview">
              <div class="preview-sidebar">
                <div class="preview-nav-item active"></div>
                <div class="preview-nav-item"></div>
                <div class="preview-nav-item"></div>
                <div class="preview-nav-item"></div>
              </div>
              <div class="preview-main">
                <div class="preview-header"></div>
                <div class="preview-accent-line"></div>
                <div class="preview-card"></div>
                <div class="preview-card"></div>
              </div>
            </div>
            <div class="theme-label">
              <div>
                <div class="theme-label-name">Dark Mode</div>
                <div class="theme-label-tag">Orange · Black</div>
              </div>
              <img src="images/logo.png" alt="" class="theme-label-logo" aria-hidden="true">
            </div>
          </button>

          <button class="theme-option" data-theme-target="minimal"
                  id="themeOptMinimal" aria-label="Switch to Tactical Minimal theme">
            <span class="active-badge">✓</span>
            <div class="theme-preview">
              <div class="preview-sidebar">
                <div class="preview-nav-item active"></div>
                <div class="preview-nav-item"></div>
                <div class="preview-nav-item"></div>
                <div class="preview-nav-item"></div>
              </div>
              <div class="preview-main">
                <div class="preview-header"></div>
                <div class="preview-accent-line"></div>
                <div class="preview-card"></div>
                <div class="preview-card"></div>
              </div>
            </div>
            <div class="theme-label">
              <div>
                <div class="theme-label-name">Default Teal</div>
                <div class="theme-label-tag">Teal · Clean</div>
              </div>
              <img src="images/logo.png" alt="" class="theme-label-logo" aria-hidden="true">
            </div>
          </button>

        </div>
      </section>

      <!-- EXTENSION CONNECTION -->
      <section class="settings-card">
        <h2>Extension Connection</h2>
        <div class="setting-row setting-row--column">
          <label>Your API Key</label>
          <div style="display:flex;gap:10px;width:100%;">
            <input type="text" value="<?= htmlspecialchars($apiKey) ?>"
                   id="apiKeyField" readonly
                   placeholder="No API Key generated yet"
                   style="flex:1;font-family:monospace;">
            <button class="secondary" id="copyKeyBtn"
                    style="<?= empty($apiKey) ? 'display:none' : '' ?>">Copy</button>
            <button class="primary" id="generateKeyBtn"
                    style="<?= !empty($apiKey) ? 'display:none' : '' ?>">Generate Key</button>
          </div>
          <p class="setting-description">
            Paste this key into the browser extension to enable saving.
            <?php if (!empty($apiKey)): ?>
              <strong>Store it securely — it cannot be recovered after leaving this page.</strong>
            <?php endif; ?>
          </p>
        </div>
      </section>

      <!-- SECURITY -->
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

      <!-- ACCOUNT -->
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

  <script nonce="<?= $nonce ?>">
    const CSRF_TOKEN = '<?= $_SESSION['csrf_token'] ?>';
  </script>
  <script src="script/nav.js"></script>
  <script src="script/settings.js"></script>
  <script src="script/modal.js"></script>
  <script src="script/theme-switcher.js"></script>
</body>
</html>