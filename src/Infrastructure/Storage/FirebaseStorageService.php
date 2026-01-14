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

        if (!is_file($localFilePath)) {
            throw new \InvalidArgumentException(sprintf('File not found for upload: %s', $localFilePath));
        }

        $handle = fopen($localFilePath, 'rb');

        if ($handle === false) {
            throw new \RuntimeException(sprintf('Unable to open file for upload: %s', $localFilePath));
        }

        try {
            $bucket->upload(
                $handle,
                [
                    'name' => $objectName,
                ]
            );

            $signedUrl = $bucket
                ->object($objectName)
                ->signedUrl(new \DateTimeImmutable('+1 day'));
        } catch (\Throwable $exception) {
            error_log(sprintf('Signed URL generation failed for %s: %s', $objectName, $exception->getMessage()));
            $signedUrl = null;
        } finally {
            fclose($handle);
        }

        return $signedUrl
            ?: sprintf('https://storage.googleapis.com/%s/%s', $bucket->name(), $objectName);
    }

    /**
     * Remove a stored contact photo (best-effort).
     */
    public function deleteContactPhoto(string $url): void
    {
        $bucket = $this->storage->getBucket($this->bucketName);
        $parsedPath = parse_url($url, PHP_URL_PATH);

        if (!$parsedPath) {
            return;
        }

        $objectName = ltrim((string) $parsedPath, '/');

        if (str_starts_with($objectName, $bucket->name() . '/')) {
            $objectName = substr($objectName, strlen($bucket->name()) + 1);
        }

        $object = $bucket->object($objectName);

        if ($object->exists()) {
            $object->delete();
        }
    }
}
