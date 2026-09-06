<?php
// actions/admin/logout.php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

destroySession();
redirect(siteUrl('/admin/login.php'));
