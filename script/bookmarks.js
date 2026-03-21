// bookmarks.js

let currentPage = 1;
const PAGE_LIMIT = 20;

/* ── Helpers ──────────────────────────────────────────────── */
function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = String(str ?? '');
    return d.innerHTML;
}

function formatDate(dateStr) {
    if (!dateStr) return 'Unknown date';
    return new Date(dateStr).toLocaleDateString('en-US', {
        month: 'short', day: '2-digit', year: 'numeric'
    });
}

// Prevent javascript: or data: URIs from being used as bookmark links
function safeUrl(url) {
    try {
        const u = new URL(url);
        if (!['http:', 'https:'].includes(u.protocol)) return '#';
        return u.href;
    } catch {
        return '#';
    }
}

/* ── Card builder ─────────────────────────────────────────── */
function buildBookmarkCard(bookmark) {
    const id    = parseInt(bookmark.id);
    const title = escHtml(bookmark.title || 'Untitled');
    const url   = escHtml(bookmark.url   || '');
    const href  = safeUrl(bookmark.url   || '');
    const date  = escHtml(formatDate(bookmark.created_at));

    return `
        <div class="state-card">
            <div class="state-left">
                <div class="device-icon">🔖</div>
                <div class="state-info">
                    <h4>${title}</h4>
                    <div class="meta">
                        <span>${url}</span>
                    </div>
                    <div class="meta">
                        <span>${date}</span>
                    </div>
                </div>
            </div>
            <div class="state-actions">
                <a href="${href}" target="_blank" rel="noopener noreferrer"
                   class="view-link">Visit</a>
                <button class="danger" data-id="${id}">Delete</button>
            </div>
        </div>`;
}

/* ── Pagination renderer ──────────────────────────────────── */
function renderPagination(page, totalPages, total) {
    let el = document.getElementById('pagination');
    if (!el) {
        el = document.createElement('div');
        el.id        = 'pagination';
        el.className = 'pagination';
        document.getElementById('states-container').after(el);
    }

    if (totalPages <= 1) { el.innerHTML = ''; return; }

    el.innerHTML = `
        <button class="page-btn"
                onclick="loadBookmarks(${page - 1})"
                ${page <= 1 ? 'disabled' : ''}>← Prev</button>
        <span class="page-info">Page ${page} of ${totalPages} &nbsp;·&nbsp; ${total} total</span>
        <button class="page-btn"
                onclick="loadBookmarks(${page + 1})"
                ${page >= totalPages ? 'disabled' : ''}>Next →</button>`;
}

/* ── Load ─────────────────────────────────────────────────── */
function loadBookmarks(page = 1) {
    currentPage = page;
    const search = document.getElementById('bookmark-search')?.value || '';
    const params = new URLSearchParams({ search, page, limit: PAGE_LIMIT });

    fetch(`ajax/fetch-bookmarks.php?${params}`)
        .then(res => res.json())
        .then(response => {
            const container = document.getElementById('states-container');

            if (!response.data || response.data.length === 0) {
                container.innerHTML = '<div class="empty-state"><h3>No bookmarks found</h3></div>';
                updateCount(0);
                renderPagination(0, 0, 0);
                return;
            }

            container.innerHTML = response.data.map(buildBookmarkCard).join('');
            updateCount(response.total);
            renderPagination(response.page, response.total_pages, response.total);
            attachDeleteListeners();
        })
        .catch(err => {
            console.error('Failed to load bookmarks:', err);
            document.getElementById('states-container').innerHTML =
                '<div class="empty-state"><h3>Failed to load bookmarks. Please refresh.</h3></div>';
        });
}

function updateCount(total) {
    const el = document.getElementById('total-count');
    if (el) el.textContent = total;
}

/* ── Delete ───────────────────────────────────────────────── */
function attachDeleteListeners() {
    document.querySelectorAll('#states-container .danger[data-id]').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            if (!confirm('Delete this bookmark?')) return;

            fetch('ajax/delete_bookmark.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': CSRF_TOKEN
                },
                body: JSON.stringify({ id })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        loadBookmarks(currentPage);
                    } else {
                        alert('Delete failed: ' + (data.error || 'Unknown error'));
                    }
                });
        });
    });
}

/* ── Init ─────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    loadBookmarks(1);

    document.getElementById('filter-form')?.addEventListener('submit', e => {
        e.preventDefault();
        loadBookmarks(1);
    });
});