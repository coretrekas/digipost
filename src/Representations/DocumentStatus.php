<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

use DateTimeImmutable;
use SimpleXMLElement;

/**
 * Status of a document.
 */
final readonly class DocumentStatus
{
    public function __construct(
        public DeliveryStatus $status,
        public ?Channel $channel = null,
        public ?DateTimeImmutable $deliveryTime = null,
        public ?DateTimeImmutable $readTime = null,
    ) {}

    /**
     * Check if the document has been delivered.
     */
    public function isDelivered(): bool
    {
        return $this->status === DeliveryStatus::DELIVERED;
    }

    /**
     * Check if the document has been read.
     */
    public function isRead(): bool
    {
        return $this->readTime instanceof DateTimeImmutable;
    }

    /**
     * Create from XML response.
     */
    public static function fromXml(string $xml): self
    {
        $element = new SimpleXMLElement($xml);

        return new self(
            status: DeliveryStatus::from((string) $element->status),
            channel: property_exists($element, 'channel') && $element->channel !== null ? Channel::from((string) $element->channel) : null,
            deliveryTime: isset($element->{'delivery-time'})
                ? new DateTimeImmutable((string) $element->{'delivery-time'})
                : null,
            readTime: isset($element->{'read-time'})
                ? new DateTimeImmutable((string) $element->{'read-time'})
                : null,
        );
    }
}
