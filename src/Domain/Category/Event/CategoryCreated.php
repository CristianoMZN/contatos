<?php

declare(strict_types=1);

namespace App\Domain\Category\Event;

use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Shared\Event\DomainEvent;
use App\Domain\User\ValueObject\UserId;
use DateTimeImmutable;

/**
 * Domain Event: Category was created
 */
final class CategoryCreated implements DomainEvent
{
    private string $eventId;
    private DateTimeImmutable $occurredOn;

    public function __construct(
        private readonly CategoryId $categoryId,
        private readonly UserId $userId,
        private readonly string $name
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

    public function categoryId(): CategoryId
    {
        return $this->categoryId;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        return [
            'eventId' => $this->eventId,
            'occurredOn' => $this->occurredOn->format('c'),
            'categoryId' => $this->categoryId->value(),
            'userId' => $this->userId->value(),
            'name' => $this->name,
        ];
    }
}
