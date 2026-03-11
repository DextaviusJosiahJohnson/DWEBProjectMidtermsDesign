const API_URL = 'http://localhost/Surtr/api/save_state.php'; 
const AUTO_SAVE_ALARM = 'surtr_auto_save';

// 1. Setup Alarm on Install/Startup based on saved settings
chrome.runtime.onInstalled.addListener(() => {
    initializeAlarm();
});
chrome.runtime.onStartup.addListener(() => {
    initializeAlarm();
});

function initializeAlarm() {
    chrome.storage.local.get(['surtr_interval'], (result) => {
        const interval = result.surtr_interval !== undefined ? result.surtr_interval : 15; // default 15 mins
        chrome.alarms.clear(AUTO_SAVE_ALARM);
        if (interval > 0) {
            chrome.alarms.create(AUTO_SAVE_ALARM, { periodInMinutes: interval });
        }
    });
}

chrome.alarms.onAlarm.addListener((alarm) => {
  if (alarm.name === AUTO_SAVE_ALARM) {
    performAutoSave();
  }
});

function getBrowserName() {
    const agent = navigator.userAgent;
    if (agent.includes("Edg")) return "Edge";
    if (agent.includes("Chrome")) return "Chrome";
    if (agent.includes("Firefox")) return "Firefox";
    if (agent.includes("Safari")) return "Safari";
    return "Other";
}

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

async function getHistoryData() {
    // Fetch last 24 hours of history to keep payload size reasonable
    const oneDayAgo = (new Date).getTime() - (1000 * 60 * 60 * 24);
    const historyItems = await chrome.history.search({ text: '', startTime: oneDayAgo, maxResults: 1000 });
    return historyItems.map(item => ({ title: item.title, url: item.url }));
}

async function performAutoSave() {
  const result = await chrome.storage.local.get(['surtr_api_key']);
  const apiKey = result.surtr_api_key;

  if (!apiKey) return;

  try {
    const tabs = await chrome.tabs.query({});
    const tabData = tabs.map(t => ({ title: t.title, url: t.url }));
    if (tabData.length === 0) return;

    const bookmarkData = await getBookmarksData();
    const historyData = await getHistoryData();

    await fetch(API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        apiKey: apiKey,
        device: 'Extension',
        browser: getBrowserName(),
        save_type: 'Auto-saved',
        tabs: tabData,
        bookmarks: bookmarkData,
        history: historyData
      })
    });
  } catch (err) {
    console.error("Surtr Auto-Save Error: ", err);
  }
}