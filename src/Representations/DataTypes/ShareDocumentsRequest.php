<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\DataTypes;

use SimpleXMLElement;

/**
 * Share documents request data type.
 */
final readonly class ShareDocumentsRequest implements DataType
{
    public function __construct(
        public int $maxShareDurationSeconds,
        public string $purpose,
    ) {}

    public function addToXml(SimpleXMLElement $parent): void
    {
        $dataType = $parent->addChild('data-type');

        if ($dataType === null) {
            return;
        }

        $shareRequest = $dataType->addChild('share-documents-request');

        if ($shareRequest === null) {
            return;
        }

        $shareRequest->addChild('max-share-duration-seconds', (string) $this->maxShareDurationSeconds);
        $shareRequest->addChild('purpose', htmlspecialchars($this->purpose, ENT_XML1));
    }
}
