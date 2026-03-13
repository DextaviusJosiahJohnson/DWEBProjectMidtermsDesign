<?php
require 'includes/auth.php';
require 'database/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Search History | Smart Browser State Manager</title>
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
      <a class="nav-item" href="bookmarks.php">Bookmarks</a>
      <div class="nav-item active">Search History</div>
      <a class="nav-item" href="settings.php">Settings</a>
      <a class="nav-item" href="logout.php">Logout</a>
    </aside>

    <main class="main">
      <div class="page-header">
        <div>
          <h1 class="page-title">Search History</h1>
          <p class="page-subtitle">View all your search history here.</p>
        </div>
        <div class="state-count"><span>Total Results</span><strong id="total-count">0</strong></div>
      </div>

      <form class="filter-bar" id="filter-form">
        <div class="filter-left">
          <input type="text" id="history-search" name="search"
                 placeholder="Search by keywords..."/>
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
  <script src="script/search-history.js"></script>
  <script src="script/theme-switcher.js"></script>
</body>
</html>