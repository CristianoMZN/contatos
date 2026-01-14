<?php

declare(strict_types=1);

namespace App\Infrastructure\Firebase\Auth;

use Kreait\Firebase\Auth;
use Kreait\Firebase\Auth\SignIn\FailedToSignIn;
use Kreait\Firebase\Exception\Auth\EmailExists;
use Kreait\Firebase\Exception\Auth\UserNotFound;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserId;
use App\Domain\User\ValueObject\DisplayName;
use App\Domain\User\ValueObject\UserRole;
use App\Domain\User\Exception\InvalidCredentialsException;
use App\Domain\User\Exception\UserAlreadyExistsException;
use App\Domain\User\Exception\UserNotFoundException as DomainUserNotFoundException;

/**
 * Firebase Authentication Service
 * Handles all Firebase Auth operations
 */
class FirebaseAuthService
{
    public function __construct(
        private Auth $auth
    ) {
    }

    /**
     * Create a new user in Firebase Auth
     * 
     * @return string Firebase UID
     */
    public function createUser(
        Email $email,
        string $password,
        DisplayName $displayName
    ): string {
        try {
            $userProperties = [
                'email' => $email->toString(),
                'password' => $password,
                'displayName' => $displayName->toString(),
                'emailVerified' => false,
            ];

            $createdUser = $this->auth->createUser($userProperties);

            // Set default custom claims (user role)
            $this->setCustomClaims(
                UserId::fromString($createdUser->uid),
                [UserRole::user()]
            );

            return $createdUser->uid;
        } catch (EmailExists $e) {
            throw new UserAlreadyExistsException($email->toString());
        }
    }

    /**
     * Sign in with email and password
     * 
     * @return array{uid: string, token: string, claims: array}
     */
    public function signInWithEmailAndPassword(
        Email $email,
        string $password
    ): array {
        try {
            $signInResult = $this->auth->signInWithEmailAndPassword(
                $email->toString(),
                $password
            );

            $idToken = $signInResult->idToken();
            $verifiedToken = $this->auth->verifyIdToken($idToken);

            return [
                'uid' => $signInResult->firebaseUserId(),
                'token' => $idToken,
                'claims' => $verifiedToken->claims()->all(),
            ];
        } catch (FailedToSignIn $e) {
            throw new InvalidCredentialsException('Invalid email or password');
        }
    }

    /**
     * Verify Firebase ID token
     * 
     * @return array{uid: string, email: string, claims: array}
     */
    public function verifyIdToken(string $idToken): array
    {
        try {
            $verifiedToken = $this->auth->verifyIdToken($idToken);
            $claims = $verifiedToken->claims();

            return [
                'uid' => $claims->get('sub'),
                'email' => $claims->get('email'),
                'claims' => $claims->all(),
            ];
        } catch (\Exception $e) {
            throw new InvalidCredentialsException('Invalid or expired token');
        }
    }

    /**
     * Set custom claims for a user (roles)
     * 
     * @param UserRole[] $roles
     */
    public function setCustomClaims(UserId $userId, array $roles): void
    {
        $customClaims = [
            'roles' => array_map(
                fn(UserRole $role) => $role->toString(),
                $roles
            ),
        ];

        // Add boolean flags for easy checking
        foreach ($roles as $role) {
            $customClaims[$role->toString()] = true;
        }

        $this->auth->setCustomUserClaims($userId->toString(), $customClaims);
    }

    /**
     * Get user by ID
     */
    public function getUserById(UserId $userId): array
    {
        try {
            $user = $this->auth->getUser($userId->toString());

            return [
                'uid' => $user->uid,
                'email' => $user->email,
                'displayName' => $user->displayName,
                'photoURL' => $user->photoUrl,
                'emailVerified' => $user->emailVerified,
                'customClaims' => $user->customClaims,
            ];
        } catch (UserNotFound $e) {
            throw new DomainUserNotFoundException();
        }
    }

    /**
     * Delete user from Firebase Auth
     */
    public function deleteUser(UserId $userId): void
    {
        try {
            $this->auth->deleteUser($userId->toString());
        } catch (UserNotFound $e) {
            throw new DomainUserNotFoundException();
        }
    }

    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail(Email $email): void
    {
        try {
            $this->auth->sendPasswordResetLink($email->toString());
        } catch (\Exception $e) {
            // Silent fail for security (don't reveal if email exists)
            error_log('Password reset error: ' . $e->getMessage());
        }
    }

    /**
     * Send email verification
     */
    public function sendEmailVerification(UserId $userId): void
    {
        try {
            $this->auth->sendEmailVerificationLink(
                $userId->toString()
            );
        } catch (\Exception $e) {
            error_log('Email verification error: ' . $e->getMessage());
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(
        UserId $userId,
        ?DisplayName $displayName = null,
        ?string $photoURL = null
    ): void {
        $properties = [];

        if ($displayName !== null) {
            $properties['displayName'] = $displayName->toString();
        }

        if ($photoURL !== null) {
            $properties['photoUrl'] = $photoURL;
        }

        if (!empty($properties)) {
            $this->auth->updateUser($userId->toString(), $properties);
        }
    }
}
