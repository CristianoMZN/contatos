<?php

namespace App\Core;

/**
 * Base Controller Class
 * All controllers should extend this class
 */
abstract class Controller
{
    protected $app;
    protected $db;
    protected $session;
    
    public function __construct()
    {
        $this->app = App::getInstance();
        $this->db = $this->app->get('db');
        $this->session = $this->app->get('session');
    }
    
    protected function view(string $view, array $data = []): void
    {
        // Extract data to variables
        extract($data);
        
        // Include view file
        $viewPath = dirname(__DIR__, 2) . "/src/Views/{$view}.php";
        
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            throw new \Exception("View {$view} not found");
        }
    }
    
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function redirect(string $url, int $statusCode = 302): void
    {
        http_response_code($statusCode);
        header("Location: {$url}");
        exit;
    }
    
    protected function back(): void
    {
        $referrer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referrer);
    }
    
    protected function requireAuth(): void
    {
        if (!$this->session->isLoggedIn()) {
            $this->redirect('/login');
        }
    }
    
    protected function requireGuest(): void
    {
        if ($this->session->isLoggedIn()) {
            $this->redirect('/dashboard');
        }
    }
    
    protected function validateCsrf(): bool
    {
        $token = $_POST['_token'] ?? '';
        return $this->session->validateCsrfToken($token);
    }
    
    protected function old(string $key, string $default = ''): string
    {
        return $this->session->get("old_{$key}", $default);
    }
    
    protected function withOldInput(): void
    {
        foreach ($_POST as $key => $value) {
            if ($key !== '_token') {
                $this->session->set("old_{$key}", $value);
            }
        }
    }
    
    protected function clearOldInput(): void
    {
        foreach ($_SESSION as $key => $value) {
            if (strpos($key, 'old_') === 0) {
                $this->session->remove($key);
            }
        }
    }
    
    protected function withError(string $message): void
    {
        $this->session->set('error', $message);
    }
    
    protected function withSuccess(string $message): void
    {
        $this->session->set('success', $message);
    }
    
    protected function getError(): ?string
    {
        $error = $this->session->get('error');
        $this->session->remove('error');
        return $error;
    }
    
    protected function getSuccess(): ?string
    {
        $success = $this->session->get('success');
        $this->session->remove('success');
        return $success;
    }
}