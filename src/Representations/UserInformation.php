<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

use SimpleXMLElement;

/**
 * User information.
 */
final readonly class UserInformation
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public ?string $middleName = null,
        public ?DigipostAddress $digipostAddress = null,
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
     * Create from XML response.
     */
    public static function fromXml(string $xml): self
    {
        $element = new SimpleXMLElement($xml);

        return new self(
            firstName: (string) $element->firstname,
            lastName: (string) $element->lastname,
            middleName: property_exists($element, 'middlename') && $element->middlename !== null ? (string) $element->middlename : null,
            digipostAddress: isset($element->{'digipost-address'})
                ? new DigipostAddress((string) $element->{'digipost-address'})
                : null,
        );
    }
}
