<?php

declare(strict_types=1);

namespace App\Domain\Subscription\Repository;

use App\Domain\Subscription\Entity\Subscription;
use App\Domain\Subscription\ValueObject\SubscriptionId;
use App\Domain\User\ValueObject\UserId;

/**
 * Subscription Repository Interface
 * 
 * Defines contract for Subscription persistence
 */
interface SubscriptionRepositoryInterface
{
    /**
     * Save subscription (insert or update)
     */
    public function save(Subscription $subscription): void;

    /**
     * Find subscription by ID
     */
    public function findById(SubscriptionId $id): ?Subscription;

    /**
     * Find active subscription for a user
     */
    public function findActiveByUser(UserId $userId): ?Subscription;

    /**
     * Find all subscriptions for a user (including expired)
     * 
     * @return Subscription[]
     */
    public function findByUser(UserId $userId): array;

    /**
     * Delete a subscription
     */
    public function delete(SubscriptionId $id): void;

    /**
     * Check if subscription exists
     */
    public function exists(SubscriptionId $id): bool;

    /**
     * Generate next identity
     */
    public function nextIdentity(): SubscriptionId;
}
