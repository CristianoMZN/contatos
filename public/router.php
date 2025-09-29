<?php

/**
 * Router for PHP built-in server
 * This file handles routing when using php -S
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve static files directly
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // Let PHP serve the static file
}

// All other requests go to index.php
require_once __DIR__ . '/index.php';