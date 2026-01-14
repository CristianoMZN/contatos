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

/**
 * Firestore implementation for Contact repository
 */
final class FirestoreContactRepository implements ContactRepositoryInterface
{
    private const COLLECTION = 'contacts';

    public function __construct(
        private readonly FirestoreClient $firestore
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
        $query = $this->firestore
            ->collection(self::COLLECTION)
            ->where('isPublic', '=', true);

        if ($categoryId) {
            $query = $query->where('categoryId', '=', $categoryId);
        }

        if ($search) {
            $query = $query->where('searchKeywords', 'array-contains', $this->normalizeSearch($search));
        }

        if ($center && $radiusKm !== null && $radiusKm > 0) {
            [$latRange, $lonRange] = $this->calculateBoundingBox($center, $radiusKm);

            $query = $query
                ->where('location.latitude', '>=', $latRange[0])
                ->where('location.latitude', '<=', $latRange[1])
                ->where('location.longitude', '>=', $lonRange[0])
                ->where('location.longitude', '<=', $lonRange[1]);
        }

        $query = $query->orderBy('createdAt', 'DESC')->limit($limit);

        if ($cursor) {
            $cursorDoc = $this->firestore
                ->collection(self::COLLECTION)
                ->document($cursor)
                ->snapshot();

            if ($cursorDoc->exists()) {
                $query = $query->startAfter($cursorDoc);
            }
        }

        $contacts = [];
        foreach ($query->documents() as $document) {
            $contact = $this->mapToEntity($document->data(), $document->id());

            if ($center && $radiusKm !== null && $radiusKm > 0 && $contact->location()) {
                if (!$contact->location()->isWithinRadius($center, $radiusKm)) {
                    continue;
                }
            }

            $contacts[] = $contact;
        }

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
        [$latRange, $lonRange] = $this->calculateBoundingBox($center, $radiusKm);

        $query = $this->firestore
            ->collection(self::COLLECTION)
            ->where('isPublic', '=', true)
            ->where('location.latitude', '>=', $latRange[0])
            ->where('location.latitude', '<=', $latRange[1])
            ->where('location.longitude', '>=', $lonRange[0])
            ->where('location.longitude', '<=', $lonRange[1])
            ->limit($limit);

        $contacts = [];
        foreach ($query->documents() as $document) {
            $contact = $this->mapToEntity($document->data(), $document->id());

            if ($contact->location() && $contact->location()->isWithinRadius($center, $radiusKm)) {
                $contacts[] = $contact;
            }
        }

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
        return $this->findById($id) !== null;
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

        if (!$location && $address && isset($address['latitude'], $address['longitude'])) {
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

        if ($location) {
            $data['geopoint'] = new GeoPoint(
                (float) $location['latitude'],
                (float) $location['longitude']
            );
        }

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
        $keywords[] = $contact->slug()?->value();
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
            return (new \DateTimeImmutable($timestamp))->format('c');
        }

        return (new \DateTimeImmutable())->format('c');
    }

    /**
     * Calculate bounding box for radius search
     *
     * @return array{0: array{0: float, 1: float}, 1: array{0: float, 1: float}}
     */
    private function calculateBoundingBox(GeoLocation $center, float $radiusKm): array
    {
        $lat = $center->latitude();
        $lon = $center->longitude();
        $latDelta = rad2deg($radiusKm / 6371);
        $lonDelta = rad2deg($radiusKm / (6371 * cos(deg2rad($lat))));

        return [
            [$lat - $latDelta, $lat + $latDelta],
            [$lon - $lonDelta, $lon + $lonDelta],
        ];
    }
}
