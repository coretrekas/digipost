<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Http;

use Coretrek\Digipost\Security\Signer;

/**
 * Signs HTTP requests for the Digipost API.
 *
 * The signature string is built as follows:
 * - uppercase(verb)
 * - lowercase(path)
 * - "date: " + dateHeader
 * - "x-content-sha256: " + sha256Header
 * - "x-digipost-userid: " + senderId
 * - lowercase(urlencode(queryParams))
 *
 * The string is then signed using SHA-256 with RSA encryption.
 */
final readonly class RequestSigner
{
    public function __construct(
        private Signer $signer,
    ) {}

    /**
     * Sign an HTTP request.
     *
     * @param array<string, string> $headers Headers must include Date, X-Content-SHA256, and X-Digipost-UserId
     *
     * @return string The base64-encoded signature
     */
    public function sign(string $method, string $url, array $headers): string
    {
        $canonicalRequest = $this->buildCanonicalRequest($method, $url, $headers);

        return $this->signer->sign($canonicalRequest);
    }

    /**
     * Calculate the SHA-256 hash of the content for the X-Content-SHA256 header.
     */
    public function calculateContentHash(string $content): string
    {
        return base64_encode(hash('sha256', $content, true));
    }

    /**
     * Build the canonical request string for signing.
     *
     * Each line ends with a newline character (\n), including the last line.
     *
     * @param array<string, string> $headers
     */
    private function buildCanonicalRequest(string $method, string $url, array $headers): string
    {
        $parsedUrl = parse_url($url);
        $path = $parsedUrl['path'] ?? '/';
        $queryString = $parsedUrl['query'] ?? '';

        $parts = [];

        // HTTP method (uppercase)
        $parts[] = strtoupper($method);

        // Path (lowercase)
        $parts[] = strtolower($path);

        // Date header with prefix
        $parts[] = 'date: '.($headers['Date'] ?? '');

        // X-Content-SHA256 header with prefix (only for requests with body)
        if (isset($headers['X-Content-SHA256']) && $headers['X-Content-SHA256'] !== '') {
            $parts[] = 'x-content-sha256: '.$headers['X-Content-SHA256'];
        }

        // X-Digipost-UserId header with prefix
        $parts[] = 'x-digipost-userid: '.($headers['X-Digipost-UserId'] ?? '');

        // Query parameters (lowercase, URL-encoded)
        $parts[] = strtolower($queryString);

        // Each line ends with \n, including the last line
        return implode("\n", $parts)."\n";
    }
}
