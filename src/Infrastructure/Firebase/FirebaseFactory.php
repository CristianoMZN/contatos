<?php

declare(strict_types=1);

namespace App\Infrastructure\Firebase;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Google\Cloud\Firestore\FirestoreClient;
use Kreait\Firebase\Storage;

/**
 * Firebase Factory
 * Centralized factory for creating Firebase service instances
 */
class FirebaseFactory
{
    private static ?Auth $auth = null;
    private static ?FirestoreClient $firestore = null;
    private static ?Storage $storage = null;

    /**
     * Get Firebase Auth instance
     */
    public static function getAuth(): Auth
    {
        if (self::$auth === null) {
            $factory = self::createFactory();
            self::$auth = $factory->createAuth();
        }

        return self::$auth;
    }

    /**
     * Get Firestore client instance
     */
    public static function getFirestore(): FirestoreClient
    {
        if (self::$firestore === null) {
            $projectId = $_ENV['FIREBASE_PROJECT_ID'] ?? getenv('FIREBASE_PROJECT_ID');
            
            self::$firestore = new FirestoreClient([
                'projectId' => $projectId,
            ]);
        }

        return self::$firestore;
    }

    /**
     * Get Firebase Storage instance
     */
    public static function getStorage(): Storage
    {
        if (self::$storage === null) {
            $factory = self::createFactory();
            self::$storage = $factory->createStorage();
        }

        return self::$storage;
    }

    /**
     * Create Firebase Factory with credentials
     */
    private static function createFactory(): Factory
    {
        $credentialsPath = $_ENV['FIREBASE_CREDENTIALS'] ?? getenv('FIREBASE_CREDENTIALS');
        
        if (!$credentialsPath || !file_exists($credentialsPath)) {
            throw new \RuntimeException(
                'Firebase credentials file not found. Please set FIREBASE_CREDENTIALS environment variable.'
            );
        }

        return (new Factory())
            ->withServiceAccount($credentialsPath);
    }

    /**
     * Reset instances (useful for testing)
     */
    public static function reset(): void
    {
        self::$auth = null;
        self::$firestore = null;
        self::$storage = null;
    }
}
