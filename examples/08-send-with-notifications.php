<?php

/**
 * Send Message with SMS/Email Notifications Example
 *
 * This example demonstrates how to send a message with
 * SMS and/or email notifications to the recipient.
 */

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Coretrek\Digipost\DigipostClient;
use Coretrek\Digipost\DigipostClientConfig;
use Coretrek\Digipost\Representations\DigipostAddress;
use Coretrek\Digipost\Representations\Document;
use Coretrek\Digipost\Representations\EmailNotification;
use Coretrek\Digipost\Representations\FileType;
use Coretrek\Digipost\Representations\Message;
use Coretrek\Digipost\Representations\SmsNotification;
use Coretrek\Digipost\Security\Signer;
use Coretrek\Digipost\SenderId;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

// Set up the client
$signer = Signer::fromPkcs12File('/path/to/certificate.p12', 'password');
$config = DigipostClientConfig::production();
$client = new DigipostClient($config, SenderId::of(123456), $signer);

// Create SMS notification - sent after specified hours
$smsNotification = new SmsNotification(
    afterHours: 0, // Send immediately when document is delivered
);

// Or send at a specific time
// $smsNotification = new SmsNotification(
//     atTime: new DateTimeImmutable('2024-12-15 09:00:00'),
// );

// Create email notification
$emailNotification = new EmailNotification(
    afterHours: 24, // Send email reminder after 24 hours if not opened
);

// Create the document with notifications
$documentUuid = Uuid::uuid4();
$document = new Document(
    uuid: $documentUuid,
    subject: 'Urgent: Action Required',
    fileType: FileType::PDF,
    smsNotification: $smsNotification,
    emailNotification: $emailNotification,
);

// Build and send the message
$message = Message::newMessage('urgent-notice-001', $document)
    ->digipostAddress(new DigipostAddress('john.doe'))
    ->build();

$pdfContent = file_get_contents('/path/to/document.pdf');

$delivery = $client->sendMessage($message, [
    $documentUuid->toString() => $pdfContent,
]);

echo "Message with notifications sent successfully!\n";
echo "SMS notification: Immediately on delivery\n";
echo "Email notification: After 24 hours if not opened\n";
