<?php

declare(strict_types=1);

namespace App\Domain\Category\Entity;

use App\Domain\Category\Event\CategoryCreated;
use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Shared\Entity\AggregateRoot;
use App\Domain\Shared\ValueObject\Slug;
use App\Domain\User\ValueObject\UserId;
use DateTimeImmutable;

/**
 * Category Aggregate Root
 * 
 * Represents a category for organizing contacts
 */
final class Category extends AggregateRoot
{
    private function __construct(
        private CategoryId $id,
        private UserId $userId,
        private string $name,
        private Slug $slug,
        private ?string $description,
        private ?string $color,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt
    ) {
    }

    /**
     * Create a new category
     */
    public static function create(
        CategoryId $id,
        UserId $userId,
        string $name,
        Slug $slug,
        ?string $description = null,
        ?string $color = null
    ): self {
        $category = new self(
            $id,
            $userId,
            $name,
            $slug,
            $description,
            $color,
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $category->recordEvent(new CategoryCreated($id, $userId, $name));

        return $category;
    }

    /**
     * Reconstruct Category from persistence
     */
    public static function fromPrimitives(
        string $id,
        string $userId,
        string $name,
        string $slug,
        ?string $description,
        ?string $color,
        string $createdAt,
        string $updatedAt
    ): self {
        return new self(
            CategoryId::fromString($id),
            UserId::fromString($userId),
            $name,
            Slug::fromString($slug),
            $description,
            $color,
            new DateTimeImmutable($createdAt),
            new DateTimeImmutable($updatedAt)
        );
    }

    /**
     * Update category information
     */
    public function update(
        string $name,
        ?string $description = null,
        ?string $color = null
    ): void {
        $this->name = $name;
        $this->description = $description;
        $this->color = $color;
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * Change slug
     */
    public function changeSlug(Slug $newSlug): void
    {
        if ($this->slug->equals($newSlug)) {
            return;
        }

        $this->slug = $newSlug;
        $this->updatedAt = new DateTimeImmutable();
    }

    // Getters

    public function id(): CategoryId
    {
        return $this->id;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function slug(): Slug
    {
        return $this->slug;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function color(): ?string
    {
        return $this->color;
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
