<?php

declare(strict_types=1);

namespace App\Domain\Subscription\Entity;

use App\Domain\Shared\Entity\AggregateRoot;
use App\Domain\Shared\ValueObject\Money;
use App\Domain\Subscription\Event\SubscriptionCreated;
use App\Domain\Subscription\ValueObject\SubscriptionId;
use App\Domain\User\ValueObject\UserId;
use DateTimeImmutable;

/**
 * Subscription Aggregate Root
 * 
 * Represents a user's subscription to premium features
 */
final class Subscription extends AggregateRoot
{
    private function __construct(
        private SubscriptionId $id,
        private UserId $userId,
        private string $plan,
        private Money $amount,
        private DateTimeImmutable $startDate,
        private DateTimeImmutable $endDate,
        private bool $isActive,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt
    ) {
    }

    /**
     * Create a new subscription
     */
    public static function create(
        SubscriptionId $id,
        UserId $userId,
        string $plan,
        Money $amount,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate
    ): self {
        $subscription = new self(
            $id,
            $userId,
            $plan,
            $amount,
            $startDate,
            $endDate,
            true,
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $subscription->recordEvent(new SubscriptionCreated($id, $userId, $plan));

        return $subscription;
    }

    /**
     * Reconstruct Subscription from persistence
     */
    public static function fromPrimitives(
        string $id,
        string $userId,
        string $plan,
        int $amountCents,
        string $currency,
        string $startDate,
        string $endDate,
        bool $isActive,
        string $createdAt,
        string $updatedAt
    ): self {
        return new self(
            SubscriptionId::fromString($id),
            UserId::fromString($userId),
            $plan,
            Money::fromCents($amountCents, $currency),
            new DateTimeImmutable($startDate),
            new DateTimeImmutable($endDate),
            $isActive,
            new DateTimeImmutable($createdAt),
            new DateTimeImmutable($updatedAt)
        );
    }

    /**
     * Renew subscription
     */
    public function renew(DateTimeImmutable $newEndDate): void
    {
        $this->endDate = $newEndDate;
        $this->isActive = true;
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * Cancel subscription
     */
    public function cancel(): void
    {
        $this->isActive = false;
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * Check if subscription has expired
     */
    public function hasExpired(): bool
    {
        return $this->endDate < new DateTimeImmutable();
    }

    /**
     * Get days remaining in subscription
     */
    public function daysRemaining(): int
    {
        $now = new DateTimeImmutable();
        if ($now > $this->endDate) {
            return 0;
        }

        $interval = $now->diff($this->endDate);
        return (int) $interval->format('%a');
    }

    // Getters

    public function id(): SubscriptionId
    {
        return $this->id;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function plan(): string
    {
        return $this->plan;
    }

    public function amount(): Money
    {
        return $this->amount;
    }

    public function startDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    public function endDate(): DateTimeImmutable
    {
        return $this->endDate;
    }

    public function isActive(): bool
    {
        return $this->isActive && !$this->hasExpired();
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Compare by identity
     */
    public function equals(self $other): bool
    {
        return $this->id->equals($other->id);
    }
}
