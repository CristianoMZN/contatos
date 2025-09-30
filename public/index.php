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
    
    // Get session manager if available
    $session = null;
    try {
        $session = $app->get('session');
    } catch (Exception $sessionEx) {
        // Session not available
    }
    
    // If session available, set flash message and redirect to home
    if ($session && $session instanceof \App\Core\SessionManager) {
        $session->setFlash('error', 'Ocorreu um erro inesperado. Por favor, tente novamente.');
        header('Location: /');
        exit;
    }
    
    // Otherwise show basic error page
    http_response_code(500);
    include dirname(__DIR__) . '/src/Views/errors/500.php';
}