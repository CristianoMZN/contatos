<?php

declare(strict_types=1);

namespace App\Application\UseCase\Subscription;

use App\Application\Service\Payment\PaymentGatewayInterface;
use App\Application\UseCase\Subscription\DTO\UpgradeSubscriptionInput;
use App\Application\UseCase\Subscription\DTO\UpgradeSubscriptionOutput;
use App\Domain\Shared\ValueObject\Money;
use App\Domain\Subscription\Entity\Subscription;
use App\Domain\Subscription\Repository\SubscriptionRepositoryInterface;
use App\Domain\Subscription\ValueObject\SubscriptionId;
use App\Domain\User\ValueObject\UserId;
use DateTimeImmutable;

/**
 * Orchestrates subscription upgrade using ASAAS gateway.
 */
final class UpgradeSubscriptionUseCase
{
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private PaymentGatewayInterface $paymentGateway
    ) {
    }

    public function execute(UpgradeSubscriptionInput $input): UpgradeSubscriptionOutput
    {
        $amount = Money::fromFloat($input->amount, 'BRL');

        $paymentResult = $this->paymentGateway->createSubscription(
            customerId: $input->userId,
            customerEmail: $input->customerEmail,
            plan: $input->plan,
            amount: $amount
        );

        $subscriptionIdValue = $paymentResult->subscriptionId();

        if ($subscriptionIdValue === '') {
            $subscriptionIdValue = $this->subscriptionRepository->nextIdentity()->value();
        }

        $subscriptionId = SubscriptionId::fromString($subscriptionIdValue);

        $now = new DateTimeImmutable();
        $endDate = (new DateTimeImmutable())->modify('+1 month');

        $subscription = Subscription::create(
            id: $subscriptionId,
            userId: UserId::fromString($input->userId),
            plan: $input->plan,
            amount: $amount,
            startDate: $now,
            endDate: $endDate
        );

        if (!$paymentResult->isApproved()) {
            $subscription->cancel();
        }

        $this->subscriptionRepository->save($subscription);

        return new UpgradeSubscriptionOutput(
            subscriptionId: $subscriptionIdValue,
            status: $paymentResult->status(),
            paymentUrl: $paymentResult->paymentUrl(),
            active: $subscription->isActive()
        );
    }
}
