<?php

declare(strict_types=1);

namespace App\Domain\Contact\Event;

use App\Domain\Contact\ValueObject\ContactId;
use App\Domain\Shared\Event\DomainEvent;
use App\Domain\User\ValueObject\UserId;
use DateTimeImmutable;

/**
 * Domain Event: Contact was deleted
 */
final class ContactDeleted implements DomainEvent
{
    private string $eventId;
    private DateTimeImmutable $occurredOn;

    public function __construct(
        private readonly ContactId $contactId,
        private readonly UserId $userId
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

    public function contactId(): ContactId
    {
        return $this->contactId;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function toArray(): array
    {
        return [
            'eventId' => $this->eventId,
            'occurredOn' => $this->occurredOn->format('c'),
            'contactId' => $this->contactId->value(),
            'userId' => $this->userId->value(),
        ];
    }
}
