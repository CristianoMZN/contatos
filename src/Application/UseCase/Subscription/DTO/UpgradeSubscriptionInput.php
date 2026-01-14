<?php

declare(strict_types=1);

namespace App\Application\UseCase\Subscription\DTO;

/**
 * Input data for upgrading or creating a subscription.
 */
final readonly class UpgradeSubscriptionInput
{
    public function __construct(
        public string $userId,
        public string $customerEmail,
        public string $plan,
        public float $amount
    ) {
    }
}
