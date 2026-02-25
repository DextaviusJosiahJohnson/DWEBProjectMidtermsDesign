const container = document.getElementById('states-container');
const totalCount = document.getElementById('total-count');
const searchInput = document.getElementById('bookmark-search');

/* =========================
   FETCH BOOKMARKS
========================= */
function fetchBookmarks(){

    container.innerHTML = "<p>Loading...</p>";

    const params = new URLSearchParams();

    if(searchInput.value){
        params.append('search', searchInput.value);
    }

    fetch('ajax/fetch-bookmarks.php?' + params.toString())
    .then(res => res.text())
    .then(html => {
        container.innerHTML = html;
        updateCount();
    });
}

/* =========================
   UPDATE COUNT
========================= */
function updateCount(){
    const count = container.querySelectorAll('.state-card').length;
    totalCount.textContent = count;

    if(count === 0){
        container.innerHTML = `
            <div class="empty-state">
                <p>No bookmarks found.</p>
            </div>
        `;
    }
}

/* =========================
   LIVE SEARCH
========================= */
searchInput.addEventListener('input', function(){
    fetchBookmarks();
});

/* =========================
   INITIAL LOAD
========================= */
fetchBookmarks();