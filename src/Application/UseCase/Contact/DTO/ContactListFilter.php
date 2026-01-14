<?php

declare(strict_types=1);

namespace App\Application\UseCase\Contact\DTO;

/**
 * Filter DTO for listing contacts (public or user specific).
 */
final readonly class ContactListFilter
{
    public function __construct(
        public ?string $categoryId = null,
        public ?string $search = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?float $radiusKm = null,
        public int $limit = 50,
        public int $offset = 0,
        public ?string $cursor = null
    ) {
    }
}
