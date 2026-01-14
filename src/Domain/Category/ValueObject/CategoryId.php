<?php

declare(strict_types=1);

namespace App\Domain\Category\ValueObject;

use App\Domain\Shared\Exception\InvalidArgumentException;

/**
 * CategoryId Value Object
 * 
 * Immutable identifier for Category aggregate
 */
final readonly class CategoryId
{
    private function __construct(
        private string $value
    ) {
        $this->validate();
    }

    /**
     * Create CategoryId from string
     */
    public static function fromString(string $value): self
    {
        return new self(trim($value));
    }

    /**
     * Generate new unique CategoryId
     */
    public static function generate(): self
    {
        return new self(uniqid('category_', true));
    }

    /**
     * Validate ID format
     */
    private function validate(): void
    {
        if (empty($this->value)) {
            throw new InvalidArgumentException('CategoryId cannot be empty');
        }

        if (strlen($this->value) > 100) {
            throw new InvalidArgumentException('CategoryId is too long');
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
     * Compare with another CategoryId
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
