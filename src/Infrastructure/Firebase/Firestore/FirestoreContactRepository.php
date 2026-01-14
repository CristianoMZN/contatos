<?php

declare(strict_types=1);

namespace App\Infrastructure\Firebase\Firestore;

use App\Domain\Contact\Entity\Contact;
use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Domain\Contact\ValueObject\ContactId;
use App\Domain\Shared\ValueObject\GeoLocation;
use App\Domain\User\ValueObject\UserId;
use Google\Cloud\Core\GeoPoint;
use Google\Cloud\Core\Timestamp;
use Google\Cloud\Firestore\FirestoreClient;
use Psr\Log\LoggerInterface;

/**
 * Firestore implementation for Contact repository
 */
final class FirestoreContactRepository implements ContactRepositoryInterface
{
    private const COLLECTION = 'contacts';
    /**
     * Multiplier used to over-fetch batches when applying client-side geofence filters.
     * This mitigates Firestore's lack of native geo queries without requiring extra indexes.
     */
    private const NEARBY_QUERY_MULTIPLIER = 3;

    public function __construct(
        private readonly FirestoreClient $firestore,
        private readonly ?LoggerInterface $logger = null
    ) {
    }

    public function save(Contact $contact): void
    {
        $data = $this->mapFromEntity($contact);

        $this->firestore
            ->collection(self::COLLECTION)
            ->document($contact->id()->value())
            ->set($data, ['merge' => true]);
    }

    public function findById(ContactId $id): ?Contact
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

    public function findByUser(
        UserId $userId,
        int $limit = 50,
        int $offset = 0,
        ?string $search = null,
        ?string $categoryId = null
    ): array {
        $query = $this->firestore
            ->collection(self::COLLECTION)
            ->where('userId', '=', $userId->value());

        if ($categoryId) {
            $query = $query->where('categoryId', '=', $categoryId);
        }

        if ($search) {
            $query = $query->where('searchKeywords', 'array-contains', $this->normalizeSearch($search));
        }

        $query = $query
            ->orderBy('createdAt', 'DESC')
            ->limit($limit)
            ->offset($offset);

        $contacts = [];
        foreach ($query->documents() as $document) {
            $contacts[] = $this->mapToEntity($document->data(), $document->id());
        }

        return $contacts;
    }

    public function findFavoritesByUser(UserId $userId): array
    {
        $query = $this->firestore
            ->collection(self::COLLECTION)
            ->where('userId', '=', $userId->value())
            ->where('isFavorite', '=', true)
            ->orderBy('createdAt', 'DESC');

        $contacts = [];
        foreach ($query->documents() as $document) {
            $contacts[] = $this->mapToEntity($document->data(), $document->id());
        }

        return $contacts;
    }

    public function findPublicContacts(
        int $limit = 50,
        ?string $cursor = null,
        ?string $categoryId = null,
        ?string $search = null,
        ?GeoLocation $center = null,
        ?float $radiusKm = null
    ): array {
        $baseQuery = $this->firestore
            ->collection(self::COLLECTION)
            ->where('isPublic', '=', true);

        if ($categoryId) {
            $baseQuery = $baseQuery->where('categoryId', '=', $categoryId);
        }

        if ($search) {
            $baseQuery = $baseQuery->where('searchKeywords', 'array-contains', $this->normalizeSearch($search));
        }

        $baseQuery = $baseQuery->orderBy('createdAt', 'DESC');

        if ($cursor) {
            $cursorDoc = $this->firestore
                ->collection(self::COLLECTION)
                ->document($cursor)
                ->snapshot();

            if ($cursorDoc->exists()) {
                $baseQuery = $baseQuery->startAfter($cursorDoc);
            }
        }

        $contacts = [];
        $lastSnapshot = null;
        $needsRadiusFilter = $center && $radiusKm !== null && $radiusKm > 0;

        do {
            // Firestore lacks native radius queries; over-fetch small batches and filter client-side.
            $batchLimit = $needsRadiusFilter ? $limit * self::NEARBY_QUERY_MULTIPLIER : $limit;
            $query = $baseQuery->limit($batchLimit);

            if ($lastSnapshot) {
                $query = $query->startAfter($lastSnapshot);
            }

            $documents = $query->documents();
            $batchCount = 0;

            foreach ($documents as $document) {
                $batchCount++;
                $lastSnapshot = $document;

                $contact = $this->mapToEntity($document->data(), $document->id());
                $location = $contact->location();

                if ($needsRadiusFilter && $location) {
                    if (!$location->isWithinRadius($center, $radiusKm)) {
                        continue;
                    }
                }

                $contacts[] = $contact;

                if (count($contacts) >= $limit) {
                    break 2;
                }
            }

            if ($batchCount === 0) {
                break;
            }
        } while ($needsRadiusFilter && count($contacts) < $limit);

        return $contacts;
    }

    public function findBySlug(string $slug): ?Contact
    {
        $documents = $this->firestore
            ->collection(self::COLLECTION)
            ->where('slug', '=', $slug)
            ->where('isPublic', '=', true)
            ->limit(1)
            ->documents();

        foreach ($documents as $document) {
            return $this->mapToEntity($document->data(), $document->id());
        }

        return null;
    }

    public function findNearbyContacts(
        float $latitude,
        float $longitude,
        float $radiusKm,
        int $limit = 50
    ): array {
        $center = GeoLocation::fromCoordinates($latitude, $longitude);

        $baseQuery = $this->firestore
            ->collection(self::COLLECTION)
            ->where('isPublic', '=', true)
            ->orderBy('createdAt', 'DESC');

        $contacts = [];
        $lastSnapshot = null;

        do {
            // Firestore lacks geo-radius queries; over-fetch limited batches and filter in memory.
            $query = $baseQuery->limit($limit * self::NEARBY_QUERY_MULTIPLIER);

            if ($lastSnapshot) {
                $query = $query->startAfter($lastSnapshot);
            }

            $documents = $query->documents();
            $batchCount = 0;

            foreach ($documents as $document) {
                $batchCount++;
                $lastSnapshot = $document;
                $contact = $this->mapToEntity($document->data(), $document->id());

                if ($contact->location() && $contact->location()->isWithinRadius($center, $radiusKm)) {
                    $contacts[] = $contact;
                }

                if (count($contacts) >= $limit) {
                    break 2;
                }
            }

            if ($batchCount === 0) {
                break;
            }
        } while (count($contacts) < $limit);

        return $contacts;
    }

    public function delete(ContactId $id): void
    {
        $this->firestore
            ->collection(self::COLLECTION)
            ->document($id->value())
            ->delete();
    }

    public function exists(ContactId $id): bool
    {
        return $this->firestore
            ->collection(self::COLLECTION)
            ->document($id->value())
            ->snapshot()
            ->exists();
    }

    public function nextIdentity(): ContactId
    {
        return ContactId::generate();
    }

    /**
     * Map Firestore document to Domain entity
     */
    private function mapToEntity(array $data, string $id): Contact
    {
        $locationArray = null;
        if (isset($data['location']) && is_array($data['location'])) {
            $locationArray = $data['location'];
        } elseif (isset($data['location']) && $data['location'] instanceof GeoPoint) {
            $locationArray = [
                'latitude' => $data['location']->latitude(),
                'longitude' => $data['location']->longitude(),
            ];
        }

        return Contact::fromPrimitives(
            id: $id,
            userId: $data['userId'],
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'] ?? null,
            address: $data['address'] ?? null,
            categoryId: $data['categoryId'] ?? null,
            slug: $data['slug'] ?? null,
            location: $locationArray,
            notes: $data['notes'] ?? '',
            isFavorite: (bool) ($data['isFavorite'] ?? false),
            isPublic: (bool) ($data['isPublic'] ?? false),
            photoUrl: $data['photoUrl'] ?? null,
            createdAt: $this->timestampToString($data['createdAt'] ?? null),
            updatedAt: $this->timestampToString($data['updatedAt'] ?? null)
        );
    }

    /**
     * Map Domain entity to Firestore document
     */
    private function mapFromEntity(Contact $contact): array
    {
        $location = $contact->location()?->toArray();
        $address = $contact->address()?->toArray();
        $address = is_array($address) ? $address : [];

        if (!$location && isset($address['latitude'], $address['longitude'])) {
            $location = [
                'latitude' => $address['latitude'],
                'longitude' => $address['longitude'],
            ];
        }

        $data = [
            'userId' => $contact->userId()->value(),
            'name' => $contact->name(),
            'email' => $contact->email()->value(),
            'phone' => $contact->phone()?->value(),
            'address' => $address,
            'categoryId' => $contact->categoryId()?->value(),
            'slug' => $contact->slug()?->value(),
            'location' => $location,
            'notes' => $contact->notes(),
            'isFavorite' => $contact->isFavorite(),
            'isPublic' => $contact->isPublic(),
            'photoUrl' => $contact->photoUrl(),
            'searchKeywords' => $this->buildSearchKeywords($contact),
            'createdAt' => new Timestamp($contact->createdAt()),
            'updatedAt' => new Timestamp($contact->updatedAt()),
        ];

        return $data;
    }

    /**
     * Normalize search term for keyword matching
     */
    private function normalizeSearch(string $search): string
    {
        return mb_strtolower(trim($search));
    }

    /**
     * Build searchable keywords array
     */
    private function buildSearchKeywords(Contact $contact): array
    {
        $keywords = [];

        $keywords[] = $this->normalizeSearch($contact->name());
        if ($contact->slug()) {
            $keywords[] = $contact->slug()->value();
        }
        $keywords[] = $contact->email()->value();

        if ($contact->phone()) {
            $keywords[] = $contact->phone()->value();
        }

        if ($contact->categoryId()) {
            $keywords[] = $contact->categoryId()->value();
        }

        foreach (explode(' ', $contact->name()) as $token) {
            $token = $this->normalizeSearch($token);
            if ($token) {
                $keywords[] = $token;
            }
        }

        return array_values(array_unique(array_filter($keywords)));
    }

    /**
     * Convert Firestore Timestamp or string to ISO8601 string
     */
    private function timestampToString(null|Timestamp|string $timestamp): string
    {
        if ($timestamp instanceof Timestamp) {
            return $timestamp->get()->format('c');
        }

        if (is_string($timestamp) && !empty($timestamp)) {
            $parsed = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $timestamp)
                ?: \DateTimeImmutable::createFromFormat('Y-m-d H:i:sP', $timestamp)
                ?: \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $timestamp, new \DateTimeZone('UTC'));

            if ($parsed instanceof \DateTimeImmutable) {
                return $parsed->format('c');
            }

            if ($this->logger) {
                $this->logger->warning('Invalid timestamp value for contact', ['value' => $timestamp]);
            }
        }

        return (new \DateTimeImmutable())->format('c');
    }

}
