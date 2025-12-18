<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Api;

use Coretrek\Digipost\Http\DigipostHttpClient;
use Coretrek\Digipost\Representations\DocumentEvents;
use Coretrek\Digipost\Representations\DocumentStatus;
use Coretrek\Digipost\SenderId;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * API for document operations.
 */
final readonly class DocumentApi
{
    public function __construct(
        private DigipostHttpClient $httpClient,
        private SenderId $senderId,
    ) {}

    /**
     * Get the status of a document.
     */
    public function getDocumentStatus(UuidInterface $documentUuid): DocumentStatus
    {
        $response = $this->httpClient->get(
            "/documents/{$documentUuid->toString()}/status",
        );

        return DocumentStatus::fromXml($response);
    }

    /**
     * Get document events.
     */
    public function getDocumentEvents(
        ?DateTimeInterface $from = null,
        ?DateTimeInterface $to = null,
        int $offset = 0,
        int $maxResults = 100,
    ): DocumentEvents {
        $params = [
            'offset' => $offset,
            'maxResults' => $maxResults,
        ];

        if ($from instanceof DateTimeInterface) {
            $params['from'] = $from->format('c');
        }

        if ($to instanceof DateTimeInterface) {
            $params['to'] = $to->format('c');
        }

        $response = $this->httpClient->get(
            '/documents/events',
            $params,
        );

        return DocumentEvents::fromXml($response);
    }
}
