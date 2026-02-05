<head>
    <link rel="stylesheet" href="css/pages/modal.css">
</head>

<!-- Restore Confirmation Modal -->
<div class="modal-overlay" id="deleteModal">
  <div class="modal small">
    <h2>Delete Account?</h2>

    <p>
       This action is permanent. Your account and all saved data will be deleted.
      Are you sure you want to continue?
    </p>

    <div class="modal-actions">
        <button class="secondary" onclick="closeDeleteModal()">Cancel</button>
      <button class="primary">Delete Account</button>
    </div>
  </div>
</div>

