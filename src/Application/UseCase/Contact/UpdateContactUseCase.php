<?php

declare(strict_types=1);

namespace App\Application\UseCase\Contact;

use App\Application\UseCase\Contact\DTO\UpdateContactInput;
use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Contact\Exception\ContactNotFoundException;
use App\Domain\Contact\Entity\Contact;
use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Domain\Contact\ValueObject\ContactId;
use App\Domain\Shared\ValueObject\Address;
use App\Domain\Shared\ValueObject\Email;
use App\Domain\Shared\ValueObject\GeoLocation;
use App\Domain\Shared\ValueObject\Phone;
use App\Domain\Shared\ValueObject\Slug;
use App\Domain\Shared\Exception\UnauthorizedActionException;
use App\Domain\User\ValueObject\UserId;
use App\Infrastructure\Storage\FirebaseStorageService;

/**
 * Use case for updating contact data in Firestore.
 */
final class UpdateContactUseCase
{
    public function __construct(
        private readonly ContactRepositoryInterface $contactRepository,
        private readonly ?FirebaseStorageService $storage = null
    ) {
    }

    public function execute(UpdateContactInput $input): Contact
    {
        $contactId = ContactId::fromString($input->contactId);
        $contact = $this->contactRepository->findById($contactId);

        if (!$contact) {
            throw ContactNotFoundException::withId($contactId);
        }

        if (!$contact->userId()->equals(UserId::fromString($input->userId))) {
            throw UnauthorizedActionException::forAction('update');
        }

        if ($input->name !== null || $input->email !== null || $input->phone !== null) {
            $contact->updateBasicInfo(
                $input->name ?? $contact->name(),
                $input->email ? Email::fromString($input->email) : $contact->email(),
                $input->phone ? Phone::fromString($input->phone) : $contact->phone()
            );
        }

        if ($input->categoryId !== null) {
            $contact->assignToCategory(
                $input->categoryId !== '' ? CategoryId::fromString($input->categoryId) : null
            );
        }

        if ($input->address) {
            $contact->setAddress(Address::fromArray($input->address));
        }

        if ($input->location) {
            $contact->setLocation(GeoLocation::fromArray($input->location));
        }

        if ($input->notes !== null) {
            $contact->updateNotes($input->notes);
        }

        if ($input->isPublic !== null) {
            $input->isPublic ? $contact->makePublic() : $contact->makePrivate();
        }

        if ($input->slug) {
            $contact->setSlug(Slug::fromString($input->slug));
        }

        if ($input->isFavorite !== null) {
            $input->isFavorite ? $contact->markAsFavorite() : $contact->unmarkAsFavorite();
        }

        if ($input->photoPath && $this->storage) {
            $photoUrl = $this->storage->uploadContactPhoto($contactId->value(), $input->photoPath);
            $contact->setPhotoUrl($photoUrl);
        }

        if ($input->removePhoto) {
            $contact->removePhoto();
        }

        $this->contactRepository->save($contact);

        return $contact;
    }
}
