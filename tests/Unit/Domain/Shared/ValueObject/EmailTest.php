<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\ValueObject;

use App\Domain\Shared\Exception\InvalidArgumentException;
use App\Domain\Shared\ValueObject\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function test_creates_valid_email(): void
    {
        $email = Email::fromString('user@example.com');

        $this->assertEquals('user@example.com', $email->value());
    }

    public function test_normalizes_email_to_lowercase(): void
    {
        $email = Email::fromString('  USER@EXAMPLE.COM  ');

        $this->assertEquals('user@example.com', $email->value());
    }

    public function test_extracts_domain(): void
    {
        $email = Email::fromString('user@example.com');

        $this->assertEquals('example.com', $email->domain());
    }

    public function test_extracts_local_part(): void
    {
        $email = Email::fromString('user@example.com');

        $this->assertEquals('user', $email->localPart());
    }

    public function test_throws_exception_for_empty_email(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Email cannot be empty');

        Email::fromString('');
    }

    public function test_throws_exception_for_invalid_format(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('is not a valid email address');

        Email::fromString('invalid-email');
    }

    public function test_throws_exception_for_too_long_email(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('cannot exceed 255 characters');

        Email::fromString(str_repeat('a', 250) . '@example.com');
    }

    public function test_equals_compares_emails(): void
    {
        $email1 = Email::fromString('user@example.com');
        $email2 = Email::fromString('user@example.com');
        $email3 = Email::fromString('other@example.com');

        $this->assertTrue($email1->equals($email2));
        $this->assertFalse($email1->equals($email3));
    }

    public function test_to_string_returns_value(): void
    {
        $email = Email::fromString('user@example.com');

        $this->assertEquals('user@example.com', (string) $email);
    }
}
