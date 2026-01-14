<?php

declare(strict_types=1);

namespace App\Application\UseCase\User;

use App\Domain\User\ValueObject\Email;
use App\Infrastructure\Firebase\Auth\FirebaseAuthService;

/**
 * Reset Password Use Case
 * Sends password reset email via Firebase
 */
final class ResetPasswordUseCase
{
    public function __construct(
        private FirebaseAuthService $firebaseAuth
    ) {
    }

    /**
     * Execute password reset
     * Always succeeds for security (don't reveal if email exists)
     */
    public function execute(string $email): void
    {
        try {
            $emailVO = Email::fromString($email);
            $this->firebaseAuth->sendPasswordResetEmail($emailVO);
        } catch (\Exception $e) {
            // Silent fail for security - don't reveal if email exists
            error_log('Password reset request: ' . $e->getMessage());
        }
    }
}
