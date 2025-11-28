<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\Batch;

use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SimpleXMLElement;

/**
 * A batch for sending multiple messages.
 */
final readonly class Batch
{
    public function __construct(
        public UuidInterface $uuid,
        public BatchStatus $status,
        public ?DateTimeImmutable $createdTime = null,
        public ?DateTimeImmutable $completedTime = null,
        public int $messageCount = 0,
        public int $completedCount = 0,
        public int $failedCount = 0,
        public ?string $selfUri = null,
        public ?string $completeUri = null,
        public ?string $cancelUri = null,
    ) {}

    /**
     * Create a new batch.
     */
    public static function create(): self
    {
        return new self(
            uuid: Uuid::uuid4(),
            status: BatchStatus::CREATED,
        );
    }

    /**
     * Create from XML response.
     */
    public static function fromXml(string $xml): self
    {
        $element = new SimpleXMLElement($xml);

        return self::fromXmlElement($element);
    }

    /**
     * Create from XML element.
     */
    public static function fromXmlElement(SimpleXMLElement $element): self
    {
        $selfUri = null;
        $completeUri = null;
        $cancelUri = null;

        foreach ($element->link as $link) {
            $rel = (string) $link['rel'];
            $href = (string) $link['href'];

            if ($rel === 'self') {
                $selfUri = $href;
            } elseif ($rel === 'complete') {
                $completeUri = $href;
            } elseif ($rel === 'cancel') {
                $cancelUri = $href;
            }
        }

        return new self(
            uuid: Uuid::fromString((string) $element->uuid),
            status: BatchStatus::from((string) $element->status),
            createdTime: isset($element->{'created-time'})
                ? new DateTimeImmutable((string) $element->{'created-time'})
                : null,
            completedTime: isset($element->{'completed-time'})
                ? new DateTimeImmutable((string) $element->{'completed-time'})
                : null,
            messageCount: isset($element->{'message-count'}) ? (int) $element->{'message-count'} : 0,
            completedCount: isset($element->{'completed-count'}) ? (int) $element->{'completed-count'} : 0,
            failedCount: isset($element->{'failed-count'}) ? (int) $element->{'failed-count'} : 0,
            selfUri: $selfUri,
            completeUri: $completeUri,
            cancelUri: $cancelUri,
        );
    }

    /**
     * Convert to XML for the API.
     */
    public function toXml(): string
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><batch xmlns="http://api.digipost.no/schema/v8"></batch>');

        $xml->addChild('uuid', $this->uuid->toString());

        $xmlString = $xml->asXML();

        return $xmlString !== false ? $xmlString : '';
    }
}
