<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

use App\Domain\Shared\Exception\InvalidArgumentException;

/**
 * Email Value Object
 * 
 * Immutable value object representing an email address with validation
 */
final readonly class Email
{
    private const MAX_LENGTH = 255;

    private function __construct(
        private string $value
    ) {
        $this->validate();
    }

    /**
     * Create Email from string
     */
    public static function fromString(string $value): self
    {
        return new self(trim(strtolower($value)));
    }

    /**
     * Validate email format and constraints
     */
    private function validate(): void
    {
        if (empty($this->value)) {
            throw new InvalidArgumentException('Email cannot be empty');
        }

        if (strlen($this->value) > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Email cannot exceed %d characters', self::MAX_LENGTH)
            );
        }

        if (!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(
                sprintf('"%s" is not a valid email address', $this->value)
            );
        }
    }

    /**
     * Get the email value
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Get the domain part of the email
     */
    public function domain(): string
    {
        $atPosition = strpos($this->value, '@');
        return $atPosition !== false ? substr($this->value, $atPosition + 1) : '';
    }

    /**
     * Get the local part of the email (before @)
     */
    public function localPart(): string
    {
        $atPosition = strpos($this->value, '@');
        return $atPosition !== false ? substr($this->value, 0, $atPosition) : '';
    }

    /**
     * Compare with another Email
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
