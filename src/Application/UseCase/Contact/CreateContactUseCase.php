<?php

declare(strict_types=1);

namespace App\Application\UseCase\Contact;

use App\Application\UseCase\Contact\DTO\CreateContactInput;
use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Contact\Entity\Contact;
use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Domain\Shared\ValueObject\Address;
use App\Domain\Shared\ValueObject\Email;
use App\Domain\Shared\ValueObject\GeoLocation;
use App\Domain\Shared\ValueObject\Phone;
use App\Domain\Shared\ValueObject\Slug;
use App\Domain\User\ValueObject\UserId;
use App\Infrastructure\Storage\FirebaseStorageService;

/**
 * Use case responsible for creating contacts in Firestore.
 */
final class CreateContactUseCase
{
    public function __construct(
        private readonly ContactRepositoryInterface $contactRepository,
        private readonly ?FirebaseStorageService $storage = null
    ) {
    }

    public function execute(CreateContactInput $input): Contact
    {
        $contactId = $this->contactRepository->nextIdentity();

        $contact = Contact::create(
            $contactId,
            UserId::fromString($input->userId),
            $input->name,
            Email::fromString($input->email),
            $input->phone ? Phone::fromString($input->phone) : null,
            $input->isPublic
        );

        if ($input->categoryId) {
            $contact->assignToCategory(CategoryId::fromString($input->categoryId));
        }

        if ($input->slug !== null) {
            $contact->setSlug(Slug::fromString($input->slug));
        } elseif ($input->isPublic) {
            $baseSlug = Slug::fromString($input->name)->value();
            $uniqueSlug = sprintf('%s-%s', $baseSlug, substr($contactId->value(), -6));
            $contact->setSlug(Slug::fromString($uniqueSlug));
        }

        if ($input->address) {
            $contact->setAddress(Address::fromArray($input->address));
        }

        if ($input->location) {
            $contact->setLocation(GeoLocation::fromArray($input->location));
        }

        if ($input->notes !== '') {
            $contact->updateNotes($input->notes);
        }

        if ($input->isFavorite) {
            $contact->markAsFavorite();
        }

        if ($input->photoPath && $this->storage) {
            $photoUrl = $this->storage->uploadContactPhoto($contactId->value(), $input->photoPath);
            $contact->setPhotoUrl($photoUrl);
        }

        $this->contactRepository->save($contact);

        return $contact;
    }
}
