// dashboard.js
document.addEventListener('DOMContentLoaded', function () {

    // Wire up View buttons in the Recent States table
    document.querySelectorAll('.view-btn[data-state-id]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            openModal(parseInt(btn.getAttribute('data-state-id')));
        });
    });

});