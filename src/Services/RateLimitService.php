<?php

namespace App\Services;

/**
 * Rate Limiting Service
 * Protects against brute force attacks and spam
 * 
 * @deprecated This class uses legacy MySQL/PDO and will be replaced by Firestore-based rate limiting.
 *             New code should use Firestore with TTL or Symfony Rate Limiter component.
 *             See docs/ARCHITECTURE.md for the new approach.
 * 
 * @todo Migrate to Firestore-based rate limiting or Symfony Rate Limiter
 * @see docs/ARCHITECTURE.md
 * @see https://symfony.com/doc/current/rate_limiter.html
 */
class RateLimitService
{
    private $db;
    
    public function __construct()
    {
        // NOTE: Database class has been removed as part of MySQL deprecation
        // This will trigger a deprecation notice. Legacy code needs migration.
        trigger_error(
            'RateLimitService is deprecated and relies on removed Database class. ' .
            'Migrate to Firestore or Symfony Rate Limiter. See docs/ARCHITECTURE.md',
            E_USER_DEPRECATED
        );
        
        // Keeping reference for compatibility, but this will fail
        if (class_exists('App\Core\Database')) {
            $this->db = \App\Core\Database::getInstance();
        }
    }
    
    public function checkLimit(string $identifier, string $type, int $maxAttempts, int $windowSeconds): bool
    {
        // Clean expired records
        $this->cleanup();
        
        $resetAt = date('Y-m-d H:i:s', time() + $windowSeconds);
        
        // Check current attempts
        $stmt = $this->db->prepare("
            SELECT attempts FROM rate_limits 
            WHERE identifier = ? AND type = ? AND reset_at > NOW()
        ");
        $stmt->execute([$identifier, $type]);
        $result = $stmt->fetch();
        
        if (!$result) {
            return true; // No existing rate limit
        }
        
        return $result['attempts'] < $maxAttempts;
    }
    
    public function recordAttempt(string $identifier, string $type, int $windowSeconds = 900): void
    {
        $resetAt = date('Y-m-d H:i:s', time() + $windowSeconds);
        
        $stmt = $this->db->prepare("
            INSERT INTO rate_limits (identifier, type, attempts, reset_at) 
            VALUES (?, ?, 1, ?)
            ON DUPLICATE KEY UPDATE 
                attempts = attempts + 1,
                reset_at = IF(reset_at < NOW(), ?, reset_at)
        ");
        $stmt->execute([$identifier, $type, $resetAt, $resetAt]);
    }
    
    private function cleanup(): void
    {
        $stmt = $this->db->prepare("DELETE FROM rate_limits WHERE reset_at < NOW()");
        $stmt->execute();
    }
}