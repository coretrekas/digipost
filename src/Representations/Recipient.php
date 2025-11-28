<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

use SimpleXMLElement;

/**
 * A recipient from search results.
 */
final readonly class Recipient
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public ?string $middleName = null,
        public ?DigipostAddress $digipostAddress = null,
        public ?string $mobileNumber = null,
    ) {}

    /**
     * Get the full name.
     */
    public function getFullName(): string
    {
        $parts = [$this->firstName];

        if ($this->middleName !== null) {
            $parts[] = $this->middleName;
        }

        $parts[] = $this->lastName;

        return implode(' ', $parts);
    }

    /**
     * Create from XML element.
     */
    public static function fromXmlElement(SimpleXMLElement $element): self
    {
        return new self(
            firstName: (string) $element->firstname,
            lastName: (string) $element->lastname,
            middleName: property_exists($element, 'middlename') && $element->middlename !== null ? (string) $element->middlename : null,
            digipostAddress: isset($element->{'digipost-address'})
                ? new DigipostAddress((string) $element->{'digipost-address'})
                : null,
            mobileNumber: isset($element->{'mobile-number'}) ? (string) $element->{'mobile-number'} : null,
        );
    }
}
