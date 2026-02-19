<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Settings | Smart Browser State Manager</title>

  <!-- Shared Styles -->
  <link rel="stylesheet" href="css/base.css" />
  <link rel="stylesheet" href="css/layout.css" />
  <link rel="stylesheet" href="css/modal.css" />

  <!-- Page Specific -->
  <link rel="stylesheet" href="css/pages/settings.css" />
  <link rel="stylesheet" href="css/pages/dashboard.css">
  
</head>
<body>

<!-- Mobile Top Bar -->
  <div class="mobile-top">
    <div class="burger" onclick="toggleMenu()">☰</div>
  </div>

<!-- Sidebar -->
 <div class="layout">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <div class="brand">Smart Browser State</div>
      <div class="nav-item"  onclick="goTo('dashboard.php')" >Dashboard</div>
      <div class="nav-item" onclick="goTo('saved-states.php')">Saved States</div>
      <div class="nav-item active" >Settings</div>
      <div class="nav-item" onclick="goTo('landing.php')">Logout</div> 
    </aside>

<!-- Main Content -->
<main class="main">
  <h1 class="page-title">Settings</h1>

  <!-- Auto Save Settings -->
  <section class="settings-card">
    <h2>Auto Save</h2>


    <div class="setting-row">
      <label>Auto-save frequency</label>
      <select>
        <option>Every 15 minutes</option>
        <option>Every 30 minutes</option>
        <option selected>Every hour</option>
        <option>Manual only</option>
      </select>
    </div>
  </section>

  <section class="settings-card security-card">
  <h2>Security</h2>

  <div class="security-description">
    <p>Update your account password to keep your account secure.</p>
    <button class="secondary" id="togglePasswordBtn">
      Change Password
    </button>
  </div>

  <!-- Collapsible Container -->
  <div class="password-collapse" id="passwordCollapse">
    <form class="password-form" id="passwordForm" novalidate>
      
      <div class="form-group">
        <label for="currentPassword">Current password</label>
        <input type="password" id="currentPassword" required>
      </div>

      <div class="form-group">
        <label for="newPassword">New password</label>
        <input type="password" id="newPassword" required>
        
        <!-- Strength Indicator -->
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
        <button type="submit" class="primary">
          Update Password
        </button>
      </div>

    </form>
  </div>
</section>


  <section class="settings-card">
    <h2>Account</h2>

    <div class="setting-row ">
        <label>Email</label>
        <input type="email" value="user@email.com" disabled />
    </div>

     <div class="setting-row ">
      <button class="danger" id="delete">Delete Account</button>
  </div>
  </section>


  <!-- Save Button -->
  <div class="settings-actions">
    <button class="primary">Save Changes</button>
  </div>

</main>

</body>
</html>

<script src="script/modal.js"></script> 
<script src="script/nav.js" ></script>
<script src="script/settings.js" ></script>
<?php include 'includes/delete-confirmation.php'; ?>