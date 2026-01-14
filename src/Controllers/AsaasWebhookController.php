<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Application\UseCase\Subscription\HandleAsaasWebhookUseCase;
use App\Core\Controller;
use App\Infrastructure\Firebase\FirebaseFactory;
use App\Infrastructure\Firebase\Firestore\FirestoreSubscriptionRepository;

/**
 * Receives ASAAS webhook callbacks and syncs subscriptions.
 */
final class AsaasWebhookController extends Controller
{
    private HandleAsaasWebhookUseCase $useCase;

    public function __construct()
    {
        parent::__construct();

        $factory = new FirebaseFactory(
            credentialsPath: getenv('GOOGLE_APPLICATION_CREDENTIALS') ?: '',
            projectId: getenv('FIREBASE_PROJECT_ID') ?: 'local-project',
            databaseUrl: getenv('FIREBASE_DATABASE_URL') ?: '',
            storageBucket: getenv('FIREBASE_STORAGE_BUCKET') ?: ''
        );

        $subscriptionRepository = new FirestoreSubscriptionRepository(
            $factory->createFirestoreClient()
        );

        $this->useCase = new HandleAsaasWebhookUseCase($subscriptionRepository);
    }

    public function handle(): void
    {
        $raw = file_get_contents('php://input') ?: '{}';
        $payload = json_decode($raw, true);

        if (!is_array($payload)) {
            $this->json(['success' => false, 'message' => 'Invalid payload'], 400);
        }

        $updated = $this->useCase->execute($payload);

        $this->json([
            'success' => $updated,
        ], $updated ? 200 : 202);
    }
}
