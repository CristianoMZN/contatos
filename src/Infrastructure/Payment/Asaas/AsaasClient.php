<?php

declare(strict_types=1);

namespace App\Infrastructure\Payment\Asaas;

use RuntimeException;

/**
 * Minimal HTTP client for ASAAS API.
 */
final class AsaasClient
{
    private const DEFAULT_BASE_URL = 'https://sandbox.asaas.com/api/v3';

    public function __construct(
        private string $apiKey,
        private string $baseUrl = self::DEFAULT_BASE_URL
    ) {
    }

    /**
     * @throws RuntimeException on HTTP or decoding errors
     */
    public function post(string $path, array $payload): array
    {
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/');

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'access_token: ' . $this->apiKey,
            ],
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_SLASHES),
            CURLOPT_TIMEOUT => 15,
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new RuntimeException(sprintf('ASAAS request failed: %s', $error));
        }

        $decoded = json_decode($response, true);

        if (!is_array($decoded)) {
            throw new RuntimeException('Invalid ASAAS response');
        }

        if ($status >= 400) {
            $message = $decoded['errors'][0]['description'] ?? $response;
            throw new RuntimeException(sprintf('ASAAS error (%d): %s', $status, $message));
        }

        return $decoded;
    }
}
