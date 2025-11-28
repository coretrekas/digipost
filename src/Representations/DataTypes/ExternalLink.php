<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\DataTypes;

use DateTimeInterface;
use SimpleXMLElement;

/**
 * External link data type for documents.
 */
final readonly class ExternalLink implements DataType
{
    public function __construct(
        public string $url,
        public ?DateTimeInterface $deadline = null,
        public ?string $description = null,
        public ?string $buttonText = null,
    ) {}

    public function addToXml(SimpleXMLElement $parent): void
    {
        $dataType = $parent->addChild('data-type');

        if ($dataType === null) {
            return;
        }

        $externalLink = $dataType->addChild('external-link');

        if ($externalLink === null) {
            return;
        }

        $externalLink->addChild('url', htmlspecialchars($this->url, ENT_XML1));

        if ($this->deadline instanceof DateTimeInterface) {
            $externalLink->addChild('deadline', $this->deadline->format('c'));
        }

        if ($this->description !== null) {
            $externalLink->addChild('description', htmlspecialchars($this->description, ENT_XML1));
        }

        if ($this->buttonText !== null) {
            $externalLink->addChild('button-text', htmlspecialchars($this->buttonText, ENT_XML1));
        }
    }
}
