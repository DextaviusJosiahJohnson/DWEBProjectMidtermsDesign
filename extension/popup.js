// CONFIGURATION
// CHANGE THIS if your folder name is different (e.g. localhost/smart-browser-state/api/...)
const API_URL = 'http://localhost/Surtr/api/save_state.php'; 

document.addEventListener('DOMContentLoaded', () => {
    const saveBtn = document.getElementById('saveBtn');
    const apiKeyInput = document.getElementById('apiKey');
    const statusDiv = document.getElementById('status');

    // 1. Load saved API Key on startup
    chrome.storage.local.get(['surtr_api_key'], (result) => {
        if (result.surtr_api_key) {
            apiKeyInput.value = result.surtr_api_key;
        }
    });

    // 2. Helper to show messages
    function showStatus(msg, type) {
        statusDiv.textContent = msg;
        statusDiv.className = type;
        statusDiv.style.display = 'block';
    }
    
    // 3. Helper to detect browser
    function getBrowserName() {
        const agent = navigator.userAgent;
        if (agent.includes("Edg")) return "Edge";
        if (agent.includes("Chrome")) return "Chrome";
        if (agent.includes("Firefox")) return "Firefox";
        if (agent.includes("Safari")) return "Safari";
        return "Other";
    }

    saveBtn.addEventListener('click', async () => {
        const apiKey = apiKeyInput.value.trim();

        if (!apiKey) {
            showStatus("Please enter your API Key.", "error");
            return;
        }

        // Save key for next time
        chrome.storage.local.set({ surtr_api_key: apiKey });

        saveBtn.disabled = true;
        saveBtn.textContent = "Saving...";
        showStatus("Capturing tabs...", "loading");

        try {
            // 4. Capture Tabs
            const tabs = await chrome.tabs.query({ currentWindow: true });
            
            // Format data for PHP
            const tabData = tabs.map(t => ({
                title: t.title,
                url: t.url,
                favIconUrl: t.favIconUrl
            }));

            // 5. Send to Localhost API
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    apiKey: apiKey,
                    device: 'Extension', 
                    browser: getBrowserName(), // Send the actual browser name
                    tabs: tabData
                })
            });

            // Check if response is ok (status 200-299)
            if (!response.ok) {
                throw new Error(`Server returned ${response.status} ${response.statusText}`);
            }

            const result = await response.json();

            if (result.success) {
                showStatus("✅ Session saved successfully!", "success");
            } else {
                showStatus("❌ Error: " + (result.error || "Unknown error"), "error");
            }

        } catch (err) {
            console.error(err);
            showStatus("❌ Connection Failed. Check if XAMPP is running and the API_URL is correct.", "error");
        } finally {
            saveBtn.disabled = false;
            saveBtn.textContent = "Save Current Session";
        }
    });
});