<?php

declare(strict_types=1);

namespace App\Domain\User\Exception;

use DomainException;

/**
 * Exception thrown when user is not found
 */
class UserNotFoundException extends DomainException
{
    public function __construct(string $message = 'User not found')
    {
        parent::__construct($message);
    }
}
