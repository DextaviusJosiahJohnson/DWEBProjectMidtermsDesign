const AUTO_SAVE_ALARM = 'surtr_auto_save';

/* ── Alarm initialisation ─────────────────────────────────── */
chrome.runtime.onInstalled.addListener(() => { initializeAlarm(); });
chrome.runtime.onStartup.addListener(()   => { initializeAlarm(); });

function initializeAlarm() {
    chrome.storage.local.get(['surtr_interval'], (result) => {
        const interval = result.surtr_interval !== undefined
            ? result.surtr_interval : 15;
        chrome.alarms.clear(AUTO_SAVE_ALARM);
        if (interval > 0) {
            chrome.alarms.create(AUTO_SAVE_ALARM, { periodInMinutes: interval });
        }
    });
}

chrome.alarms.onAlarm.addListener((alarm) => {
    if (alarm.name === AUTO_SAVE_ALARM) performAutoSave();
});

/* ── Helpers ──────────────────────────────────────────────── */

// browser name is detected in popup.js (which has navigator access)
// and stored under surtr_browser — we just read it here
function getStoredBrowserName(callback) {
    chrome.storage.local.get(['surtr_browser'], (result) => {
        callback(result.surtr_browser || 'Unknown');
    });
}

function getApiUrl(serverUrl) {
    return serverUrl.replace(/\/+$/, '') + '/api/save_state.php';
}

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

async function getHistoryData() {
    const stored    = await chrome.storage.local.get(['surtr_last_history_sync']);
    const startTime = stored.surtr_last_history_sync
        ? stored.surtr_last_history_sync
        : Date.now() - (1000 * 60 * 60 * 24);

    const items = await chrome.history.search({
        text: '', startTime, maxResults: 1000
    });
    return items.map(item => ({
        title:      item.title,
        url:        item.url,
        visited_at: item.lastVisitTime
    }));
}

/* ── Auto-save ────────────────────────────────────────────── */
async function performAutoSave() {
    const stored = await chrome.storage.local.get([
        'surtr_api_key',
        'surtr_server_url'
    ]);

    const apiKey    = stored.surtr_api_key;
    const serverUrl = stored.surtr_server_url;

    // Abort silently if either required value is missing
    if (!apiKey || !serverUrl) return;

    const apiUrl = getApiUrl(serverUrl);

    // Read browser name from storage (set by popup.js)
    getStoredBrowserName(async (browserName) => {
        try {
            const tabs = await chrome.tabs.query({});
            const tabData = tabs.map(t => ({ title: t.title, url: t.url }));
            if (tabData.length === 0) return;

            const bookmarkData = await getBookmarksData();
            const historyData  = await getHistoryData();

            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    apiKey,
                    device:    'Extension',
                    browser:   browserName,
                    save_type: 'Auto-saved',
                    tabs:      tabData,
                    bookmarks: bookmarkData,
                    history:   historyData
                })
            });

            const result = await response.json();
            if (result.success) {
                chrome.storage.local.set({ surtr_last_history_sync: Date.now() });
            } else {
                console.error('Surtr Auto-Save: server rejected save —', result.error);
            }

        } catch (err) {
            console.error('Surtr Auto-Save Error:', err);
        }
    });
}