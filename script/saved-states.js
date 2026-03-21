// saved-states.js

let currentPage = 1;
const PAGE_LIMIT = 20;

/* ── Helpers ──────────────────────────────────────────────── */
function escHtml(str) {
    var d = document.createElement('div');
    d.textContent = String(str ?? '');
    return d.innerHTML;
}

function formatDate(dateStr) {
    if (!dateStr) return 'Unknown date';
    return new Date(dateStr).toLocaleDateString('en-US', {
        month: 'short', day: '2-digit', year: 'numeric'
    });
}

/* ── Card builder ─────────────────────────────────────────── */
function buildStateCard(state) {
    var saveType   = state.save_type || 'Manual';
    var badgeClass = saveType === 'Auto-saved' ? 'auto' : 'manual';
    var tabCount   = parseInt(state.tab_count) || 0;
    var id         = parseInt(state.id);
    var browser    = escHtml(state.browser || 'Unknown');
    var device     = (state.device || '').toLowerCase();
    var date       = escHtml(formatDate(state.created_at));

    var iconMap = { laptop: '💻', desktop: '🖥️', 'work pc': '🖥️', mobile: '📱' };
    var icon    = iconMap[device] || '💻';

    // Note: NO inline onclick anywhere — all wired via data attributes
    return `
        <div class="state-card">
            <div class="state-left">
                <div class="device-icon">${icon}</div>
                <div class="state-info">
                    <h4>${date}</h4>
                    <div class="meta">
                        <span>${tabCount} tab${tabCount !== 1 ? 's' : ''}</span>
                        <span>${browser}</span>
                        <span class="badge ${badgeClass}">${escHtml(saveType)}</span>
                    </div>
                </div>
            </div>
            <div class="state-actions">
                <a href="#" class="view-link" data-modal-id="${id}">View</a>
                <button class="danger"          data-id="${id}">Delete</button>
                <button class="primary restore" data-id="${id}">Restore</button>
            </div>
        </div>`;
}

/* ── Pagination ───────────────────────────────────────────── */
function renderPagination(page, totalPages, total) {
    var el = document.getElementById('pagination');
    if (!el) {
        el = document.createElement('div');
        el.id        = 'pagination';
        el.className = 'pagination';
        document.getElementById('states-container').after(el);
    }

    if (totalPages <= 1) { el.innerHTML = ''; return; }

    el.innerHTML = `
        <button class="page-btn" data-page="${page - 1}"
                ${page <= 1 ? 'disabled' : ''}>← Prev</button>
        <span class="page-info">Page ${page} of ${totalPages} &nbsp;·&nbsp; ${total} total</span>
        <button class="page-btn" data-page="${page + 1}"
                ${page >= totalPages ? 'disabled' : ''}>Next →</button>`;

    // Wire pagination buttons via data attribute (no onclick)
    el.querySelectorAll('.page-btn[data-page]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            loadStates(parseInt(btn.getAttribute('data-page')));
        });
    });
}

/* ── Load ─────────────────────────────────────────────────── */
function loadStates(page) {
    page        = page || 1;
    currentPage = page;

    var device  = document.getElementById('device')?.value  || '';
    var browser = document.getElementById('browser')?.value || '';
    var date    = document.getElementById('date')?.value    || '';

    var params = new URLSearchParams({ device, browser, date, page, limit: PAGE_LIMIT });

    fetch('ajax/fetch-states.php?' + params)
        .then(function (res) { return res.json(); })
        .then(function (response) {
            var container = document.getElementById('states-container');

            if (!response.data || response.data.length === 0) {
                container.innerHTML = '<div class="empty-state"><h3>No saved states found</h3></div>';
                updateCount(0);
                renderPagination(0, 0, 0);
                return;
            }

            container.innerHTML = response.data.map(buildStateCard).join('');
            updateCount(response.total);
            renderPagination(response.page, response.total_pages, response.total);

            attachViewListeners();
            attachDeleteListeners();
            attachRestoreListeners();
        })
        .catch(function (err) {
            console.error('Failed to load states:', err);
            document.getElementById('states-container').innerHTML =
                '<div class="empty-state"><h3>Failed to load states. Please refresh.</h3></div>';
        });
}

function updateCount(total) {
    var el = document.getElementById('total-count');
    if (el) el.textContent = total;
}

/* ── View ─────────────────────────────────────────────────── */
function attachViewListeners() {
    document.querySelectorAll('#states-container .view-link[data-modal-id]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            openModal(link.getAttribute('data-modal-id'));
        });
    });
}

/* ── Delete ───────────────────────────────────────────────── */
function attachDeleteListeners() {
    document.querySelectorAll('#states-container .danger[data-id]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.dataset.id;
            if (!confirm('Delete this saved state? This cannot be undone.')) return;

            fetch('ajax/delete_state.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': CSRF_TOKEN
                },
                body: JSON.stringify({ id: id })
            })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.success) {
                        loadStates(currentPage);
                    } else {
                        alert('Delete failed: ' + (data.error || 'Unknown error'));
                    }
                });
        });
    });
}

/* ── Restore — goes directly to confirm dialog ───────────── */
function attachRestoreListeners() {
    document.querySelectorAll('#states-container .restore[data-id]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            showDirectRestoreConfirm(btn.dataset.id);
        });
    });
}

/* ── Init ─────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
    loadStates(1);

    ['device', 'browser', 'date'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('change', function () { loadStates(1); });
    });
});