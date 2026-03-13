<?php
require 'includes/auth.php';
require 'database/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bookmarks | Smart Browser State Manager</title>
  <script nonce="<?= $nonce ?>">(function(){var t=localStorage.getItem("surtr_theme")||"minimal";document.documentElement.setAttribute("data-theme",t);})();</script>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/pages/bookmarks.css">
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
      <div class="nav-item active">Bookmarks</div>
      <a class="nav-item" href="search-history.php">Search History</a>
      <a class="nav-item" href="settings.php">Settings</a>
      <a class="nav-item" href="logout.php">Logout</a>
    </aside>

    <main class="main">
      <div class="page-header">
        <div>
          <h1 class="page-title">Bookmarks</h1>
          <p class="page-subtitle">View and manage all your saved bookmarks here.</p>
        </div>
        <div class="state-count"><span>Total Bookmarks</span><strong id="total-count">0</strong></div>
      </div>

      <form class="filter-bar" id="filter-form">
        <div class="filter-left">
          <input type="text" id="bookmark-search" name="search"
                 placeholder="Search bookmarks..."/>
        </div>
        <button type="submit" class="search-btn">Search</button>
      </form>

      <div id="states-container"></div>
    </main>
  </div>

  <?php include 'includes/modal.php'; ?>
  <?php include 'includes/restore-confirmation.php'; ?>

  <script nonce="<?= $nonce ?>">
    const CSRF_TOKEN = '<?= $_SESSION['csrf_token'] ?>';
  </script>
  <script src="script/modal.js"></script>
  <script src="script/nav.js"></script>
  <script src="script/bookmarks.js"></script>
  <script src="script/theme-switcher.js"></script>
</body>
</html>