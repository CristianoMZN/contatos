<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\User\ValueObject;

use App\Domain\User\ValueObject\DisplayName;
use PHPUnit\Framework\TestCase;

class DisplayNameTest extends TestCase
{
    public function test_creates_valid_display_name(): void
    {
        $name = DisplayName::fromString('João Silva');
        
        $this->assertEquals('João Silva', $name->toString());
    }

    public function test_trims_whitespace(): void
    {
        $name = DisplayName::fromString('  João Silva  ');
        
        $this->assertEquals('João Silva', $name->toString());
    }

    public function test_throws_exception_for_short_name(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        DisplayName::fromString('A');
    }

    public function test_throws_exception_for_long_name(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        DisplayName::fromString(str_repeat('A', 101));
    }

    public function test_accepts_minimum_length(): void
    {
        $name = DisplayName::fromString('AB');
        
        $this->assertEquals('AB', $name->toString());
    }

    public function test_accepts_maximum_length(): void
    {
        $longName = str_repeat('A', 100);
        $name = DisplayName::fromString($longName);
        
        $this->assertEquals($longName, $name->toString());
    }

    public function test_equals_compares_names(): void
    {
        $name1 = DisplayName::fromString('João Silva');
        $name2 = DisplayName::fromString('João Silva');
        $name3 = DisplayName::fromString('Maria Santos');
        
        $this->assertTrue($name1->equals($name2));
        $this->assertFalse($name1->equals($name3));
    }

    public function test_display_name_is_immutable(): void
    {
        $name = DisplayName::fromString('João Silva');
        $reflection = new \ReflectionClass($name);
        
        $this->assertTrue($reflection->isReadOnly());
    }
}
