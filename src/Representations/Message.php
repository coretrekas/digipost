<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

use Coretrek\Digipost\Representations\Recipients\MessageRecipient;
use Coretrek\Digipost\SenderId;
use DOMDocument;
use Ramsey\Uuid\UuidInterface;
use SimpleXMLElement;

/**
 * Represents a message to be sent through Digipost.
 */
final readonly class Message
{
    /**
     * @param Document[] $attachments
     *
     * @internal Use Message::newMessage() to create instances
     */
    public function __construct(
        public string $messageId,
        public Document $primaryDocument,
        public ?MessageRecipient $recipient = null,
        public array $attachments = [],
        public ?SenderId $senderId = null,
        public ?RequestForRegistration $requestForRegistration = null,
        public ?UuidInterface $batchId = null,
    ) {}

    /**
     * Create a new message builder.
     */
    public static function newMessage(string $messageId, Document $primaryDocument): MessageBuilder
    {
        return new MessageBuilder($messageId, $primaryDocument);
    }

    /**
     * Convert to XML for the API.
     */
    public function toXml(): string
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><message xmlns="http://api.digipost.no/schema/v8"></message>');

        $xml->addChild('message-id', $this->messageId);

        if ($this->senderId instanceof SenderId) {
            $xml->addChild('sender-id', (string) $this->senderId);
        }

        if ($this->recipient instanceof MessageRecipient) {
            $this->recipient->addToXml($xml);
        }

        $this->primaryDocument->addToXml($xml, 'primary-document');

        foreach ($this->attachments as $attachment) {
            $attachment->addToXml($xml, 'attachment');
        }

        if ($this->requestForRegistration instanceof RequestForRegistration) {
            $this->requestForRegistration->addToXml($xml);
        }

        $dom = dom_import_simplexml($xml);
        if ($dom->ownerDocument instanceof DOMDocument) {
            $dom->ownerDocument->formatOutput = true;
            $result = $dom->ownerDocument->saveXML();

            return $result !== false ? $result : '';
        }

        $xmlString = $xml->asXML();

        return $xmlString !== false ? $xmlString : '';
    }
}

/**
 * Builder for creating Message instances.
 */
final class MessageBuilder
{
    private ?MessageRecipient $recipient = null;

    /** @var Document[] */
    private array $attachments = [];

    private ?SenderId $senderId = null;

    private ?RequestForRegistration $requestForRegistration = null;

    private ?UuidInterface $batchId = null;

    public function __construct(
        private readonly string $messageId,
        private readonly Document $primaryDocument,
    ) {}

    /**
     * Set the recipient.
     */
    public function recipient(MessageRecipient $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * Set the recipient using a personal identification number.
     */
    public function personalIdentificationNumber(PersonalIdentificationNumber $pin): self
    {
        $this->recipient = MessageRecipient::fromPersonalIdentificationNumber($pin);

        return $this;
    }

    /**
     * Set the recipient using a Digipost address.
     */
    public function digipostAddress(DigipostAddress $address): self
    {
        $this->recipient = MessageRecipient::fromDigipostAddress($address);

        return $this;
    }

    /**
     * Add attachments.
     */
    public function attachments(Document ...$attachments): self
    {
        $this->attachments = $attachments;

        return $this;
    }

    /**
     * Set the sender ID.
     */
    public function senderId(SenderId $senderId): self
    {
        $this->senderId = $senderId;

        return $this;
    }

    /**
     * Set the request for registration.
     */
    public function requestForRegistration(RequestForRegistration $request): self
    {
        $this->requestForRegistration = $request;

        return $this;
    }

    /**
     * Set the batch ID.
     */
    public function batch(UuidInterface $batchId): self
    {
        $this->batchId = $batchId;

        return $this;
    }

    /**
     * Build the message.
     */
    public function build(): Message
    {
        return new Message(
            messageId: $this->messageId,
            primaryDocument: $this->primaryDocument,
            recipient: $this->recipient,
            attachments: $this->attachments,
            senderId: $this->senderId,
            requestForRegistration: $this->requestForRegistration,
            batchId: $this->batchId,
        );
    }
}
