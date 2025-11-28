<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\DataTypes;

use SimpleXMLElement;

/**
 * Address for an appointment.
 */
final readonly class AppointmentAddress
{
    public function __construct(
        public string $streetAddress,
        public string $postalCode,
        public string $city,
    ) {}

    /**
     * Add this address to an XML element.
     */
    public function addToXml(SimpleXMLElement $parent): void
    {
        $address = $parent->addChild('address');

        if ($address === null) {
            return;
        }

        $address->addChild('street-address', htmlspecialchars($this->streetAddress, ENT_XML1));
        $address->addChild('postal-code', $this->postalCode);
        $address->addChild('city', htmlspecialchars($this->city, ENT_XML1));
    }
}
