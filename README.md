# Digipost API Client for PHP

[![Latest Version](https://img.shields.io/packagist/v/coretrekas/digipost.svg)](https://packagist.org/packages/coretrekas/digipost)
[![PHP Version](https://img.shields.io/packagist/php-v/coretrekas/digipost.svg)](https://packagist.org/packages/coretrekas/digipost)
[![License](https://img.shields.io/packagist/l/coretrekas/digipost.svg)](https://packagist.org/packages/coretrekas/digipost)

A PHP SDK for integrating with the [Digipost](https://www.digipost.no/) digital mailbox service. This library allows you to send digital mail, manage documents, and interact with the Digipost API.

## Requirements

- PHP 8.2 or higher
- OpenSSL extension
- A Digipost enterprise account with API access
- A PKCS#12 certificate for authentication

## Installation

Install via Composer:

```bash
composer require coretrekas/digipost
```

## Quick Start

```php
<?php

use Coretrek\Digipost\DigipostClient;
use Coretrek\Digipost\DigipostClientConfig;
use Coretrek\Digipost\Security\Signer;
use Coretrek\Digipost\SenderId;
use Coretrek\Digipost\Representations\Document;
use Coretrek\Digipost\Representations\FileType;
use Coretrek\Digipost\Representations\Message;
use Coretrek\Digipost\Representations\DigipostAddress;
use Ramsey\Uuid\Uuid;

// Create the signer from your PKCS#12 certificate
$signer = Signer::fromPkcs12File('/path/to/certificate.p12', 'certificate-password');

// Create the client configuration
$config = DigipostClientConfig::production();

// Create the client
$client = new DigipostClient(
    config: $config,
    senderId: SenderId::of(123456), // Your sender ID
    signer: $signer,
);

// Create a document
$documentUuid = Uuid::uuid4();
$document = new Document(
    uuid: $documentUuid,
    subject: 'Important Document',
    fileType: FileType::PDF,
);

// Create and send a message
$message = Message::newMessage('unique-message-id', $document)
    ->digipostAddress(new DigipostAddress('recipient.address'))
    ->build();

$pdfContent = file_get_contents('/path/to/document.pdf');

$delivery = $client->sendMessage($message, [
    $documentUuid->toString() => $pdfContent,
]);

echo "Message delivered via: " . $delivery->channel->value;
```

## Configuration

### Environments

The SDK supports multiple environments:

```php
// Production environment
$config = DigipostClientConfig::production();

// Test environment
$config = DigipostClientConfig::test();

// NHN (Norwegian Health Network) environment
$config = DigipostClientConfig::nhn();

// Custom configuration using builder
$config = DigipostClientConfig::builder()
    ->apiUri('https://custom.api.example.com')
    ->requestTimeout(60)
    ->connectionTimeout(15)
    ->build();
```

### Authentication

The SDK uses PKCS#12 certificates for authentication:

```php
// From a .p12 file
$signer = Signer::fromPkcs12File('/path/to/certificate.p12', 'password');

// From a PKCS#12 string (e.g., from environment variable)
$signer = Signer::fromPkcs12String($pkcs12Content, 'password');

// From separate PEM files
$signer = Signer::fromPemFiles('/path/to/cert.pem', '/path/to/key.pem', 'key-password');
```

## Sending Messages

### Basic Message

```php
use Coretrek\Digipost\Representations\Document;
use Coretrek\Digipost\Representations\FileType;
use Coretrek\Digipost\Representations\Message;
use Coretrek\Digipost\Representations\DigipostAddress;
use Ramsey\Uuid\Uuid;

$documentUuid = Uuid::uuid4();
$document = new Document(
    uuid: $documentUuid,
    subject: 'Monthly Invoice',
    fileType: FileType::PDF,
);

$message = Message::newMessage('invoice-2024-001', $document)
    ->digipostAddress(new DigipostAddress('john.doe'))
    ->build();

$delivery = $client->sendMessage($message, [
    $documentUuid->toString() => $pdfContent,
]);
```

### Message with Attachments

```php
$primaryUuid = Uuid::uuid4();
$attachmentUuid = Uuid::uuid4();

$primaryDocument = new Document(
    uuid: $primaryUuid,
    subject: 'Invoice',
    fileType: FileType::PDF,
);

$attachment = new Document(
    uuid: $attachmentUuid,
    subject: 'Terms and Conditions',
    fileType: FileType::PDF,
);

$message = Message::newMessage('invoice-with-terms', $primaryDocument)
    ->digipostAddress(new DigipostAddress('john.doe'))
    ->attachments($attachment)
    ->build();

$delivery = $client->sendMessage($message, [
    $primaryUuid->toString() => $invoicePdf,
    $attachmentUuid->toString() => $termsPdf,
]);
```

### Message with Print Fallback

If the recipient doesn't have a Digipost account, the message can be printed and sent via regular mail:

```php
use Coretrek\Digipost\Representations\PersonalIdentificationNumber;
use Coretrek\Digipost\Representations\Recipients\MessageRecipient;
use Coretrek\Digipost\Representations\Print\PrintDetails;
use Coretrek\Digipost\Representations\Print\PrintRecipient;
use Coretrek\Digipost\Representations\Print\NorwegianAddress;

$recipientAddress = new NorwegianAddress(
    addressLine1: 'Testgata 1',
    postalCode: '0123',
    city: 'Oslo',
);

$printRecipient = new PrintRecipient(
    name: 'John Doe',
    address: $recipientAddress,
);

$returnAddress = new NorwegianAddress(
    addressLine1: 'Company Street 1',
    postalCode: '0456',
    city: 'Bergen',
);

$returnRecipient = new PrintRecipient(
    name: 'Your Company AS',
    address: $returnAddress,
);

$printDetails = new PrintDetails(
    recipient: $printRecipient,
    returnAddress: $returnRecipient,
);

$recipient = MessageRecipient::fromPersonalIdentificationNumber(
    new PersonalIdentificationNumber('12345678901'),
    $printDetails,
);

$message = Message::newMessage('msg-with-print', $document)
    ->recipient($recipient)
    ->build();
```

## Identifying Recipients

Check if a recipient has a Digipost account:

```php
use Coretrek\Digipost\Representations\Identification;
use Coretrek\Digipost\Representations\DigipostAddress;

$identification = Identification::fromDigipostAddress(
    new DigipostAddress('john.doe')
);

$result = $client->identify($identification);

if ($result->isDigipostUser()) {
    echo "Recipient has Digipost account";
} elseif ($result->isIdentified()) {
    echo "Recipient is identified but not a Digipost user";
} else {
    echo "Recipient not found";
}
```

## Special Document Types

### Invoice

```php
use Coretrek\Digipost\Representations\DataTypes\Invoice;

$invoice = new Invoice(
    dueDate: new DateTimeImmutable('2024-12-31'),
    amount: '1500.00',
    kid: '1234567890123',
    accountNumber: '12345678901',
);

$document = new Document(
    uuid: Uuid::uuid4(),
    subject: 'Invoice December 2024',
    fileType: FileType::PDF,
    dataType: $invoice,
);
```

### Appointment

```php
use Coretrek\Digipost\Representations\DataTypes\Appointment;
use Coretrek\Digipost\Representations\DataTypes\AppointmentAddress;

$appointment = new Appointment(
    startTime: new DateTimeImmutable('2024-12-15 10:00:00'),
    endTime: new DateTimeImmutable('2024-12-15 11:00:00'),
    place: 'Oslo Hospital',
    address: new AppointmentAddress(
        streetAddress: 'Hospital Street 1',
        postalCode: '0123',
        city: 'Oslo',
    ),
    arrivalInfo: 'Please arrive 10 minutes early',
);

$document = new Document(
    uuid: Uuid::uuid4(),
    subject: 'Appointment Confirmation',
    fileType: FileType::PDF,
    dataType: $appointment,
);
```

## Batch Operations

Send multiple messages in a batch:

```php
// Create a batch
$batch = $client->createBatch();

// Add messages to the batch
foreach ($recipients as $recipient) {
    $message = Message::newMessage("batch-msg-{$recipient->id}", $document)
        ->recipient($recipient)
        ->build();
    
    $client->addMessageToBatch($batch->uuid, $message, $documentContents);
}

// Complete the batch to start processing
$batch = $client->completeBatch($batch);

// Or cancel the batch
// $client->cancelBatch($batch);
```

## Archive Operations

Store documents in the archive:

```php
use Coretrek\Digipost\Representations\Archive\ArchiveDocumentContent;

$archiveDocument = ArchiveDocumentContent::create(
    fileName: 'contract-2024.pdf',
    fileType: FileType::PDF,
    content: $pdfContent,
    referenceId: 'contract-123',
    attributes: [
        'customer_id' => '12345',
        'contract_type' => 'annual',
    ],
);

$archivedDocument = $client->archiveDocument($archiveDocument);

// Retrieve documents by reference ID
$archive = $client->getArchiveDocumentsByReferenceId('contract-123');
```

## Inbox Operations

Read documents from the inbox:

```php
// Get inbox documents
$inbox = $client->getInbox(offset: 0, limit: 100);

foreach ($inbox->documents as $document) {
    echo "Document: {$document->subject}\n";
    
    // Get document content
    $content = $client->getInboxDocumentContent($document);
    
    // Delete document
    $client->deleteInboxDocument($document);
}
```

## Document Events

Track document events:

```php
$events = $client->getDocumentEvents(
    from: new DateTimeImmutable('-7 days'),
    to: new DateTimeImmutable('now'),
);

foreach ($events->events as $event) {
    echo "{$event->type->value} at {$event->timestamp->format('Y-m-d H:i:s')}\n";
}
```

## Development

### Running Tests

```bash
composer test
```

### Static Analysis

```bash
composer analyse
```

### Code Formatting

```bash
composer format
```

### All Quality Checks

```bash
composer quality
```

## License

This library is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Contributing

Contributions are welcome! Please read our contributing guidelines before submitting pull requests.

## Support

For support, please open an issue on GitHub.

