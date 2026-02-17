<?php
session_start();
require 'database/db.php';

// 1. Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['fullname'];

// 2. Fetch Stats
// A. Total Saved Sessions
$stmt = $pdo->prepare("SELECT COUNT(*) FROM browser_states WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_sessions = $stmt->fetchColumn();

// B. Active Devices (Unique device names)
$stmt = $pdo->prepare("SELECT COUNT(DISTINCT device) FROM browser_states WHERE user_id = ?");
$stmt->execute([$user_id]);
$active_devices = $stmt->fetchColumn();

// C. Recent States (Get top 3)
$stmt = $pdo->prepare("SELECT * FROM browser_states WHERE user_id = ? ORDER BY created_at DESC LIMIT 3");
$stmt->execute([$user_id]);
$recent_states = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">  
  <title>Dashboard | Smart Browser State Manager</title>
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/pages/dashboard.css">
  <link rel="stylesheet" href="css/pages/modal.css"> </head>
<body>

  <div class="mobile-top">
    <div class="burger" onclick="toggleMenu()">☰</div>
  </div>

  <div class="layout"> 
    <aside class="sidebar" id="sidebar">
      <div class="brand">Smart Browser State</div>
      <div class="nav-item active">Dashboard</div>
      <div class="nav-item" onclick="goTo('saved-states.php')">Saved States</div>
      <div class="nav-item" onclick="goTo('settings.php')">Settings</div>
      <div class="nav-item" onclick="window.location.href='logout.php'">Logout</div>
    </aside>

    <main class="main">
      <div class="header">
        <h1>Dashboard</h1>
        <div class="user">Welcome, <?= htmlspecialchars($user_name) ?></div>
      </div>

      <section class="status-bar">
        <div class="status-item">
          <span class="label">API Connection</span>
          <span class="value active">
            <span class="dot"></span> Active
          </span>
        </div>
        <div class="status-item">
          <span class="label">Your API Key</span>
          <span class="value" style="font-size: 0.8rem; font-family: monospace;">
             (To access API Key, Check Settings)
          </span>
        </div>
      </section>

      <section class="cards">
        <div class="card">
          <h2><?= $total_sessions ?></h2>
          <p>Total Saved Sessions</p>
        </div>
        <div class="card">
          <h2><?= $active_devices ?></h2>
          <p>Active Devices</p>
        </div>
        <div class="card">
          <h2>—</h2>
          <p>Auto-Saves</p>
        </div>
      </section> 

      <section class="table-container">
        <h3>Recent Browser States</h3>
        <?php if (count($recent_states) > 0): ?>
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
            <?php foreach ($recent_states as $state): 
                $tabs = json_decode($state['tab_data'] ?? '[]', true);
                $count = is_array($tabs) ? count($tabs) : 0;
            ?>
            <tr>
              <td><?= date("M d, Y", strtotime($state['created_at'])) ?></td>
              <td><?= htmlspecialchars($state['device']) ?></td>
              <td><?= $count ?></td>
              <td>
                <a href="#" class="view-btn" onclick="openModal(<?= $state['id'] ?>); return false;">View</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php else: ?>
            <p style="padding:1rem; color:#666;">No sessions saved yet.</p>
        <?php endif; ?>
      </section>
    </main>
  </div>

   <?php include 'includes/modal.php'; ?>
   <?php include 'includes/restore-confirmation.php'; ?>
   
   <script>
       // This allows our modal.js to talk to the backend
       const API_BASE_URL = 'ajax/';
   </script>

   <script src="script/modal.js"></script>
   <script src="script/nav.js" ></script>

</body>
</html>