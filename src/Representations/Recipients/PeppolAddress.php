<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\Recipients;

use SimpleXMLElement;

/**
 * A PEPPOL address consisting of a scheme ID and endpoint ID.
 */
final readonly class PeppolAddress
{
    public function __construct(
        public string $schemeId,
        public string $endpointId,
    ) {}

    /**
     * Add PEPPOL address to an XML element.
     */
    public function addToXml(SimpleXMLElement $parent, string $elementName): void
    {
        $element = $parent->addChild($elementName);

        if ($element === null) {
            return;
        }

        $element->addChild('schemeID', htmlspecialchars($this->schemeId, ENT_XML1));
        $element->addChild('endpointID', htmlspecialchars($this->endpointId, ENT_XML1));
    }
}
