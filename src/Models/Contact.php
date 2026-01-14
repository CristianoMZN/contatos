<?php

namespace App\Models;

use App\Core\Model;

/**
 * Contact Model
 * Handles contact management and relationships
 * 
 * @deprecated This model uses legacy MySQL/PDO and will be replaced by Domain\Entity\Contact
 *             and Infrastructure\Repository\FirestoreContactRepository.
 *             See docs/ARCHITECTURE.md and docs/DDD_GUIDE.md for the new approach.
 * 
 * @todo Migrate to Domain\Entity\Contact + FirestoreContactRepository
 * @see docs/ARCHITECTURE.md
 * @see docs/FIREBASE_SETUP.md
 * @see docs/DDD_GUIDE.md
 */
class Contact extends Model
{
    protected $table = 'contacts';
    protected $fillable = [
        'user_id', 'type', 'category_id', 'name', 'slug', 
        'description', 'address', 'website', 'main_image', 'is_public'
    ];
    
    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("
            SELECT c.*, cc.name as category_name, cc.slug as category_slug, cc.icon as category_icon,
                   u.name as owner_name
            FROM {$this->table} c
            LEFT JOIN company_categories cc ON c.category_id = cc.id
            LEFT JOIN users u ON c.user_id = u.id
            WHERE c.slug = ?
        ");
        $stmt->execute([$slug]);
        
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    public function generateSlug(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Ensure uniqueness
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->findBySlug($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    public function getPublicContacts(int $page = 1, int $perPage = 12, ?int $categoryId = null, ?string $search = null): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $conditions = ["c.is_public = 1", "c.type = 'company'"];
        
        if ($categoryId) {
            $conditions[] = "c.category_id = ?";
            $params[] = $categoryId;
        }
        
        if ($search) {
            $conditions[] = "(c.name LIKE ? OR c.description LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        
        $whereClause = implode(' AND ', $conditions);
        
        // Count total
        $countSql = "SELECT COUNT(*) as total FROM contacts c WHERE {$whereClause}";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = $countStmt->fetch()['total'];
        
        // Get data
        $sql = "
            SELECT c.*, cc.name as category_name, cc.slug as category_slug, cc.icon as category_icon
            FROM contacts c
            LEFT JOIN company_categories cc ON c.category_id = cc.id
            WHERE {$whereClause}
            ORDER BY c.created_at DESC
            LIMIT ? OFFSET ?
        ";
        
        $params[] = $perPage;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return [
            'data' => $stmt->fetchAll(),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }
    
    public function getUserContacts(int $userId, int $page = 1, int $perPage = 12, ?string $search = null): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [$userId];
        $conditions = ["c.user_id = ?"];
        
        if ($search) {
            $conditions[] = "(c.name LIKE ? OR c.description LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        
        $whereClause = implode(' AND ', $conditions);
        
        // Count total
        $countSql = "SELECT COUNT(*) as total FROM contacts c WHERE {$whereClause}";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = $countStmt->fetch()['total'];
        
        // Get data
        $sql = "
            SELECT c.*, cc.name as category_name, cc.slug as category_slug, cc.icon as category_icon
            FROM contacts c
            LEFT JOIN company_categories cc ON c.category_id = cc.id
            WHERE {$whereClause}
            ORDER BY c.updated_at DESC
            LIMIT ? OFFSET ?
        ";
        
        $params[] = $perPage;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return [
            'data' => $stmt->fetchAll(),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }
    
    public function getPhones(int $contactId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM contact_phones WHERE contact_id = ? ORDER BY id");
        $stmt->execute([$contactId]);
        return $stmt->fetchAll();
    }
    
    public function getEmails(int $contactId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM contact_emails WHERE contact_id = ? ORDER BY id");
        $stmt->execute([$contactId]);
        return $stmt->fetchAll();
    }
    
    public function getImages(int $contactId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM contact_images WHERE contact_id = ? ORDER BY is_main DESC, id");
        $stmt->execute([$contactId]);
        return $stmt->fetchAll();
    }
    
    public function addPhone(int $contactId, string $phone, ?string $department = null, bool $isWhatsapp = false): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO contact_phones (contact_id, phone, department, is_whatsapp) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$contactId, $phone, $department, $isWhatsapp]);
        return (int) $this->db->lastInsertId();
    }
    
    public function addEmail(int $contactId, string $email, ?string $department = null): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO contact_emails (contact_id, email, department) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$contactId, $email, $department]);
        return (int) $this->db->lastInsertId();
    }
    
    public function addImage(int $contactId, array $imageData): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO contact_images (contact_id, filename, original_name, mime_type, file_size, is_main, alt_text) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $contactId,
            $imageData['filename'],
            $imageData['original_name'],
            $imageData['mime_type'],
            $imageData['file_size'],
            $imageData['is_main'] ?? false,
            $imageData['alt_text'] ?? null
        ]);
        return (int) $this->db->lastInsertId();
    }
    
    public function belongsToUser(int $contactId, int $userId): bool
    {
        $stmt = $this->db->prepare("SELECT user_id FROM {$this->table} WHERE id = ?");
        $stmt->execute([$contactId]);
        $result = $stmt->fetch();
        
        return $result && $result['user_id'] == $userId;
    }
    
    public function getSitemapContacts(): array
    {
        $stmt = $this->db->prepare("
            SELECT slug, updated_at 
            FROM {$this->table} 
            WHERE is_public = 1 AND type = 'company'
            ORDER BY updated_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}