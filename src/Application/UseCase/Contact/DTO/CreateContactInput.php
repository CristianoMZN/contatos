<?php

declare(strict_types=1);

namespace App\Application\UseCase\Contact\DTO;

/**
 * Input DTO for creating a contact in Firestore.
 */
final readonly class CreateContactInput
{
    public function __construct(
        public string $userId,
        public string $name,
        public string $email,
        public ?string $phone = null,
        public bool $isPublic = false,
        public ?string $categoryId = null,
        public ?array $address = null,
        public ?array $location = null,
        public string $notes = '',
        public ?string $photoPath = null,
        public ?string $slug = null,
        public bool $isFavorite = false
    ) {
    }
}
