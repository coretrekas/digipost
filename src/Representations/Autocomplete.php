<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

use SimpleXMLElement;

/**
 * Autocomplete suggestions for recipient search.
 */
final readonly class Autocomplete
{
    /**
     * @param string[] $suggestions
     */
    public function __construct(
        public array $suggestions = [],
    ) {}

    /**
     * Create from XML response.
     */
    public static function fromXml(string $xml): self
    {
        $element = new SimpleXMLElement($xml);
        $suggestions = [];

        foreach ($element->suggestion as $suggestion) {
            $suggestions[] = (string) $suggestion;
        }

        return new self(suggestions: $suggestions);
    }
}
