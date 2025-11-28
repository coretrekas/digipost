<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

use SimpleXMLElement;

/**
 * User account information.
 */
final readonly class UserAccount
{
    public function __construct(
        public DigipostAddress $digipostAddress,
        public ?string $encryptionKey = null,
    ) {}

    /**
     * Create from XML response.
     */
    public static function fromXml(string $xml): self
    {
        $element = new SimpleXMLElement($xml);

        return new self(
            digipostAddress: new DigipostAddress((string) $element->{'digipost-address'}),
            encryptionKey: isset($element->{'encryption-key'}) ? (string) $element->{'encryption-key'} : null,
        );
    }
}
