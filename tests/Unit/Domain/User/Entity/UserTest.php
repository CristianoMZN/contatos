<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\User\Entity;

use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\UserId;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\DisplayName;
use App\Domain\User\ValueObject\UserRole;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test_creates_new_user(): void
    {
        $user = User::create(
            UserId::generate(),
            Email::fromString('user@example.com'),
            DisplayName::fromString('João Silva')
        );

        $this->assertEquals('user@example.com', $user->email()->toString());
        $this->assertEquals('João Silva', $user->displayName()->toString());
        $this->assertFalse($user->isEmailVerified());
        $this->assertCount(1, $user->roles());
        $this->assertTrue($user->roles()[0]->isUser());
    }

    public function test_reconstitutes_user_from_primitives(): void
    {
        $user = User::fromPrimitives(
            id: 'user123',
            email: 'user@example.com',
            displayName: 'João Silva',
            createdAt: '2024-01-01T00:00:00+00:00',
            roles: ['user', 'premium'],
            photoURL: 'https://example.com/photo.jpg',
            emailVerified: true
        );

        $this->assertEquals('user123', $user->id()->toString());
        $this->assertEquals('user@example.com', $user->email()->toString());
        $this->assertTrue($user->isEmailVerified());
        $this->assertEquals('https://example.com/photo.jpg', $user->photoURL());
        $this->assertCount(2, $user->roles());
    }

    public function test_changes_display_name(): void
    {
        $user = User::create(
            UserId::generate(),
            Email::fromString('user@example.com'),
            DisplayName::fromString('Old Name')
        );

        $user->changeDisplayName(DisplayName::fromString('New Name'));

        $this->assertEquals('New Name', $user->displayName()->toString());
    }

    public function test_verifies_email(): void
    {
        $user = User::create(
            UserId::generate(),
            Email::fromString('user@example.com'),
            DisplayName::fromString('João Silva')
        );

        $this->assertFalse($user->isEmailVerified());

        $user->verifyEmail();

        $this->assertTrue($user->isEmailVerified());
    }

    public function test_grants_role(): void
    {
        $user = User::create(
            UserId::generate(),
            Email::fromString('user@example.com'),
            DisplayName::fromString('João Silva')
        );

        $user->grantRole(UserRole::premium());

        $this->assertTrue($user->isPremium());
        $this->assertCount(2, $user->roles()); // user + premium
    }

    public function test_does_not_duplicate_roles(): void
    {
        $user = User::create(
            UserId::generate(),
            Email::fromString('user@example.com'),
            DisplayName::fromString('João Silva')
        );

        $user->grantRole(UserRole::user());
        $user->grantRole(UserRole::user());

        $this->assertCount(1, $user->roles());
    }

    public function test_revokes_role(): void
    {
        $user = User::create(
            UserId::generate(),
            Email::fromString('user@example.com'),
            DisplayName::fromString('João Silva')
        );

        $user->grantRole(UserRole::premium());
        $this->assertTrue($user->isPremium());

        $user->revokeRole(UserRole::premium());
        $this->assertFalse($user->isPremium());
    }

    public function test_always_keeps_user_role(): void
    {
        $user = User::create(
            UserId::generate(),
            Email::fromString('user@example.com'),
            DisplayName::fromString('João Silva')
        );

        $user->revokeRole(UserRole::user());

        $this->assertCount(1, $user->roles());
        $this->assertTrue($user->roles()[0]->isUser());
    }

    public function test_checks_role(): void
    {
        $user = User::create(
            UserId::generate(),
            Email::fromString('user@example.com'),
            DisplayName::fromString('João Silva')
        );

        $this->assertTrue($user->hasRole(UserRole::user()));
        $this->assertFalse($user->hasRole(UserRole::admin()));
    }

    public function test_is_admin(): void
    {
        $user = User::create(
            UserId::generate(),
            Email::fromString('admin@example.com'),
            DisplayName::fromString('Admin User')
        );

        $user->grantRole(UserRole::admin());

        $this->assertTrue($user->isAdmin());
    }

    public function test_updates_photo_url(): void
    {
        $user = User::create(
            UserId::generate(),
            Email::fromString('user@example.com'),
            DisplayName::fromString('João Silva')
        );

        $this->assertNull($user->photoURL());

        $user->updatePhotoURL('https://example.com/photo.jpg');

        $this->assertEquals('https://example.com/photo.jpg', $user->photoURL());
    }

    public function test_converts_to_array(): void
    {
        $user = User::create(
            UserId::fromString('user123'),
            Email::fromString('user@example.com'),
            DisplayName::fromString('João Silva')
        );

        $array = $user->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('user123', $array['id']);
        $this->assertEquals('user@example.com', $array['email']);
        $this->assertEquals('João Silva', $array['displayName']);
        $this->assertIsArray($array['roles']);
        $this->assertContains('user', $array['roles']);
    }
}
