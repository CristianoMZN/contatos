<?php

namespace App\Models;

use App\Core\Model;

/**
 * Company Category Model
 * Handles business categories for contact classification
 * 
 * @deprecated This model uses legacy MySQL/PDO and will be replaced by Domain\Entity\Category
 *             and Infrastructure\Repository\FirestoreCategoryRepository.
 *             See docs/ARCHITECTURE.md and docs/DDD_GUIDE.md for the new approach.
 * 
 * @todo Migrate to Domain\Entity\Category + FirestoreCategoryRepository
 * @see docs/ARCHITECTURE.md
 * @see docs/FIREBASE_SETUP.md
 * @see docs/DDD_GUIDE.md
 */
class CompanyCategory extends Model
{
    protected $table = 'company_categories';
    protected $fillable = ['name', 'slug', 'description', 'icon'];
    
    public function findBySlug(string $slug): ?array
    {
        return $this->findBy('slug', $slug);
    }
    
    public function getWithContactCount(): array
    {
        $stmt = $this->db->prepare("
            SELECT cc.*, COUNT(c.id) as contact_count
            FROM {$this->table} cc
            LEFT JOIN contacts c ON cc.id = c.category_id AND c.is_public = 1
            GROUP BY cc.id
            ORDER BY cc.name
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}