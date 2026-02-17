// Export functions for different data types
class BrowserDataExporter {
  
  // Export bookmarks as HTML (Firefox format)
  static async exportBookmarks() {
    try {
      // Get all bookmarks
      const bookmarks = await browser.bookmarks.getTree();
      
      // Convert to Firefox HTML format
      let htmlContent = `<!DOCTYPE NETSCAPE-Bookmark-file-1>
<!-- This is an automatically generated file.
     It will be read and overwritten.
     DO NOT EDIT! -->
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<TITLE>Bookmarks</TITLE>
<H1>Bookmarks Menu</H1>

<DL><p>
`;
      
      // Recursive function to process bookmarks
      function processBookmarks(items, depth = 0) {
        for (const item of items) {
          const indent = '    '.repeat(depth);
          
          if (item.type === 'folder') {
            htmlContent += `${indent}<DT><H3>${escapeHtml(item.title)}</H3>\n`;
            htmlContent += `${indent}<DL><p>\n`;
            if (item.children) {
              processBookmarks(item.children, depth + 1);
            }
            htmlContent += `${indent}</DL><p>\n`;
          } else if (item.type === 'bookmark' && item.url) {
            const addDate = item.dateAdded ? Math.floor(item.dateAdded / 1000) : Math.floor(Date.now() / 1000);
            htmlContent += `${indent}<DT><A HREF="${escapeHtml(item.url)}" ADD_DATE="${addDate}" LAST_VISIT="0">${escapeHtml(item.title)}</A>\n`;
          }
        }
      }
      
      // Start processing from root
      if (bookmarks[0] && bookmarks[0].children) {
        processBookmarks(bookmarks[0].children);
      }
      
      htmlContent += '</DL><p>';
      
      return {
        filename: `firefox_bookmarks_${Date.now()}.html`,
        content: htmlContent,
        count: countBookmarks(bookmarks[0])
      };
    } catch (error) {
      console.error('Error exporting bookmarks:', error);
      throw new Error(`Bookmark export failed: ${error.message}`);
    }
  }
  
  // Export browsing history as JSON
  static async exportHistoryJSON() {
    try {
      // Get history (last 10000 items - adjust as needed)
      const historyItems = await browser.history.search({
        text: '',
        maxResults: 10000,
        startTime: 0
      });
      
      // Format for export
      const historyData = {
        exportDate: new Date().toISOString(),
        browser: "Firefox",
        version: "1.0",
        totalItems: historyItems.length,
        items: historyItems.map(item => ({
          url: item.url,
          title: item.title || '',
          lastVisitTime: item.lastVisitTime ? new Date(item.lastVisitTime).toISOString() : null,
          visitCount: item.visitCount || 1,
          typed: item.typed || false
        }))
      };
      
      return {
        filename: `firefox_history_${Date.now()}.json`,
        content: JSON.stringify(historyData, null, 2),
        count: historyItems.length
      };
    } catch (error) {
      console.error('Error exporting history:', error);
      throw new Error(`History export failed: ${error.message}`);
    }
  }
  
  // Export browsing history as CSV (for Excel)
  static async exportHistoryCSV() {
    try {
      // Get history
      const historyItems = await browser.history.search({
        text: '',
        maxResults: 10000,
        startTime: 0
      });
      
      // Create CSV content with headers
      let csvContent = 'URL,Title,Last Visit,Visit Count,Typed\n';
      
      for (const item of historyItems) {
        const url = escapeCsv(item.url || '');
        const title = escapeCsv(item.title || '');
        const lastVisit = item.lastVisitTime ? 
          escapeCsv(new Date(item.lastVisitTime).toLocaleString()) : '';
        const visitCount = escapeCsv(String(item.visitCount || 1));
        const typed = escapeCsv(item.typed ? 'Yes' : 'No');
        
        csvContent += `${url},${title},${lastVisit},${visitCount},${typed}\n`;
      }
      
      return {
        filename: `firefox_history_${Date.now()}.csv`,
        content: csvContent,
        count: historyItems.length
      };
    } catch (error) {
      console.error('Error exporting history CSV:', error);
      throw new Error(`History CSV export failed: ${error.message}`);
    }
  }
  
  // Download a file
  static async downloadFile(filename, content) {
    // Create a Blob and download link
    const blob = new Blob([content], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    
    // Use downloads API to save file
    await browser.downloads.download({
      url: url,
      filename: `firefox_export/${filename}`,
      saveAs: false,
      conflictAction: 'uniquify'
    });
    
    // Clean up
    setTimeout(() => URL.revokeObjectURL(url), 1000);
  }
}

// Helper functions
function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

function escapeCsv(field) {
  // Escape quotes and wrap in quotes if contains comma or quote
  if (field.includes('"') || field.includes(',') || field.includes('\n')) {
    return '"' + field.replace(/"/g, '""') + '"';
  }
  return field;
}

// Count bookmarks recursively
function countBookmarks(bookmarkNode) {
  let count = 0;
  if (bookmarkNode.type === 'bookmark' && bookmarkNode.url) {
    count = 1;
  } else if (bookmarkNode.children) {
    for (const child of bookmarkNode.children) {
      count += countBookmarks(child);
    }
  }
  return count;
}

export default BrowserDataExporter;