# AS OF 13/03/2026

complete 4-phase security and structural overhaul

PHASE 1 — Critical Security
- Add session_regenerate_id(true) on login to prevent session fixation
- Add session_start() + auth guards to bookmarks.php, saved-states.php,
  and search-history.php (previously unprotected page shells)
- Fix logout navigation across all pages (was pointing to landing.php,
  never actually destroyed the session)
- Implement CSRF token system — token generated in PHP session, embedded
  in every protected page, validated via X-CSRF-Token header in all
  state-changing AJAX endpoints (delete_state, delete_bookmark,
  delete_search, update_settings, generate_api_key)
- Add brute-force protection on login — 5 failed attempts triggers a
  15-minute lockout via session-tracked counter
- Fix Saved States nav link in search-history.php sidebar (was pointing
  to dashboard.php)
- Remove stray duplicate closing div in search-history.php

PHASE 2 — Structural and Logic Bugs
- Fix background.js service worker navigator.userAgent crash — MV3
  service workers have no navigator access. Browser name is now detected
  in popup.js (which runs in a normal window context) and stored in
  chrome.storage.local as surtr_browser, then read by background.js
- Add configurable Server URL field to popup.html and popup.js —
  replaces hardcoded http://localhost constant in both extension files.
  URL is persisted in chrome.storage.local
- Wire up dead Restore button on saved state cards — added
  attachRestoreListeners() in saved-states.js, buttons now correctly
  trigger openModal() with the state ID
- Add data-id attribute to Restore button in fetch-states.php output
  so the JS listener can read it

PHASE 3 — API and Data Layer
- Hash API keys at rest using SHA-256 — generate_api_key.php now stores
  hash('sha256', $rawKey) and returns the raw key once to the user.
  save_state.php hashes the incoming key before DB lookup. Raw keys are
  never persisted. Existing plain-text keys are invalidated — users must
  regenerate.
- Restrict CORS in save_state.php — wildcard Access-Control-Allow-Origin
  replaced with ALLOWED_ORIGIN constant, chrome-extension:// origins
  explicitly allowed, OPTIONS preflight handled, POST-only enforced
- Convert all three AJAX fetch endpoints from HTML string output to JSON
  — fetch-states.php, fetch-bookmarks.php, fetch-search.php now return
  structured JSON with data, total, page, limit, total_pages
- Add pagination to all three fetch endpoints — default 20 per page,
  configurable via page and limit query params
- Rebuild saved-states.js, bookmarks.js, search-history.js to consume
  JSON and build DOM in JavaScript using escHtml() helper (prevents XSS
  from injected content), with renderPagination() controls
- Add safeUrl() in bookmarks.js to block javascript: and data: URI
  schemes from bookmark links
- Add install lock file to db.php — auto-install block only runs if
  database/. installed does not exist, writes lock on first successful
  install to prevent re-execution on a live server
- Add pagination styles to layout.css (.pagination, .page-btn,
  .page-info) with responsive handling

PHASE 4 — Hardening and Production Readiness
- Add config.php — single source of truth for DB credentials,
  ALLOWED_ORIGIN, APP_ENV, session lifetime, cookie flags. Inline
  credential strings removed from all files.
- Add includes/auth.php — single bootstrap for all protected pages.
  Handles HTTPS redirect (production), CSP nonce generation, security
  response headers (CSP, X-Frame-Options, X-Content-Type-Options,
  Referrer-Policy, Permissions-Policy, HSTS in production), session
  cookie configuration with httponly/secure/samesite flags, auth check
  with redirect, and CSRF token generation. Replaces ~10 lines of
  duplicated boilerplate across every page with one require call.
- Add includes/public_header.php — same as auth.php but without the
  auth check, used by login.php and landing.php
- Apply nonce to all inline <script> tags across every page to satisfy
  the script-src CSP directive
- Harden get_state_details.php — added intval() cast and range check on
  $_GET['id'], proper HTTP status codes (400, 401, 403) on all error
  paths, Content-Type header added
- Rewrite landing.php — added proper DOCTYPE and html/head/body
  structure, removed Cloudflare email obfuscation dependency, moved
  inline script to nonce-bearing tag, connected to public_header.php
- Update manifest.json — removed unused downloads permission, added
  background.service_worker declaration, added https://localhost/* to
  host_permissions
- Add API key recovery warning to settings.php UI
