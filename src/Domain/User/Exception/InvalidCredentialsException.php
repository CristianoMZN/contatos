<?php

declare(strict_types=1);

namespace App\Domain\User\Exception;

use DomainException;

/**
 * Exception thrown when credentials are invalid
 */
class InvalidCredentialsException extends DomainException
{
    public function __construct(string $message = 'Invalid email or password')
    {
        parent::__construct($message);
    }
}
