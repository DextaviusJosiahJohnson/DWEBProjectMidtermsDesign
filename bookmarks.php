<?php require 'database/db.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bookmarks|Smart Browser State Manager</title>

  <!-- Shared Styles -->
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">

  <!-- Page Specific -->
  <link rel="stylesheet" href="css/pages/bookmarks.css">

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
      <div class="nav-item "onclick="goTo('saved-states.php')">Saved States</div>
      <div class="nav-item active">Bookmarks</div>
      <div class="nav-item "onclick="goTo('search-history.php')">Search History</div>
      <div class="nav-item" onclick="goTo('settings.php')">Settings</div>
      <div class="nav-item" onclick="goTo('landing.php')">Logout</div>
    </aside>

    <!-- Main Content -->
<main class="main">
  
  <div class="page-header">
    <div>
      <h1 class="page-title">Bookmarks</h1>
      <p class="page-subtitle">
          View and manage all your saved bookmarks here.
      </p>
    </div>

    <div class="state-count"><span>Total States</span><strong id="total-count">0</strong></div>
  </div>

⁡⁢⁣⁣
<!-- Filter Section -->
<form class="filter-bar" id="filter-form" onsubmit="return false;">
  <div class="filter-left">
    <input type="text" id="bookmark-search" name="search" placeholder="Search bookmarks..."/> 
  </div>
  <button type="submit" class="search-btn">Search</button>
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
<script src="script/bookmarks.js" ></script>

</body>
</html>

