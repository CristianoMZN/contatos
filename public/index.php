<?php

/**
 * Front Controller
 * Entry point for all requests
 */

// Enable error reporting during development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set('America/Sao_Paulo');

// Load Composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Start the application
try {
    $app = \App\Core\App::getInstance();
    
    // Load routes
    require_once dirname(__DIR__) . '/routes/web.php';
    
    // Run the application
    $app->run();
    
} catch (Exception $e) {
    // Log error in production
    error_log($e->getMessage());
    
    // Show error page
    http_response_code(500);
    echo "Erro interno do servidor. Tente novamente mais tarde.";
}