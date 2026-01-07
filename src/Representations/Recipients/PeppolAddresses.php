<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\Recipients;

use SimpleXMLElement;

/**
 * PEPPOL addresses containing receiver and sender addresses.
 */
final readonly class PeppolAddresses
{
    public function __construct(
        public PeppolAddress $receiver,
        public PeppolAddress $sender,
    ) {}

    /**
     * Add PEPPOL addresses to an XML element.
     */
    public function addToXml(SimpleXMLElement $parent): void
    {
        $peppolAddresses = $parent->addChild('peppol-addresses');

        if ($peppolAddresses === null) {
            return;
        }

        $this->receiver->addToXml($peppolAddresses, 'receiver');
        $this->sender->addToXml($peppolAddresses, 'sender');
    }
}
