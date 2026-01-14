<?php

declare(strict_types=1);

namespace App\Application\UseCase\Contact;

use App\Application\UseCase\Contact\DTO\ContactListFilter;
use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Domain\Shared\ValueObject\GeoLocation;

/**
 * Use case to list public contacts (agenda pública) with filters.
 */
final class ListPublicContactsUseCase
{
    public function __construct(
        private readonly ContactRepositoryInterface $contactRepository
    ) {
    }

    public function execute(ContactListFilter $filter): array
    {
        $center = null;
        if ($filter->latitude !== null && $filter->longitude !== null) {
            $center = GeoLocation::fromCoordinates($filter->latitude, $filter->longitude);
        }

        return $this->contactRepository->findPublicContacts(
            limit: $filter->limit,
            cursor: $filter->cursor,
            categoryId: $filter->categoryId,
            search: $filter->search,
            center: $center,
            radiusKm: $filter->radiusKm
        );
    }
}
