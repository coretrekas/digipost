<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Api;

use Coretrek\Digipost\Http\DigipostHttpClient;
use Coretrek\Digipost\Representations\SharedDocuments\SharedDocument;
use Coretrek\Digipost\Representations\SharedDocuments\SharedDocuments;
use Coretrek\Digipost\SenderId;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * API for shared documents.
 */
final readonly class SharedDocumentsApi
{
    public function __construct(
        private DigipostHttpClient $httpClient,
        private SenderId $senderId,
    ) {}

    /**
     * Get shared documents by share ID.
     */
    public function getSharedDocuments(UuidInterface $shareId): SharedDocuments
    {
        $response = $this->httpClient->get(
            "/shared-documents/{$shareId->toString()}",
        );

        return SharedDocuments::fromXml($response);
    }

    /**
     * Get the content of a shared document.
     */
    public function getDocumentContent(SharedDocument $document): StreamInterface
    {
        if ($document->contentUri === null) {
            throw new InvalidArgumentException('Document has no content URI');
        }

        return $this->httpClient->getStream($document->contentUri);
    }

    /**
     * Stop sharing documents.
     */
    public function stopSharing(UuidInterface $shareId): void
    {
        $this->httpClient->delete(
            "/shared-documents/{$shareId->toString()}",
        );
    }
}
