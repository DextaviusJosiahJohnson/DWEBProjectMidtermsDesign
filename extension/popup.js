const API_URL = 'http://localhost/Surtr/api/save_state.php'; 

document.addEventListener('DOMContentLoaded', () => {
    const saveBtn = document.getElementById('saveBtn');
    const apiKeyInput = document.getElementById('apiKey');
    const intervalSelect = document.getElementById('autoSaveInterval');
    const statusDiv = document.getElementById('status');
    
    // 1. Populate Autosave Intervals
    for (let i = 1; i <= 60; i++) {
        let opt = document.createElement('option');
        opt.value = i;
        opt.textContent = `Every ${i} minute${i > 1 ? 's' : ''}`;
        intervalSelect.appendChild(opt);
    }
    const longIntervals = [
        { val: 1440, text: 'Every 24 hours' },
        { val: 4320, text: 'Every 3 days' },
        { val: 10080, text: 'Every 1 week' }
    ];
    longIntervals.forEach(inv => {
        let opt = document.createElement('option');
        opt.value = inv.val;
        opt.textContent = inv.text;
        intervalSelect.appendChild(opt);
    });

    // 2. Load Saved Settings
    chrome.storage.local.get(['surtr_api_key', 'surtr_interval'], (result) => {
        if (result.surtr_api_key) apiKeyInput.value = result.surtr_api_key;
        if (result.surtr_interval !== undefined) intervalSelect.value = result.surtr_interval;
    });

    // 3. Handle Interval Changes
    intervalSelect.addEventListener('change', () => {
        const val = parseInt(intervalSelect.value);
        chrome.storage.local.set({ surtr_interval: val });
        
        chrome.alarms.clear('surtr_auto_save');
        if (val > 0) {
            chrome.alarms.create('surtr_auto_save', { periodInMinutes: val });
        }
        showStatus(`Autosave set to ${intervalSelect.options[intervalSelect.selectedIndex].text}`, "success");
    });

    function showStatus(msg, type) {
        statusDiv.textContent = msg;
        statusDiv.className = type;
        statusDiv.style.display = 'block';
        setTimeout(() => { statusDiv.style.display = 'none'; }, 3000);
    }
    
    function getBrowserName() {
        const agent = navigator.userAgent;
        if (agent.includes("Edg")) return "Edge";
        if (agent.includes("Chrome")) return "Chrome";
        if (agent.includes("Firefox")) return "Firefox";
        if (agent.includes("Safari")) return "Safari";
        return "Other";
    }

    // Helper: Flatten Bookmarks
    async function getBookmarksData() {
        const tree = await chrome.bookmarks.getTree();
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

    // Helper: Get Recent History
    async function getHistoryData() {
        const oneDayAgo = (new Date).getTime() - (1000 * 60 * 60 * 24);
        const historyItems = await chrome.history.search({ text: '', startTime: oneDayAgo, maxResults: 1000 });
        return historyItems.map(item => ({ title: item.title, url: item.url }));
    }

    // 4. MAIN SAVE LOGIC
    saveBtn.addEventListener('click', async () => {
        const apiKey = apiKeyInput.value.trim();
        if (!apiKey) {
            showStatus("Please enter your API Key.", "error");
            return;
        }

        chrome.storage.local.set({ surtr_api_key: apiKey });
        saveBtn.disabled = true;
        saveBtn.textContent = "Saving...";

        try {
            const tabs = await chrome.tabs.query({ currentWindow: true });
            const tabData = tabs.map(t => ({ title: t.title, url: t.url }));
            const bookmarkData = await getBookmarksData();
            const historyData = await getHistoryData();

            const response = await fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    apiKey: apiKey,
                    device: 'Extension', 
                    browser: getBrowserName(),
                    save_type: 'Manual',
                    tabs: tabData,
                    bookmarks: bookmarkData,
                    history: historyData
                })
            });

            if (!response.ok) throw new Error("Server Error");
            const result = await response.json();

            if (result.success) showStatus("✅ Session saved!", "success");
            else showStatus("❌ Error: " + (result.error), "error");

        } catch (err) {
            console.error(err);
            showStatus("❌ Connection Failed.", "error");
        } finally {
            saveBtn.disabled = false;
            saveBtn.textContent = "Save Current Session";
        }
    });
});