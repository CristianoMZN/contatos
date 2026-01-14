<?php

declare(strict_types=1);

namespace App\Application\UseCase\Subscription;

use App\Domain\Subscription\Repository\SubscriptionRepositoryInterface;
use App\Domain\Subscription\ValueObject\SubscriptionId;
use DateTimeImmutable;

/**
 * Process ASAAS webhook events and sync subscription status.
 */
final class HandleAsaasWebhookUseCase
{
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptionRepository
    ) {
    }

    /**
     * @return bool True when a known subscription was updated
     */
    public function execute(array $payload): bool
    {
        $subscriptionId = $this->extractSubscriptionId($payload);

        if ($subscriptionId === null) {
            return false;
        }

        $subscription = $this->subscriptionRepository->findById(
            SubscriptionId::fromString($subscriptionId)
        );

        if ($subscription === null) {
            return false;
        }

        $event = strtoupper($payload['event'] ?? '');

        if (in_array($event, ['PAYMENT_CONFIRMED', 'PAYMENT_RECEIVED', 'SUBSCRIPTION_ACTIVATED'], true)) {
            $endDate = $this->resolveEndDate($payload);
            $subscription->renew($endDate);
        }

        if (in_array($event, ['SUBSCRIPTION_CANCELED', 'PAYMENT_REFUNDED', 'PAYMENT_OVERDUE'], true)) {
            $subscription->cancel();
        }

        $this->subscriptionRepository->save($subscription);

        return true;
    }

    private function extractSubscriptionId(array $payload): ?string
    {
        if (!empty($payload['subscription'])) {
            return (string) $payload['subscription'];
        }

        if (!empty($payload['payment']['subscription'])) {
            return (string) $payload['payment']['subscription'];
        }

        return null;
    }

    private function resolveEndDate(array $payload): DateTimeImmutable
    {
        $candidate = $payload['payment']['nextDueDate'] ?? $payload['nextDueDate'] ?? null;

        if (is_string($candidate) && !empty($candidate)) {
            try {
                return new DateTimeImmutable($candidate);
            } catch (\Exception) {
                // fallback below
            }
        }

        return new DateTimeImmutable('+1 month');
    }
}
