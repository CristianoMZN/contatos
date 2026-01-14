<?php

declare(strict_types=1);

namespace App\Domain\Subscription\Event;

use App\Domain\Shared\Event\DomainEvent;
use App\Domain\Subscription\ValueObject\SubscriptionId;
use App\Domain\User\ValueObject\UserId;
use DateTimeImmutable;

/**
 * Domain Event: Subscription was created
 */
final class SubscriptionCreated implements DomainEvent
{
    private string $eventId;
    private DateTimeImmutable $occurredOn;

    public function __construct(
        private readonly SubscriptionId $subscriptionId,
        private readonly UserId $userId,
        private readonly string $plan
    ) {
        $this->eventId = uniqid('event_', true);
        $this->occurredOn = new DateTimeImmutable();
    }

    public function eventId(): string
    {
        return $this->eventId;
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }

    public function subscriptionId(): SubscriptionId
    {
        return $this->subscriptionId;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function plan(): string
    {
        return $this->plan;
    }

    public function toArray(): array
    {
        return [
            'eventId' => $this->eventId,
            'occurredOn' => $this->occurredOn->format('c'),
            'subscriptionId' => $this->subscriptionId->value(),
            'userId' => $this->userId->value(),
            'plan' => $this->plan,
        ];
    }
}
