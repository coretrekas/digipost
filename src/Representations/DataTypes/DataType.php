<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\DataTypes;

use SimpleXMLElement;

/**
 * Base interface for document data types.
 */
interface DataType
{
    /**
     * Add this data type to an XML element.
     */
    public function addToXml(SimpleXMLElement $parent): void;
}
