<!--this file displays the detailed saved information (such as time,date, the saved tabs list)-->
<!--will connect to the backend-->

<head>
    <link rel="stylesheet" href="css/pages/modal.css">
</head>

<!-- Modal -->
  <div class="modal-overlay" id="modalOverlay">
    <div class="modal">
      <h2>Browser State Details</h2>

      <p><strong>Date:</strong> Jan 25, 2026</p>
      <p><strong>Browser:</strong> Chrome</p>

      <!--MAKE THIS FUNCTIONAL-->
      <h3>Saved Tabs</h3>
      <ul class="tab-list">
        <li>
          <span class="tab-title">ChatGPT</span>
          <span class="tab-url">https://chat.openai.com</span>
        </li>
        <li>
          <span class="tab-title">YouTube</span>
          <span class="tab-url">https://youtube.com</span>
        </li>
        <li>
          <span class="tab-title">YouTube</span>
          <span class="tab-url">https://youtube.com</span>
        </li>

         <li>
          <span class="tab-title">YouTube</span>
          <span class="tab-url">https://youtube.com</span>
        </li>
        <li>
          <span class="tab-title">YouTube</span>
          <span class="tab-url">https://youtube.com</span>
        </li>
      </ul>

      <div class="modal-actions">
         <button  class="primary" id="restoreButton">Restore</button>
        <button class="secondary" onclick="closeModal()">Close</button>
      </div>
    </div>
  </div>

