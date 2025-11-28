<?php

/**
 * Send Message Example
 *
 * This example demonstrates how to send a simple message
 * to a recipient using their Digipost address.
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

// Set up the client (see 01-basic-setup.php for details)
$signer = Signer::fromPkcs12File('/path/to/certificate.p12', 'password');
$config = DigipostClientConfig::production();
$client = new DigipostClient($config, SenderId::of(123456), $signer);

// Create a unique ID for the document
$documentUuid = Uuid::uuid4();

// Create the document metadata
$document = new Document(
    uuid: $documentUuid,
    subject: 'Your Monthly Invoice',
    fileType: FileType::PDF,
);

// Read the PDF file content
$pdfContent = file_get_contents('/path/to/invoice.pdf');

// Build the message with recipient's Digipost address
$message = Message::newMessage('invoice-2024-001', $document)
    ->digipostAddress(new DigipostAddress('john.doe'))
    ->build();

// Send the message
$delivery = $client->sendMessage($message, [
    $documentUuid->toString() => $pdfContent,
]);

echo "Message sent successfully!\n";
echo "Delivery channel: {$delivery->channel->value}\n";
echo "Delivery time: {$delivery->deliveryTime->format('Y-m-d H:i:s')}\n";
