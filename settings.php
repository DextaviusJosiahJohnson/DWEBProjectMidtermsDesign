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
        <option>Manual only</option>//make this responsive on mobile
      </select>
    </div>
  </section>

  <section class="settings-card">
    <h2>Security</h2>

    <div class="setting-row setting-row--column">
      <label>Change Password</label>

      <div class="password-fields">
        <input type="password" placeholder="Current password">
        <input type="password" placeholder="New password">
        <input type="password" placeholder="Confirm new password">
        <button class="primary">Update Password</button> //make this functional
      </div>
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

  <?php include 'includes/delete-confirmation.php'; ?>
  </section>


  <!-- Save Button -->
  <div class="settings-actions">
    <button class="primary">Save Changes</button>
  </div>

  
</main>

<script src="script/nav.js" ></script>

</body>
</html>

<script src="script/modal.js"></script> 




