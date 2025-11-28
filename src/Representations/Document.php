<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

use Coretrek\Digipost\Representations\DataTypes\DataType;
use Ramsey\Uuid\UuidInterface;
use SimpleXMLElement;

/**
 * Represents a document in a message.
 */
final readonly class Document
{
    public function __construct(
        public UuidInterface $uuid,
        public string $subject,
        public FileType $fileType,
        public ?DataType $dataType = null,
        public ?SmsNotification $smsNotification = null,
        public ?EmailNotification $emailNotification = null,
        public AuthenticationLevel $authenticationLevel = AuthenticationLevel::PASSWORD,
        public SensitivityLevel $sensitivityLevel = SensitivityLevel::NORMAL,
    ) {}

    /**
     * Add this document to an XML element.
     */
    public function addToXml(SimpleXMLElement $parent, string $elementName): void
    {
        $doc = $parent->addChild($elementName);

        if ($doc === null) {
            return;
        }

        $doc->addChild('uuid', $this->uuid->toString());
        $doc->addChild('subject', htmlspecialchars($this->subject, ENT_XML1));
        $doc->addChild('file-type', $this->fileType->value);
        $doc->addChild('authentication-level', $this->authenticationLevel->value);
        $doc->addChild('sensitivity-level', $this->sensitivityLevel->value);

        if ($this->smsNotification instanceof SmsNotification) {
            $this->smsNotification->addToXml($doc);
        }

        if ($this->emailNotification instanceof EmailNotification) {
            $this->emailNotification->addToXml($doc);
        }

        if ($this->dataType instanceof DataType) {
            $this->dataType->addToXml($doc);
        }
    }
}
