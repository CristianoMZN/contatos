<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObject;

use InvalidArgumentException;

/**
 * DisplayName Value Object
 * Represents a user's display name with validation
 */
final readonly class DisplayName
{
    private const MIN_LENGTH = 2;
    private const MAX_LENGTH = 100;

    private function __construct(
        private string $value
    ) {
        $length = mb_strlen($value);
        
        if ($length < self::MIN_LENGTH || $length > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf(
                    'Display name must be between %d and %d characters',
                    self::MIN_LENGTH,
                    self::MAX_LENGTH
                )
            );
        }
    }

    public static function fromString(string $value): self
    {
        return new self(trim($value));
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(DisplayName $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
