<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Api;

use Coretrek\Digipost\Http\DigipostHttpClient;
use Coretrek\Digipost\Representations\Autocomplete;
use Coretrek\Digipost\Representations\Identification;
use Coretrek\Digipost\Representations\IdentificationResult;
use Coretrek\Digipost\Representations\Message;
use Coretrek\Digipost\Representations\MessageDelivery;
use Coretrek\Digipost\Representations\Recipients;
use Coretrek\Digipost\Representations\SenderInformation;

/**
 * API for sending messages.
 */
final readonly class MessageApi
{
    public function __construct(
        private DigipostHttpClient $httpClient,
    ) {}

    /**
     * Send a message with document content.
     *
     * @param array<string, string> $documentContents Map of document UUID to content
     */
    public function sendMessage(Message $message, array $documentContents): MessageDelivery
    {
        $multipart = $this->buildMultipartRequest($message, $documentContents);

        $response = $this->httpClient->postMultipart(
            '/messages',
            $multipart,
        );

        return MessageDelivery::fromXml($response);
    }

    /**
     * Identify a recipient.
     */
    public function identify(Identification $identification): IdentificationResult
    {
        $response = $this->httpClient->post(
            '/identification',
            $identification->toXml(),
        );

        return IdentificationResult::fromXml($response);
    }

    /**
     * Search for recipients.
     */
    public function search(string $query): Recipients
    {
        $response = $this->httpClient->get(
            "/recipients/search/{$query}",
        );

        return Recipients::fromXml($response);
    }

    /**
     * Get autocomplete suggestions for recipient search.
     */
    public function searchSuggest(string $query): Autocomplete
    {
        $response = $this->httpClient->get(
            '/recipients/autocomplete',
            ['search' => $query],
        );

        return Autocomplete::fromXml($response);
    }

    /**
     * Get sender information.
     */
    public function getSenderInformation(): SenderInformation
    {
        $response = $this->httpClient->get('/sender');

        return SenderInformation::fromXml($response);
    }

    /**
     * Build multipart request for sending a message.
     *
     * @param array<string, string> $documentContents
     *
     * @return array<int, array{name: string, contents: string, headers?: array<string, string>, filename?: string}>
     */
    private function buildMultipartRequest(Message $message, array $documentContents): array
    {
        $multipart = [
            [
                'name' => 'message',
                'contents' => $message->toXml(),
                'filename' => 'message',
                'headers' => [
                    'Content-Type' => 'application/vnd.digipost-v8+xml',
                ],
            ],
        ];

        // Add primary document content
        $primaryUuid = $message->primaryDocument->uuid->toString();
        if (isset($documentContents[$primaryUuid])) {
            $multipart[] = [
                'name' => $primaryUuid,
                'contents' => $documentContents[$primaryUuid],
                'filename' => $primaryUuid,
                'headers' => [
                    'Content-Type' => $message->primaryDocument->fileType->getMimeType(),
                ],
            ];
        }

        // Add attachment contents
        foreach ($message->attachments as $attachment) {
            $attachmentUuid = $attachment->uuid->toString();
            if (isset($documentContents[$attachmentUuid])) {
                $multipart[] = [
                    'name' => $attachmentUuid,
                    'contents' => $documentContents[$attachmentUuid],
                    'filename' => $attachmentUuid,
                    'headers' => [
                        'Content-Type' => $attachment->fileType->getMimeType(),
                    ],
                ];
            }
        }

        return $multipart;
    }
}
