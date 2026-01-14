<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\App;
use App\Infrastructure\Firebase\FirebaseFactory;

/**
 * Authentication Middleware
 * Ensures user is authenticated before accessing protected routes
 * Supports both Firebase JWT tokens and legacy session-based auth
 */
class AuthMiddleware
{
    public function handle(): bool
    {
        $app = App::getInstance();
        $session = $app->get('session');
        
        // Check for Firebase JWT token in Authorization header
        if ($this->hasAuthorizationHeader()) {
            return $this->validateFirebaseToken();
        }
        
        // Fallback to legacy session-based authentication
        if (!$session->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
        
        return true;
    }

    /**
     * Check if Authorization header is present
     */
    private function hasAuthorizationHeader(): bool
    {
        return isset($_SERVER['HTTP_AUTHORIZATION']) 
            && str_starts_with($_SERVER['HTTP_AUTHORIZATION'], 'Bearer ');
    }

    /**
     * Validate Firebase JWT token
     */
    private function validateFirebaseToken(): bool
    {
        try {
            $token = substr($_SERVER['HTTP_AUTHORIZATION'], 7);
            
            $auth = FirebaseFactory::getAuth();
            $verifiedToken = $auth->verifyIdToken($token);
            
            // Store user data in session for compatibility
            $app = App::getInstance();
            $session = $app->get('session');
            
            $claims = $verifiedToken->claims();
            $session->set('user_id', $claims->get('sub'));
            $session->set('user_email', $claims->get('email'));
            $session->set('user_authenticated', true);
            $session->set('firebase_token', $token);
            
            // Store custom claims (roles)
            $customClaims = $claims->get('roles', ['user']);
            $session->set('user_roles', $customClaims);
            
            return true;
        } catch (\Exception $e) {
            error_log('Firebase token validation failed: ' . $e->getMessage());
            
            // Return 401 for API requests
            if ($this->isApiRequest()) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Unauthorized', 'message' => 'Invalid or expired token']);
                exit;
            }
            
            // Redirect to login for web requests
            header('Location: /login');
            exit;
        }
    }

    /**
     * Check if request is an API request
     */
    private function isApiRequest(): bool
    {
        return isset($_SERVER['HTTP_ACCEPT']) 
            && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json');
    }
}
