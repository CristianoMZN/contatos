<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\DTO;

/**
 * Auth Result Output DTO
 */
final readonly class AuthResult
{
    public function __construct(
        public string $uid,
        public string $email,
        public string $displayName,
        public string $token,
        public array $roles,
        public bool $emailVerified,
        public ?string $photoURL = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            uid: $data['uid'],
            email: $data['email'],
            displayName: $data['displayName'],
            token: $data['token'],
            roles: $data['roles'] ?? ['user'],
            emailVerified: $data['emailVerified'] ?? false,
            photoURL: $data['photoURL'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'uid' => $this->uid,
            'email' => $this->email,
            'displayName' => $this->displayName,
            'token' => $this->token,
            'roles' => $this->roles,
            'emailVerified' => $this->emailVerified,
            'photoURL' => $this->photoURL,
        ];
    }
}
