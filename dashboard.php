<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">  
<title>Dashboard </title>
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/pages/dashboard.css">
</head>
<body>


<!--=============================================
            NAVIGATION (SIDEBAR)
=================================================-->

  <!-- Mobile Top Bar -->
  <div class="mobile-top">
    <div class="burger" onclick="toggleMenu()">☰</div>
  </div>

  <div class="layout"> <!--Page container --->

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <div class="brand">Smart Browser State</div>
      <div class="nav-item active" >Dashboard</div>
      <div class="nav-item" onclick="goTo('saved-states.php')">Saved States</div>
      <div class="nav-item" onclick="goTo('settings.php')">Settings</div>
      <div class="nav-item" onclick="goTo('landing.php')">Logout</div>⁡⁢⁣⁢<!--NOT SURE YET IF AADD-->⁡
    </aside>

 ⁡⁣⁣

 <!--===============================================
                    Page Content
 ===============================================-->
  ⁡⁣⁣⁢  <!-- Main Content -->⁡
    <main class="main">
      <div class="header">
        <h1>Dashboard</h1>
        <div class="user">Welcome, User</div>
      </div>

    ⁡⁣⁣⁢<!--Status Bar-->⁡  
    <section class="status-bar">
        <div class="status-item">
          <span class="label">Autosave Status</span>
          <span class="value active">
            <span class="dot"></span> Active
          </span>
        </div>

⁡⁢⁣     ⁣<!-- 𝘌𝘋𝘐𝘛: 𝘔𝘢𝘬𝘦 𝘵𝘩𝘪𝘴 𝘳𝘦𝘴𝘱𝘰𝘯𝘴𝘪𝘷𝘦, 𝘓𝘪𝘬𝘦 𝘪𝘵 𝘸𝘪𝘭𝘭 𝘥𝘪𝘴𝘱𝘭𝘢𝘺 𝘨𝘳𝘦𝘦𝘯:=𝘰𝘯, 𝘨𝘳𝘢𝘺=𝘰𝘧𝘧 -->⁡
        <div class="status-item">
          <span class="label">Last Auto Save</span>
          <span class="value">Jan 20, 2026 – 7:15 AM</span>
        </div>

    </section>

       ⁣<!-- 𝘌𝘋𝘐𝘛: 𝘔𝘢𝘬𝘦 𝘵𝘩𝘪𝘴 𝘳𝘦𝘴𝘱𝘰𝘯𝘴𝘪𝘷𝘦, 𝘓𝘪𝘬𝘦 𝘪𝘵 𝘸𝘪𝘭𝘭 𝘥𝘪𝘴𝘱𝘭𝘢𝘺 total saves -->⁡
      <section class="cards">
        <div class="card">
          <h2>12</h2>
          <p>Total Saved Sessions</p>
        </div>
        <div class="card">
          <h2>3</h2>
          <p>Active Devices</p>
        </div>
        <div class="card">
          <h2>5</h2>
          <p>Restores This Month</p>
        </div>
      </section> 

       ⁣<!-- 𝘌𝘋𝘐𝘛: 𝘔𝘢𝘬𝘦 𝘵𝘩𝘪𝘴 𝘳𝘦𝘴𝘱𝘰𝘯𝘴𝘪𝘷𝘦, 𝘓𝘪𝘬𝘦 𝘪𝘵 𝘸𝘪𝘭𝘭 𝘥𝘪𝘴𝘱𝘭𝘢𝘺 recent saves-->⁡
      <section class="table-container">
        <h3>Recent Browser States</h3>
        <table>
          <thead>
            <tr>
              <th>Date</th>
              <th>Device</th>
              <th>Tabs</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Jan 20, 2026</td>
              <td>Laptop</td>
              <td>18</td>
              <td> <a href="#" class="view-btn" onclick="openModal(); return false;">View</a></td>
            </tr>
            <tr>
              <td>Jan 18, 2026</td>
              <td>Desktop</td>
              <td>25</td>
              <td> <a href="#" class="view-btn" onclick="openModal(); return false;">View</a></td>
            </tr>
            <tr>
              <td>Jan 15, 2026</td>
              <td>Tablet</td>
              <td>9</td>
              <td> <a href="#" class="view-btn" onclick="openModal(); return false;">View</a></td>
            </tr>
          </tbody>
        </table>
      </section>
    </main>
  </div>

   <?php include 'includes/modal.php'; ?> <!--will appear if "view" button clicked. -->
   <?php include 'includes/restore-confirmation.php'; ?> <!--Confirmation messg about restoring tab after the restore button in modal.php was clicked -->
   <script src="script/modal.js"></script> <!--modal behavior --->
   <script src="script/nav.js" ></script> <!-- to make the navigation work --->

</body>
</html>

