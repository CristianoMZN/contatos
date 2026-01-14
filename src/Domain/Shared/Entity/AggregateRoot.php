<?php

declare(strict_types=1);

namespace App\Domain\Shared\Entity;

use App\Domain\Shared\Event\DomainEvent;

/**
 * Base class for all aggregate roots
 * 
 * Provides event recording capabilities for domain events
 */
abstract class AggregateRoot
{
    /**
     * @var DomainEvent[]
     */
    private array $events = [];

    /**
     * Record a domain event
     */
    protected function recordEvent(DomainEvent $event): void
    {
        $this->events[] = $event;
    }

    /**
     * Get all recorded events and clear them
     * 
     * @return DomainEvent[]
     */
    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }

    /**
     * Check if there are pending events
     */
    public function hasEvents(): bool
    {
        return !empty($this->events);
    }
}
