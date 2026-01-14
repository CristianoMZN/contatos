<?php

declare(strict_types=1);

namespace App\Application\UseCase\User;

use App\Application\UseCase\User\DTO\LoginUserInput;
use App\Application\UseCase\User\DTO\AuthResult;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Infrastructure\Firebase\Auth\FirebaseAuthService;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\Exception\InvalidCredentialsException;

/**
 * Login User Use Case
 * Authenticates user with Firebase and returns auth data
 */
final class LoginUserUseCase
{
    public function __construct(
        private FirebaseAuthService $firebaseAuth,
        private UserRepositoryInterface $userRepository
    ) {
    }

    /**
     * Execute the login process
     * 
     * @throws InvalidCredentialsException if credentials are invalid
     */
    public function execute(LoginUserInput $input): AuthResult
    {
        $email = Email::fromString($input->email);

        // Authenticate with Firebase
        $authData = $this->firebaseAuth->signInWithEmailAndPassword(
            $email,
            $input->password
        );

        // Get user from Firestore
        $user = $this->userRepository->findByEmail($email);

        if ($user === null) {
            throw new UserNotFoundException('User profile not found');
        }

        // Extract roles from custom claims
        $roles = $authData['claims']['roles'] ?? ['user'];

        return new AuthResult(
            uid: $user->id()->toString(),
            email: $user->email()->toString(),
            displayName: $user->displayName()->toString(),
            token: $authData['token'],
            roles: $roles,
            emailVerified: $user->isEmailVerified(),
            photoURL: $user->photoURL()
        );
    }
}
