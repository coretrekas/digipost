<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SimpleXMLElement;

/**
 * Result of a message delivery.
 */
final readonly class MessageDelivery
{
    /**
     * @param DeliveredDocument[] $documents
     */
    public function __construct(
        public string $messageId,
        public Channel $channel,
        public DeliveryStatus $status,
        public DateTimeImmutable $deliveryTime,
        public array $documents = [],
        public ?DigipostAddress $digipostAddress = null,
        public ?string $primaryDocumentUri = null,
    ) {}

    /**
     * Create from XML response.
     */
    public static function fromXml(string $xml): self
    {
        $element = new SimpleXMLElement($xml);

        $documents = [];
        foreach ($element->document as $docElement) {
            $documents[] = DeliveredDocument::fromXmlElement($docElement);
        }

        $primaryDocumentUri = null;
        foreach ($element->link as $link) {
            if ((string) $link['rel'] === 'get_primary_document_content') {
                $primaryDocumentUri = (string) $link['href'];
            }
        }

        return new self(
            messageId: (string) $element->{'message-id'},
            channel: Channel::from((string) $element->{'delivery-method'}),
            status: DeliveryStatus::from((string) $element->status),
            deliveryTime: new DateTimeImmutable((string) $element->{'delivery-time'}),
            documents: $documents,
            digipostAddress: isset($element->{'digipost-address'})
                ? new DigipostAddress((string) $element->{'digipost-address'})
                : null,
            primaryDocumentUri: $primaryDocumentUri,
        );
    }
}

/**
 * A delivered document.
 */
final readonly class DeliveredDocument
{
    public function __construct(
        public UuidInterface $uuid,
        public string $subject,
        public FileType $fileType,
        public ?string $contentUri = null,
    ) {}

    /**
     * Create from XML element.
     */
    public static function fromXmlElement(SimpleXMLElement $element): self
    {
        $contentUri = null;
        foreach ($element->link as $link) {
            if ((string) $link['rel'] === 'get_document_content') {
                $contentUri = (string) $link['href'];
            }
        }

        return new self(
            uuid: Uuid::fromString((string) $element->uuid),
            subject: (string) $element->subject,
            fileType: FileType::from((string) $element->{'file-type'}),
            contentUri: $contentUri,
        );
    }
}
