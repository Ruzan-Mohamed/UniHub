<?php
// ─── UniHub Site Configuration ────────────────────────────────────────────
// APP_BASE  = the URL path prefix Apache uses (from the Alias directive)
// APP_URL   = full base URL with scheme + host
// Adjust APP_BASE if you rename the Alias or move to a VirtualHost (set to '')
// ──────────────────────────────────────────────────────────────────────────

define('APP_BASE', '/UniHub');   // matches: Alias /UniHub "e:/UniHub"

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('APP_URL', $scheme . '://' . $host . APP_BASE);

// ─── Database ─────────────────────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'unihub');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
