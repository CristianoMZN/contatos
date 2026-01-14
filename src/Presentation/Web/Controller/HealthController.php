<?php

declare(strict_types=1);

namespace App\Presentation\Web\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Health Check Controller
 * 
 * Simple controller to verify Symfony setup
 */
final class HealthController
{
    #[Route('/health', name: 'health', methods: ['GET'])]
    public function health(): Response
    {
        return new JsonResponse([
            'status' => 'ok',
            'message' => 'Symfony 7.x is running',
            'symfony_version' => \Symfony\Component\HttpKernel\Kernel::VERSION,
            'php_version' => PHP_VERSION,
            'timestamp' => (new \DateTimeImmutable())->format('c'),
        ]);
    }
}
