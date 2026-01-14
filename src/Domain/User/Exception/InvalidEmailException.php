<?php

declare(strict_types=1);

namespace App\Domain\User\Exception;

use DomainException;

/**
 * Exception thrown when email format is invalid
 */
class InvalidEmailException extends DomainException
{
    public function __construct(string $message = 'Invalid email address')
    {
        parent::__construct($message);
    }
}
