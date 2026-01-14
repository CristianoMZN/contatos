<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

use App\Domain\Shared\Exception\InvalidArgumentException;

/**
 * Phone Value Object
 * 
 * Immutable value object representing a phone number with Brazilian format support
 */
final readonly class Phone
{
    private function __construct(
        private string $value,
        private string $countryCode,
        private string $areaCode,
        private string $number
    ) {
    }

    /**
     * Create Phone from string
     * 
     * Supports formats:
     * - (11) 98765-4321
     * - +55 (11) 98765-4321
     * - 11987654321
     * - +5511987654321
     */
    public static function fromString(string $value): self
    {
        $cleaned = preg_replace('/[^0-9]/', '', $value);

        if (strlen($cleaned) < 10 || strlen($cleaned) > 15) {
            throw new InvalidArgumentException(
                sprintf('Invalid phone number length: %s', $value)
            );
        }

        // Parse Brazilian format: +55 (11) 98765-4321
        if (strlen($cleaned) === 13 && str_starts_with($cleaned, '55')) {
            // +5511987654321
            $countryCode = '55';
            $areaCode = substr($cleaned, 2, 2);
            $number = substr($cleaned, 4);
        } elseif (strlen($cleaned) === 11) {
            // 11987654321
            $countryCode = '55';
            $areaCode = substr($cleaned, 0, 2);
            $number = substr($cleaned, 2);
        } elseif (strlen($cleaned) === 10) {
            // 1198765432 (8 digits)
            $countryCode = '55';
            $areaCode = substr($cleaned, 0, 2);
            $number = substr($cleaned, 2);
        } else {
            // International generic
            $countryCode = substr($cleaned, 0, strlen($cleaned) - 10);
            $areaCode = substr($cleaned, -10, 3);
            $number = substr($cleaned, -7);
        }

        return new self($cleaned, $countryCode, $areaCode, $number);
    }

    /**
     * Get the phone value (only numbers)
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Get formatted phone number
     */
    public function formatted(): string
    {
        if ($this->countryCode === '55') {
            if (strlen($this->number) === 9) {
                // Brazilian mobile: +55 (11) 98765-4321
                return sprintf(
                    '+%s (%s) %s-%s',
                    $this->countryCode,
                    $this->areaCode,
                    substr($this->number, 0, 5),
                    substr($this->number, 5)
                );
            } elseif (strlen($this->number) === 8) {
                // Brazilian landline: +55 (11) 8765-4321
                return sprintf(
                    '+%s (%s) %s-%s',
                    $this->countryCode,
                    $this->areaCode,
                    substr($this->number, 0, 4),
                    substr($this->number, 4)
                );
            }
        }

        // International format
        return sprintf('+%s %s %s', $this->countryCode, $this->areaCode, $this->number);
    }

    /**
     * Get country code
     */
    public function countryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * Get area code
     */
    public function areaCode(): string
    {
        return $this->areaCode;
    }

    /**
     * Compare with another Phone
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * String representation
     */
    public function __toString(): string
    {
        return $this->formatted();
    }
}
