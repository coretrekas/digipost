<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\Archive;

use Coretrek\Digipost\Representations\FileType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SimpleXMLElement;

/**
 * Content to be archived.
 */
final readonly class ArchiveDocumentContent
{
    /**
     * @param array<string, string> $attributes
     */
    public function __construct(
        public UuidInterface $uuid,
        public string $fileName,
        public FileType $fileType,
        public string $contentType,
        public string $content,
        public ?string $referenceId = null,
        public array $attributes = [],
    ) {}

    /**
     * Create a new archive document content.
     *
     * @param array<string, string> $attributes
     */
    public static function create(
        string $fileName,
        FileType $fileType,
        string $content,
        ?string $referenceId = null,
        array $attributes = [],
    ): self {
        return new self(
            uuid: Uuid::uuid4(),
            fileName: $fileName,
            fileType: $fileType,
            contentType: $fileType->getMimeType(),
            content: $content,
            referenceId: $referenceId,
            attributes: $attributes,
        );
    }

    /**
     * Convert to XML for the API.
     */
    public function toXml(): string
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><archive-document xmlns="http://api.digipost.no/schema/v8"></archive-document>');

        $xml->addChild('uuid', $this->uuid->toString());
        $xml->addChild('file-name', htmlspecialchars($this->fileName, ENT_XML1));
        $xml->addChild('file-type', $this->fileType->value);
        $xml->addChild('content-type', $this->contentType);

        if ($this->referenceId !== null) {
            $xml->addChild('reference-id', htmlspecialchars($this->referenceId, ENT_XML1));
        }

        foreach ($this->attributes as $key => $value) {
            $attr = $xml->addChild('attribute', htmlspecialchars($value, ENT_XML1));
            if ($attr !== null) {
                $attr->addAttribute('key', $key);
            }
        }

        $xmlString = $xml->asXML();

        return $xmlString !== false ? $xmlString : '';
    }
}
