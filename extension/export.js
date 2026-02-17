// export.js
class BrowserDataExporter {
  
    // 1. Export Bookmarks (HTML)
    static async exportBookmarks() {
      try {
        const bookmarks = await chrome.bookmarks.getTree();
        
        let htmlContent = `<!DOCTYPE NETSCAPE-Bookmark-file-1>
  <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
  <TITLE>Bookmarks</TITLE>
  <H1>Bookmarks Menu</H1>
  <DL><p>
  `;
        
        function processBookmarks(items, depth = 0) {
          for (const item of items) {
            const indent = '    '.repeat(depth);
            if (item.children) {
              htmlContent += `${indent}<DT><H3>${escapeHtml(item.title)}</H3>\n`;
              htmlContent += `${indent}<DL><p>\n`;
              processBookmarks(item.children, depth + 1);
              htmlContent += `${indent}</DL><p>\n`;
            } else if (item.url) {
              const addDate = item.dateAdded ? Math.floor(item.dateAdded / 1000) : 0;
              htmlContent += `${indent}<DT><A HREF="${escapeHtml(item.url)}" ADD_DATE="${addDate}">${escapeHtml(item.title)}</A>\n`;
            }
          }
        }
        
        if (bookmarks[0] && bookmarks[0].children) {
          processBookmarks(bookmarks[0].children);
        }
        htmlContent += '</DL><p>';
        
        this.downloadFile(`bookmarks_${Date.now()}.html`, htmlContent);
        return { success: true };
      } catch (error) {
        console.error(error);
        return { success: false, error: error.message };
      }
    }
    
    // 2. Export History (JSON)
    static async exportHistoryJSON() {
      try {
        const historyItems = await chrome.history.search({ text: '', maxResults: 10000, startTime: 0 });
        
        const historyData = {
          exportDate: new Date().toISOString(),
          items: historyItems
        };
        
        this.downloadFile(`history_${Date.now()}.json`, JSON.stringify(historyData, null, 2));
        return { success: true };
      } catch (error) {
        return { success: false, error: error.message };
      }
    }
    
    // 3. Export History (CSV)
    static async exportHistoryCSV() {
      try {
        const historyItems = await chrome.history.search({ text: '', maxResults: 10000, startTime: 0 });
        
        let csvContent = 'URL,Title,Last Visit Count,Typed\n';
        
        for (const item of historyItems) {
            const url = escapeCsv(item.url || '');
            const title = escapeCsv(item.title || '');
            const visitCount = item.visitCount || 0;
            // Handle typedCount if available, otherwise just check if typed
            const typed = item.typedCount > 0 ? 'Yes' : 'No'; 
            
            csvContent += `${url},${title},${visitCount},${typed}\n`;
        }
        
        this.downloadFile(`history_${Date.now()}.csv`, csvContent);
        return { success: true };
      } catch (error) {
        return { success: false, error: error.message };
      }
    }
    
    // Helper: Trigger Download
    static downloadFile(filename, content) {
      const blob = new Blob([content], { type: 'text/plain' });
      const url = URL.createObjectURL(blob);
      
      chrome.downloads.download({
        url: url,
        filename: filename,
        saveAs: true // Let user pick location
      });
    }
  }
  
  // Helpers
  function escapeHtml(text) {
    if (!text) return "";
    return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
  }
  
  function escapeCsv(field) {
    if (field.includes('"') || field.includes(',') || field.includes('\n')) {
      return '"' + field.replace(/"/g, '""') + '"';
    }
    return field;
  }
  
  export default BrowserDataExporter;