<?php

declare(strict_types=1);

namespace App\Application\Service\FeatureFlag;

/**
 * Immutable feature flags for a user.
 */
final readonly class FeatureFlags
{
    public function __construct(
        private bool $isPremium,
        private int $contactLimit,
        private int $storageLimitMb,
        private ?string $plan = null
    ) {
    }

    public function isPremium(): bool
    {
        return $this->isPremium;
    }

    public function contactLimit(): int
    {
        return $this->contactLimit;
    }

    public function storageLimitMb(): int
    {
        return $this->storageLimitMb;
    }

    public function plan(): ?string
    {
        return $this->plan;
    }
}
