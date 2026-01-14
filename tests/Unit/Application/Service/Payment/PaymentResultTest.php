<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Service\Payment;

use App\Application\Service\Payment\PaymentResult;
use PHPUnit\Framework\TestCase;

final class PaymentResultTest extends TestCase
{
    public function test_it_maps_active_status_as_approved(): void
    {
        $result = PaymentResult::fromAsaasResponse([
            'id' => 'sub_123',
            'status' => 'ACTIVE',
            'invoiceUrl' => 'https://pay.test/sub_123',
        ]);

        $this->assertSame('sub_123', $result->subscriptionId());
        $this->assertTrue($result->isApproved());
        $this->assertSame('ACTIVE', $result->status());
        $this->assertSame('https://pay.test/sub_123', $result->paymentUrl());
    }

    public function test_it_handles_pending_status(): void
    {
        $result = PaymentResult::fromAsaasResponse([
            'subscription' => 'sub_456',
            'status' => 'PENDING',
        ]);

        $this->assertFalse($result->isApproved());
        $this->assertSame('sub_456', $result->subscriptionId());
    }
}
