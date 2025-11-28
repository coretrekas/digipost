<?php

/**
 * Autocomplete Example
 *
 * This example demonstrates how to use the autocomplete
 * feature to search for Digipost recipients.
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

// Search for recipients by name or address
$searchTerm = 'john';

$results = $client->autocomplete($searchTerm);

echo "Search results for '{$searchTerm}':\n";
echo "===================================\n\n";

foreach ($results->recipients as $recipient) {
    echo "Name: {$recipient->firstName} {$recipient->lastName}\n";

    if ($recipient->digipostAddress !== null) {
        echo "Digipost address: {$recipient->digipostAddress->value}\n";
    }

    echo "---\n";
}

if (count($results->recipients) === 0) {
    echo "No recipients found matching '{$searchTerm}'\n";
}

// The autocomplete is useful for building recipient search interfaces
// where users can type a name and see matching Digipost users.
