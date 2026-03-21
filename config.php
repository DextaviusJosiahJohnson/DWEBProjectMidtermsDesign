<?php
/**
 * surtr/config.php
 * Central configuration.
 *
 * Reads from environment variables first (set by Docker Compose),
 * then falls back to the defaults below for local development.
 *
 * Never commit real credentials to version control although i added security to the webapp as best as i could I GENUINELY DO NOT TRUST IT.
 */

// ── Environment ───────────────────────────────────────────
// 'production' enables HTTPS enforcement, secure cookies, HSTS.
define('APP_ENV', getenv('APP_ENV') ?: 'development');

// ── Database ──────────────────────────────────────────────
// In Docker, DB_HOST is the service name ('db') set in docker-compose.yml.
// Outside Docker (local), it falls back to 'localhost'.
define('DB_HOST',    getenv('DB_HOST')    ?: 'localhost');
define('DB_NAME',    getenv('DB_NAME')    ?: 'smart_browser_state');
define('DB_USER',    getenv('DB_USER')    ?: 'root');
define('DB_PASS',    getenv('DB_PASS')    ?: '');
define('DB_CHARSET', 'utf8mb4');

// ── CORS ──────────────────────────────────────────────────
// Used by api/save_state.php.
// In Docker: set to http://localhost:8080 (your mapped port).
// In production: set to your actual domain.
define('ALLOWED_ORIGIN', getenv('ALLOWED_ORIGIN') ?: 'http://localhost');

// ── Session / Cookie ──────────────────────────────────────
define('SESSION_LIFETIME', 0);
define('COOKIE_SECURE',    APP_ENV === 'production');
define('COOKIE_SAMESITE',  'Strict');