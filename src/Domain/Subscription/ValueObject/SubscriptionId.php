<?php

declare(strict_types=1);

namespace App\Domain\Subscription\ValueObject;

use App\Domain\Shared\Exception\InvalidArgumentException;

/**
 * SubscriptionId Value Object
 * 
 * Immutable identifier for Subscription aggregate
 */
final readonly class SubscriptionId
{
    private function __construct(
        private string $value
    ) {
        $this->validate();
    }

    /**
     * Create SubscriptionId from string
     */
    public static function fromString(string $value): self
    {
        return new self(trim($value));
    }

    /**
     * Generate new unique SubscriptionId
     */
    public static function generate(): self
    {
        return new self(uniqid('subscription_', true));
    }

    /**
     * Validate ID format
     */
    private function validate(): void
    {
        if (empty($this->value)) {
            throw new InvalidArgumentException('SubscriptionId cannot be empty');
        }

        if (strlen($this->value) > 100) {
            throw new InvalidArgumentException('SubscriptionId is too long');
        }
    }

    /**
     * Get the ID value
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Compare with another SubscriptionId
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * String representation
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
