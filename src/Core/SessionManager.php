<?php

namespace App\Core;

/**
 * Session Manager
 * Handles secure session management
 */
class SessionManager
{
    private $sessionName = 'CONTATOS_SESSION';
    private $isStarted = false;
    
    public function start(): void
    {
        if ($this->isStarted) {
            return;
        }
        
        // Configure session
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_samesite', 'Strict');
        
        session_name($this->sessionName);
        
        if (session_start()) {
            $this->isStarted = true;
            $this->regenerateIdPeriodically();
        }
    }
    
    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }
    
    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }
    
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }
    
    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }
    
    public function destroy(): void
    {
        if ($this->isStarted) {
            session_destroy();
            $this->isStarted = false;
        }
    }
    
    public function regenerateId(): void
    {
        if ($this->isStarted) {
            session_regenerate_id(true);
        }
    }
    
    private function regenerateIdPeriodically(): void
    {
        $lastRegeneration = $this->get('last_regeneration', 0);
        
        // Regenerate session ID every 30 minutes
        if (time() - $lastRegeneration > 1800) {
            $this->regenerateId();
            $this->set('last_regeneration', time());
        }
    }
    
    public function isLoggedIn(): bool
    {
        return $this->has('user_id') && $this->has('user_authenticated');
    }
    
    public function getUserId(): ?int
    {
        return $this->isLoggedIn() ? $this->get('user_id') : null;
    }
    
    public function login(int $userId, array $userData = []): void
    {
        $this->regenerateId();
        $this->set('user_id', $userId);
        $this->set('user_authenticated', true);
        $this->set('login_time', time());
        
        foreach ($userData as $key => $value) {
            $this->set("user_{$key}", $value);
        }
    }
    
    public function logout(): void
    {
        session_unset();
        $this->destroy();
    }
    
    public function generateCsrfToken(): string
    {
        if (!$this->has('csrf_token')) {
            $this->set('csrf_token', bin2hex(random_bytes(32)));
        }
        
        return $this->get('csrf_token');
    }
    
    public function validateCsrfToken(string $token): bool
    {
        return hash_equals($this->get('csrf_token', ''), $token);
    }
    
    /**
     * Set a flash message
     */
    public function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }
    
    /**
     * Get flash message and remove it
     */
    public function getFlash(string $type): ?string
    {
        if (isset($_SESSION['flash'][$type])) {
            $message = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $message;
        }
        return null;
    }
    
    /**
     * Check if flash message exists
     */
    public function hasFlash(string $type): bool
    {
        return isset($_SESSION['flash'][$type]);
    }
}