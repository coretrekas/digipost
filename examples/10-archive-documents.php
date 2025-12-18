<?php

/**
 * Archive Documents Example
 *
 * This example demonstrates how to store documents in
 * the Digipost archive for long-term storage.
 */

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Coretrek\Digipost\DigipostClient;
use Coretrek\Digipost\DigipostClientConfig;
use Coretrek\Digipost\Representations\Archive\ArchiveDocumentContent;
use Coretrek\Digipost\Representations\FileType;
use Coretrek\Digipost\Security\Signer;
use Coretrek\Digipost\SenderId;

// Set up the client
$signer = Signer::fromPkcs12File('/path/to/certificate.p12', 'password');
$config = DigipostClientConfig::production();
$client = new DigipostClient($config, SenderId::of(123456), $signer);

// Read document content
$pdfContent = file_get_contents('/path/to/contract.pdf');

// Create archive document with metadata
$archiveDocument = ArchiveDocumentContent::create(
    fileName: 'contract-2024-001.pdf',
    fileType: FileType::PDF,
    content: $pdfContent,
    referenceId: 'contract-2024-001',
    attributes: [
        'customer_id' => 'CUST-12345',
        'contract_type' => 'annual',
        'department' => 'sales',
        'year' => '2024',
    ],
);

// Store document in archive
$archivedDocument = $client->archiveDocument($archiveDocument);

echo "Document archived successfully!\n";
echo "Archive UUID: {$archivedDocument->uuid->toString()}\n";
echo "Reference ID: {$archivedDocument->referenceId}\n";

// List all archives
$archives = $client->getArchives();
echo "\nAvailable archives:\n";
foreach ($archives->archives as $archive) {
    echo "- {$archive->name}\n";
}

// Retrieve documents by reference ID
$foundDocuments = $client->getArchiveDocumentsByReferenceId('contract-2024-001');
echo "\nDocuments with reference ID 'contract-2024-001':\n";
foreach ($foundDocuments->documents as $doc) {
    echo "- {$doc->fileName} (UUID: {$doc->uuid->toString()})\n";
}

// Get specific archive by name
// $archive = $client->getArchiveByName('default');

// Get document content from archive
// $content = $client->getArchiveDocumentContent($archivedDocument);
