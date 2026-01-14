<?php

declare(strict_types=1);

namespace App\Application\UseCase\Subscription\DTO;

/**
 * Output data for subscription upgrade flow.
 */
final readonly class UpgradeSubscriptionOutput
{
    public function __construct(
        public string $subscriptionId,
        public string $status,
        public ?string $paymentUrl,
        public bool $active
    ) {
    }
}
