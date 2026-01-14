<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

use App\Domain\Shared\Event\DomainEvent;
use App\Domain\Shared\ValueObject\Email;
use App\Domain\User\ValueObject\UserId;
use DateTimeImmutable;

/**
 * Domain Event: User was registered
 */
final class UserRegistered implements DomainEvent
{
    private string $eventId;
    private DateTimeImmutable $occurredOn;

    public function __construct(
        private readonly UserId $userId,
        private readonly Email $email,
        private readonly string $displayName
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

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function displayName(): string
    {
        return $this->displayName;
    }

    public function toArray(): array
    {
        return [
            'eventId' => $this->eventId,
            'occurredOn' => $this->occurredOn->format('c'),
            'userId' => $this->userId->value(),
            'email' => $this->email->value(),
            'displayName' => $this->displayName,
        ];
    }
}
