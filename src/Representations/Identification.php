<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

use SimpleXMLElement;

/**
 * Identification request for checking if a recipient has a Digipost account.
 */
final readonly class Identification
{
    private function __construct(
        public ?PersonalIdentificationNumber $personalIdentificationNumber = null,
        public ?DigipostAddress $digipostAddress = null,
    ) {}

    /**
     * Create identification from a personal identification number.
     */
    public static function fromPersonalIdentificationNumber(PersonalIdentificationNumber $pin): self
    {
        return new self(personalIdentificationNumber: $pin);
    }

    /**
     * Create identification from a Digipost address.
     */
    public static function fromDigipostAddress(DigipostAddress $address): self
    {
        return new self(digipostAddress: $address);
    }

    /**
     * Convert to XML for the API.
     */
    public function toXml(): string
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><identification xmlns="http://api.digipost.no/schema/v8"></identification>');

        if ($this->personalIdentificationNumber instanceof PersonalIdentificationNumber) {
            $xml->addChild('personal-identification-number', $this->personalIdentificationNumber->value);
        }

        if ($this->digipostAddress instanceof DigipostAddress) {
            $xml->addChild('digipost-address', $this->digipostAddress->value);
        }

        $xmlString = $xml->asXML();

        return $xmlString !== false ? $xmlString : '';
    }
}
