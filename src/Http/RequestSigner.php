<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Http;

use Coretrek\Digipost\Security\Signer;

/**
 * Signs HTTP requests for the Digipost API.
 */
final readonly class RequestSigner
{
    public function __construct(
        private Signer $signer,
    ) {}

    /**
     * Sign an HTTP request.
     *
     * @param array<string, string> $headers
     *
     * @return string The signature
     */
    public function sign(string $method, string $url, string $body, array $headers): string
    {
        $canonicalRequest = $this->buildCanonicalRequest($method, $url, $body, $headers);

        return $this->signer->sign($canonicalRequest);
    }

    /**
     * Build the canonical request string for signing.
     *
     * @param array<string, string> $headers
     */
    private function buildCanonicalRequest(string $method, string $url, string $body, array $headers): string
    {
        $parts = [];

        // HTTP method
        $parts[] = strtoupper($method);

        // URL path
        $parsedUrl = parse_url($url);
        $path = $parsedUrl['path'] ?? '/';
        $parts[] = $path;

        // Date header
        $parts[] = $headers['Date'] ?? gmdate('D, d M Y H:i:s T');

        // User ID header
        $parts[] = $headers['X-Digipost-UserId'] ?? '';

        // Content-MD5 (if body is present)
        if ($body !== '') {
            $contentMd5 = base64_encode(md5($body, true));
            $parts[] = $contentMd5;
        }

        return implode("\n", $parts);
    }
}
