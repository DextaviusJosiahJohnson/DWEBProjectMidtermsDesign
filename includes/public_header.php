<?php
/**
 * surtr/includes/public_header.php
 * Bootstrap for PUBLIC pages (login.php, landing.php).
 *
 * Same security setup as auth.php but with NO auth check or redirect.
 *
 * Exposes to the including page:
 *   $nonce — embed in every <script nonce="..."> tag
 */

require_once __DIR__ . '/../config.php';

// ── HTTPS enforcement (production only) ───────────────────
if (APP_ENV === 'production' &&
    (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on')) {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true, 301);
    exit();
}

// ── CSP nonce ─────────────────────────────────────────────
$nonce = base64_encode(random_bytes(16));

// ── Security headers ──────────────────────────────────────
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

// ── Session configuration + start ────────────────────────
session_set_cookie_params([
    'lifetime' => SESSION_LIFETIME,
    'path'     => '/',
    'secure'   => COOKIE_SECURE,
    'httponly' => true,
    'samesite' => COOKIE_SAMESITE,
]);
session_start();