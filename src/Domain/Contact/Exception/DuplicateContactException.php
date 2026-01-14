<?php

declare(strict_types=1);

namespace App\Domain\Contact\Exception;

use App\Domain\Shared\Exception\DomainException;
use App\Domain\Shared\ValueObject\Email;

/**
 * Exception thrown when attempting to create a duplicate contact
 */
final class DuplicateContactException extends DomainException
{
    public static function withEmail(Email $email): self
    {
        return new self(sprintf('Contact with email "%s" already exists', $email->value()));
    }
}
