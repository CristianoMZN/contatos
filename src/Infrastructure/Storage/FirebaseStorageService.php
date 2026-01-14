<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use Kreait\Firebase\Storage;

/**
 * Service responsible for storing contact assets in Firebase Storage.
 */
final class FirebaseStorageService
{
    public function __construct(
        private readonly Storage $storage,
        private readonly string $bucketName
    ) {
    }

    /**
     * Upload a contact photo and return its public URL.
     */
    public function uploadContactPhoto(string $contactId, string $localFilePath, ?string $filename = null): string
    {
        $bucket = $this->storage->getBucket($this->bucketName);
        $objectName = sprintf(
            'contacts/%s/%s',
            $contactId,
            $filename ?: basename($localFilePath)
        );

        $bucket->upload(
            fopen($localFilePath, 'r'),
            [
                'name' => $objectName,
                'predefinedAcl' => 'publicRead',
            ]
        );

        return sprintf('https://storage.googleapis.com/%s/%s', $bucket->name(), $objectName);
    }

    /**
     * Remove a stored contact photo (best-effort).
     */
    public function deleteContactPhoto(string $url): void
    {
        $bucket = $this->storage->getBucket($this->bucketName);
        $parsed = parse_url($url, PHP_URL_PATH);

        if (!$parsed) {
            return;
        }

        $objectName = ltrim((string) $parsed, '/');
        $object = $bucket->object($objectName);

        if ($object->exists()) {
            $object->delete();
        }
    }
}
