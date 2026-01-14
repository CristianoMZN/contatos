<?php

declare(strict_types=1);

namespace App\Domain\User\Exception;

use DomainException;

/**
 * Exception thrown when user already exists
 */
class UserAlreadyExistsException extends DomainException
{
    public function __construct(string $email)
    {
        parent::__construct("User with email '{$email}' already exists");
    }
}
