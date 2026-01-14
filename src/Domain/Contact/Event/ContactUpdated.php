<?php

declare(strict_types=1);

namespace App\Domain\Contact\Event;

use App\Domain\Contact\ValueObject\ContactId;
use App\Domain\Shared\Event\DomainEvent;
use DateTimeImmutable;

/**
 * Domain Event: Contact was updated
 */
final class ContactUpdated implements DomainEvent
{
    private string $eventId;
    private DateTimeImmutable $occurredOn;

    public function __construct(
        private readonly ContactId $contactId
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

    public function toArray(): array
    {
        return [
            'eventId' => $this->eventId,
            'occurredOn' => $this->occurredOn->format('c'),
            'contactId' => $this->contactId->value(),
        ];
    }
}
