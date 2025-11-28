<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

use SimpleXMLElement;

/**
 * Information about a sender.
 */
final readonly class SenderInformation
{
    /**
     * @param SenderFeature[] $features
     */
    public function __construct(
        public int $senderId,
        public SenderStatus $status,
        public array $features = [],
    ) {}

    /**
     * Check if the sender has a specific feature.
     */
    public function hasFeature(SenderFeature $feature): bool
    {
        return in_array($feature, $this->features, true);
    }

    /**
     * Create from XML response.
     */
    public static function fromXml(string $xml): self
    {
        $element = new SimpleXMLElement($xml);

        $features = [];
        foreach ($element->feature as $feature) {
            $features[] = SenderFeature::from((string) $feature);
        }

        return new self(
            senderId: (int) $element->{'sender-id'},
            status: SenderStatus::from((string) $element->status),
            features: $features,
        );
    }
}
