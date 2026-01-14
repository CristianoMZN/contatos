<?php

declare(strict_types=1);

namespace App\Application\Service\Payment;

/**
 * Immutable payment result for subscription operations.
 */
final readonly class PaymentResult
{
    private function __construct(
        private string $subscriptionId,
        private string $status,
        private ?string $paymentUrl,
        private bool $approved
    ) {
    }

    public static function fromAsaasResponse(array $response): self
    {
        $status = strtoupper($response['status'] ?? 'PENDING');
        $approvedStatuses = ['ACTIVE', 'RECEIVED', 'RECEIVED_IN_CASH', 'CONFIRMED'];

        return new self(
            subscriptionId: (string) ($response['id'] ?? $response['subscription'] ?? ''),
            status: $status,
            paymentUrl: $response['invoiceUrl'] ?? $response['paymentLink'] ?? null,
            approved: in_array($status, $approvedStatuses, true)
        );
    }

    public function subscriptionId(): string
    {
        return $this->subscriptionId;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function paymentUrl(): ?string
    {
        return $this->paymentUrl;
    }

    public function isApproved(): bool
    {
        return $this->approved;
    }
}
