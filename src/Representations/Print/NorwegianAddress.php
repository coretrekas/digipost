<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\Print;

use SimpleXMLElement;

/**
 * Norwegian postal address.
 */
final readonly class NorwegianAddress implements PrintAddress
{
    public function __construct(
        public string $addressLine1,
        public string $postalCode,
        public string $city,
        public ?string $addressLine2 = null,
    ) {}

    public function addToXml(SimpleXMLElement $parent): void
    {
        $address = $parent->addChild('norwegian-address');

        if ($address === null) {
            return;
        }

        $address->addChild('addressline1', htmlspecialchars($this->addressLine1, ENT_XML1));

        if ($this->addressLine2 !== null) {
            $address->addChild('addressline2', htmlspecialchars($this->addressLine2, ENT_XML1));
        }

        $address->addChild('zip-code', $this->postalCode);
        $address->addChild('city', htmlspecialchars($this->city, ENT_XML1));
    }
}
