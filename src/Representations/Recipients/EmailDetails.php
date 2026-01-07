<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\Recipients;

use InvalidArgumentException;
use SimpleXMLElement;

/**
 * Email details for message recipient.
 * Contains 1-50 email addresses.
 */
final readonly class EmailDetails
{
    /** @var string[] */
    public array $emailAddresses;

    /**
     * @param string[] $emailAddresses
     */
    public function __construct(array $emailAddresses)
    {
        if (count($emailAddresses) < 1) {
            throw new InvalidArgumentException('At least one email address is required');
        }

        if (count($emailAddresses) > 50) {
            throw new InvalidArgumentException('Maximum 50 email addresses allowed');
        }

        foreach ($emailAddresses as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                throw new InvalidArgumentException("Invalid email address: {$email}");
            }
        }

        $this->emailAddresses = $emailAddresses;
    }

    /**
     * Create from a single email address.
     */
    public static function fromEmail(string $email): self
    {
        return new self([$email]);
    }

    /**
     * Add email details to an XML element.
     */
    public function addToXml(SimpleXMLElement $parent): void
    {
        $emailDetails = $parent->addChild('email-details');

        if ($emailDetails === null) {
            return;
        }

        foreach ($this->emailAddresses as $email) {
            $emailDetails->addChild('email-address', $email);
        }
    }
}
