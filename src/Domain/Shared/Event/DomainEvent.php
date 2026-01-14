<?php

declare(strict_types=1);

namespace App\Domain\Shared\Event;

use DateTimeImmutable;

/**
 * Interface for all domain events
 */
interface DomainEvent
{
    /**
     * Unique identifier for this event
     */
    public function eventId(): string;

    /**
     * When this event occurred
     */
    public function occurredOn(): DateTimeImmutable;

    /**
     * Convert event to array for serialization
     */
    public function toArray(): array;
}
