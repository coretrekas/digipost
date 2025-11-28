<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations;

use DateTimeInterface;
use SimpleXMLElement;

/**
 * SMS notification settings for a document.
 */
final readonly class SmsNotification
{
    /**
     * @param int|null $afterHours Number of hours after delivery to send SMS
     * @param DateTimeInterface|null $atTime Specific time to send SMS
     */
    public function __construct(
        public ?int $afterHours = null,
        public ?DateTimeInterface $atTime = null,
    ) {}

    /**
     * Create an SMS notification to be sent after a number of hours.
     */
    public static function afterHours(int $hours): self
    {
        return new self(afterHours: $hours);
    }

    /**
     * Create an SMS notification to be sent at a specific time.
     */
    public static function atTime(DateTimeInterface $time): self
    {
        return new self(atTime: $time);
    }

    /**
     * Add this notification to an XML element.
     */
    public function addToXml(SimpleXMLElement $parent): void
    {
        $sms = $parent->addChild('sms-notification');

        if ($sms === null) {
            return;
        }

        if ($this->afterHours !== null) {
            $sms->addChild('after-hours', (string) $this->afterHours);
        }

        if ($this->atTime instanceof DateTimeInterface) {
            $sms->addChild('at-time', $this->atTime->format('c'));
        }
    }
}
