<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\App;

/**
 * Role-Based Access Control Middleware
 * Ensures user has required roles to access routes
 */
class RoleMiddleware
{
    private array $requiredRoles;

    public function __construct(array $roles)
    {
        $this->requiredRoles = $roles;
    }

    /**
     * Handle role checking
     */
    public function handle(): bool
    {
        $app = App::getInstance();
        $session = $app->get('session');

        if (!$session->isLoggedIn()) {
            $this->accessDenied('Authentication required');
            return false;
        }

        $userRoles = $session->get('user_roles', ['user']);

        // Check if user has any of the required roles
        foreach ($this->requiredRoles as $requiredRole) {
            if (in_array($requiredRole, $userRoles, true)) {
                return true; // User has required role
            }
        }

        // User doesn't have required role
        $this->accessDenied('Insufficient permissions');
        return false;
    }

    /**
     * Handle access denied
     */
    private function accessDenied(string $message): void
    {
        // For API requests, return JSON
        if ($this->isApiRequest()) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode([
                'error' => 'Forbidden',
                'message' => $message
            ]);
            exit;
        }

        // For web requests, redirect with flash message
        $app = App::getInstance();
        $session = $app->get('session');
        $session->setFlash('error', 'Você não tem permissão para acessar esta página');
        header('Location: /dashboard');
        exit;
    }

    /**
     * Check if request is an API request
     */
    private function isApiRequest(): bool
    {
        return isset($_SERVER['HTTP_ACCEPT']) 
            && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json');
    }

    /**
     * Create middleware that requires admin role
     */
    public static function admin(): self
    {
        return new self(['admin']);
    }

    /**
     * Create middleware that requires premium role
     */
    public static function premium(): self
    {
        return new self(['premium', 'admin']); // Admins have all permissions
    }

    /**
     * Create middleware that requires user role (any authenticated user)
     */
    public static function user(): self
    {
        return new self(['user', 'premium', 'admin']);
    }
}
