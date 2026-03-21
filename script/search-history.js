// search-history.js

let currentPage = 1;
const PAGE_LIMIT = 20;

/* ── Helpers ──────────────────────────────────────────────── */
function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = String(str ?? '');
    return d.innerHTML;
}

function formatDateTime(dateStr) {
    if (!dateStr) return 'Unknown date';
    return new Date(dateStr).toLocaleString('en-US', {
        month: 'short', day: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
}

/* ── Card builder ─────────────────────────────────────────── */
function buildHistoryCard(item) {
    const id     = parseInt(item.id);
    const query  = escHtml(item.search_query   || 'Unknown query');
    const engine = escHtml(item.search_engine  || 'Unknown Engine');
    const browser= escHtml(item.browser        || 'Unknown Browser');
    const date   = escHtml(formatDateTime(item.created_at));

    return `
        <div class="state-card">
            <div class="state-left">
                <div class="device-icon">🔍</div>
                <div class="state-info">
                    <h4>${query}</h4>
                    <div class="meta">
                        <span class="engine-tag">${engine}</span>
                        <span>${browser}</span>
                    </div>
                    <div class="meta">
                        <span>${date}</span>
                    </div>
                </div>
            </div>
            <div class="state-actions">
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
                onclick="loadHistory(${page - 1})"
                ${page <= 1 ? 'disabled' : ''}>← Prev</button>
        <span class="page-info">Page ${page} of ${totalPages} &nbsp;·&nbsp; ${total} total</span>
        <button class="page-btn"
                onclick="loadHistory(${page + 1})"
                ${page >= totalPages ? 'disabled' : ''}>Next →</button>`;
}

/* ── Load ─────────────────────────────────────────────────── */
function loadHistory(page = 1) {
    currentPage = page;
    const search = document.getElementById('history-search')?.value || '';
    const params = new URLSearchParams({ search, page, limit: PAGE_LIMIT });

    fetch(`ajax/fetch-search.php?${params}`)
        .then(res => res.json())
        .then(response => {
            const container = document.getElementById('states-container');

            if (!response.data || response.data.length === 0) {
                container.innerHTML = '<div class="empty-state"><h3>No search history found</h3></div>';
                updateCount(0);
                renderPagination(0, 0, 0);
                return;
            }

            container.innerHTML = response.data.map(buildHistoryCard).join('');
            updateCount(response.total);
            renderPagination(response.page, response.total_pages, response.total);
            attachDeleteListeners();
        })
        .catch(err => {
            console.error('Failed to load history:', err);
            document.getElementById('states-container').innerHTML =
                '<div class="empty-state"><h3>Failed to load history. Please refresh.</h3></div>';
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
            if (!confirm('Delete this search history entry?')) return;

            fetch('ajax/delete_search.php', {
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
                        loadHistory(currentPage);
                    } else {
                        alert('Delete failed: ' + (data.error || 'Unknown error'));
                    }
                });
        });
    });
}

/* ── Init ─────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    loadHistory(1);

    document.getElementById('filter-form')?.addEventListener('submit', e => {
        e.preventDefault();
        loadHistory(1);
    });
});