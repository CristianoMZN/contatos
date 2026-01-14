<?php

declare(strict_types=1);

namespace App\Infrastructure\Firebase\Firestore;

use App\Domain\Shared\ValueObject\Money;
use App\Domain\Subscription\Entity\Subscription;
use App\Domain\Subscription\Repository\SubscriptionRepositoryInterface;
use App\Domain\Subscription\ValueObject\SubscriptionId;
use App\Domain\User\ValueObject\UserId;
use Google\Cloud\Core\Timestamp;
use Google\Cloud\Firestore\FirestoreClient;

/**
 * Firestore repository for Subscription aggregate.
 */
final class FirestoreSubscriptionRepository implements SubscriptionRepositoryInterface
{
    private const COLLECTION = 'subscriptions';

    public function __construct(private FirestoreClient $firestore)
    {
    }

    public function save(Subscription $subscription): void
    {
        $data = $this->mapFromEntity($subscription);

        $this->firestore
            ->collection(self::COLLECTION)
            ->document($subscription->id()->value())
            ->set($data, ['merge' => true]);
    }

    public function findById(SubscriptionId $id): ?Subscription
    {
        $snapshot = $this->firestore
            ->collection(self::COLLECTION)
            ->document($id->value())
            ->snapshot();

        if (!$snapshot->exists()) {
            return null;
        }

        return $this->mapToEntity($snapshot->data(), $snapshot->id());
    }

    public function findActiveByUser(UserId $userId): ?Subscription
    {
        $query = $this->firestore
            ->collection(self::COLLECTION)
            ->where('userId', '=', $userId->value())
            ->where('isActive', '=', true)
            ->limit(1);

        foreach ($query->documents() as $document) {
            return $this->mapToEntity($document->data(), $document->id());
        }

        return null;
    }

    public function findByUser(UserId $userId): array
    {
        $query = $this->firestore
            ->collection(self::COLLECTION)
            ->where('userId', '=', $userId->value());

        $result = [];

        foreach ($query->documents() as $document) {
            $result[] = $this->mapToEntity($document->data(), $document->id());
        }

        return $result;
    }

    public function delete(SubscriptionId $id): void
    {
        $this->firestore
            ->collection(self::COLLECTION)
            ->document($id->value())
            ->delete();
    }

    public function exists(SubscriptionId $id): bool
    {
        $snapshot = $this->firestore
            ->collection(self::COLLECTION)
            ->document($id->value())
            ->snapshot();

        return $snapshot->exists();
    }

    public function nextIdentity(): SubscriptionId
    {
        return SubscriptionId::generate();
    }

    private function mapToEntity(array $data, string $id): Subscription
    {
        $startDate = $data['startDate'] instanceof Timestamp ? $data['startDate']->get() : new \DateTimeImmutable();
        $endDate = $data['endDate'] instanceof Timestamp ? $data['endDate']->get() : new \DateTimeImmutable('+1 month');
        $createdAt = $data['createdAt'] instanceof Timestamp ? $data['createdAt']->get() : new \DateTimeImmutable();
        $updatedAt = $data['updatedAt'] instanceof Timestamp ? $data['updatedAt']->get() : new \DateTimeImmutable();

        $subscription = Subscription::fromPrimitives(
            id: $id,
            userId: $data['userId'],
            plan: $data['plan'],
            amountCents: (int) ($data['amountCents'] ?? 0),
            currency: $data['currency'] ?? 'BRL',
            startDate: $startDate->format('c'),
            endDate: $endDate->format('c'),
            isActive: (bool) ($data['isActive'] ?? false),
            createdAt: $createdAt->format('c'),
            updatedAt: $updatedAt->format('c')
        );

        return $subscription;
    }

    private function mapFromEntity(Subscription $subscription): array
    {
        return [
            'userId' => $subscription->userId()->toString(),
            'plan' => $subscription->plan(),
            'amountCents' => $subscription->amount()->amount(),
            'currency' => $subscription->amount()->currency(),
            'startDate' => new Timestamp($subscription->startDate()),
            'endDate' => new Timestamp($subscription->endDate()),
            'isActive' => $subscription->isActive(),
            'createdAt' => new Timestamp($subscription->createdAt()),
            'updatedAt' => new Timestamp($subscription->updatedAt()),
        ];
    }
}
