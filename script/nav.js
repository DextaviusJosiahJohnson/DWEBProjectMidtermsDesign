// nav.js

// ── Mobile burger ─────────────────────────────────────────
// Attached here instead of inline onclick to satisfy CSP.
document.addEventListener('DOMContentLoaded', function () {

    // Burger button — present on every protected page
    var burger = document.getElementById('burgerBtn');
    if (burger) {
        burger.addEventListener('click', function () {
            var sidebar = document.getElementById('sidebar');
            if (sidebar) sidebar.classList.toggle('show');
        });
    }

    // data-goto fallback — kept for anything that still uses it
    document.querySelectorAll('[data-goto]').forEach(function (el) {
        el.addEventListener('click', function () {
            window.location.href = el.getAttribute('data-goto');
        });
    });

});

// Globals kept so existing JS that calls these doesn't break
window.toggleMenu = function () {
    var sidebar = document.getElementById('sidebar');
    if (sidebar) sidebar.classList.toggle('show');
};

window.goTo = function (page) {
    window.location.href = page;
};