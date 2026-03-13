// modal.js — event delegation throughout, no direct element lookup at boot time

(function () {

    /* ── Close helpers — exposed globally ─────────────────── */
    window.closeModal = function () {
        var el = document.getElementById('modalOverlay');
        if (el) el.classList.remove('active');
    };

    window.closeConfirmModal = function () {
        var el = document.getElementById('confirmModal');
        if (el) el.classList.remove('active');
    };

    window.closeDeleteModal = function () {
        var el = document.getElementById('deleteModal');
        if (el) el.classList.remove('active');
    };

    /* ── State ────────────────────────────────────────────── */
    var currentTabs      = [];
    var pendingRestoreId = null;

    /* ── openModal ────────────────────────────────────────── */
    window.openModal = function (stateId) {
        var modal   = document.getElementById('modalOverlay');
        var tabList = document.getElementById('modalTabList');
        var loading = document.getElementById('modalLoading');
        var restBtn = document.getElementById('restoreButton');
        if (!modal) return;

        pendingRestoreId  = null;
        currentTabs       = [];
        tabList.innerHTML = '';

        loading.style.display = 'block';
        if (restBtn) restBtn.disabled = true;
        modal.classList.add('active');

        fetch('ajax/get_state_details.php?id=' + encodeURIComponent(stateId))
            .then(function (res) { return res.json(); })
            .then(function (data) {
                loading.style.display = 'none';
                if (restBtn) restBtn.disabled = false;
                currentTabs = Array.isArray(data) ? data : [];

                if (!currentTabs.length) {
                    var li = document.createElement('li');
                    li.textContent   = 'No tabs found in this session.';
                    li.style.padding = '10px';
                    li.style.color   = '#666';
                    tabList.appendChild(li);
                    return;
                }

                currentTabs.forEach(function (tab) {
                    var li       = document.createElement('li');
                    var titleDiv = document.createElement('div');
                    titleDiv.style.cssText = 'overflow:hidden;text-overflow:ellipsis;white-space:nowrap;';

                    var icon = document.createElement('span');
                    icon.textContent = '📄 ';
                    titleDiv.appendChild(icon);

                    var strong = document.createElement('strong');
                    strong.textContent = tab.title || 'Untitled Tab';
                    titleDiv.appendChild(strong);

                    var urlDiv = document.createElement('div');
                    urlDiv.style.cssText = 'font-size:0.8rem;color:#888;margin-left:24px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;';
                    urlDiv.textContent   = tab.url || '#';

                    li.appendChild(titleDiv);
                    li.appendChild(urlDiv);
                    tabList.appendChild(li);
                });
            })
            .catch(function (err) {
                loading.style.display = 'none';
                var li = document.createElement('li');
                li.textContent = 'Error loading data.';
                li.style.color = 'red';
                tabList.appendChild(li);
                console.error(err);
            });
    };

    /* ── showDirectRestoreConfirm ─────────────────────────── */
    window.showDirectRestoreConfirm = function (stateId) {
        pendingRestoreId = stateId;
        currentTabs      = [];
        var el = document.getElementById('confirmModal');
        if (el) el.classList.add('active');
    };

    /* ── ALL button clicks via delegation on document ─────── */
    document.addEventListener('click', function (e) {
        var id = e.target && e.target.id;

        // Close / cancel buttons
        if (id === 'closeModalBtn')    { e.preventDefault(); closeModal();        return; }
        if (id === 'cancelRestoreBtn') { e.preventDefault(); closeConfirmModal(); return; }
        if (id === 'cancelDeleteBtn')  { e.preventDefault(); closeDeleteModal();  return; }

        // "Restore All Tabs" inside the preview modal
        if (id === 'restoreButton') {
            e.preventDefault();
            closeModal();
            var confirmModal = document.getElementById('confirmModal');
            if (confirmModal) confirmModal.classList.add('active');
            return;
        }

        // Confirm restore
        if (id === 'confirmRestoreBtn') {
            e.preventDefault();
            closeConfirmModal();

            if (pendingRestoreId && currentTabs.length === 0) {
                fetch('ajax/get_state_details.php?id=' + encodeURIComponent(pendingRestoreId))
                    .then(function (res) { return res.json(); })
                    .then(function (tabs) {
                        openTabsSequentially(Array.isArray(tabs) ? tabs : []);
                    })
                    .catch(function (err) {
                        console.error('Restore fetch failed:', err);
                        alert('Failed to load tabs. Please try again.');
                    });
            } else {
                openTabsSequentially(currentTabs);
            }
            return;
        }

        // Backdrop click — close whichever overlay was clicked directly
        var modalOverlay  = document.getElementById('modalOverlay');
        var confirmModal  = document.getElementById('confirmModal');
        var deleteModal   = document.getElementById('deleteModal');

        if (e.target === modalOverlay)  { closeModal();        return; }
        if (e.target === confirmModal)  { closeConfirmModal(); return; }
        if (e.target === deleteModal)   { closeDeleteModal();  return; }
    });

    /* ── openTabsSequentially ─────────────────────────────── */
    function openTabsSequentially(tabs) {
        var delay = 0;
        tabs.forEach(function (tab) {
            if (tab.url) {
                setTimeout(function () { window.open(tab.url, '_blank'); }, delay);
                delay += 200;
            }
        });
    }

})();