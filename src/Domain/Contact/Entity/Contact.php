<?php

declare(strict_types=1);

namespace App\Domain\Contact\Entity;

use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Contact\Event\ContactCreated;
use App\Domain\Contact\Event\ContactUpdated;
use App\Domain\Contact\ValueObject\ContactId;
use App\Domain\Shared\Entity\AggregateRoot;
use App\Domain\Shared\ValueObject\Address;
use App\Domain\Shared\ValueObject\Email;
use App\Domain\Shared\ValueObject\GeoLocation;
use App\Domain\Shared\ValueObject\Phone;
use App\Domain\Shared\ValueObject\Slug;
use App\Domain\User\ValueObject\UserId;
use DateTimeImmutable;

/**
 * Contact Aggregate Root
 * 
 * Represents a contact belonging to a user
 */
final class Contact extends AggregateRoot
{
    private function __construct(
        private ContactId $id,
        private UserId $userId,
        private string $name,
        private Email $email,
        private ?Phone $phone,
        private ?Address $address,
        private ?CategoryId $categoryId,
        private ?Slug $slug,
        private ?GeoLocation $location,
        private string $notes,
        private bool $isFavorite,
        private bool $isPublic,
        private ?string $photoUrl,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt
    ) {
    }

    /**
     * Create a new contact
     */
    public static function create(
        ContactId $id,
        UserId $userId,
        string $name,
        Email $email,
        ?Phone $phone = null,
        bool $isPublic = false
    ): self {
        $contact = new self(
            $id,
            $userId,
            $name,
            $email,
            $phone,
            null,
            null,
            null,
            null,
            '',
            false,
            $isPublic,
            null,
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $contact->recordEvent(new ContactCreated($id, $userId, $name, $email));

        return $contact;
    }

    /**
     * Reconstruct Contact from persistence
     */
    public static function fromPrimitives(
        string $id,
        string $userId,
        string $name,
        string $email,
        ?string $phone,
        ?array $address,
        ?string $categoryId,
        ?string $slug,
        ?array $location,
        string $notes,
        bool $isFavorite,
        bool $isPublic,
        ?string $photoUrl,
        string $createdAt,
        string $updatedAt
    ): self {
        return new self(
            ContactId::fromString($id),
            UserId::fromString($userId),
            $name,
            Email::fromString($email),
            $phone ? Phone::fromString($phone) : null,
            $address ? Address::fromArray($address) : null,
            $categoryId ? CategoryId::fromString($categoryId) : null,
            $slug ? Slug::fromString($slug) : null,
            $location ? GeoLocation::fromArray($location) : null,
            $notes,
            $isFavorite,
            $isPublic,
            $photoUrl,
            new DateTimeImmutable($createdAt),
            new DateTimeImmutable($updatedAt)
        );
    }

    /**
     * Update basic contact information
     */
    public function updateBasicInfo(
        string $name,
        Email $email,
        ?Phone $phone = null
    ): void {
        $hasChanges = false;

        if ($this->name !== $name) {
            $this->name = $name;
            $hasChanges = true;
        }

        if (!$this->email->equals($email)) {
            $this->email = $email;
            $hasChanges = true;
        }

        if ($phone && (!$this->phone || !$this->phone->equals($phone))) {
            $this->phone = $phone;
            $hasChanges = true;
        } elseif (!$phone && $this->phone) {
            $this->phone = null;
            $hasChanges = true;
        }

        if ($hasChanges) {
            $this->updatedAt = new DateTimeImmutable();
            $this->recordEvent(new ContactUpdated($this->id));
        }
    }

    /**
     * Set contact address
     */
    public function setAddress(Address $address): void
    {
        $this->address = $address;

        // Update location if address has coordinates
        if ($address->hasCoordinates()) {
            $this->location = $address->location();
        }

        $this->updatedAt = new DateTimeImmutable();
        $this->recordEvent(new ContactUpdated($this->id));
    }

    /**
     * Set geographic location
     */
    public function setLocation(GeoLocation $location): void
    {
        $this->location = $location;
        $this->updatedAt = new DateTimeImmutable();
        $this->recordEvent(new ContactUpdated($this->id));
    }

    /**
     * Assign contact to category
     */
    public function assignToCategory(?CategoryId $categoryId): void
    {
        if ($categoryId && $this->categoryId && $this->categoryId->equals($categoryId)) {
            return;
        }

        $this->categoryId = $categoryId;
        $this->updatedAt = new DateTimeImmutable();
        $this->recordEvent(new ContactUpdated($this->id));
    }

    /**
     * Set public slug for contact
     */
    public function setSlug(Slug $slug): void
    {
        $this->slug = $slug;
        $this->updatedAt = new DateTimeImmutable();
        $this->recordEvent(new ContactUpdated($this->id));
    }

    /**
     * Update notes
     */
    public function updateNotes(string $notes): void
    {
        $this->notes = $notes;
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * Mark contact as favorite
     */
    public function markAsFavorite(): void
    {
        if ($this->isFavorite) {
            return;
        }

        $this->isFavorite = true;
        $this->updatedAt = new DateTimeImmutable();
        $this->recordEvent(new ContactUpdated($this->id));
    }

    /**
     * Unmark contact as favorite
     */
    public function unmarkAsFavorite(): void
    {
        if (!$this->isFavorite) {
            return;
        }

        $this->isFavorite = false;
        $this->updatedAt = new DateTimeImmutable();
        $this->recordEvent(new ContactUpdated($this->id));
    }

    /**
     * Make contact public
     */
    public function makePublic(): void
    {
        if ($this->isPublic) {
            return;
        }

        $this->isPublic = true;
        $this->updatedAt = new DateTimeImmutable();
        $this->recordEvent(new ContactUpdated($this->id));
    }

    /**
     * Make contact private
     */
    public function makePrivate(): void
    {
        if (!$this->isPublic) {
            return;
        }

        $this->isPublic = false;
        $this->slug = null; // Remove slug when making private
        $this->updatedAt = new DateTimeImmutable();
        $this->recordEvent(new ContactUpdated($this->id));
    }

    /**
     * Set photo URL
     */
    public function setPhotoUrl(string $photoUrl): void
    {
        $this->photoUrl = $photoUrl;
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * Remove photo
     */
    public function removePhoto(): void
    {
        $this->photoUrl = null;
        $this->updatedAt = new DateTimeImmutable();
    }

    // Getters

    public function id(): ContactId
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

    public function email(): Email
    {
        return $this->email;
    }

    public function phone(): ?Phone
    {
        return $this->phone;
    }

    public function address(): ?Address
    {
        return $this->address;
    }

    public function categoryId(): ?CategoryId
    {
        return $this->categoryId;
    }

    public function slug(): ?Slug
    {
        return $this->slug;
    }

    public function location(): ?GeoLocation
    {
        return $this->location;
    }

    public function notes(): string
    {
        return $this->notes;
    }

    public function isFavorite(): bool
    {
        return $this->isFavorite;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function photoUrl(): ?string
    {
        return $this->photoUrl;
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
