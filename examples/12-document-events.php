<?php

/**
 * Document Events Example
 *
 * This example demonstrates how to track document events
 * such as delivery, opening, and printing.
 */

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Coretrek\Digipost\DigipostClient;
use Coretrek\Digipost\DigipostClientConfig;
use Coretrek\Digipost\Security\Signer;
use Coretrek\Digipost\SenderId;
use DateTimeImmutable;

// Set up the client
$signer = Signer::fromPkcs12File('/path/to/certificate.p12', 'password');
$config = DigipostClientConfig::production();
$client = new DigipostClient($config, SenderId::of(123456), $signer);

// Get events from the last 7 days
$from = new DateTimeImmutable('-7 days');
$to = new DateTimeImmutable('now');

$events = $client->getDocumentEvents($from, $to);

echo "Document Events (Last 7 days):\n";
echo "==============================\n\n";

foreach ($events->events as $event) {
    echo "Event: {$event->type->value}\n";
    echo "Time: {$event->timestamp->format('Y-m-d H:i:s')}\n";
    echo "Document UUID: {$event->documentUuid}\n";

    if ($event->messageId !== null) {
        echo "Message ID: {$event->messageId}\n";
    }

    echo "---\n";
}

// Count events by type
$eventCounts = [];
foreach ($events->events as $event) {
    $type = $event->type->value;
    $eventCounts[$type] = ($eventCounts[$type] ?? 0) + 1;
}

echo "\nEvent Summary:\n";
foreach ($eventCounts as $type => $count) {
    echo "- {$type}: {$count}\n";
}

// Get status of a specific document
// $documentStatus = $client->getDocumentStatus('document-uuid-here');
// echo "Document status: {$documentStatus->status->value}\n";
// echo "Delivered: {$documentStatus->deliveryTime->format('Y-m-d H:i:s')}\n";
// if ($documentStatus->isRead()) {
//     echo "Read at: {$documentStatus->readTime->format('Y-m-d H:i:s')}\n";
// }
