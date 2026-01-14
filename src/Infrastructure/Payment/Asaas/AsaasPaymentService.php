<?php

declare(strict_types=1);

namespace App\Infrastructure\Payment\Asaas;

use App\Application\Service\Payment\PaymentGatewayInterface;
use App\Application\Service\Payment\PaymentResult;
use App\Domain\Shared\ValueObject\Money;

/**
 * ASAAS payment gateway implementation.
 */
final class AsaasPaymentService implements PaymentGatewayInterface
{
    public function __construct(
        private AsaasClient $client
    ) {
    }

    public function createSubscription(
        string $customerId,
        string $customerEmail,
        string $plan,
        Money $amount
    ): PaymentResult {
        $payload = [
            'customer' => $customerId,
            'billingType' => 'CREDIT_CARD',
            'value' => $amount->toFloat(),
            'cycle' => 'MONTHLY',
            'description' => sprintf('Plano %s', $plan),
            'nextDueDate' => (new \DateTimeImmutable('+1 day'))->format('Y-m-d'),
        ];

        // ASAAS requires customer email in customer creation; include for idempotency
        if (!empty($customerEmail)) {
            $payload['customerEmail'] = $customerEmail;
        }

        $response = $this->client->post('/subscriptions', $payload);

        return PaymentResult::fromAsaasResponse($response);
    }
}
