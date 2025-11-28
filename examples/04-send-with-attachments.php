<?php

/**
 * Send Message with Attachments Example
 *
 * This example demonstrates how to send a message
 * with multiple document attachments.
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

// Create UUIDs for all documents
$invoiceUuid = Uuid::uuid4();
$termsUuid = Uuid::uuid4();
$receiptUuid = Uuid::uuid4();

// Create the primary document (invoice)
$primaryDocument = new Document(
    uuid: $invoiceUuid,
    subject: 'Invoice December 2024',
    fileType: FileType::PDF,
);

// Create attachments
$termsAttachment = new Document(
    uuid: $termsUuid,
    subject: 'Terms and Conditions',
    fileType: FileType::PDF,
);

$receiptAttachment = new Document(
    uuid: $receiptUuid,
    subject: 'Payment Receipt',
    fileType: FileType::PDF,
);

// Build the message with attachments
$message = Message::newMessage('invoice-with-attachments-001', $primaryDocument)
    ->digipostAddress(new DigipostAddress('john.doe'))
    ->attachments($termsAttachment, $receiptAttachment)
    ->build();

// Read all document contents
$invoiceContent = file_get_contents('/path/to/invoice.pdf');
$termsContent = file_get_contents('/path/to/terms.pdf');
$receiptContent = file_get_contents('/path/to/receipt.pdf');

// Send the message with all document contents
$delivery = $client->sendMessage($message, [
    $invoiceUuid->toString() => $invoiceContent,
    $termsUuid->toString() => $termsContent,
    $receiptUuid->toString() => $receiptContent,
]);

echo "Message with attachments sent successfully!\n";
echo 'Primary document + '.count($message->attachments)." attachments\n";
