<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\Print;

use SimpleXMLElement;

/**
 * Recipient for printed mail.
 */
final readonly class PrintRecipient
{
    public function __construct(
        public string $name,
        public PrintAddress $address,
    ) {}

    /**
     * Add this recipient to an XML element.
     */
    public function addToXml(SimpleXMLElement $parent, string $elementName): void
    {
        $recipient = $parent->addChild($elementName);

        if ($recipient === null) {
            return;
        }

        $recipient->addChild('name', htmlspecialchars($this->name, ENT_XML1));
        $this->address->addToXml($recipient);
    }
}
