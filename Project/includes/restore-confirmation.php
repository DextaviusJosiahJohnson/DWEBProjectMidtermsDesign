<head>
    <link rel="stylesheet" href="css/pages/modal.css">
</head>

<!-- Restore Confirmation Modal -->
<div class="modal-overlay" id="confirmModal">
  <div class="modal small">
    <h2>Restore Browser State?  </h2>

    <p>
      This will restore all saved tabs from this browser state.
      Your current browser session may be affected.
    </p>

    <div class="modal-actions">
       <button class="secondary" onclick="closeConfirmModal()">Cancel</button>
      <button class="primary">Confirm Restore</button>
    </div>
  </div>
</div>

