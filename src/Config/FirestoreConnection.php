<?php

namespace App\Config;

use Google\Cloud\Firestore\FirestoreClient;

/**
 * Firestore Connection Manager
 * Handles Firestore client initialization with credentials from environment
 */
class FirestoreConnection
{
    private static ?FirestoreClient $db = null;

    /**
     * Get Firestore database instance
     * 
     * @return FirestoreClient The Firestore client instance
     * @throws \Exception If required environment variables are missing
     */
    public static function getDb(): FirestoreClient
    {
        if (self::$db !== null) {
            return self::$db;
        }

        $projectId = getenv('GCP_PROJECT_ID');
        if (!$projectId) {
            throw new \Exception('GCP_PROJECT_ID environment variable is not set');
        }

        $config = [
            'projectId' => $projectId
        ];

        // Check if Firebase credentials are provided as JSON string
        $firebaseCredentials = getenv('FIREBASE_CREDENTIALS');
        if ($firebaseCredentials) {
            $keyFileData = json_decode($firebaseCredentials, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid FIREBASE_CREDENTIALS JSON: ' . json_last_error_msg());
            }
            $config['keyFile'] = $keyFileData;
        }

        self::$db = new FirestoreClient($config);

        return self::$db;
    }
}
