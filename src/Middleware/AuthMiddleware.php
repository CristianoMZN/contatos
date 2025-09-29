<?php

namespace App\Middleware;

use App\Core\App;

/**
 * Authentication Middleware
 * Ensures user is authenticated before accessing protected routes
 */
class AuthMiddleware
{
    public function handle(): bool
    {
        $app = App::getInstance();
        $session = $app->get('session');
        
        if (!$session->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
        
        return true;
    }
}