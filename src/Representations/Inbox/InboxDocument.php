<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\Inbox;

use Coretrek\Digipost\Representations\AuthenticationLevel;
use Coretrek\Digipost\Representations\FileType;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use SimpleXMLElement;

/**
 * A document in the inbox.
 */
final readonly class InboxDocument
{
    public function __construct(
        public int $id,
        public UuidInterface $uuid,
        public string $subject,
        public string $sender,
        public DateTimeImmutable $deliveryTime,
        public FileType $fileType,
        public AuthenticationLevel $authenticationLevel,
        public bool $opened = false,
        public ?string $contentUri = null,
        public ?string $deleteUri = null,
    ) {}

    /**
     * Create from XML element.
     */
    public static function fromXmlElement(SimpleXMLElement $element): self
    {
        $contentUri = null;
        $deleteUri = null;

        foreach ($element->link as $link) {
            $rel = (string) $link['rel'];
            $href = (string) $link['href'];

            if ($rel === 'get_document_content') {
                $contentUri = $href;
            } elseif ($rel === 'delete') {
                $deleteUri = $href;
            }
        }

        return new self(
            id: (int) $element->id,
            uuid: Uuid::fromString((string) $element->uuid),
            subject: (string) $element->subject,
            sender: (string) $element->sender,
            deliveryTime: new DateTimeImmutable((string) $element->{'delivery-time'}),
            fileType: FileType::from((string) $element->{'file-type'}),
            authenticationLevel: AuthenticationLevel::from((string) $element->{'authentication-level'}),
            opened: property_exists($element, 'opened') && $element->opened !== null && (string) $element->opened === 'true',
            contentUri: $contentUri,
            deleteUri: $deleteUri,
        );
    }
}
