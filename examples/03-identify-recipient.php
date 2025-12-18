<?php

/**
 * Identify Recipient Example
 *
 * This example demonstrates how to check if a recipient
 * has a Digipost account before sending a message.
 */

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Coretrek\Digipost\DigipostClient;
use Coretrek\Digipost\DigipostClientConfig;
use Coretrek\Digipost\Representations\DigipostAddress;
use Coretrek\Digipost\Representations\Identification;
use Coretrek\Digipost\Representations\PersonalIdentificationNumber;
use Coretrek\Digipost\Security\Signer;
use Coretrek\Digipost\SenderId;

// Set up the client
$signer = Signer::fromPkcs12File('/path/to/certificate.p12', 'password');
$config = DigipostClientConfig::production();
$client = new DigipostClient($config, SenderId::of(123456), $signer);

// Identify by Digipost address
$identification = Identification::fromDigipostAddress(
    new DigipostAddress('john.doe'),
);

$result = $client->identify($identification);

if ($result->isDigipostUser()) {
    echo "Recipient has a Digipost account - can send digitally!\n";
    echo "Digipost address: {$result->digipostAddress->value}\n";
} elseif ($result->isIdentified()) {
    echo "Recipient is identified but does not have Digipost.\n";
    echo "Consider using print fallback.\n";
} else {
    echo "Recipient could not be identified.\n";
    echo "Result code: {$result->result->value}\n";
}

// You can also identify by personal identification number (fødselsnummer)
// $identification = Identification::fromPersonalIdentificationNumber(
//     new PersonalIdentificationNumber('12345678901')
// );
// $result = $client->identify($identification);
