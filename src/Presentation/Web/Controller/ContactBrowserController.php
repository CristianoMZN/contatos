<?php

declare(strict_types=1);

namespace App\Presentation\Web\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Web controller responsible for rendering the public contact browser using Symfony UX.
 */
final class ContactBrowserController extends AbstractController
{
    #[Route('/contacts/public', name: 'public_contacts', methods: ['GET'])]
    public function publicContacts(Request $request): Response
    {
        $search = $this->normalizeString($request->query->get('q', ''));
        $categoryId = $this->normalizeOptionalString($request->query->get('category'));
        $radiusKm = $this->normalizeFloat($request->query->get('radius'));
        $latitude = $this->normalizeFloat($request->query->get('lat'));
        $longitude = $this->normalizeFloat($request->query->get('lng'));
        $viewMode = $this->sanitizeViewMode((string) $request->query->get('view', 'grid'));
        $limit = $this->sanitizeLimit($request->query->get('limit'));

        return $this->render('contacts/public.html.twig', [
            'search' => $search,
            'categoryId' => $categoryId,
            'radiusKm' => $radiusKm,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'viewMode' => $viewMode,
            'limit' => $limit,
        ]);
    }

    #[Route('/contacts/public/frame', name: 'public_contacts_frame', methods: ['GET'])]
    public function publicContactsFrame(Request $request): Response
    {
        $search = $this->normalizeString($request->query->get('q', ''));
        $categoryId = $this->normalizeOptionalString($request->query->get('category'));
        $radiusKm = $this->normalizeFloat($request->query->get('radius'));
        $latitude = $this->normalizeFloat($request->query->get('lat'));
        $longitude = $this->normalizeFloat($request->query->get('lng'));
        $viewMode = $this->sanitizeViewMode((string) $request->query->get('view', 'grid'));
        $limit = $this->sanitizeLimit($request->query->get('limit'));

        return $this->render('contacts/_public_list_frame.html.twig', [
            'search' => $search,
            'categoryId' => $categoryId,
            'radiusKm' => $radiusKm,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'viewMode' => $viewMode,
            'limit' => $limit,
        ]);
    }

    private function normalizeString(string $value): string
    {
        return trim($value);
    }

    private function normalizeOptionalString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);

        return $trimmed !== '' ? $trimmed : null;
    }

    private function normalizeFloat(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        $validated = filter_var($value, FILTER_VALIDATE_FLOAT);

        return $validated !== false ? (float) $validated : null;
    }

    private function sanitizeViewMode(string $value): string
    {
        return in_array($value, ['grid', 'list'], true) ? $value : 'grid';
    }

    private function sanitizeLimit(mixed $value): int
    {
        $validated = filter_var($value, FILTER_VALIDATE_INT);
        if ($validated === false) {
            return 20;
        }

        return max(1, min(50, (int) $validated));
    }
}
