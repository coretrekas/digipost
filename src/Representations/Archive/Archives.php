<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\Archive;

use SimpleXMLElement;

/**
 * Collection of archives.
 */
final readonly class Archives
{
    /**
     * @param Archive[] $archives
     */
    public function __construct(
        public array $archives = [],
    ) {}

    /**
     * Create from XML response.
     */
    public static function fromXml(string $xml): self
    {
        $element = new SimpleXMLElement($xml);
        $archives = [];

        foreach ($element->archive as $archiveElement) {
            $archives[] = Archive::fromXmlElement($archiveElement);
        }

        return new self(archives: $archives);
    }
}
