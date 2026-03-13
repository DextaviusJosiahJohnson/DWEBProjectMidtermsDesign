/**
 * theme-switcher.js
 * Handles hover-preview and click-to-apply for the theme switcher card.
 * The actual theme token is applied to <html data-theme="..."> so all
 * CSS variables cascade down automatically.
 *
 * Also exports applyTheme() globally so any page can call it if needed.
 */

(function () {
  'use strict';

  const STORAGE_KEY  = 'surtr_theme';
  const THEMES       = ['division', 'minimal'];
  const DEFAULT      = 'minimal';

  /* ── Apply theme to <html> and persist ──────────────────── */
  function applyTheme(name, persist = true) {
    if (!THEMES.includes(name)) name = DEFAULT;
    document.documentElement.setAttribute('data-theme', name);
    if (persist) localStorage.setItem(STORAGE_KEY, name);
  }

  /* ── Read saved theme on every page load ────────────────── */
  const saved = localStorage.getItem(STORAGE_KEY) || DEFAULT;
  applyTheme(saved, false); // already applied by inline head script, just syncs

  /* ── Settings page logic (only runs if card exists) ─────── */
  const card          = document.getElementById('themeSwitcherCard');
  const options       = document.querySelectorAll('.theme-option');

  if (!card || !options.length) return; // not on settings page

  /* Mark the currently active option */
  function syncActiveState(activeTheme) {
    options.forEach(opt => {
      const isActive = opt.dataset.themeTarget === activeTheme;
      opt.classList.toggle('is-active', isActive);
    });
  }

  syncActiveState(saved);

  /* ── Hover → live preview on the card ─────────────────── */
  options.forEach(opt => {
    const target = opt.dataset.themeTarget;

    opt.addEventListener('mouseenter', () => {
      // Remove any existing preview class
      card.classList.remove('preview-division', 'preview-minimal');
      // Apply the hovered theme's preview
      card.classList.add(`preview-${target}`);
    });

    opt.addEventListener('mouseleave', () => {
      card.classList.remove('preview-division', 'preview-minimal');
    });

    /* ── Click → apply globally ──────────────────────────── */
    opt.addEventListener('click', () => {
      applyTheme(target);
      syncActiveState(target);

      // Brief "applied" flash on the card
      card.classList.remove('preview-division', 'preview-minimal');
      card.classList.add(`preview-${target}`);
      setTimeout(() => card.classList.remove(`preview-${target}`), 600);
    });
  });

  /* Expose globally for any other script that needs it */
  window.surtrApplyTheme = applyTheme;

})();