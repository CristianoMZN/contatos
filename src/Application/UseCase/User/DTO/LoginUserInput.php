<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\DTO;

/**
 * Login User Input DTO
 */
final readonly class LoginUserInput
{
    public function __construct(
        public string $email,
        public string $password
    ) {
    }
}
