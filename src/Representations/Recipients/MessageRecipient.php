<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\Recipients;

use Coretrek\Digipost\Representations\DigipostAddress;
use Coretrek\Digipost\Representations\PersonalIdentificationNumber;
use Coretrek\Digipost\Representations\Print\PrintDetails;
use SimpleXMLElement;

/**
 * Represents a message recipient.
 */
final readonly class MessageRecipient
{
    private function __construct(
        public ?PersonalIdentificationNumber $personalIdentificationNumber = null,
        public ?OrganisationNumber $organisationNumber = null,
        public ?DigipostAddress $digipostAddress = null,
        public ?EmailDetails $emailDetails = null,
        public ?PeppolAddresses $peppolAddresses = null,
        public ?NameAndAddress $nameAndAddress = null,
        public ?BankAccountNumber $bankAccountNumber = null,
        public ?PrintDetails $printDetails = null,
    ) {}

    /**
     * Create a recipient from a personal identification number.
     */
    public static function fromPersonalIdentificationNumber(
        PersonalIdentificationNumber $pin,
        ?PrintDetails $printDetails = null,
    ): self {
        return new self(
            personalIdentificationNumber: $pin,
            printDetails: $printDetails,
        );
    }

    /**
     * Create a recipient from an organisation number.
     */
    public static function fromOrganisationNumber(
        OrganisationNumber $organisationNumber,
        ?PrintDetails $printDetails = null,
    ): self {
        return new self(
            organisationNumber: $organisationNumber,
            printDetails: $printDetails,
        );
    }

    /**
     * Create a recipient from a Digipost address.
     */
    public static function fromDigipostAddress(DigipostAddress $address): self
    {
        return new self(digipostAddress: $address);
    }

    /**
     * Create a recipient from email details.
     */
    public static function fromEmailDetails(EmailDetails $emailDetails): self
    {
        return new self(emailDetails: $emailDetails);
    }

    /**
     * Create a recipient from PEPPOL addresses.
     */
    public static function fromPeppolAddresses(PeppolAddresses $peppolAddresses): self
    {
        return new self(peppolAddresses: $peppolAddresses);
    }

    /**
     * Create a recipient from name and address.
     */
    public static function fromNameAndAddress(NameAndAddress $nameAndAddress): self
    {
        return new self(nameAndAddress: $nameAndAddress);
    }

    /**
     * Create a recipient from a bank account number.
     */
    public static function fromBankAccountNumber(BankAccountNumber $accountNumber): self
    {
        return new self(bankAccountNumber: $accountNumber);
    }

    /**
     * Add this recipient to an XML element.
     */
    public function addToXml(SimpleXMLElement $parent): void
    {
        $recipient = $parent->addChild('recipient');

        if ($recipient === null) {
            return;
        }

        if ($this->personalIdentificationNumber instanceof PersonalIdentificationNumber) {
            $recipient->addChild('personal-identification-number', $this->personalIdentificationNumber->value);
        }

        if ($this->organisationNumber instanceof OrganisationNumber) {
            $recipient->addChild('organisation-number', $this->organisationNumber->value);
        }

        if ($this->digipostAddress instanceof DigipostAddress) {
            $recipient->addChild('digipost-address', $this->digipostAddress->value);
        }

        if ($this->emailDetails instanceof EmailDetails) {
            $this->emailDetails->addToXml($recipient);
        }

        if ($this->peppolAddresses instanceof PeppolAddresses) {
            $this->peppolAddresses->addToXml($recipient);
        }

        if ($this->nameAndAddress instanceof NameAndAddress) {
            $this->nameAndAddress->addToXml($recipient);
        }

        if ($this->bankAccountNumber instanceof BankAccountNumber) {
            $recipient->addChild('bank-account-number', $this->bankAccountNumber->value);
        }

        if ($this->printDetails instanceof PrintDetails) {
            $this->printDetails->addToXml($recipient);
        }
    }
}
