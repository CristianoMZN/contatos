<?php

declare(strict_types=1);

namespace App\Domain\User\Repository;

use App\Domain\Shared\ValueObject\Email;
use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\UserId;

/**
 * User Repository Interface
 * 
 * Defines contract for User persistence
 */
interface UserRepositoryInterface
{
    /**
     * Save user (insert or update)
     */
    public function save(User $user): void;

    /**
     * Find user by ID
     */
    public function findById(UserId $id): ?User;

    /**
     * Find user by email
     */
    public function findByEmail(Email $email): ?User;

    /**
     * Delete a user
     */
    public function delete(UserId $id): void;

    /**
     * Check if user exists
     */
    public function exists(UserId $id): bool;

    /**
     * Check if email is already registered
     */
    public function emailExists(Email $email): bool;

    /**
     * Generate next identity
     */
    public function nextIdentity(): UserId;
}
