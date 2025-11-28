<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\SharedDocuments;

use Coretrek\Digipost\Representations\FileType;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SimpleXMLElement;

/**
 * A shared document.
 */
final readonly class SharedDocument
{
    public function __construct(
        public UuidInterface $uuid,
        public string $subject,
        public FileType $fileType,
        public string $sender,
        public DateTimeImmutable $deliveryTime,
        public ?string $contentUri = null,
    ) {}

    /**
     * Create from XML element.
     */
    public static function fromXmlElement(SimpleXMLElement $element): self
    {
        $contentUri = null;

        foreach ($element->link as $link) {
            $rel = (string) $link['rel'];
            $href = (string) $link['href'];

            if ($rel === 'get_document_content') {
                $contentUri = $href;
            }
        }

        return new self(
            uuid: Uuid::fromString((string) $element->uuid),
            subject: (string) $element->subject,
            fileType: FileType::from((string) $element->{'file-type'}),
            sender: (string) $element->sender,
            deliveryTime: new DateTimeImmutable((string) $element->{'delivery-time'}),
            contentUri: $contentUri,
        );
    }
}
