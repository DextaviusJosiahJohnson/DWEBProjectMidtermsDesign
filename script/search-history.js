//js for search history
const container = document.getElementById('states-container');
const totalCount = document.getElementById('total-count');
const searchInput = document.getElementById('history-search');

/* =========================
   FETCH SEARCH HISTORY
========================= */
function fetchHistory(){

    container.innerHTML = "<p>Loading...</p>";

    const params = new URLSearchParams();

    if(searchInput.value){
        params.append('search', searchInput.value);
    }

    fetch('ajax/fetch-search.php?' + params.toString())
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
                <p>No search history found.</p>
            </div>
        `;
    }
}

/* =========================
   LIVE SEARCH
========================= */
searchInput.addEventListener('input', fetchHistory);

/* =========================
   INITIAL LOAD
========================= */
fetchHistory();