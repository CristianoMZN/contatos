<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\DTO;

/**
 * Register User Input DTO
 */
final readonly class RegisterUserInput
{
    public function __construct(
        public string $email,
        public string $password,
        public string $displayName
    ) {
    }
}
