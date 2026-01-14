<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObject;

use App\Domain\Shared\Exception\InvalidArgumentException;

/**
 * UserId Value Object
 * 
 * Immutable identifier for User aggregate
 */
final readonly class UserId
{
    private function __construct(
        private string $value
    ) {
        $this->validate();
    }

    /**
     * Create UserId from string
     */
    public static function fromString(string $value): self
    {
        return new self(trim($value));
    }

    /**
     * Generate new unique UserId
     */
    public static function generate(): self
    {
        return new self(uniqid('user_', true));
    }

    /**
     * Validate ID format
     */
    private function validate(): void
    {
        if (empty($this->value)) {
            throw new InvalidArgumentException('UserId cannot be empty');
        }

        if (strlen($this->value) > 100) {
            throw new InvalidArgumentException('UserId is too long');
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
     * Compare with another UserId
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
