<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObject;

use App\Domain\User\Exception\InvalidEmailException;

/**
 * Email Value Object
 * Immutable value object representing a valid email address
 */
final readonly class Email
{
    private function __construct(
        private string $value
    ) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException("Invalid email format: {$value}");
        }
    }

    public static function fromString(string $value): self
    {
        return new self(trim(strtolower($value)));
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(Email $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
