<?php

declare(strict_types=1);

namespace App\Application\UseCase\User;

use App\Application\UseCase\User\DTO\RegisterUserInput;
use App\Application\UseCase\User\DTO\AuthResult;
use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserId;
use App\Domain\User\ValueObject\DisplayName;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Infrastructure\Firebase\Auth\FirebaseAuthService;
use App\Domain\User\Exception\UserAlreadyExistsException;

/**
 * Register User Use Case
 * Creates a new user in Firebase Auth and Firestore
 */
final class RegisterUserUseCase
{
    public function __construct(
        private FirebaseAuthService $firebaseAuth,
        private UserRepositoryInterface $userRepository
    ) {
    }

    /**
     * Execute the registration process
     * 
     * @throws UserAlreadyExistsException if email is already registered
     */
    public function execute(RegisterUserInput $input): AuthResult
    {
        // Create value objects
        $email = Email::fromString($input->email);
        $displayName = DisplayName::fromString($input->displayName);

        // Check if user already exists
        if ($this->userRepository->existsByEmail($email)) {
            throw new UserAlreadyExistsException($email->toString());
        }

        // Create user in Firebase Auth
        $uid = $this->firebaseAuth->createUser(
            $email,
            $input->password,
            $displayName
        );

        // Create User entity
        $user = User::create(
            UserId::fromString($uid),
            $email,
            $displayName
        );

        // Save to Firestore
        $this->userRepository->save($user);

        // Send email verification
        $this->firebaseAuth->sendEmailVerification($user->id());

        // Sign in the user to get token
        $authData = $this->firebaseAuth->signInWithEmailAndPassword(
            $email,
            $input->password
        );

        return new AuthResult(
            uid: $user->id()->toString(),
            email: $user->email()->toString(),
            displayName: $user->displayName()->toString(),
            token: $authData['token'],
            roles: $user->rolesAsStrings(),
            emailVerified: false,
            photoURL: $user->photoURL()
        );
    }
}
