<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\Print;

use SimpleXMLElement;

/**
 * Base interface for print addresses.
 */
interface PrintAddress
{
    /**
     * Add this address to an XML element.
     */
    public function addToXml(SimpleXMLElement $parent): void;
}
