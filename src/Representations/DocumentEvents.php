<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

use SimpleXMLElement;

/**
 * Collection of document events.
 */
final readonly class DocumentEvents
{
    /**
     * @param DocumentEvent[] $events
     */
    public function __construct(
        public array $events = [],
    ) {}

    /**
     * Create from XML response.
     */
    public static function fromXml(string $xml): self
    {
        $element = new SimpleXMLElement($xml);
        $events = [];

        foreach ($element->event as $eventElement) {
            $events[] = DocumentEvent::fromXmlElement($eventElement);
        }

        return new self(events: $events);
    }
}
