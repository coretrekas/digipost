<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\Recipients;

use SimpleXMLElement;

/**
 * Represents a recipient identified by name and address.
 */
final readonly class NameAndAddress
{
    public function __construct(
        public string $fullName,
        public string $addressLine1,
        public ?string $addressLine2 = null,
        public string $postalCode = '',
        public string $city = '',
    ) {}

    /**
     * Add this name and address to an XML element.
     */
    public function addToXml(SimpleXMLElement $parent): void
    {
        $nameAndAddress = $parent->addChild('name-and-address');

        if ($nameAndAddress === null) {
            return;
        }

        $nameAndAddress->addChild('fullname', htmlspecialchars($this->fullName, ENT_XML1));
        $nameAndAddress->addChild('addressline1', htmlspecialchars($this->addressLine1, ENT_XML1));

        if ($this->addressLine2 !== null) {
            $nameAndAddress->addChild('addressline2', htmlspecialchars($this->addressLine2, ENT_XML1));
        }

        $nameAndAddress->addChild('postalcode', $this->postalCode);
        $nameAndAddress->addChild('city', htmlspecialchars($this->city, ENT_XML1));
    }
}
