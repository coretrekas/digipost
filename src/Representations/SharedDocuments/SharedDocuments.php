<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\SharedDocuments;

use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SimpleXMLElement;

/**
 * Shared documents from a user.
 */
final readonly class SharedDocuments
{
    /**
     * @param SharedDocument[] $documents
     */
    public function __construct(
        public UuidInterface $shareId,
        public DateTimeImmutable $sharedAt,
        public DateTimeImmutable $expiresAt,
        public array $documents = [],
    ) {}

    /**
     * Create from XML response.
     */
    public static function fromXml(string $xml): self
    {
        $element = new SimpleXMLElement($xml);

        $documents = [];
        foreach ($element->document as $docElement) {
            $documents[] = SharedDocument::fromXmlElement($docElement);
        }

        return new self(
            shareId: Uuid::fromString((string) $element->{'share-id'}),
            sharedAt: new DateTimeImmutable((string) $element->{'shared-at'}),
            expiresAt: new DateTimeImmutable((string) $element->{'expires-at'}),
            documents: $documents,
        );
    }
}
