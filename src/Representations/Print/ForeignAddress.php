<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\Print;

use SimpleXMLElement;

/**
 * Foreign (non-Norwegian) postal address.
 */
final readonly class ForeignAddress implements PrintAddress
{
    public function __construct(
        public string $addressLine1,
        public string $country,
        public string $countryCode,
        public ?string $addressLine2 = null,
        public ?string $addressLine3 = null,
        public ?string $addressLine4 = null,
    ) {}

    public function addToXml(SimpleXMLElement $parent): void
    {
        $address = $parent->addChild('foreign-address');

        if ($address === null) {
            return;
        }

        $address->addChild('addressline1', htmlspecialchars($this->addressLine1, ENT_XML1));

        if ($this->addressLine2 !== null) {
            $address->addChild('addressline2', htmlspecialchars($this->addressLine2, ENT_XML1));
        }

        if ($this->addressLine3 !== null) {
            $address->addChild('addressline3', htmlspecialchars($this->addressLine3, ENT_XML1));
        }

        if ($this->addressLine4 !== null) {
            $address->addChild('addressline4', htmlspecialchars($this->addressLine4, ENT_XML1));
        }

        $address->addChild('country', htmlspecialchars($this->country, ENT_XML1));
        $address->addChild('country-code', $this->countryCode);
    }
}
