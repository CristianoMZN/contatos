<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\ValueObject;

use App\Domain\Shared\Exception\InvalidArgumentException;
use App\Domain\Shared\ValueObject\Slug;
use PHPUnit\Framework\TestCase;

class SlugTest extends TestCase
{
    public function test_creates_slug_from_simple_string(): void
    {
        $slug = Slug::fromString('hello world');

        $this->assertEquals('hello-world', $slug->value());
    }

    public function test_removes_accents(): void
    {
        $slug = Slug::fromString('São Paulo');

        $this->assertEquals('sao-paulo', $slug->value());
    }

    public function test_removes_special_characters(): void
    {
        $slug = Slug::fromString('Hello@World#123!');

        $this->assertEquals('hello-world-123', $slug->value());
    }

    public function test_removes_multiple_hyphens(): void
    {
        $slug = Slug::fromString('hello---world');

        $this->assertEquals('hello-world', $slug->value());
    }

    public function test_removes_leading_and_trailing_hyphens(): void
    {
        $slug = Slug::fromString('---hello-world---');

        $this->assertEquals('hello-world', $slug->value());
    }

    public function test_handles_portuguese_characters(): void
    {
        $slug = Slug::fromString('Programação em Português');

        $this->assertEquals('programacao-em-portugues', $slug->value());
    }

    public function test_throws_exception_for_empty_slug(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Slug cannot be empty');

        Slug::fromString('');
    }

    public function test_throws_exception_for_too_long_slug(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('cannot exceed 100 characters');

        Slug::fromString(str_repeat('a', 110));
    }

    public function test_equals_compares_slugs(): void
    {
        $slug1 = Slug::fromString('hello-world');
        $slug2 = Slug::fromString('hello world');
        $slug3 = Slug::fromString('other-slug');

        $this->assertTrue($slug1->equals($slug2));
        $this->assertFalse($slug1->equals($slug3));
    }

    public function test_to_string_returns_value(): void
    {
        $slug = Slug::fromString('hello-world');

        $this->assertEquals('hello-world', (string) $slug);
    }
}
