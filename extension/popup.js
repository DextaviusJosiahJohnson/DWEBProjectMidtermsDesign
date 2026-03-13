document.addEventListener('DOMContentLoaded', () => {
    const saveBtn        = document.getElementById('saveBtn');
    const apiKeyInput    = document.getElementById('apiKey');
    const serverUrlInput = document.getElementById('serverUrl');
    const statusDiv      = document.getElementById('status');
    const themeToggle    = document.getElementById('themeToggle');
    const themeLabel     = document.getElementById('themeLabel');
    const intervalBtns   = document.querySelectorAll('.interval-btn');

    const THEME_LABELS = { division: 'Dark', minimal: 'Light' };

    /* ── Helpers ──────────────────────────────────────────── */
    function showStatus(msg, type) {
        statusDiv.textContent   = msg;
        statusDiv.className     = type;
        statusDiv.style.display = 'block';
        setTimeout(() => { statusDiv.style.display = 'none'; }, 3000);
    }

    function getBrowserName() {
        const agent = navigator.userAgent;
        if (agent.includes("Edg"))     return "Edge";
        if (agent.includes("Chrome"))  return "Chrome";
        if (agent.includes("Firefox")) return "Firefox";
        if (agent.includes("Safari"))  return "Safari";
        return "Other";
    }

    function getApiUrl(serverUrl) {
        return serverUrl.replace(/\/+$/, '') + '/api/save_state.php';
    }

    /* ── Theme ────────────────────────────────────────────── */
    function applyTheme(name) {
        document.documentElement.setAttribute('data-theme', name);
        themeLabel.textContent = THEME_LABELS[name] || 'Dark';
    }

    /* ── Load saved settings ──────────────────────────────── */
    chrome.storage.local.get(
        ['surtr_theme', 'surtr_api_key', 'surtr_interval', 'surtr_server_url'],
        (result) => {
            applyTheme(result.surtr_theme || 'minimal');
            if (result.surtr_api_key)    apiKeyInput.value    = result.surtr_api_key;
            if (result.surtr_server_url) serverUrlInput.value = result.surtr_server_url;
            const savedInterval = result.surtr_interval !== undefined
                ? result.surtr_interval : 0;
            setActiveInterval(savedInterval);
        }
    );

    /* ── Theme toggle ─────────────────────────────────────── */
    themeToggle.addEventListener('click', () => {
        const current = document.documentElement.getAttribute('data-theme') || 'minimal';
        const next    = current === 'division' ? 'minimal' : 'division';
        applyTheme(next);
        chrome.storage.local.set({ surtr_theme: next });
    });

    /* ── Server URL ───────────────────────────────────────── */
    serverUrlInput.addEventListener('blur', () => {
        const val = serverUrlInput.value.trim();
        if (val) chrome.storage.local.set({ surtr_server_url: val });
    });

    /* ── Interval pills ───────────────────────────────────── */
    function setActiveInterval(val) {
        intervalBtns.forEach(btn => {
            btn.classList.toggle('active', parseInt(btn.dataset.value) === val);
        });
    }

    intervalBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const val = parseInt(btn.dataset.value);
            chrome.storage.local.set({ surtr_interval: val });
            chrome.alarms.clear('surtr_auto_save');
            if (val > 0) {
                chrome.alarms.create('surtr_auto_save', { periodInMinutes: val });
            }
            setActiveInterval(val);
            showStatus(
                val === 0 ? 'Autosave turned off' : `Autosave set to ${btn.textContent}`,
                'info'
            );
        });
    });

    /* ── Bookmarks ────────────────────────────────────────── */
    async function getBookmarksData() {
        const tree      = await chrome.bookmarks.getTree();
        const bookmarks = [];
        function processNode(nodes) {
            for (const node of nodes) {
                if (node.url) bookmarks.push({ title: node.title, url: node.url });
                if (node.children) processNode(node.children);
            }
        }
        processNode(tree);
        return bookmarks;
    }

    /* ── History — NOW includes visited_at (lastVisitTime) ── */
    async function getHistoryData() {
        const oneDayAgo = Date.now() - (1000 * 60 * 60 * 24);
        const items     = await chrome.history.search({
            text: '', startTime: oneDayAgo, maxResults: 1000
        });
        return items.map(item => ({
            title:      item.title,
            url:        item.url,
            visited_at: item.lastVisitTime   // ← was missing, caused duplicates
        }));
    }

    /* ── Save ─────────────────────────────────────────────── */
    saveBtn.addEventListener('click', async () => {
        const apiKey    = apiKeyInput.value.trim();
        const serverUrl = serverUrlInput.value.trim();

        if (!serverUrl) { showStatus('Enter your Server URL first.', 'error'); return; }
        if (!apiKey)    { showStatus('Enter your API Key first.',    'error'); return; }

        const browserName = getBrowserName();
        chrome.storage.local.set({
            surtr_api_key:    apiKey,
            surtr_server_url: serverUrl,
            surtr_browser:    browserName
        });

        saveBtn.disabled  = true;
        saveBtn.innerHTML = `
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5"
                 stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="2"  x2="12" y2="6"/>
              <line x1="12" y1="18" x2="12" y2="22"/>
              <line x1="4.93" y1="4.93"   x2="7.76"  y2="7.76"/>
              <line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/>
              <line x1="2"  y1="12" x2="6"  y2="12"/>
              <line x1="18" y1="12" x2="22" y2="12"/>
              <line x1="4.93"  y1="19.07" x2="7.76"  y2="16.24"/>
              <line x1="16.24" y1="7.76"  x2="19.07" y2="4.93"/>
            </svg>
            Saving...`;

        try {
            const tabs         = await chrome.tabs.query({ currentWindow: true });
            const tabData      = tabs.map(t => ({ title: t.title, url: t.url }));
            const bookmarkData = await getBookmarksData();
            const historyData  = await getHistoryData();

            const response = await fetch(getApiUrl(serverUrl), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    apiKey,
                    device:    'Extension',
                    browser:   browserName,
                    save_type: 'Manual',
                    tabs:      tabData,
                    bookmarks: bookmarkData,
                    history:   historyData
                })
            });

            if (!response.ok) throw new Error('Server error: ' + response.status);
            const result = await response.json();

            if (result.success) showStatus('Session saved successfully.', 'success');
            else showStatus('Error: ' + (result.error || 'Unknown error'), 'error');

        } catch (err) {
            console.error(err);
            showStatus('Connection failed. Check your Server URL.', 'error');
        } finally {
            saveBtn.disabled  = false;
            saveBtn.innerHTML = `
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                  <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                  <polyline points="17 21 17 13 7 13 7 21"/>
                  <polyline points="7 3 7 8 15 8"/>
                </svg>
                Save Current Session`;
        }
    });
});