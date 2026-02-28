<?php require 'database/db.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Saved States|Smart Browser State Manager</title>

  <!-- Shared Styles -->
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">  
  <link rel="stylesheet" href="css/pages/saved-states.css">
</head>
<body>

  <!-- Mobile Top Bar -->
  <div class="mobile-top">
    <div class="burger" onclick="toggleMenu()">☰</div>
  </div>

  <div class="layout">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <div class="brand">Smart Browser State</div>
      <div class="nav-item" onclick="goTo('dashboard.php')">Dashboard</div>
      <div class="nav-item active">Saved States</div>
      <div class="nav-item" onclick="goTo('bookmarks.php')">Bookmarks</div>
      <div class="nav-item "onclick="goTo('search-history.php')">Search History</div>
      <div class="nav-item" onclick="goTo('settings.php')">Settings</div>
      <div class="nav-item" onclick="goTo('landing.php')">Logout</div>
    </aside>

    <!-- Main Content -->
<main class="main">
  <div class="page-header">
    <div>
      <h1 class="page-title">Saved Data</h1>
      <p class="page-subtitle">
       Access and restore previously saved sessions.
      </p>
    </div>

    <div class="state-count"><span>Total States</span><strong id="total-count">0</strong></div>
  </div>

⁡⁢⁣⁣

  <!-- Filter Section -->
  <form class="filter-bar" id="filter-form">
  <div class="filter-left" id="filter-section">
  
    <!-- Device Dropdown -->
    <select name="device" id="device">
      <option value="">All Devices</option>
      <option>Laptop</option>
      <option>Desktop</option>
      <option>Work PC</option>
      <option>Mobile</option>
    </select>

    <!-- Browser Dropdown -->
    <select name="browser" id="browser">
      <option value="">All Browsers</option>
      <option>Chrome</option>
      <option>Firefox</option>
      <option>Edge</option>
      <option>Safari</option>
    </select>

    <!-- Date Picker -->
    <input type="date" name="date" id="date" placeholder="Select Date">
  </div>

  
</form>

  <!-- Saved States Container -->
   <div id="states-container" >
    <!-- Dynamic content loads here -->
  </div>
</main>

</div>


<?php include 'includes/modal.php'; ?>
<?php include 'includes/restore-confirmation.php'; ?>
<script src="script/modal.js"></script> 
<script src="script/nav.js" ></script>
<script src="script/saved-states.js" ></script>

</body>
</html>

