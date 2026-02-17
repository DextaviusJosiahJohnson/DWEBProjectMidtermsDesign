document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById('modalOverlay');      // Details modal
    const confirmModal = document.getElementById('confirmModal'); // Confirmation modal
    const restoreBtn = document.getElementById('restoreButton'); // Restore button inside details modal
    const deleteModal = document.getElementById('deleteModal');  
    const deleteBtn = document.getElementById('delete'); // Delete Account button
   
   
/**------------------------------------------------------------------------
 *                          Detail Model
 *------------------------------------------------------------------------**/
    // Open/close details modal
    window.openModal = function() {
        modal.classList.add('active');
    }
    window.closeModal = function() {
        modal.classList.remove('active');
    }

    // When clicking restore in details modal
    if (restoreBtn ) {
        restoreBtn.addEventListener('click', () => {
            closeModal();       // close details modal
            openConfirmModal(); // open confirmation modal
        });
    }

/**------------------------------------------------------------------------
 *                        Restore Confirmation Modal
 *------------------------------------------------------------------------**/

    // Open/close confirmation modal
    window.openConfirmModal = function() {
        confirmModal.classList.add('active');
    }
    window.closeConfirmModal = function() {
        confirmModal.classList.remove('active');
    }

    //  When clicking restore in saved-states.php
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('restore')) {
            openConfirmModal();
        }
    });

/**------------------------------------------------------------------------
 *                           Delete Confirmation Modal
 *------------------------------------------------------------------------**/
    // Open/close delete modal
    window.openDeleteModal = function() {
       deleteModal.classList.add('active');
    }
    window.closeDeleteModal = function() {
       deleteModal.classList.remove('active');
    }

    // Attach click listener
    if (deleteBtn) {
        deleteBtn.addEventListener('click', openDeleteModal);
    }




/**------------------------------------------------------------------------
 *                           ALL MODALS
 *------------------------------------------------------------------------**/
    // Click outside to close modals
    [modal, confirmModal, deleteModal].forEach(m => {
        if (m) {
            m.addEventListener('click', e => {
                if (e.target === m) m.classList.remove('active');
            });
        }
    });



});
