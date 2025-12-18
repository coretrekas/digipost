<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Api;

use Coretrek\Digipost\Http\DigipostHttpClient;
use Coretrek\Digipost\Representations\Batch\Batch;
use Coretrek\Digipost\Representations\Message;
use Coretrek\Digipost\Representations\MessageDelivery;
use Coretrek\Digipost\SenderId;
use InvalidArgumentException;
use Ramsey\Uuid\UuidInterface;

/**
 * API for batch operations.
 */
final readonly class BatchApi
{
    public function __construct(
        private DigipostHttpClient $httpClient,
        private SenderId $senderId,
    ) {}

    /**
     * Create a new batch.
     */
    public function createBatch(): Batch
    {
        $batch = Batch::create();

        $response = $this->httpClient->post(
            '/batches',
            $batch->toXml(),
        );

        return Batch::fromXml($response);
    }

    /**
     * Get a batch by UUID.
     */
    public function getBatch(UuidInterface $batchId): Batch
    {
        $response = $this->httpClient->get(
            "/batches/{$batchId->toString()}",
        );

        return Batch::fromXml($response);
    }

    /**
     * Add a message to a batch.
     *
     * @param array<string, string> $documentContents Map of document UUID to content
     */
    public function addMessage(UuidInterface $batchId, Message $message, array $documentContents): MessageDelivery
    {
        $multipart = $this->buildMultipartRequest($message, $documentContents);

        $response = $this->httpClient->postMultipart(
            "/batches/{$batchId->toString()}/messages",
            $multipart,
        );

        return MessageDelivery::fromXml($response);
    }

    /**
     * Complete a batch (start processing).
     */
    public function completeBatch(Batch $batch): Batch
    {
        if ($batch->completeUri === null) {
            throw new InvalidArgumentException('Batch has no complete URI');
        }

        $response = $this->httpClient->post($batch->completeUri, '');

        return Batch::fromXml($response);
    }

    /**
     * Cancel a batch.
     */
    public function cancelBatch(Batch $batch): void
    {
        if ($batch->cancelUri === null) {
            throw new InvalidArgumentException('Batch has no cancel URI');
        }

        $this->httpClient->post($batch->cancelUri, '');
    }

    /**
     * Build multipart request for adding a message to a batch.
     *
     * @param array<string, string> $documentContents
     *
     * @return array<int, array{name: string, contents: string, headers?: array<string, string>}>
     */
    private function buildMultipartRequest(Message $message, array $documentContents): array
    {
        $multipart = [
            [
                'name' => 'message',
                'contents' => $message->toXml(),
                'headers' => ['Content-Type' => 'application/vnd.digipost-v8+xml'],
            ],
        ];

        // Add primary document content
        $primaryUuid = $message->primaryDocument->uuid->toString();
        if (isset($documentContents[$primaryUuid])) {
            $multipart[] = [
                'name' => $primaryUuid,
                'contents' => $documentContents[$primaryUuid],
                'headers' => ['Content-Type' => $message->primaryDocument->fileType->getMimeType()],
            ];
        }

        // Add attachment contents
        foreach ($message->attachments as $attachment) {
            $attachmentUuid = $attachment->uuid->toString();
            if (isset($documentContents[$attachmentUuid])) {
                $multipart[] = [
                    'name' => $attachmentUuid,
                    'contents' => $documentContents[$attachmentUuid],
                    'headers' => ['Content-Type' => $attachment->fileType->getMimeType()],
                ];
            }
        }

        return $multipart;
    }
}
