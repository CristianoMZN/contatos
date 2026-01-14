<?php

declare(strict_types=1);

namespace App\Application\Service\Payment;

use App\Application\Service\Payment\PaymentResult;
use App\Domain\Shared\ValueObject\Money;

/**
 * Payment Gateway contract for subscription billing.
 */
interface PaymentGatewayInterface
{
    /**
     * Create or initiate a subscription charge.
     */
    public function createSubscription(
        string $customerId,
        string $customerEmail,
        string $plan,
        Money $amount
    ): PaymentResult;
}
