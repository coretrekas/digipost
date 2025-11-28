<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\Archive;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SimpleXMLElement;

/**
 * An archive for storing documents.
 */
final readonly class Archive
{
    /**
     * @param ArchiveDocument[] $documents
     */
    public function __construct(
        public string $name,
        public ?UuidInterface $uuid = null,
        public array $documents = [],
        public ?string $selfUri = null,
        public ?string $documentsUri = null,
    ) {}

    /**
     * Create from XML element.
     */
    public static function fromXmlElement(SimpleXMLElement $element): self
    {
        $documents = [];
        foreach ($element->document as $docElement) {
            $documents[] = ArchiveDocument::fromXmlElement($docElement);
        }

        $selfUri = null;
        $documentsUri = null;

        foreach ($element->link as $link) {
            $rel = (string) $link['rel'];
            $href = (string) $link['href'];

            if ($rel === 'self') {
                $selfUri = $href;
            } elseif ($rel === 'documents') {
                $documentsUri = $href;
            }
        }

        return new self(
            name: (string) $element->name,
            uuid: property_exists($element, 'uuid') && $element->uuid !== null ? Uuid::fromString((string) $element->uuid) : null,
            documents: $documents,
            selfUri: $selfUri,
            documentsUri: $documentsUri,
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
}
