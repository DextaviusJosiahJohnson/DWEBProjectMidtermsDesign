<?php
/**
 * surtr/includes/auth.php
 * Bootstrap for every PROTECTED page.
 *
 * Handles in order:
 *   1. HTTPS redirect (production only)
 *   2. CSP nonce generation
 *   3. Security response headers
 *   4. Session cookie configuration + session_start()
 *   5. Authentication check — redirects to login.php if not logged in
 *   6. CSRF token generation
 *
 * Exposes to the including page:
 *   $nonce  — embed in every <script nonce="..."> tag
 */

require_once __DIR__ . '/../config.php';

// ── 1. HTTPS enforcement (production only) ────────────────
if (APP_ENV === 'production' &&
    (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on')) {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true, 301);
    exit();
}

// ── 2. CSP nonce — generated before any output ───────────
$nonce = base64_encode(random_bytes(16));

// ── 3. Security headers ───────────────────────────────────
header('Content-Security-Policy: ' . implode('; ', [
    "default-src 'self'",
    "script-src 'self' 'nonce-{$nonce}'",
    "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
    "font-src 'self' https://fonts.gstatic.com data:",
    "img-src 'self' data: https:",
    "connect-src 'self'",
    "frame-ancestors 'none'",
    "base-uri 'self'",
    "form-action 'self'",
]));
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-XSS-Protection: 1; mode=block');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

if (APP_ENV === 'production') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
}

// ── 4. Session configuration + start ─────────────────────
session_set_cookie_params([
    'lifetime' => SESSION_LIFETIME,
    'path'     => '/',
    'secure'   => COOKIE_SECURE,
    'httponly' => true,
    'samesite' => COOKIE_SAMESITE,
]);
session_start();

// ── 5. Auth check ─────────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// ── 6. CSRF token ─────────────────────────────────────────
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}