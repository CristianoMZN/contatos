<?php

declare(strict_types=1);

namespace App\Infrastructure\Firebase;

use Google\Cloud\Firestore\FirestoreClient;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Storage;

/**
 * Firebase Factory Service
 * 
 * Creates and configures Firebase services (Auth, Firestore, Storage)
 * following the architecture defined in docs/FIREBASE_SETUP.md
 */
final class FirebaseFactory
{
    private ?Factory $factory = null;

    public function __construct(
        private string $credentialsPath,
        private string $projectId,
        private string $databaseUrl,
        private string $storageBucket
    ) {
    }

    /**
     * Get or create Firebase Factory instance
     */
    private function getFactory(): Factory
    {
        if ($this->factory === null) {
            $this->factory = (new Factory())
                ->withProjectId($this->projectId)
                ->withDatabaseUri($this->databaseUrl);

            // Load service account credentials from file or GCP Secret Manager
            if (file_exists($this->credentialsPath)) {
                $this->factory = $this->factory->withServiceAccount($this->credentialsPath);
            } else {
                // Credentials will be loaded from GOOGLE_APPLICATION_CREDENTIALS env var
                // or from GCP metadata server when running on GCP
                error_log(sprintf(
                    'Firebase credentials file not found at %s. Using default credentials.',
                    $this->credentialsPath
                ));
            }
        }

        return $this->factory;
    }

    /**
     * Create Firebase Auth service
     */
    public function createAuth(): Auth
    {
        return $this->getFactory()->createAuth();
    }

    /**
     * Create Firebase Storage service
     */
    public function createStorage(): Storage
    {
        return $this->getFactory()
            ->withDefaultStorageBucket($this->storageBucket)
            ->createStorage();
    }

    /**
     * Create Firestore Client
     * 
     * Note: This creates a Google Cloud Firestore client directly,
     * which provides more features than the Firebase SDK's database
     */
    public function createFirestoreClient(): FirestoreClient
    {
        $config = [
            'projectId' => $this->projectId,
        ];

        // Add credentials if file exists
        if (file_exists($this->credentialsPath)) {
            $config['keyFilePath'] = $this->credentialsPath;
        }

        return new FirestoreClient($config);
    }

    /**
     * Get Firebase project ID
     */
    public function getProjectId(): string
    {
        return $this->projectId;
    }

    /**
     * Get storage bucket name
     */
    public function getStorageBucket(): string
    {
        return $this->storageBucket;
    }
}
