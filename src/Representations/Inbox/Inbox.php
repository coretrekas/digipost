<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\Inbox;

use SimpleXMLElement;

/**
 * Represents an inbox with documents.
 */
final readonly class Inbox
{
    /**
     * @param InboxDocument[] $documents
     */
    public function __construct(
        public array $documents = [],
        public int $count = 0,
        public int $offset = 0,
    ) {}

    /**
     * Create from XML response.
     */
    public static function fromXml(string $xml): self
    {
        $element = new SimpleXMLElement($xml);
        $documents = [];

        foreach ($element->document as $docElement) {
            $documents[] = InboxDocument::fromXmlElement($docElement);
        }

        return new self(
            documents: $documents,
            count: isset($element['count']) ? (int) $element['count'] : count($documents),
            offset: isset($element['offset']) ? (int) $element['offset'] : 0,
        );
    }
}
