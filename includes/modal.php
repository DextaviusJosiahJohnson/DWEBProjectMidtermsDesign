<!--this file displays the detailed saved information (such as time,date, the saved tabs list)-->
<!--will connect to the backend-->

<head>
    <link rel="stylesheet" href="css/pages/modal.css">
</head>

<!-- Modal -->
<div class="modal-overlay" id="modalOverlay">
  <div class="modal">
    <h2 id="modalTitle">Browser State Details</h2>

    <div id="modalLoading" style="display:none; text-align:center; color:#666;">Loading tabs...</div>
    
    <ul class="tab-list" id="modalTabList">
        </ul>

    <div class="modal-actions">
       <button class="primary" id="restoreButton">Restore All Tabs</button>
       <button class="secondary" onclick="closeModal()">Close</button>
    </div>
  </div>
</div>
  
