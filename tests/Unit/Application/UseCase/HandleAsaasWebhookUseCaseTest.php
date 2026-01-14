<?php

declare(strict_types=1);

namespace Tests\Unit\Application\UseCase;

use App\Application\UseCase\Subscription\HandleAsaasWebhookUseCase;
use App\Domain\Shared\ValueObject\Money;
use App\Domain\Subscription\Entity\Subscription;
use App\Domain\Subscription\Repository\SubscriptionRepositoryInterface;
use App\Domain\Subscription\ValueObject\SubscriptionId;
use App\Domain\User\ValueObject\UserId;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class HandleAsaasWebhookUseCaseTest extends TestCase
{
    private InMemorySubscriptionRepository $repository;
    private HandleAsaasWebhookUseCase $useCase;
    private Subscription $subscription;

    protected function setUp(): void
    {
        $this->repository = new InMemorySubscriptionRepository();
        $this->useCase = new HandleAsaasWebhookUseCase($this->repository);

        $this->subscription = Subscription::create(
            id: SubscriptionId::generate(),
            userId: UserId::fromString('user_1'),
            plan: 'premium',
            amount: Money::fromFloat(29.90),
            startDate: new DateTimeImmutable(),
            endDate: new DateTimeImmutable('+30 days')
        );

        $this->repository->save($this->subscription);
    }

    public function test_payment_confirmed_renews_subscription(): void
    {
        $nextDueDate = (new DateTimeImmutable('+40 days'))->format('Y-m-d');

        $handled = $this->useCase->execute([
            'event' => 'PAYMENT_CONFIRMED',
            'payment' => [
                'subscription' => $this->subscription->id()->value(),
                'nextDueDate' => $nextDueDate,
            ],
        ]);

        $this->assertTrue($handled);

        $reloaded = $this->repository->findById($this->subscription->id());
        $this->assertNotNull($reloaded);
        $this->assertTrue($reloaded->isActive());
        $this->assertSame($nextDueDate, $reloaded->endDate()->format('Y-m-d'));
    }

    public function test_cancel_event_marks_subscription_inactive(): void
    {
        $handled = $this->useCase->execute([
            'event' => 'SUBSCRIPTION_CANCELED',
            'subscription' => $this->subscription->id()->value(),
        ]);

        $this->assertTrue($handled);

        $reloaded = $this->repository->findById($this->subscription->id());
        $this->assertNotNull($reloaded);
        $this->assertFalse($reloaded->isActive());
    }
}

/**
 * Simple in-memory repository for testing webhooks.
 */
final class InMemorySubscriptionRepository implements SubscriptionRepositoryInterface
{
    /** @var array<string, Subscription> */
    private array $items = [];

    public function save(Subscription $subscription): void
    {
        $this->items[$subscription->id()->value()] = $subscription;
    }

    public function findById(SubscriptionId $id): ?Subscription
    {
        return $this->items[$id->value()] ?? null;
    }

    public function findActiveByUser(UserId $userId): ?Subscription
    {
        foreach ($this->items as $subscription) {
            if ($subscription->userId()->equals($userId) && $subscription->isActive()) {
                return $subscription;
            }
        }

        return null;
    }

    public function findByUser(UserId $userId): array
    {
        return array_values(array_filter(
            $this->items,
            static fn(Subscription $subscription): bool => $subscription->userId()->equals($userId)
        ));
    }

    public function delete(SubscriptionId $id): void
    {
        unset($this->items[$id->value()]);
    }

    public function exists(SubscriptionId $id): bool
    {
        return isset($this->items[$id->value()]);
    }

    public function nextIdentity(): SubscriptionId
    {
        return SubscriptionId::generate();
    }
}
