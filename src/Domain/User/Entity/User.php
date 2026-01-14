<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Domain\User\ValueObject\UserId;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\DisplayName;
use App\Domain\User\ValueObject\UserRole;
use DateTimeImmutable;

/**
 * User Entity
 * Represents a user in the domain with business logic
 */
class User
{
    /** @var UserRole[] */
    private array $roles = [];
    
    private ?string $photoURL = null;
    private bool $emailVerified = false;

    private function __construct(
        private UserId $id,
        private Email $email,
        private DisplayName $displayName,
        private DateTimeImmutable $createdAt
    ) {
        // Default role is user
        $this->roles = [UserRole::user()];
    }

    /**
     * Create a new user (for registration)
     */
    public static function create(
        UserId $id,
        Email $email,
        DisplayName $displayName
    ): self {
        return new self(
            $id,
            $email,
            $displayName,
            new DateTimeImmutable()
        );
    }

    /**
     * Reconstitute user from persistence
     */
    public static function fromPrimitives(
        string $id,
        string $email,
        string $displayName,
        string $createdAt,
        array $roles = [],
        ?string $photoURL = null,
        bool $emailVerified = false
    ): self {
        $user = new self(
            UserId::fromString($id),
            Email::fromString($email),
            DisplayName::fromString($displayName),
            new DateTimeImmutable($createdAt)
        );

        $user->roles = array_map(
            fn(string $role) => UserRole::fromString($role),
            $roles ?: ['user']
        );
        $user->photoURL = $photoURL;
        $user->emailVerified = $emailVerified;

        return $user;
    }

    // Business methods

    public function changeDisplayName(DisplayName $newDisplayName): void
    {
        $this->displayName = $newDisplayName;
    }

    public function verifyEmail(): void
    {
        $this->emailVerified = true;
    }

    public function grantRole(UserRole $role): void
    {
        foreach ($this->roles as $existingRole) {
            if ($existingRole->equals($role)) {
                return; // Already has this role
            }
        }
        $this->roles[] = $role;
    }

    public function revokeRole(UserRole $role): void
    {
        $this->roles = array_filter(
            $this->roles,
            fn(UserRole $r) => !$r->equals($role)
        );

        // Ensure user always has at least 'user' role
        if (empty($this->roles)) {
            $this->roles = [UserRole::user()];
        }
    }

    public function hasRole(UserRole $role): bool
    {
        foreach ($this->roles as $userRole) {
            if ($userRole->equals($role)) {
                return true;
            }
        }
        return false;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::admin());
    }

    public function isPremium(): bool
    {
        return $this->hasRole(UserRole::premium());
    }

    public function updatePhotoURL(?string $url): void
    {
        $this->photoURL = $url;
    }

    // Getters

    public function id(): UserId
    {
        return $this->id;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function displayName(): DisplayName
    {
        return $this->displayName;
    }

    /** @return UserRole[] */
    public function roles(): array
    {
        return $this->roles;
    }

    public function rolesAsStrings(): array
    {
        return array_map(
            fn(UserRole $role) => $role->toString(),
            $this->roles
        );
    }

    public function photoURL(): ?string
    {
        return $this->photoURL;
    }

    public function isEmailVerified(): bool
    {
        return $this->emailVerified;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Convert to array for persistence or API response
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id->toString(),
            'email' => $this->email->toString(),
            'displayName' => $this->displayName->toString(),
            'roles' => $this->rolesAsStrings(),
            'photoURL' => $this->photoURL,
            'emailVerified' => $this->emailVerified,
            'createdAt' => $this->createdAt->format('c'),
        ];
    }
}
