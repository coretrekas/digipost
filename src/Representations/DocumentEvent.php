<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SimpleXMLElement;

/**
 * A document event.
 */
final readonly class DocumentEvent
{
    public function __construct(
        public UuidInterface $uuid,
        public DocumentEventType $type,
        public DateTimeImmutable $timestamp,
    ) {}

    /**
     * Create from XML element.
     */
    public static function fromXmlElement(SimpleXMLElement $element): self
    {
        return new self(
            uuid: Uuid::fromString((string) $element->uuid),
            type: DocumentEventType::from((string) $element->type),
            timestamp: new DateTimeImmutable((string) $element->timestamp),
        );
    }
}
