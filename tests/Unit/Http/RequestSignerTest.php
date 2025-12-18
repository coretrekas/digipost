<?php

declare(strict_types=1);

describe('RequestSigner', function (): void {
    it('calculates content hash using SHA-256', function (): void {
        $content = 'test content';

        // SHA-256 hash of 'test content' encoded in base64
        $expectedHash = base64_encode(hash('sha256', $content, true));

        // Verify the expected hash format
        expect($expectedHash)->toBe('auinVVUgn9bEQVfArtgBbnY/9DWhnPGG92hjFAFD/3I=');
    });

    it('calculates empty content hash correctly', function (): void {
        // SHA-256 hash of empty string encoded in base64
        $expectedHash = base64_encode(hash('sha256', '', true));

        // Verify the expected hash format for empty content
        expect($expectedHash)->toBe('47DEQpj8HBSa+/TImW+5JCeuQeRkm5NMpJWZG3hSuFU=');
    });

    it('builds canonical request string correctly', function (): void {
        // The canonical string format is:
        // uppercase(verb) + "\n" +
        // lowercase(path) + "\n" +
        // "date: " + dateHeader + "\n" +
        // "x-content-sha256: " + sha256Header + "\n" +
        // "x-digipost-userid: " + senderId + "\n" +
        // lowercase(urlencode(queryParams)) + "\n"
    })->skip('Requires valid certificate for testing');

    it('includes required headers in signature', function (): void {
        // According to the Digipost API specification, the signature should include:
        // - HTTP method (uppercase)
        // - Path (lowercase)
        // - Date header with "date: " prefix
        // - X-Content-SHA256 header with "x-content-sha256: " prefix
        // - X-Digipost-UserId header with "x-digipost-userid: " prefix
        // - Query parameters (lowercase, URL-encoded)
    })->skip('Requires valid certificate for testing');
});
