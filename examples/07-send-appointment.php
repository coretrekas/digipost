<?php

/**
 * Send Appointment Example
 *
 * This example demonstrates how to send an appointment confirmation
 * with structured appointment data that will be displayed in Digipost.
 */

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Coretrek\Digipost\DigipostClient;
use Coretrek\Digipost\DigipostClientConfig;
use Coretrek\Digipost\Representations\DataTypes\Appointment;
use Coretrek\Digipost\Representations\DataTypes\AppointmentAddress;
use Coretrek\Digipost\Representations\DataTypes\InfoItem;
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

// Create appointment address
$address = new AppointmentAddress(
    streetAddress: 'Sykehusveien 1',
    postalCode: '0372',
    city: 'Oslo',
);

// Create additional info items
$infoItems = [
    new InfoItem(title: 'Doctor', text: 'Dr. Hansen'),
    new InfoItem(title: 'Department', text: 'Cardiology'),
    new InfoItem(title: 'Bring', text: 'Previous test results'),
];

// Create appointment data type
$appointment = new Appointment(
    startTime: new DateTimeImmutable('2024-12-15 10:00:00'),
    endTime: new DateTimeImmutable('2024-12-15 11:00:00'),
    place: 'Oslo University Hospital',
    address: $address,
    arrivalInfo: 'Please arrive 15 minutes before your appointment. Report to reception on the 2nd floor.',
    info: $infoItems,
);

// Create the document with appointment data
$documentUuid = Uuid::uuid4();
$document = new Document(
    uuid: $documentUuid,
    subject: 'Appointment Confirmation - December 15',
    fileType: FileType::PDF,
    dataType: $appointment,
);

// Build and send the message
$message = Message::newMessage('appointment-001', $document)
    ->digipostAddress(new DigipostAddress('john.doe'))
    ->build();

$pdfContent = file_get_contents('/path/to/appointment-confirmation.pdf');

$delivery = $client->sendMessage($message, [
    $documentUuid->toString() => $pdfContent,
]);

echo "Appointment confirmation sent successfully!\n";
echo "Date: {$appointment->startTime->format('Y-m-d')}\n";
echo "Time: {$appointment->startTime->format('H:i')} - {$appointment->endTime->format('H:i')}\n";
echo "Location: {$appointment->place}\n";
