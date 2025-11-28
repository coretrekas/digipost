<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

use Coretrek\Digipost\Representations\Print\PrintDetails;
use DateTimeInterface;
use SimpleXMLElement;

/**
 * Request for registration - sends SMS to non-Digipost users asking them to register.
 */
final readonly class RequestForRegistration
{
    public function __construct(
        public DateTimeInterface $deadline,
        public PhoneNumber $phoneNumber,
        public ?EmailAddress $emailAddress = null,
        public ?PrintDetails $printDetails = null,
    ) {}

    /**
     * Add this request to an XML element.
     */
    public function addToXml(SimpleXMLElement $parent): void
    {
        $request = $parent->addChild('request-for-registration');

        if ($request === null) {
            return;
        }

        $request->addChild('registration-deadline', $this->deadline->format('c'));
        $request->addChild('phone-number', $this->phoneNumber->value);

        if ($this->emailAddress instanceof EmailAddress) {
            $request->addChild('email-address', $this->emailAddress->value);
        }

        if ($this->printDetails instanceof PrintDetails) {
            $this->printDetails->addToXml($request);
        }
    }
}
