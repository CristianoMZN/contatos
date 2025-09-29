<?php

namespace App\Services;

use App\Core\Database;

/**
 * Rate Limiting Service
 * Protects against brute force attacks and spam
 */
class RateLimitService
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
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