<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Domain\Shared\Entity\AggregateRoot;
use App\Domain\Shared\ValueObject\Email;
use App\Domain\User\Event\UserRegistered;
use App\Domain\User\ValueObject\UserId;
use DateTimeImmutable;

/**
 * User Aggregate Root
 * 
 * Represents a user in the system with authentication and profile information
 */
final class User extends AggregateRoot
{
    private function __construct(
        private UserId $id,
        private Email $email,
        private string $displayName,
        private string $passwordHash,
        private bool $isActive,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt
    ) {
    }

    /**
     * Register a new user
     */
    public static function register(
        UserId $id,
        Email $email,
        string $displayName,
        string $passwordHash
    ): self {
        $user = new self(
            $id,
            $email,
            $displayName,
            $passwordHash,
            true,
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $user->recordEvent(new UserRegistered($id, $email, $displayName));

        return $user;
    }

    /**
     * Reconstruct User from persistence
     */
    public static function fromPrimitives(
        string $id,
        string $email,
        string $displayName,
        string $passwordHash,
        bool $isActive,
        string $createdAt,
        string $updatedAt
    ): self {
        return new self(
            UserId::fromString($id),
            Email::fromString($email),
            $displayName,
            $passwordHash,
            $isActive,
            new DateTimeImmutable($createdAt),
            new DateTimeImmutable($updatedAt)
        );
    }

    /**
     * Change email address
     */
    public function changeEmail(Email $newEmail): void
    {
        if ($this->email->equals($newEmail)) {
            return;
        }

        $this->email = $newEmail;
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * Update display name
     */
    public function updateDisplayName(string $newDisplayName): void
    {
        if (empty($newDisplayName)) {
            throw new \InvalidArgumentException('Display name cannot be empty');
        }

        if ($this->displayName === $newDisplayName) {
            return;
        }

        $this->displayName = $newDisplayName;
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * Change password
     */
    public function changePassword(string $newPasswordHash): void
    {
        $this->passwordHash = $newPasswordHash;
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * Deactivate user account
     */
    public function deactivate(): void
    {
        if (!$this->isActive) {
            return;
        }

        $this->isActive = false;
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * Reactivate user account
     */
    public function reactivate(): void
    {
        if ($this->isActive) {
            return;
        }

        $this->isActive = true;
        $this->updatedAt = new DateTimeImmutable();
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

    public function displayName(): string
    {
        return $this->displayName;
    }

    public function passwordHash(): string
    {
        return $this->passwordHash;
    }

    public function isActive(): bool
    {
        return $this->isActive;
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
