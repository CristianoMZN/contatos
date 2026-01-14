<?php

declare(strict_types=1);

namespace App\Domain\User\Repository;

use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\UserId;
use App\Domain\User\ValueObject\Email;

/**
 * User Repository Interface
 * Defines contract for user persistence (implemented in Infrastructure layer)
 */
interface UserRepositoryInterface
{
    /**
     * Find user by ID
     */
    public function findById(UserId $id): ?User;

    /**
     * Find user by email
     */
    public function findByEmail(Email $email): ?User;

    /**
     * Save user (create or update)
     */
    public function save(User $user): void;

    /**
     * Delete user
     */
    public function delete(UserId $id): void;

    /**
     * Check if user exists by email
     */
    public function existsByEmail(Email $email): bool;

    /**
     * Get all users with pagination
     * 
     * @return array{data: User[], total: int, page: int, perPage: int}
     */
    public function findAll(int $page = 1, int $perPage = 50): array;
}
