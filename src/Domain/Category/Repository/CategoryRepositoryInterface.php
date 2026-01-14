<?php

declare(strict_types=1);

namespace App\Domain\Category\Repository;

use App\Domain\Category\Entity\Category;
use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Shared\ValueObject\Slug;
use App\Domain\User\ValueObject\UserId;

/**
 * Category Repository Interface
 * 
 * Defines contract for Category persistence
 */
interface CategoryRepositoryInterface
{
    /**
     * Save category (insert or update)
     */
    public function save(Category $category): void;

    /**
     * Find category by ID
     */
    public function findById(CategoryId $id): ?Category;

    /**
     * Find category by slug (for a specific user)
     */
    public function findBySlug(UserId $userId, Slug $slug): ?Category;

    /**
     * Find all categories for a user
     * 
     * @return Category[]
     */
    public function findByUser(UserId $userId): array;

    /**
     * Delete a category
     */
    public function delete(CategoryId $id): void;

    /**
     * Check if category exists
     */
    public function exists(CategoryId $id): bool;

    /**
     * Check if slug is already used by user
     */
    public function slugExists(UserId $userId, Slug $slug, ?CategoryId $excludeId = null): bool;

    /**
     * Generate next identity
     */
    public function nextIdentity(): CategoryId;
}
