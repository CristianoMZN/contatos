<?php

declare(strict_types=1);

namespace App\Presentation\Web\Controller;

use App\Infrastructure\Firebase\FirebaseFactory;
use Google\Cloud\Firestore\FirestoreClient;
use Kreait\Firebase\Auth;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Index Controller
 * 
 * Basic controller to verify Symfony and Firebase setup
 */
final class IndexController extends AbstractController
{
    public function __construct(
        private FirebaseFactory $firebaseFactory,
        private FirestoreClient $firestore,
        private Auth $auth
    ) {
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $firebaseData = [
            'project_id' => $this->firebaseFactory->getProjectId(),
            'storage_bucket' => $this->firebaseFactory->getStorageBucket(),
            'firestore_connected' => false,
            'firestore_error' => null,
            'auth_configured' => false,
            'auth_error' => null,
        ];

        try {
            $firebaseData['firestore_connected'] = $this->testFirestoreConnection();
        } catch (\Exception $e) {
            $firebaseData['firestore_error'] = $e->getMessage();
        }

        try {
            $firebaseData['auth_configured'] = $this->testAuthConfiguration();
        } catch (\Exception $e) {
            $firebaseData['auth_error'] = $e->getMessage();
        }

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Symfony 7.x + Firebase setup is working!',
            'symfony_version' => \Symfony\Component\HttpKernel\Kernel::VERSION,
            'php_version' => PHP_VERSION,
            'firebase' => $firebaseData,
            'timestamp' => (new \DateTimeImmutable())->format('c'),
        ]);
    }

    private function testFirestoreConnection(): bool
    {
        try {
            // Try to list collections (this will work even without data)
            $collections = $this->firestore->collections();
            return true;
        } catch (\Exception $e) {
            error_log('Firestore connection test failed: ' . $e->getMessage());
            return false;
        }
    }

    private function testAuthConfiguration(): bool
    {
        try {
            // Just check if auth service is configured
            // We don't actually try to authenticate
            return $this->auth !== null;
        } catch (\Exception $e) {
            error_log('Firebase Auth configuration test failed: ' . $e->getMessage());
            return false;
        }
    }
}
