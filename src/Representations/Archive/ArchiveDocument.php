<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\Archive;

use Coretrek\Digipost\Representations\FileType;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SimpleXMLElement;

/**
 * A document in an archive.
 */
final readonly class ArchiveDocument
{
    /**
     * @param array<string, string> $attributes
     */
    public function __construct(
        public UuidInterface $uuid,
        public string $fileName,
        public FileType $fileType,
        public string $contentType,
        public ?DateTimeImmutable $archivedTime = null,
        public ?string $referenceId = null,
        public array $attributes = [],
        public ?string $contentUri = null,
        public ?string $deleteUri = null,
        public ?string $updateUri = null,
    ) {}

    /**
     * Create from XML element.
     */
    public static function fromXmlElement(SimpleXMLElement $element): self
    {
        $attributes = [];
        foreach ($element->attribute as $attr) {
            $key = (string) $attr['key'];
            $value = (string) $attr;
            $attributes[$key] = $value;
        }

        $contentUri = null;
        $deleteUri = null;
        $updateUri = null;

        foreach ($element->link as $link) {
            $rel = (string) $link['rel'];
            $href = (string) $link['href'];

            if ($rel === 'get_document_content') {
                $contentUri = $href;
            } elseif ($rel === 'delete') {
                $deleteUri = $href;
            } elseif ($rel === 'update') {
                $updateUri = $href;
            }
        }

        return new self(
            uuid: Uuid::fromString((string) $element->uuid),
            fileName: (string) $element->{'file-name'},
            fileType: FileType::from((string) $element->{'file-type'}),
            contentType: (string) $element->{'content-type'},
            archivedTime: isset($element->{'archived-time'})
                ? new DateTimeImmutable((string) $element->{'archived-time'})
                : null,
            referenceId: isset($element->{'reference-id'}) ? (string) $element->{'reference-id'} : null,
            attributes: $attributes,
            contentUri: $contentUri,
            deleteUri: $deleteUri,
            updateUri: $updateUri,
        );
    }
}
