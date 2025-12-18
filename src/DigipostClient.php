<?php

declare(strict_types=1);

namespace Coretrek\Digipost;

use Coretrek\Digipost\Api\ArchiveApi;
use Coretrek\Digipost\Api\BatchApi;
use Coretrek\Digipost\Api\DocumentApi;
use Coretrek\Digipost\Api\InboxApi;
use Coretrek\Digipost\Api\MessageApi;
use Coretrek\Digipost\Api\SharedDocumentsApi;
use Coretrek\Digipost\Api\UserApi;
use Coretrek\Digipost\Http\DigipostHttpClient;
use Coretrek\Digipost\Representations\Archive\Archive;
use Coretrek\Digipost\Representations\Archive\ArchiveDocument;
use Coretrek\Digipost\Representations\Archive\ArchiveDocumentContent;
use Coretrek\Digipost\Representations\Archive\Archives;
use Coretrek\Digipost\Representations\Autocomplete;
use Coretrek\Digipost\Representations\Batch\Batch;
use Coretrek\Digipost\Representations\DocumentEvents;
use Coretrek\Digipost\Representations\DocumentStatus;
use Coretrek\Digipost\Representations\Identification;
use Coretrek\Digipost\Representations\IdentificationResult;
use Coretrek\Digipost\Representations\Inbox\Inbox;
use Coretrek\Digipost\Representations\Inbox\InboxDocument;
use Coretrek\Digipost\Representations\Message;
use Coretrek\Digipost\Representations\MessageDelivery;
use Coretrek\Digipost\Representations\PersonalIdentificationNumber;
use Coretrek\Digipost\Representations\Recipients;
use Coretrek\Digipost\Representations\SenderInformation;
use Coretrek\Digipost\Representations\SharedDocuments\SharedDocument;
use Coretrek\Digipost\Representations\SharedDocuments\SharedDocuments;
use Coretrek\Digipost\Representations\UserAccount;
use Coretrek\Digipost\Security\Signer;
use DateTimeInterface;
use Psr\Http\Message\StreamInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * A client for sending letters through Digipost.
 *
 * If an object of this class is created with a working certificate and associated password,
 * you can search and send letters through Digipost.
 */
final readonly class DigipostClient
{
    private DigipostHttpClient $httpClient;

    private MessageApi $messageApi;

    private InboxApi $inboxApi;

    private DocumentApi $documentApi;

    private ArchiveApi $archiveApi;

    private BatchApi $batchApi;

    private SharedDocumentsApi $sharedDocumentsApi;

    private UserApi $userApi;

    public function __construct(
        DigipostClientConfig $config,
        SenderId $senderId,
        Signer $signer,
    ) {
        $this->httpClient = new DigipostHttpClient($config, $senderId, $signer);

        $this->messageApi = new MessageApi($this->httpClient);
        $this->inboxApi = new InboxApi($this->httpClient, $senderId);
        $this->documentApi = new DocumentApi($this->httpClient);
        $this->archiveApi = new ArchiveApi($this->httpClient);
        $this->batchApi = new BatchApi($this->httpClient);
        $this->sharedDocumentsApi = new SharedDocumentsApi($this->httpClient);
        $this->userApi = new UserApi($this->httpClient);
    }

    /**
     * Send a message through Digipost.
     *
     * @param Message $message The message to send
     * @param array<string, string> $documentContents Map of document UUID to content
     */
    public function sendMessage(Message $message, array $documentContents): MessageDelivery
    {
        return $this->messageApi->sendMessage($message, $documentContents);
    }

    /**
     * Identify a recipient to check if they have a Digipost account.
     */
    public function identify(Identification $identification): IdentificationResult
    {
        return $this->messageApi->identify($identification);
    }

    /**
     * Search for recipients.
     */
    public function search(string $query): Recipients
    {
        return $this->messageApi->search($query);
    }

    /**
     * Get autocomplete suggestions for recipient search.
     */
    public function searchSuggest(string $query): Autocomplete
    {
        return $this->messageApi->searchSuggest($query);
    }

    /**
     * Get information about the sender.
     */
    public function getSenderInformation(): SenderInformation
    {
        return $this->messageApi->getSenderInformation();
    }

    /**
     * Get document events within a time range.
     */
    public function getDocumentEvents(
        ?DateTimeInterface $from = null,
        ?DateTimeInterface $to = null,
        int $offset = 0,
        int $maxResults = 100,
    ): DocumentEvents {
        return $this->documentApi->getDocumentEvents($from, $to, $offset, $maxResults);
    }

    /**
     * Get the status of a document.
     */
    public function getDocumentStatus(UuidInterface $documentUuid): DocumentStatus
    {
        return $this->documentApi->getDocumentStatus($documentUuid);
    }

    /**
     * Get inbox documents.
     */
    public function getInbox(int $offset = 0, int $limit = 100): Inbox
    {
        return $this->inboxApi->getInbox($offset, $limit);
    }

    /**
     * Get inbox document content as a stream.
     */
    public function getInboxDocumentContent(InboxDocument $document): StreamInterface
    {
        return $this->inboxApi->getDocumentContent($document);
    }

    /**
     * Delete an inbox document.
     */
    public function deleteInboxDocument(InboxDocument $document): void
    {
        $this->inboxApi->deleteDocument($document);
    }

    /**
     * Get all archives.
     */
    public function getArchives(): Archives
    {
        return $this->archiveApi->getArchives();
    }

    /**
     * Get a specific archive by name.
     */
    public function getArchive(string $archiveName): Archive
    {
        return $this->archiveApi->getArchive($archiveName);
    }

    /**
     * Get the default archive.
     */
    public function getDefaultArchive(): Archive
    {
        return $this->archiveApi->getDefaultArchive();
    }

    /**
     * Archive a document.
     */
    public function archiveDocument(ArchiveDocumentContent $document, string $archiveName = 'default'): ArchiveDocument
    {
        return $this->archiveApi->archiveDocument($document, $archiveName);
    }

    /**
     * Get archive document content as a stream.
     */
    public function getArchiveDocumentContent(ArchiveDocument $document): StreamInterface
    {
        return $this->archiveApi->getDocumentContent($document);
    }

    /**
     * Delete an archive document.
     */
    public function deleteArchiveDocument(ArchiveDocument $document): void
    {
        $this->archiveApi->deleteDocument($document);
    }

    /**
     * Get archive documents by reference ID.
     */
    public function getArchiveDocumentsByReferenceId(string $referenceId, string $archiveName = 'default'): Archive
    {
        return $this->archiveApi->getDocumentsByReferenceId($referenceId, $archiveName);
    }

    /**
     * Create a new batch.
     */
    public function createBatch(): Batch
    {
        return $this->batchApi->createBatch();
    }

    /**
     * Get a batch by UUID.
     */
    public function getBatch(UuidInterface $batchId): Batch
    {
        return $this->batchApi->getBatch($batchId);
    }

    /**
     * Add a message to a batch.
     *
     * @param array<string, string> $documentContents Map of document UUID to content
     */
    public function addMessageToBatch(UuidInterface $batchId, Message $message, array $documentContents): MessageDelivery
    {
        return $this->batchApi->addMessage($batchId, $message, $documentContents);
    }

    /**
     * Complete a batch (start processing).
     */
    public function completeBatch(Batch $batch): Batch
    {
        return $this->batchApi->completeBatch($batch);
    }

    /**
     * Cancel a batch.
     */
    public function cancelBatch(Batch $batch): void
    {
        $this->batchApi->cancelBatch($batch);
    }

    /**
     * Get shared documents by share ID.
     */
    public function getSharedDocuments(UuidInterface $shareId): SharedDocuments
    {
        return $this->sharedDocumentsApi->getSharedDocuments($shareId);
    }

    /**
     * Get shared document content as a stream.
     */
    public function getSharedDocumentContent(SharedDocument $document): StreamInterface
    {
        return $this->sharedDocumentsApi->getDocumentContent($document);
    }

    /**
     * Stop sharing documents.
     */
    public function stopSharing(UuidInterface $shareId): void
    {
        $this->sharedDocumentsApi->stopSharing($shareId);
    }

    /**
     * Create or activate a user account.
     */
    public function createOrActivateUserAccount(PersonalIdentificationNumber $pin): UserAccount
    {
        return $this->userApi->createOrActivateUserAccount($pin);
    }

    /**
     * Get the message API for advanced operations.
     */
    public function messages(): MessageApi
    {
        return $this->messageApi;
    }

    /**
     * Get the inbox API for advanced operations.
     */
    public function inbox(): InboxApi
    {
        return $this->inboxApi;
    }

    /**
     * Get the document API for advanced operations.
     */
    public function documents(): DocumentApi
    {
        return $this->documentApi;
    }

    /**
     * Get the archive API for advanced operations.
     */
    public function archives(): ArchiveApi
    {
        return $this->archiveApi;
    }

    /**
     * Get the batch API for advanced operations.
     */
    public function batches(): BatchApi
    {
        return $this->batchApi;
    }

    /**
     * Get the shared documents API for advanced operations.
     */
    public function sharedDocuments(): SharedDocumentsApi
    {
        return $this->sharedDocumentsApi;
    }

    /**
     * Get the user API for advanced operations.
     */
    public function users(): UserApi
    {
        return $this->userApi;
    }
}
