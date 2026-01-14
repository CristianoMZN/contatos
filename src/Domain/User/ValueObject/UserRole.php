<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObject;

use InvalidArgumentException;

/**
 * UserRole Value Object
 * Represents user roles in the system (stored as Firebase custom claims)
 */
final readonly class UserRole
{
    public const ROLE_USER = 'user';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_PREMIUM = 'premium';

    private const VALID_ROLES = [
        self::ROLE_USER,
        self::ROLE_ADMIN,
        self::ROLE_PREMIUM,
    ];

    private function __construct(
        private string $value
    ) {
        if (!in_array($value, self::VALID_ROLES, true)) {
            throw new InvalidArgumentException(
                sprintf('Invalid role: %s. Valid roles: %s', $value, implode(', ', self::VALID_ROLES))
            );
        }
    }

    public static function fromString(string $value): self
    {
        return new self(strtolower(trim($value)));
    }

    public static function user(): self
    {
        return new self(self::ROLE_USER);
    }

    public static function admin(): self
    {
        return new self(self::ROLE_ADMIN);
    }

    public static function premium(): self
    {
        return new self(self::ROLE_PREMIUM);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(UserRole $other): bool
    {
        return $this->value === $other->value;
    }

    public function isUser(): bool
    {
        return $this->value === self::ROLE_USER;
    }

    public function isAdmin(): bool
    {
        return $this->value === self::ROLE_ADMIN;
    }

    public function isPremium(): bool
    {
        return $this->value === self::ROLE_PREMIUM;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
