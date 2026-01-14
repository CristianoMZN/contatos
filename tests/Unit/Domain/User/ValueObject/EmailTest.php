<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\User\ValueObject;

use App\Domain\User\ValueObject\Email;
use App\Domain\User\Exception\InvalidEmailException;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function test_creates_valid_email(): void
    {
        $email = Email::fromString('user@example.com');
        
        $this->assertEquals('user@example.com', $email->toString());
    }

    public function test_normalizes_email(): void
    {
        $email = Email::fromString('  USER@EXAMPLE.COM  ');
        
        $this->assertEquals('user@example.com', $email->toString());
    }

    public function test_throws_exception_for_invalid_email(): void
    {
        $this->expectException(InvalidEmailException::class);
        
        Email::fromString('invalid-email');
    }

    public function test_equals_compares_emails(): void
    {
        $email1 = Email::fromString('user@example.com');
        $email2 = Email::fromString('user@example.com');
        $email3 = Email::fromString('other@example.com');
        
        $this->assertTrue($email1->equals($email2));
        $this->assertFalse($email1->equals($email3));
    }

    public function test_email_is_immutable(): void
    {
        $email = Email::fromString('user@example.com');
        $reflection = new \ReflectionClass($email);
        
        $this->assertTrue($reflection->isReadOnly());
    }

    /**
     * @dataProvider invalidEmailProvider
     */
    public function test_rejects_invalid_email_formats(string $invalidEmail): void
    {
        $this->expectException(InvalidEmailException::class);
        Email::fromString($invalidEmail);
    }

    public function invalidEmailProvider(): array
    {
        return [
            [''],
            ['not-an-email'],
            ['@example.com'],
            ['user@'],
            ['user @example.com'],
            ['user@example'],
        ];
    }
}
