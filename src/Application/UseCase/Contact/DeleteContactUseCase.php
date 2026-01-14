<?php

declare(strict_types=1);

namespace App\Application\UseCase\Contact;

use App\Domain\Contact\Exception\ContactNotFoundException;
use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Domain\Contact\ValueObject\ContactId;
use App\Domain\Shared\Exception\UnauthorizedActionException;
use App\Domain\User\ValueObject\UserId;
use App\Infrastructure\Storage\FirebaseStorageService;

/**
 * Use case for deleting contacts from Firestore (and their photos).
 */
final class DeleteContactUseCase
{
    public function __construct(
        private readonly ContactRepositoryInterface $contactRepository,
        private readonly ?FirebaseStorageService $storage = null
    ) {
    }

    public function execute(string $contactId, string $userId): void
    {
        $id = ContactId::fromString($contactId);
        $contact = $this->contactRepository->findById($id);

        if (!$contact) {
            throw ContactNotFoundException::withId($id);
        }

        if (!$contact->userId()->equals(UserId::fromString($userId))) {
            throw UnauthorizedActionException::forAction('delete');
        }

        if ($contact->photoUrl() && $this->storage) {
            $this->storage->deleteContactPhoto($contact->photoUrl());
        }

        $this->contactRepository->delete($id);
    }
}
