<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\ValueObject;

use App\Domain\Shared\Exception\InvalidArgumentException;
use App\Domain\Shared\ValueObject\Phone;
use PHPUnit\Framework\TestCase;

class PhoneTest extends TestCase
{
    public function test_creates_brazilian_mobile_phone(): void
    {
        $phone = Phone::fromString('11987654321');

        $this->assertEquals('11987654321', $phone->value());
        $this->assertEquals('55', $phone->countryCode());
        $this->assertEquals('11', $phone->areaCode());
    }

    public function test_creates_phone_with_formatting(): void
    {
        $phone = Phone::fromString('(11) 98765-4321');

        $this->assertEquals('11987654321', $phone->value());
    }

    public function test_creates_phone_with_country_code(): void
    {
        $phone = Phone::fromString('+55 (11) 98765-4321');

        $this->assertEquals('5511987654321', $phone->value());
        $this->assertEquals('55', $phone->countryCode());
    }

    public function test_formats_brazilian_mobile(): void
    {
        $phone = Phone::fromString('11987654321');

        $this->assertEquals('+55 (11) 98765-4321', $phone->formatted());
    }

    public function test_formats_brazilian_landline(): void
    {
        $phone = Phone::fromString('1187654321'); // 10 digits: 11 + 87654321

        $this->assertEquals('+55 (11) 8765-4321', $phone->formatted());
    }

    public function test_throws_exception_for_too_short(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid phone number length');

        Phone::fromString('123');
    }

    public function test_throws_exception_for_too_long(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid phone number length');

        Phone::fromString(str_repeat('1', 20));
    }

    public function test_equals_compares_phones(): void
    {
        $phone1 = Phone::fromString('11987654321');
        $phone2 = Phone::fromString('(11) 98765-4321');
        $phone3 = Phone::fromString('21987654321');

        $this->assertTrue($phone1->equals($phone2));
        $this->assertFalse($phone1->equals($phone3));
    }

    public function test_to_string_returns_formatted(): void
    {
        $phone = Phone::fromString('11987654321');

        $this->assertEquals('+55 (11) 98765-4321', (string) $phone);
    }
}
