<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

use SimpleXMLElement;

/**
 * Result of an identification request.
 */
final readonly class IdentificationResult
{
    public function __construct(
        public IdentificationResultCode $result,
        public ?DigipostAddress $digipostAddress = null,
        public ?string $invalidReason = null,
        public ?string $unidentifiedReason = null,
    ) {}

    /**
     * Check if the recipient is a Digipost user.
     */
    public function isDigipostUser(): bool
    {
        return $this->result === IdentificationResultCode::DIGIPOST;
    }

    /**
     * Check if the recipient is identified.
     */
    public function isIdentified(): bool
    {
        return $this->result === IdentificationResultCode::IDENTIFIED;
    }

    /**
     * Check if the identification was invalid.
     */
    public function isInvalid(): bool
    {
        return $this->result === IdentificationResultCode::INVALID;
    }

    /**
     * Check if the recipient is unidentified.
     */
    public function isUnidentified(): bool
    {
        return $this->result === IdentificationResultCode::UNIDENTIFIED;
    }

    /**
     * Create from XML response.
     */
    public static function fromXml(string $xml): self
    {
        $element = new SimpleXMLElement($xml);

        $result = IdentificationResultCode::from((string) $element->result);
        $digipostAddress = isset($element->{'digipost-address'})
            ? new DigipostAddress((string) $element->{'digipost-address'})
            : null;
        $invalidReason = isset($element->{'invalid-reason'})
            ? (string) $element->{'invalid-reason'}
            : null;
        $unidentifiedReason = isset($element->{'unidentified-reason'})
            ? (string) $element->{'unidentified-reason'}
            : null;

        return new self(
            result: $result,
            digipostAddress: $digipostAddress,
            invalidReason: $invalidReason,
            unidentifiedReason: $unidentifiedReason,
        );
    }
}
