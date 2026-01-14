<?php

declare(strict_types=1);

namespace App\Application\UseCase\Contact\DTO;

/**
 * Input DTO for updating a contact.
 */
final readonly class UpdateContactInput
{
    public function __construct(
        public string $contactId,
        public string $userId,
        public ?string $name = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $categoryId = null,
        public ?array $address = null,
        public ?array $location = null,
        public ?string $notes = null,
        public ?bool $isPublic = null,
        public ?bool $isFavorite = null,
        public ?string $photoPath = null,
        public bool $removePhoto = false,
        public ?string $slug = null
    ) {
    }
}
