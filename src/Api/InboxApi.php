<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Api;

use Coretrek\Digipost\Http\DigipostHttpClient;
use Coretrek\Digipost\Representations\Inbox\Inbox;
use Coretrek\Digipost\Representations\Inbox\InboxDocument;
use Coretrek\Digipost\SenderId;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use SimpleXMLElement;

/**
 * API for managing inbox documents.
 */
final readonly class InboxApi
{
    public function __construct(
        private DigipostHttpClient $httpClient,
        private SenderId $senderId,
    ) {}

    /**
     * Get inbox documents.
     */
    public function getInbox(int $offset = 0, int $limit = 100): Inbox
    {
        $response = $this->httpClient->get(
            "/{$this->senderId}/inbox",
            [
                'offset' => $offset,
                'limit' => $limit,
            ],
        );

        return Inbox::fromXml($response);
    }

    /**
     * Get a specific inbox document.
     */
    public function getDocument(int $documentId): InboxDocument
    {
        $response = $this->httpClient->get(
            "/{$this->senderId}/inbox/{$documentId}",
        );

        $element = new SimpleXMLElement($response);

        return InboxDocument::fromXmlElement($element);
    }

    /**
     * Get the content of an inbox document.
     */
    public function getDocumentContent(InboxDocument $document): StreamInterface
    {
        if ($document->contentUri === null) {
            throw new InvalidArgumentException('Document has no content URI');
        }

        return $this->httpClient->getStream($document->contentUri);
    }

    /**
     * Delete an inbox document.
     */
    public function deleteDocument(InboxDocument $document): void
    {
        if ($document->deleteUri === null) {
            throw new InvalidArgumentException('Document has no delete URI');
        }

        $this->httpClient->delete($document->deleteUri);
    }
}
