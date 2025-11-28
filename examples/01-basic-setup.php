<?php

/**
 * Basic Setup Example
 *
 * This example demonstrates how to set up the Digipost client
 * with your certificate and configuration.
 */

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Coretrek\Digipost\DigipostClient;
use Coretrek\Digipost\DigipostClientConfig;
use Coretrek\Digipost\Security\Signer;
use Coretrek\Digipost\SenderId;

// Load certificate from file
$signer = Signer::fromPkcs12File(
    path: '/path/to/your/certificate.p12',
    password: 'your-certificate-password',
);

// Or load from environment variable / string
// $pkcs12Content = base64_decode($_ENV['DIGIPOST_CERTIFICATE_BASE64']);
// $signer = Signer::fromPkcs12String($pkcs12Content, $_ENV['DIGIPOST_CERTIFICATE_PASSWORD']);

// Or use separate PEM files
// $signer = Signer::fromPemFiles(
//     certificatePath: '/path/to/cert.pem',
//     privateKeyPath: '/path/to/key.pem',
//     privateKeyPassword: 'key-password',
// );

// Create configuration for production
$config = DigipostClientConfig::production();

// Or for test environment
// $config = DigipostClientConfig::test();

// Or for NHN (Norwegian Health Network)
// $config = DigipostClientConfig::nhn();

// Or with custom settings
// $config = DigipostClientConfig::builder()
//     ->apiUri('https://api.digipost.no')
//     ->requestTimeout(60)
//     ->connectionTimeout(15)
//     ->build();

// Create the client
$client = new DigipostClient(
    config: $config,
    senderId: SenderId::of(123456), // Your sender ID from Digipost
    signer: $signer,
);

echo "Digipost client configured successfully!\n";
