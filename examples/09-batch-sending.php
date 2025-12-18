<?php

/**
 * Batch Sending Example
 *
 * This example demonstrates how to send multiple messages
 * in a batch for efficient bulk sending.
 */

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Coretrek\Digipost\DigipostClient;
use Coretrek\Digipost\DigipostClientConfig;
use Coretrek\Digipost\Representations\DigipostAddress;
use Coretrek\Digipost\Representations\Document;
use Coretrek\Digipost\Representations\FileType;
use Coretrek\Digipost\Representations\Message;
use Coretrek\Digipost\Security\Signer;
use Coretrek\Digipost\SenderId;
use Ramsey\Uuid\Uuid;

// Set up the client
$signer = Signer::fromPkcs12File('/path/to/certificate.p12', 'password');
$config = DigipostClientConfig::production();
$client = new DigipostClient($config, SenderId::of(123456), $signer);

// Example recipients with their documents
$recipients = [
    ['address' => 'john.doe', 'name' => 'John Doe'],
    ['address' => 'jane.smith', 'name' => 'Jane Smith'],
    ['address' => 'bob.wilson', 'name' => 'Bob Wilson'],
];

// Create a batch
$batch = $client->createBatch();
echo "Batch created: {$batch->uuid->toString()}\n";

// Add messages to the batch
foreach ($recipients as $index => $recipient) {
    $documentUuid = Uuid::uuid4();

    $document = new Document(
        uuid: $documentUuid,
        subject: "Monthly Newsletter - {$recipient['name']}",
        fileType: FileType::PDF,
    );

    $message = Message::newMessage("newsletter-{$index}", $document)
        ->digipostAddress(new DigipostAddress($recipient['address']))
        ->build();

    // Generate personalized PDF content (or use same content for all)
    $pdfContent = file_get_contents('/path/to/newsletter.pdf');

    // Add message to batch
    $client->addMessageToBatch($batch->uuid, $message, [
        $documentUuid->toString() => $pdfContent,
    ]);

    echo "Added message for: {$recipient['name']}\n";
}

// Complete the batch to start processing
$completedBatch = $client->completeBatch($batch);
echo "Batch completed and processing started!\n";
echo 'Total messages: '.count($recipients)."\n";

// You can check batch status later
// $batchStatus = $client->getBatch($batch->uuid);
// echo "Batch status: {$batchStatus->status}\n";

// Or cancel the batch if needed (before completing)
// $client->cancelBatch($batch);
