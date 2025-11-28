<?php

/**
 * Send Message with Print Fallback Example
 *
 * This example demonstrates how to send a message that will
 * be printed and sent via regular mail if the recipient
 * doesn't have a Digipost account.
 */

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Coretrek\Digipost\DigipostClient;
use Coretrek\Digipost\DigipostClientConfig;
use Coretrek\Digipost\Representations\Document;
use Coretrek\Digipost\Representations\FileType;
use Coretrek\Digipost\Representations\Message;
use Coretrek\Digipost\Representations\PersonalIdentificationNumber;
use Coretrek\Digipost\Representations\Print\NorwegianAddress;
use Coretrek\Digipost\Representations\Print\PrintDetails;
use Coretrek\Digipost\Representations\Print\PrintRecipient;
use Coretrek\Digipost\Representations\Recipients\MessageRecipient;
use Coretrek\Digipost\Security\Signer;
use Coretrek\Digipost\SenderId;
use Ramsey\Uuid\Uuid;

// Set up the client
$signer = Signer::fromPkcs12File('/path/to/certificate.p12', 'password');
$config = DigipostClientConfig::production();
$client = new DigipostClient($config, SenderId::of(123456), $signer);

// Create the document
$documentUuid = Uuid::uuid4();
$document = new Document(
    uuid: $documentUuid,
    subject: 'Important Notice',
    fileType: FileType::PDF,
);

// Set up the recipient's address for print fallback
$recipientAddress = new NorwegianAddress(
    addressLine1: 'Storgata 1',
    postalCode: '0123',
    city: 'Oslo',
);

$printRecipient = new PrintRecipient(
    name: 'Ola Nordmann',
    address: $recipientAddress,
);

// Set up your return address
$returnAddress = new NorwegianAddress(
    addressLine1: 'Bedriftsgata 10',
    postalCode: '5000',
    city: 'Bergen',
);

$returnRecipient = new PrintRecipient(
    name: 'Your Company AS',
    address: $returnAddress,
);

// Create print details
$printDetails = new PrintDetails(
    recipient: $printRecipient,
    returnAddress: $returnRecipient,
);

// Create recipient with personal ID number and print fallback
$recipient = MessageRecipient::fromPersonalIdentificationNumber(
    new PersonalIdentificationNumber('12345678901'),
    $printDetails,
);

// Build the message
$message = Message::newMessage('print-fallback-001', $document)
    ->recipient($recipient)
    ->build();

// Read document content
$pdfContent = file_get_contents('/path/to/document.pdf');

// Send the message
$delivery = $client->sendMessage($message, [
    $documentUuid->toString() => $pdfContent,
]);

echo "Message sent successfully!\n";
echo "Delivery channel: {$delivery->channel->value}\n";

if ($delivery->channel->value === 'PRINT') {
    echo "Message will be printed and sent via regular mail.\n";
} else {
    echo "Message was delivered digitally via Digipost.\n";
}
