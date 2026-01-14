<?php

declare(strict_types=1);

namespace App\Domain\Contact\Repository;

use App\Domain\Contact\Entity\Contact;
use App\Domain\Contact\ValueObject\ContactId;
use App\Domain\Shared\ValueObject\GeoLocation;
use App\Domain\User\ValueObject\UserId;

/**
 * Contact Repository Interface
 * 
 * Defines contract for Contact persistence
 */
interface ContactRepositoryInterface
{
    /**
     * Save contact (insert or update)
     */
    public function save(Contact $contact): void;

    /**
     * Find contact by ID
     */
    public function findById(ContactId $id): ?Contact;

    /**
     * Find all contacts for a user
     * 
     * @return Contact[]
     */
    public function findByUser(
        UserId $userId,
        int $limit = 50,
        int $offset = 0,
        ?string $search = null,
        ?string $categoryId = null
    ): array;

    /**
     * Find favorite contacts for a user
     * 
     * @return Contact[]
     */
    public function findFavoritesByUser(UserId $userId): array;

    /**
     * Find public contacts (paginated)
     * 
     * @return Contact[]
     */
    public function findPublicContacts(
        int $limit = 50,
        ?string $cursor = null,
        ?string $categoryId = null,
        ?string $search = null,
        ?GeoLocation $center = null,
        ?float $radiusKm = null
    ): array;

    /**
     * Find contact by slug (public contacts only)
     */
    public function findBySlug(string $slug): ?Contact;

    /**
     * Find contacts nearby a location
     * 
     * @return Contact[]
     */
    public function findNearbyContacts(
        float $latitude,
        float $longitude,
        float $radiusKm,
        int $limit = 50
    ): array;

    /**
     * Delete a contact
     */
    public function delete(ContactId $id): void;

    /**
     * Check if contact exists
     */
    public function exists(ContactId $id): bool;

    /**
     * Generate next identity
     */
    public function nextIdentity(): ContactId;
}
