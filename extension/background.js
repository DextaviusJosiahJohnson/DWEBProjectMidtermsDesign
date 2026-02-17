// CONFIGURATION
const API_URL = 'http://localhost/Surtr/api/save_state.php'; 
const AUTO_SAVE_ALARM = 'surtr_auto_save';
const SAVE_INTERVAL_MINUTES = 15; // Auto-save every 15 minutes

// 1. Setup Alarm on Install
chrome.runtime.onInstalled.addListener(() => {
  console.log("Surtr Session Saver installed.");
  chrome.alarms.create(AUTO_SAVE_ALARM, {
    periodInMinutes: SAVE_INTERVAL_MINUTES
  });
});

// 2. Listen for Alarm
chrome.alarms.onAlarm.addListener((alarm) => {
  if (alarm.name === AUTO_SAVE_ALARM) {
    performAutoSave();
  }
});

// 3. Browser Detection Helper
function getBrowserName() {
    // Note: In background service workers, navigator.userAgent might be limited, 
    // but usually still works for basic detection.
    const agent = navigator.userAgent;
    if (agent.includes("Edg")) return "Edge";
    if (agent.includes("Chrome")) return "Chrome";
    if (agent.includes("Firefox")) return "Firefox";
    if (agent.includes("Safari")) return "Safari";
    return "Other";
}

// 4. Main Auto-Save Logic
async function performAutoSave() {
  // Check for API Key
  const result = await chrome.storage.local.get(['surtr_api_key']);
  const apiKey = result.surtr_api_key;

  if (!apiKey) {
    console.log("Surtr Auto-Save: No API Key found. Skipping.");
    return;
  }

  try {
    // Get Tabs
    const tabs = await chrome.tabs.query({});
    
    const tabData = tabs.map(t => ({
      title: t.title,
      url: t.url,
      favIconUrl: t.favIconUrl
    }));

    if (tabData.length === 0) return;

    // Send to API
    const response = await fetch(API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        apiKey: apiKey,
        device: 'Extension',
        browser: getBrowserName(),
        save_type: 'Auto-saved', // Requires backend support or it will default to 'Manual'
        tabs: tabData
      })
    });

    if (response.ok) {
      console.log("Surtr Auto-Save: Success");
    } else {
      console.error("Surtr Auto-Save: Server Error", response.status);
    }

  } catch (err) {
    console.error("Surtr Auto-Save: Connection Failed", err);
  }
}