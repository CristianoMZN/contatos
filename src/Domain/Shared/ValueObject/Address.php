<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

use App\Domain\Shared\Exception\InvalidArgumentException;

/**
 * Address Value Object
 * 
 * Immutable value object representing a physical address
 */
final readonly class Address
{
    private function __construct(
        private string $street,
        private string $number,
        private ?string $complement,
        private string $neighborhood,
        private string $city,
        private string $state,
        private string $zipCode,
        private string $country,
        private ?GeoLocation $location
    ) {
        $this->validate();
    }

    /**
     * Create Address from components
     */
    public static function create(
        string $street,
        string $number,
        ?string $complement,
        string $neighborhood,
        string $city,
        string $state,
        string $zipCode,
        string $country = 'Brasil',
        ?GeoLocation $location = null
    ): self {
        return new self(
            trim($street),
            trim($number),
            $complement ? trim($complement) : null,
            trim($neighborhood),
            trim($city),
            trim($state),
            preg_replace('/[^0-9]/', '', $zipCode),
            trim($country),
            $location
        );
    }

    /**
     * Create Address from array
     */
    public static function fromArray(array $data): self
    {
        $location = null;
        if (isset($data['latitude']) && isset($data['longitude'])) {
            $location = GeoLocation::fromCoordinates(
                (float) $data['latitude'],
                (float) $data['longitude']
            );
        }

        return self::create(
            $data['street'] ?? '',
            $data['number'] ?? '',
            $data['complement'] ?? null,
            $data['neighborhood'] ?? '',
            $data['city'] ?? '',
            $data['state'] ?? '',
            $data['zipCode'] ?? '',
            $data['country'] ?? 'Brasil',
            $location
        );
    }

    /**
     * Validate address components
     */
    private function validate(): void
    {
        if (empty($this->street)) {
            throw new InvalidArgumentException('Street cannot be empty');
        }

        if (empty($this->number)) {
            throw new InvalidArgumentException('Number cannot be empty');
        }

        if (empty($this->city)) {
            throw new InvalidArgumentException('City cannot be empty');
        }

        if (empty($this->state)) {
            throw new InvalidArgumentException('State cannot be empty');
        }

        if (empty($this->zipCode)) {
            throw new InvalidArgumentException('Zip code cannot be empty');
        }

        // Brazilian zip code validation (8 digits)
        if ($this->country === 'Brasil' && strlen($this->zipCode) !== 8) {
            throw new InvalidArgumentException('Brazilian zip code must have 8 digits');
        }
    }

    public function street(): string
    {
        return $this->street;
    }

    public function number(): string
    {
        return $this->number;
    }

    public function complement(): ?string
    {
        return $this->complement;
    }

    public function neighborhood(): string
    {
        return $this->neighborhood;
    }

    public function city(): string
    {
        return $this->city;
    }

    public function state(): string
    {
        return $this->state;
    }

    public function zipCode(): string
    {
        return $this->zipCode;
    }

    /**
     * Get formatted Brazilian zip code (12345-678)
     */
    public function formattedZipCode(): string
    {
        if ($this->country === 'Brasil' && strlen($this->zipCode) === 8) {
            return substr($this->zipCode, 0, 5) . '-' . substr($this->zipCode, 5);
        }
        return $this->zipCode;
    }

    public function country(): string
    {
        return $this->country;
    }

    public function location(): ?GeoLocation
    {
        return $this->location;
    }

    /**
     * Check if address has coordinates
     */
    public function hasCoordinates(): bool
    {
        return $this->location !== null;
    }

    /**
     * Get full address as single line
     */
    public function fullAddress(): string
    {
        $parts = [
            $this->street . ', ' . $this->number,
        ];

        if ($this->complement) {
            $parts[] = $this->complement;
        }

        $parts[] = $this->neighborhood;
        $parts[] = $this->city . ' - ' . $this->state;
        $parts[] = $this->formattedZipCode();
        $parts[] = $this->country;

        return implode(', ', $parts);
    }

    /**
     * Compare with another Address
     */
    public function equals(self $other): bool
    {
        return $this->street === $other->street
            && $this->number === $other->number
            && $this->complement === $other->complement
            && $this->neighborhood === $other->neighborhood
            && $this->city === $other->city
            && $this->state === $other->state
            && $this->zipCode === $other->zipCode
            && $this->country === $other->country;
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'street' => $this->street,
            'number' => $this->number,
            'complement' => $this->complement,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'state' => $this->state,
            'zipCode' => $this->zipCode,
            'country' => $this->country,
            'latitude' => $this->location?->latitude(),
            'longitude' => $this->location?->longitude(),
        ];
    }

    /**
     * String representation
     */
    public function __toString(): string
    {
        return $this->fullAddress();
    }
}
