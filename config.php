<?php
/**
 * surtr/config.php
 * Central configuration. This is the only file you edit before deploying.
 * Never commit real credentials to version control.
 */

// ── Environment ───────────────────────────────────────────
// Switch to 'production' on your live server.
// Controls: HTTPS enforcement, secure cookie flag, HSTS header.
define('APP_ENV', 'development');

// ── Database ──────────────────────────────────────────────
define('DB_HOST',    'localhost');
define('DB_NAME',    'smart_browser_state');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

// ── CORS ──────────────────────────────────────────────────
// Used by api/save_state.php.
// In production set this to your actual domain: 'https://yourdomain.com'
define('ALLOWED_ORIGIN', 'http://localhost');

// ── Session / Cookie ──────────────────────────────────────
define('SESSION_LIFETIME', 0);                       // 0 = until browser closes
define('COOKIE_SECURE',    APP_ENV === 'production'); // true forces HTTPS-only cookies
define('COOKIE_SAMESITE',  'Strict');