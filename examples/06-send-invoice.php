<?php

/**
 * Send Invoice Example
 *
 * This example demonstrates how to send an invoice document
 * with payment information that will be displayed in Digipost.
 */

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Coretrek\Digipost\DigipostClient;
use Coretrek\Digipost\DigipostClientConfig;
use Coretrek\Digipost\Representations\DataTypes\Invoice;
use Coretrek\Digipost\Representations\DigipostAddress;
use Coretrek\Digipost\Representations\Document;
use Coretrek\Digipost\Representations\FileType;
use Coretrek\Digipost\Representations\Message;
use Coretrek\Digipost\Security\Signer;
use Coretrek\Digipost\SenderId;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

// Set up the client
$signer = Signer::fromPkcs12File('/path/to/certificate.p12', 'password');
$config = DigipostClientConfig::production();
$client = new DigipostClient($config, SenderId::of(123456), $signer);

// Create invoice data type with payment information
$invoice = new Invoice(
    dueDate: new DateTimeImmutable('2024-12-31'),
    amount: '1500.00',
    kid: '1234567890123',           // KID number for payment
    accountNumber: '12345678901',    // Bank account number
);

// You can also add creditor account for international payments
// $invoice = new Invoice(
//     dueDate: new DateTimeImmutable('2024-12-31'),
//     amount: '1500.00',
//     kid: '1234567890123',
//     accountNumber: '12345678901',
//     creditorAccount: 'NO9386011117947', // IBAN
// );

// Create the document with invoice data
$documentUuid = Uuid::uuid4();
$document = new Document(
    uuid: $documentUuid,
    subject: 'Invoice #2024-001 - December',
    fileType: FileType::PDF,
    dataType: $invoice,
);

// Build and send the message
$message = Message::newMessage('invoice-2024-001', $document)
    ->digipostAddress(new DigipostAddress('john.doe'))
    ->build();

$pdfContent = file_get_contents('/path/to/invoice.pdf');

$delivery = $client->sendMessage($message, [
    $documentUuid->toString() => $pdfContent,
]);

echo "Invoice sent successfully!\n";
echo "Due date: {$invoice->dueDate->format('Y-m-d')}\n";
echo "Amount: {$invoice->amount} NOK\n";
echo "KID: {$invoice->kid}\n";
