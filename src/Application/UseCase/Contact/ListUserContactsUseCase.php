<?php

declare(strict_types=1);

namespace App\Application\UseCase\Contact;

use App\Application\UseCase\Contact\DTO\ContactListFilter;
use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Domain\User\ValueObject\UserId;

/**
 * Use case to list authenticated user's contacts.
 */
final class ListUserContactsUseCase
{
    public function __construct(
        private readonly ContactRepositoryInterface $contactRepository
    ) {
    }

    public function execute(string $userId, ContactListFilter $filter): array
    {
        return $this->contactRepository->findByUser(
            userId: UserId::fromString($userId),
            limit: $filter->limit,
            offset: $filter->offset,
            search: $filter->search,
            categoryId: $filter->categoryId
        );
    }
}
