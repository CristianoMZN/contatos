<?php

declare(strict_types=1);

namespace App\Domain\Contact\ValueObject;

use App\Domain\Shared\Exception\InvalidArgumentException;

/**
 * ContactId Value Object
 * 
 * Immutable identifier for Contact aggregate
 */
final readonly class ContactId
{
    private function __construct(
        private string $value
    ) {
        $this->validate();
    }

    /**
     * Create ContactId from string
     */
    public static function fromString(string $value): self
    {
        return new self(trim($value));
    }

    /**
     * Generate new unique ContactId
     */
    public static function generate(): self
    {
        return new self(uniqid('contact_', true));
    }

    /**
     * Validate ID format
     */
    private function validate(): void
    {
        if (empty($this->value)) {
            throw new InvalidArgumentException('ContactId cannot be empty');
        }

        if (strlen($this->value) > 100) {
            throw new InvalidArgumentException('ContactId is too long');
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
     * Compare with another ContactId
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
