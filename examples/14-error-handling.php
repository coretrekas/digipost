<?php

/**
 * Error Handling Example
 *
 * This example demonstrates how to handle errors and exceptions
 * when using the Digipost SDK.
 */

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Coretrek\Digipost\DigipostClient;
use Coretrek\Digipost\DigipostClientConfig;
use Coretrek\Digipost\Exceptions\ApiException;
use Coretrek\Digipost\Exceptions\CertificateException;
use Coretrek\Digipost\Representations\DigipostAddress;
use Coretrek\Digipost\Representations\Document;
use Coretrek\Digipost\Representations\FileType;
use Coretrek\Digipost\Representations\Message;
use Coretrek\Digipost\Security\Signer;
use Coretrek\Digipost\SenderId;
use GuzzleHttp\Exception\ConnectException;
use Ramsey\Uuid\Uuid;

// Handle certificate errors
try {
    $signer = Signer::fromPkcs12File('/path/to/certificate.p12', 'password');
} catch (CertificateException $e) {
    echo "Certificate error: {$e->getMessage()}\n";

    // Specific certificate error types:
    // - CertificateException::fileNotFound() - File doesn't exist
    // - CertificateException::cannotRead() - File not readable
    // - CertificateException::invalidPkcs12() - Invalid PKCS#12 format or wrong password
    // - CertificateException::invalidPrivateKey() - Private key extraction failed
    // - CertificateException::invalidCertificate() - Certificate extraction failed

    exit(1);
}

$config = DigipostClientConfig::production();
$client = new DigipostClient($config, SenderId::of(123456), $signer);

// Handle API errors
try {
    $documentUuid = Uuid::uuid4();
    $document = new Document(
        uuid: $documentUuid,
        subject: 'Test Document',
        fileType: FileType::PDF,
    );

    $message = Message::newMessage('test-001', $document)
        ->digipostAddress(new DigipostAddress('nonexistent.user'))
        ->build();

    $pdfContent = file_get_contents('/path/to/document.pdf');

    $delivery = $client->sendMessage($message, [
        $documentUuid->toString() => $pdfContent,
    ]);
} catch (ApiException $e) {
    echo "API error: {$e->getMessage()}\n";
    echo "Status code: {$e->statusCode}\n";

    if ($e->errorCode !== null) {
        echo "Error code: {$e->errorCode}\n";
    }

    if ($e->errorType !== null) {
        echo "Error type: {$e->errorType}\n";
    }

    // Common API errors:
    // - 400 Bad Request: Invalid request data
    // - 401 Unauthorized: Invalid or expired certificate
    // - 403 Forbidden: Not authorized for this operation
    // - 404 Not Found: Recipient or resource not found
    // - 409 Conflict: Duplicate message ID
    // - 429 Too Many Requests: Rate limit exceeded
    // - 500 Internal Server Error: Server-side error

    exit(1);
} catch (ConnectException $e) {
    echo "Connection error: {$e->getMessage()}\n";
    echo "Check your network connection and try again.\n";
    exit(1);
}

// Validate input before sending
try {
    // DigipostAddress validates format
    $address = new DigipostAddress('a'); // Too short - will throw
} catch (InvalidArgumentException $e) {
    echo "Invalid address: {$e->getMessage()}\n";
}

try {
    // SenderId validates positive integer
    $senderId = SenderId::of(-1); // Negative - will throw
} catch (InvalidArgumentException $e) {
    echo "Invalid sender ID: {$e->getMessage()}\n";
}

echo "Error handling example complete.\n";
