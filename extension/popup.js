import BrowserDataExporter from './export.js';

// CONFIGURATION
const API_URL = 'http://localhost/Surtr/api/save_state.php'; 

document.addEventListener('DOMContentLoaded', () => {
    // Elements
    const saveBtn = document.getElementById('saveBtn');
    const apiKeyInput = document.getElementById('apiKey');
    const statusDiv = document.getElementById('status');
    
    const btnBookmarks = document.getElementById('exportBookmarksBtn');
    const btnHistoryJson = document.getElementById('exportHistoryJsonBtn');
    const btnHistoryCsv = document.getElementById('exportHistoryCsvBtn');

    // 1. Load saved API Key
    chrome.storage.local.get(['surtr_api_key'], (result) => {
        if (result.surtr_api_key) apiKeyInput.value = result.surtr_api_key;
    });

    // Helper: Show Status
    function showStatus(msg, type) {
        statusDiv.textContent = msg;
        statusDiv.className = type;
        statusDiv.style.display = 'block';
        setTimeout(() => { statusDiv.style.display = 'none'; }, 3000);
    }
    
    // Helper: Browser Detection
    function getBrowserName() {
        const agent = navigator.userAgent;
        if (agent.includes("Edg")) return "Edge";
        if (agent.includes("Chrome")) return "Chrome";
        if (agent.includes("Firefox")) return "Firefox";
        if (agent.includes("Safari")) return "Safari";
        return "Other";
    }

    // --- MAIN SAVE FUNCTION ---
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
            const tabData = tabs.map(t => ({
                title: t.title,
                url: t.url,
                favIconUrl: t.favIconUrl
            }));

            const response = await fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    apiKey: apiKey,
                    device: 'Extension', 
                    browser: getBrowserName(),
                    tabs: tabData
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

    // --- EXPORT HANDLERS ---
    
    btnBookmarks.addEventListener('click', async () => {
        await BrowserDataExporter.exportBookmarks();
        showStatus("Bookmarks exported!", "success");
    });

    btnHistoryJson.addEventListener('click', async () => {
        await BrowserDataExporter.exportHistoryJSON();
        showStatus("History (JSON) exported!", "success");
    });

    btnHistoryCsv.addEventListener('click', async () => {
        await BrowserDataExporter.exportHistoryCSV();
        showStatus("History (CSV) exported!", "success");
    });
});