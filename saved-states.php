<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Saved Browser States</title>

  <!-- Shared Styles -->
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">

  <!-- Page Specific -->
  <link rel="stylesheet" href="css/pages/dashboard.css">
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
      <div class="nav-item" onclick="goTo('settings.php')">Settings</div>
      <div class="nav-item" onclick="goTo('landing.php')">Logout</div>⁡⁢⁣⁢<!--NOT SURE YET IF AADD-->⁡
    </aside>

    <!-- Main Content -->
    <main class="main">

      <h1 class="page-title">Saved Browser States</h1>

    ⁡⁢⁣⁣  <!--Make THis fucntional-->⁡
      <!-- Filter/Search -->
      <form class="filter-bar">
        <input type="text" placeholder="Search by device or date..." name="search"/>
        <span class="user">Total States: 12</span>
      </form> 

   ⁡⁣⁣⁡⁢⁣⁣  <!--Make THis fucntional-->⁡
      <!-- Saved State Item -->
      <div class="state-card">
        <div class="state-info">
          <h4>Jan 20, 2026 – Laptop</h4>
          <p>18 tabs • Chrome • Auto-saved</p>
        </div>

        <div class="state-actions">
        <a href="#" class="view-link" onclick="openModal(); return false;">View</a>

          <button class="danger">Delete</button>
          <button class="primary restore">Restore</button> 
        </div>
    </div>

     <div class="state-card">
        <div class="state-info">
          <h4>Jan 20, 2026 – Laptop</h4>
          <p>18 tabs • Chrome • Auto-saved</p>
        </div>

        <div class="state-actions">
         <a href="#" class="view-link" onclick="openModal(); return false;">View</a>


          <button class="danger">Delete</button>
          <button class="primary restore">Restore</button>
        </div>
    </div>


      <div class="state-card">
        <div class="state-info">
          <h4>Jan 20, 2026 – Laptop</h4>
          <p>18 tabs • Chrome • Auto-saved</p>
        </div>

        <div class="state-actions">
          <a href="#" class="view-link" onclick="openModal(); return false;">View</a>


          <button class="danger">Delete</button>
          <button class="primary restore">Restore</button>
        </div>
    </div>

    </main>
  </div>


  <?php include 'includes/modal.php'; ?>
  <?php include 'includes/restore-confirmation.php'; ?>
   <script src="script/modal.js"></script> 
   <script src="script/nav.js" ></script>

   
   
   
  

</body>
</html>
