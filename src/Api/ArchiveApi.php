<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Api;

use Coretrek\Digipost\Http\DigipostHttpClient;
use Coretrek\Digipost\Representations\Archive\Archive;
use Coretrek\Digipost\Representations\Archive\ArchiveDocument;
use Coretrek\Digipost\Representations\Archive\ArchiveDocumentContent;
use Coretrek\Digipost\Representations\Archive\Archives;
use Coretrek\Digipost\SenderId;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Ramsey\Uuid\UuidInterface;
use SimpleXMLElement;

/**
 * API for managing archives.
 */
final readonly class ArchiveApi
{
    public function __construct(
        private DigipostHttpClient $httpClient,
        private SenderId $senderId,
    ) {}

    /**
     * Get all archives.
     */
    public function getArchives(): Archives
    {
        $response = $this->httpClient->get("/archives");

        return Archives::fromXml($response);
    }

    /**
     * Get a specific archive by name.
     */
    public function getArchive(string $archiveName): Archive
    {
        $response = $this->httpClient->get(
            "/archives/{$archiveName}",
        );

        return Archive::fromXml($response);
    }

    /**
     * Get the default archive.
     */
    public function getDefaultArchive(): Archive
    {
        return $this->getArchive('default');
    }

    /**
     * Archive a document.
     */
    public function archiveDocument(ArchiveDocumentContent $document, string $archiveName = 'default'): ArchiveDocument
    {
        $multipart = [
            [
                'name' => 'document',
                'contents' => $document->toXml(),
                'headers' => ['Content-Type' => 'application/vnd.digipost-v8+xml'],
            ],
            [
                'name' => $document->uuid->toString(),
                'contents' => $document->content,
                'headers' => ['Content-Type' => $document->contentType],
            ],
        ];

        $response = $this->httpClient->postMultipart(
            "/archives/{$archiveName}/documents",
            $multipart,
        );

        $element = new SimpleXMLElement($response);

        return ArchiveDocument::fromXmlElement($element);
    }

    /**
     * Get a document from an archive.
     */
    public function getDocument(string $archiveName, UuidInterface $documentUuid): ArchiveDocument
    {
        $response = $this->httpClient->get(
            "/archives/{$archiveName}/documents/{$documentUuid->toString()}",
        );

        $element = new SimpleXMLElement($response);

        return ArchiveDocument::fromXmlElement($element);
    }

    /**
     * Get the content of an archived document.
     */
    public function getDocumentContent(ArchiveDocument $document): StreamInterface
    {
        if ($document->contentUri === null) {
            throw new InvalidArgumentException('Document has no content URI');
        }

        return $this->httpClient->getStream($document->contentUri);
    }

    /**
     * Delete an archived document.
     */
    public function deleteDocument(ArchiveDocument $document): void
    {
        if ($document->deleteUri === null) {
            throw new InvalidArgumentException('Document has no delete URI');
        }

        $this->httpClient->delete($document->deleteUri);
    }

    /**
     * Get documents by reference ID.
     */
    public function getDocumentsByReferenceId(string $referenceId, string $archiveName = 'default'): Archive
    {
        $response = $this->httpClient->get(
            "/archives/{$archiveName}/documents",
            ['reference-id' => $referenceId],
        );

        return Archive::fromXml($response);
    }
}
