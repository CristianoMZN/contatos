<?php

declare(strict_types=1);

namespace App\Infrastructure\Firebase\Firestore;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\UserId;
use App\Domain\User\ValueObject\Email;
use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Core\Timestamp;

/**
 * Firestore User Repository
 * Implements user persistence using Google Cloud Firestore
 */
class FirestoreUserRepository implements UserRepositoryInterface
{
    private const COLLECTION = 'users';

    public function __construct(
        private FirestoreClient $firestore
    ) {
    }

    public function findById(UserId $id): ?User
    {
        $snapshot = $this->firestore
            ->collection(self::COLLECTION)
            ->document($id->toString())
            ->snapshot();

        if (!$snapshot->exists()) {
            return null;
        }

        return $this->mapToEntity($snapshot->data(), $snapshot->id());
    }

    public function findByEmail(Email $email): ?User
    {
        $query = $this->firestore
            ->collection(self::COLLECTION)
            ->where('email', '=', $email->toString())
            ->limit(1);

        $documents = $query->documents();

        foreach ($documents as $document) {
            return $this->mapToEntity($document->data(), $document->id());
        }

        return null;
    }

    public function save(User $user): void
    {
        $data = $this->mapFromEntity($user);

        $this->firestore
            ->collection(self::COLLECTION)
            ->document($user->id()->toString())
            ->set($data, ['merge' => true]);
    }

    public function delete(UserId $id): void
    {
        $this->firestore
            ->collection(self::COLLECTION)
            ->document($id->toString())
            ->delete();
    }

    public function existsByEmail(Email $email): bool
    {
        return $this->findByEmail($email) !== null;
    }

    public function findAll(int $page = 1, int $perPage = 50): array
    {
        $offset = ($page - 1) * $perPage;

        // Get total count
        $allDocs = $this->firestore
            ->collection(self::COLLECTION)
            ->documents();
        $total = iterator_count($allDocs);

        // Get paginated results
        $query = $this->firestore
            ->collection(self::COLLECTION)
            ->orderBy('createdAt', 'DESC')
            ->limit($perPage)
            ->offset($offset);

        $documents = $query->documents();
        $users = [];

        foreach ($documents as $document) {
            $users[] = $this->mapToEntity($document->data(), $document->id());
        }

        return [
            'data' => $users,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
        ];
    }

    /**
     * Map Firestore document to User entity
     */
    private function mapToEntity(array $data, string $id): User
    {
        return User::fromPrimitives(
            id: $id,
            email: $data['email'],
            displayName: $data['displayName'],
            createdAt: $this->timestampToString($data['createdAt'] ?? null),
            roles: $data['roles'] ?? ['user'],
            photoURL: $data['photoURL'] ?? null,
            emailVerified: $data['emailVerified'] ?? false
        );
    }

    /**
     * Map User entity to Firestore document
     */
    private function mapFromEntity(User $user): array
    {
        $data = [
            'email' => $user->email()->toString(),
            'displayName' => $user->displayName()->toString(),
            'roles' => $user->rolesAsStrings(),
            'photoURL' => $user->photoURL(),
            'emailVerified' => $user->isEmailVerified(),
            'updatedAt' => new Timestamp(new \DateTime()),
        ];

        // Only set createdAt for new users
        if (!$this->findById($user->id())) {
            $data['createdAt'] = new Timestamp($user->createdAt());
        }

        return $data;
    }

    /**
     * Convert Firestore Timestamp to ISO 8601 string
     */
    private function timestampToString(?Timestamp $timestamp): string
    {
        if ($timestamp === null) {
            return (new \DateTimeImmutable())->format('c');
        }

        return $timestamp->get()->format('c');
    }
}
