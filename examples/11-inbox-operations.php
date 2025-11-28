<?php

/**
 * Inbox Operations Example
 *
 * This example demonstrates how to read and manage
 * documents in your organization's inbox.
 */

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Coretrek\Digipost\DigipostClient;
use Coretrek\Digipost\DigipostClientConfig;
use Coretrek\Digipost\Security\Signer;
use Coretrek\Digipost\SenderId;

// Set up the client
$signer = Signer::fromPkcs12File('/path/to/certificate.p12', 'password');
$config = DigipostClientConfig::production();
$client = new DigipostClient($config, SenderId::of(123456), $signer);

// Get inbox documents with pagination
$offset = 0;
$limit = 100;

$inbox = $client->getInbox($offset, $limit);

echo "Inbox contains documents:\n";
echo "=========================\n\n";

foreach ($inbox->documents as $document) {
    echo "Subject: {$document->subject}\n";
    echo "From: {$document->sender}\n";
    echo "Delivered: {$document->deliveryTime->format('Y-m-d H:i:s')}\n";
    echo "File type: {$document->fileType->value}\n";
    echo 'Opened: '.($document->opened ? 'Yes' : 'No')."\n";

    // Get document content
    $content = $client->getInboxDocumentContent($document);
    echo 'Content size: '.strlen($content)." bytes\n";

    // Save to file
    // file_put_contents("/path/to/downloads/{$document->subject}.pdf", $content);

    echo "---\n";
}

// Delete a document from inbox (uncomment to use)
// foreach ($inbox->documents as $document) {
//     $client->deleteInboxDocument($document);
//     echo "Deleted: {$document->subject}\n";
// }

// Process all inbox documents with pagination
// $offset = 0;
// $limit = 100;
// do {
//     $inbox = $client->getInbox($offset, $limit);
//     foreach ($inbox->documents as $document) {
//         // Process document...
//     }
//     $offset += $limit;
// } while (count($inbox->documents) === $limit);
