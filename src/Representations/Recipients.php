<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

use SimpleXMLElement;

/**
 * Search results for recipients.
 */
final readonly class Recipients
{
    /**
     * @param Recipient[] $recipients
     */
    public function __construct(
        public array $recipients = [],
    ) {}

    /**
     * Create from XML response.
     */
    public static function fromXml(string $xml): self
    {
        $element = new SimpleXMLElement($xml);
        $recipients = [];

        foreach ($element->recipient as $recipientElement) {
            $recipients[] = Recipient::fromXmlElement($recipientElement);
        }

        return new self(recipients: $recipients);
    }
}
