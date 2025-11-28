<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\DataTypes;

use SimpleXMLElement;

/**
 * Additional information for appointments.
 */
final readonly class Info
{
    public function __construct(
        public string $title,
        public string $text,
    ) {}

    /**
     * Add this info to an XML element.
     */
    public function addToXml(SimpleXMLElement $parent): void
    {
        $info = $parent->addChild('info');

        if ($info === null) {
            return;
        }

        $info->addChild('title', htmlspecialchars($this->title, ENT_XML1));
        $info->addChild('text', htmlspecialchars($this->text, ENT_XML1));
    }
}
