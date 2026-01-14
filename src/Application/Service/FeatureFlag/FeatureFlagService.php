<?php

declare(strict_types=1);

namespace App\Application\Service\FeatureFlag;

use App\Domain\Subscription\Repository\SubscriptionRepositoryInterface;
use App\Domain\User\ValueObject\UserId;

/**
 * Provides premium feature flags based on subscription status.
 */
final class FeatureFlagService
{
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptionRepository
    ) {
    }

    public function forUser(string $userId): FeatureFlags
    {
        $subscription = $this->subscriptionRepository->findActiveByUser(UserId::fromString($userId));

        $isPremium = $subscription !== null && $subscription->isActive();
        $plan = $subscription?->plan();

        return new FeatureFlags(
            isPremium: $isPremium,
            contactLimit: $isPremium ? 1000 : 100,
            storageLimitMb: $isPremium ? 512 : 50,
            plan: $plan
        );
    }
}
