<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\User\ValueObject;

use App\Domain\User\ValueObject\UserRole;
use PHPUnit\Framework\TestCase;

class UserRoleTest extends TestCase
{
    public function test_creates_user_role(): void
    {
        $role = UserRole::user();
        
        $this->assertEquals('user', $role->toString());
        $this->assertTrue($role->isUser());
        $this->assertFalse($role->isAdmin());
        $this->assertFalse($role->isPremium());
    }

    public function test_creates_admin_role(): void
    {
        $role = UserRole::admin();
        
        $this->assertEquals('admin', $role->toString());
        $this->assertFalse($role->isUser());
        $this->assertTrue($role->isAdmin());
        $this->assertFalse($role->isPremium());
    }

    public function test_creates_premium_role(): void
    {
        $role = UserRole::premium();
        
        $this->assertEquals('premium', $role->toString());
        $this->assertFalse($role->isUser());
        $this->assertFalse($role->isAdmin());
        $this->assertTrue($role->isPremium());
    }

    public function test_creates_role_from_string(): void
    {
        $role = UserRole::fromString('admin');
        
        $this->assertTrue($role->isAdmin());
    }

    public function test_normalizes_role_string(): void
    {
        $role = UserRole::fromString('  ADMIN  ');
        
        $this->assertEquals('admin', $role->toString());
    }

    public function test_throws_exception_for_invalid_role(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        UserRole::fromString('invalid_role');
    }

    public function test_equals_compares_roles(): void
    {
        $role1 = UserRole::user();
        $role2 = UserRole::user();
        $role3 = UserRole::admin();
        
        $this->assertTrue($role1->equals($role2));
        $this->assertFalse($role1->equals($role3));
    }

    public function test_role_is_immutable(): void
    {
        $role = UserRole::user();
        $reflection = new \ReflectionClass($role);
        
        $this->assertTrue($reflection->isReadOnly());
    }
}
