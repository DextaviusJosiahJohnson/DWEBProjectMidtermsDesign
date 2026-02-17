document.addEventListener("DOMContentLoaded", function() {
    // DOM Elements
    const modal = document.getElementById('modalOverlay');
    const tabList = document.getElementById('modalTabList');
    const loading = document.getElementById('modalLoading');
    const restoreBtn = document.getElementById('restoreButton');
    
    // Confirmation Modal Elements
    const confirmModal = document.getElementById('confirmModal'); 
    const finalRestoreBtn = confirmModal ? confirmModal.querySelector('.primary') : null;

    // State Data
    let currentTabs = []; 

    /**------------------------------------------------------------------------
     * OPEN / CLOSE LOGIC
     *------------------------------------------------------------------------**/
    
    window.openModal = function(stateId) {
        if(!modal) return;
        
        modal.classList.add('active');
        tabList.innerHTML = ''; // Clear previous
        loading.style.display = 'block';
        restoreBtn.disabled = true;

        // Fetch Data from Backend
        fetch(`ajax/get_state_details.php?id=${stateId}`)
            .then(res => res.json())
            .then(data => {
                loading.style.display = 'none';
                restoreBtn.disabled = false;
                
                // Parse Data
                currentTabs = Array.isArray(data) ? data : JSON.parse(data); 

                if(!currentTabs || currentTabs.length === 0) {
                    const emptyLi = document.createElement('li');
                    emptyLi.textContent = "No tabs found in this session.";
                    emptyLi.style.padding = "10px";
                    emptyLi.style.color = "#666";
                    tabList.appendChild(emptyLi);
                    return;
                }

                // Render Tabs (SECURE METHOD)
                currentTabs.forEach(tab => {
                    const li = document.createElement('li');
                    
                    // 1. Create Title Row
                    const titleDiv = document.createElement('div');
                    titleDiv.style.overflow = 'hidden';
                    titleDiv.style.textOverflow = 'ellipsis';
                    titleDiv.style.whiteSpace = 'nowrap';
                    
                    // Icon
                    if (tab.favIconUrl) {
                        const img = document.createElement('img');
                        img.src = tab.favIconUrl;
                        img.style.width = '16px';
                        img.style.height = '16px';
                        img.style.verticalAlign = 'middle';
                        img.style.marginRight = '8px';
                        titleDiv.appendChild(img);
                    } else {
                        const iconSpan = document.createElement('span');
                        iconSpan.textContent = '📄 ';
                        titleDiv.appendChild(iconSpan);
                    }

                    // Title Text (Safe from XSS)
                    const strong = document.createElement('strong');
                    strong.textContent = tab.title || 'Untitled Tab';
                    titleDiv.appendChild(strong);

                    // 2. Create URL Row
                    const urlDiv = document.createElement('div');
                    urlDiv.style.fontSize = '0.8rem';
                    urlDiv.style.color = '#888';
                    urlDiv.style.marginLeft = '24px';
                    urlDiv.style.overflow = 'hidden';
                    urlDiv.style.textOverflow = 'ellipsis';
                    urlDiv.style.whiteSpace = 'nowrap';
                    
                    // URL Text (Safe from XSS)
                    urlDiv.textContent = tab.url || '#';

                    // Append to List Item
                    li.appendChild(titleDiv);
                    li.appendChild(urlDiv);
                    
                    // Append to List
                    tabList.appendChild(li);
                });
            })
            .catch(err => {
                loading.style.display = 'none';
                const errorLi = document.createElement('li');
                errorLi.textContent = "Error loading data.";
                errorLi.style.color = "red";
                tabList.appendChild(errorLi);
                console.error(err);
            });
    };

    window.closeModal = function() {
        if(modal) modal.classList.remove('active');
    };

    /**------------------------------------------------------------------------
     * RESTORE LOGIC
     *------------------------------------------------------------------------**/

    if (restoreBtn) {
        restoreBtn.addEventListener('click', () => {
            closeModal(); 
            if(confirmModal) confirmModal.classList.add('active');
        });
    }

    if (finalRestoreBtn) {
        finalRestoreBtn.addEventListener('click', () => {
            if(confirmModal) confirmModal.classList.remove('active');
            
            let delay = 0;
            currentTabs.forEach(tab => {
                if(tab.url) {
                    setTimeout(() => {
                        window.open(tab.url, '_blank');
                    }, delay);
                    delay += 200;
                }
            });
        });
    }

    window.closeConfirmModal = function() {
        if(confirmModal) confirmModal.classList.remove('active');
    }

    window.onclick = function(event) {
        if (event.target == modal) closeModal();
        if (event.target == confirmModal) closeConfirmModal();
    }
});