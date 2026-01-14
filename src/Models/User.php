<?php

namespace App\Models;

use App\Core\Model;

/**
 * User Model
 * Handles user authentication and management
 * 
 * @deprecated This model uses legacy MySQL/PDO and will be replaced by Domain\Entity\User
 *             with Firebase Authentication integration.
 *             See docs/ARCHITECTURE.md and docs/FIREBASE_AUTH.md for the new approach.
 * 
 * @todo Migrate to Domain\Entity\User + Firebase Authentication
 * @see docs/ARCHITECTURE.md
 * @see docs/FIREBASE_AUTH.md
 * @see docs/DDD_GUIDE.md
 */
class User extends Model
{
    protected $table = 'users';
    protected $fillable = [
        'name', 'email', 'password_hash', 'two_factor_secret', 
        'two_factor_enabled', 'email_verified', 'verification_token',
        'reset_token', 'reset_token_expires'
    ];
    
    public function findByEmail(string $email): ?array
    {
        return $this->findBy('email', $email);
    }
    
    public function createUser(array $data): int
    {
        // Hash password
        $data['password_hash'] = password_hash($data['password'], PASSWORD_ARGON2ID);
        unset($data['password']);
        
        // Generate verification token
        $data['verification_token'] = bin2hex(random_bytes(32));
        
        return $this->create($data);
    }
    
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
    
    public function updatePassword(int $userId, string $newPassword): bool
    {
        return $this->update($userId, [
            'password_hash' => password_hash($newPassword, PASSWORD_ARGON2ID)
        ]);
    }
    
    public function generateResetToken(int $userId): string
    {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $this->update($userId, [
            'reset_token' => $token,
            'reset_token_expires' => $expires
        ]);
        
        return $token;
    }
    
    public function findByResetToken(string $token): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE reset_token = ? AND reset_token_expires > NOW()
        ");
        $stmt->execute([$token]);
        
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    public function clearResetToken(int $userId): bool
    {
        return $this->update($userId, [
            'reset_token' => null,
            'reset_token_expires' => null
        ]);
    }
    
    public function incrementLoginAttempts(int $userId): void
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET login_attempts = login_attempts + 1,
                lockout_until = CASE 
                    WHEN login_attempts >= 4 THEN DATE_ADD(NOW(), INTERVAL 15 MINUTE)
                    ELSE lockout_until
                END
            WHERE {$this->primaryKey} = ?
        ");
        $stmt->execute([$userId]);
    }
    
    public function resetLoginAttempts(int $userId): bool
    {
        return $this->update($userId, [
            'login_attempts' => 0,
            'lockout_until' => null,
            'last_login' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function isLockedOut(int $userId): bool
    {
        $user = $this->find($userId);
        if (!$user || !$user['lockout_until']) {
            return false;
        }
        
        return strtotime($user['lockout_until']) > time();
    }
    
    public function enable2FA(int $userId, string $secret): bool
    {
        return $this->update($userId, [
            'two_factor_secret' => $secret,
            'two_factor_enabled' => true
        ]);
    }
    
    public function disable2FA(int $userId): bool
    {
        return $this->update($userId, [
            'two_factor_secret' => null,
            'two_factor_enabled' => false
        ]);
    }
    
    public function verifyEmail(string $token): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET email_verified = 1, verification_token = NULL 
            WHERE verification_token = ?
        ");
        
        return $stmt->execute([$token]);
    }
    
    public function getUserContacts(int $userId, int $page = 1, int $perPage = 12): array
    {
        $offset = ($page - 1) * $perPage;
        
        // Count total
        $countStmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM contacts 
            WHERE user_id = ?
        ");
        $countStmt->execute([$userId]);
        $total = $countStmt->fetch()['total'];
        
        // Get contacts with category info  
        $stmt = $this->db->prepare("
            SELECT c.*, cc.name as category_name, cc.icon as category_icon
            FROM contacts c
            LEFT JOIN company_categories cc ON c.category_id = cc.id
            WHERE c.user_id = ?
            ORDER BY c.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$userId, $perPage, $offset]);
        
        return [
            'data' => $stmt->fetchAll(),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }
}