const container = document.getElementById('states-container');
const totalCount = document.getElementById('total-count');

// Only filter fields: device, browser, date
const formElements = ['device','browser','date'].map(id => document.getElementById(id));

function fetchStates(){
    const params = new URLSearchParams();
    formElements.forEach(el => {
        if(el.value) params.append(el.name, el.value);
    });

    container.innerHTML = '<p>Loading...</p>';

    fetch('ajax/fetch-states.php?' + params.toString()) //itoooooo
    .then(res => res.text())
    .then(html => {
        container.innerHTML = html;
        const count = container.querySelectorAll('.state-card').length;
        totalCount.textContent = count;
    });
}

// Trigger fetch on change
formElements.forEach(el => {
    el.addEventListener('change', fetchStates);
});

// Initial load
fetchStates();
