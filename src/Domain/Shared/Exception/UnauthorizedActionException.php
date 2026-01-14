<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

/**
 * Exception thrown when an operation is not allowed for the current user.
 */
final class UnauthorizedActionException extends DomainException
{
    public static function forAction(string $action): self
    {
        return new self(sprintf('Not allowed to %s this resource', $action));
    }
}
