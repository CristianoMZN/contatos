<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

use App\Domain\Shared\Exception\InvalidArgumentException;

/**
 * Money Value Object
 * 
 * Immutable value object representing monetary values
 */
final readonly class Money
{
    private function __construct(
        private int $amount,
        private string $currency
    ) {
        $this->validate();
    }

    /**
     * Create Money from amount in cents
     */
    public static function fromCents(int $cents, string $currency = 'BRL'): self
    {
        return new self($cents, strtoupper($currency));
    }

    /**
     * Create Money from decimal amount
     */
    public static function fromFloat(float $amount, string $currency = 'BRL'): self
    {
        return new self((int) round($amount * 100), strtoupper($currency));
    }

    /**
     * Validate money constraints
     */
    private function validate(): void
    {
        if ($this->amount < 0) {
            throw new InvalidArgumentException('Amount cannot be negative');
        }

        if (empty($this->currency)) {
            throw new InvalidArgumentException('Currency cannot be empty');
        }

        if (strlen($this->currency) !== 3) {
            throw new InvalidArgumentException('Currency must be a 3-letter ISO code');
        }
    }

    /**
     * Get amount in cents
     */
    public function amount(): int
    {
        return $this->amount;
    }

    /**
     * Get amount as decimal
     */
    public function toFloat(): float
    {
        return $this->amount / 100;
    }

    /**
     * Get currency code
     */
    public function currency(): string
    {
        return $this->currency;
    }

    /**
     * Format money for display
     */
    public function formatted(): string
    {
        $value = number_format($this->toFloat(), 2, ',', '.');

        return match ($this->currency) {
            'BRL' => 'R$ ' . $value,
            'USD' => '$ ' . $value,
            'EUR' => '€ ' . $value,
            default => $this->currency . ' ' . $value,
        };
    }

    /**
     * Add money
     */
    public function add(self $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount + $other->amount, $this->currency);
    }

    /**
     * Subtract money
     */
    public function subtract(self $other): self
    {
        $this->assertSameCurrency($other);
        $newAmount = $this->amount - $other->amount;

        if ($newAmount < 0) {
            throw new InvalidArgumentException('Subtraction would result in negative amount');
        }

        return new self($newAmount, $this->currency);
    }

    /**
     * Multiply by factor
     */
    public function multiply(float $factor): self
    {
        if ($factor < 0) {
            throw new InvalidArgumentException('Factor cannot be negative');
        }

        return new self((int) round($this->amount * $factor), $this->currency);
    }

    /**
     * Check if greater than other money
     */
    public function greaterThan(self $other): bool
    {
        $this->assertSameCurrency($other);
        return $this->amount > $other->amount;
    }

    /**
     * Check if less than other money
     */
    public function lessThan(self $other): bool
    {
        $this->assertSameCurrency($other);
        return $this->amount < $other->amount;
    }

    /**
     * Check if equal to other money
     */
    public function equals(self $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }

    /**
     * Check if zero
     */
    public function isZero(): bool
    {
        return $this->amount === 0;
    }

    /**
     * Assert same currency for operations
     */
    private function assertSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException(
                sprintf(
                    'Cannot perform operation with different currencies: %s and %s',
                    $this->currency,
                    $other->currency
                )
            );
        }
    }

    /**
     * String representation
     */
    public function __toString(): string
    {
        return $this->formatted();
    }
}
