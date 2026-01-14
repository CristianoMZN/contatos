<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

use App\Domain\Shared\Exception\InvalidArgumentException;

/**
 * GeoLocation Value Object
 * 
 * Immutable value object representing geographical coordinates
 */
final readonly class GeoLocation
{
    private const EARTH_RADIUS_KM = 6371;

    private function __construct(
        private float $latitude,
        private float $longitude
    ) {
        $this->validate();
    }

    /**
     * Create GeoLocation from coordinates
     */
    public static function fromCoordinates(float $latitude, float $longitude): self
    {
        return new self($latitude, $longitude);
    }

    /**
     * Create GeoLocation from array
     */
    public static function fromArray(array $data): self
    {
        if (!isset($data['latitude']) || !isset($data['longitude'])) {
            throw new InvalidArgumentException('Missing latitude or longitude');
        }

        return new self(
            (float) $data['latitude'],
            (float) $data['longitude']
        );
    }

    /**
     * Validate coordinates
     */
    private function validate(): void
    {
        if ($this->latitude < -90 || $this->latitude > 90) {
            throw new InvalidArgumentException(
                sprintf('Latitude must be between -90 and 90, got: %f', $this->latitude)
            );
        }

        if ($this->longitude < -180 || $this->longitude > 180) {
            throw new InvalidArgumentException(
                sprintf('Longitude must be between -180 and 180, got: %f', $this->longitude)
            );
        }
    }

    public function latitude(): float
    {
        return $this->latitude;
    }

    public function longitude(): float
    {
        return $this->longitude;
    }

    /**
     * Calculate distance to another location using Haversine formula
     * 
     * @return float Distance in kilometers
     */
    public function distanceTo(self $other): float
    {
        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($other->latitude);
        $lonTo = deg2rad($other->longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) ** 2 +
            cos($latFrom) * cos($latTo) *
            sin($lonDelta / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_KM * $c;
    }

    /**
     * Check if location is within radius from center
     */
    public function isWithinRadius(self $center, float $radiusKm): bool
    {
        return $this->distanceTo($center) <= $radiusKm;
    }

    /**
     * Compare with another GeoLocation (using small tolerance for floating point)
     */
    public function equals(self $other): bool
    {
        return abs($this->latitude - $other->latitude) < 0.000001
            && abs($this->longitude - $other->longitude) < 0.000001;
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }

    /**
     * String representation
     */
    public function __toString(): string
    {
        return sprintf('%.6f, %.6f', $this->latitude, $this->longitude);
    }
}
