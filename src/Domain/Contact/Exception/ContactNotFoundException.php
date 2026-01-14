<?php

declare(strict_types=1);

namespace App\Domain\Contact\Exception;

use App\Domain\Contact\ValueObject\ContactId;
use App\Domain\Shared\Exception\DomainException;

/**
 * Exception thrown when a contact is not found
 */
final class ContactNotFoundException extends DomainException
{
    public static function withId(ContactId $id): self
    {
        return new self(sprintf('Contact with ID "%s" was not found', $id->value()));
    }

    public static function withSlug(string $slug): self
    {
        return new self(sprintf('Contact with slug "%s" was not found', $slug));
    }
}
