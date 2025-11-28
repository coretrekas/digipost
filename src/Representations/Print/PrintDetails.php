<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\Print;

use SimpleXMLElement;

/**
 * Details for printing and sending via regular mail.
 */
final readonly class PrintDetails
{
    public function __construct(
        public PrintRecipient $recipient,
        public PrintRecipient $returnAddress,
        public PrintColors $color = PrintColors::MONOCHROME,
        public NondeliverableHandling $nondeliverableHandling = NondeliverableHandling::RETURN_TO_SENDER,
    ) {}

    /**
     * Add print details to an XML element.
     */
    public function addToXml(SimpleXMLElement $parent): void
    {
        $printDetails = $parent->addChild('print-details');

        if ($printDetails === null) {
            return;
        }

        $this->recipient->addToXml($printDetails, 'recipient');
        $this->returnAddress->addToXml($printDetails, 'return-address');

        $printDetails->addChild('color', $this->color->value);
        $printDetails->addChild('nondeliverable-handling', $this->nondeliverableHandling->value);
    }
}
