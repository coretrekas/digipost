<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\DataTypes;

use DateTimeInterface;
use SimpleXMLElement;

/**
 * Appointment data type for documents.
 */
final readonly class Appointment implements DataType
{
    /**
     * @param Info[] $info
     */
    public function __construct(
        public DateTimeInterface $startTime,
        public ?DateTimeInterface $endTime = null,
        public ?string $arrivalInfo = null,
        public ?string $place = null,
        public ?AppointmentAddress $address = null,
        public ?string $subTitle = null,
        public array $info = [],
        public Language $language = Language::NO,
    ) {}

    public function addToXml(SimpleXMLElement $parent): void
    {
        $dataType = $parent->addChild('data-type');

        if ($dataType === null) {
            return;
        }

        $appointment = $dataType->addChild('appointment');

        if ($appointment === null) {
            return;
        }

        $appointment->addChild('start-time', $this->startTime->format('c'));

        if ($this->endTime instanceof DateTimeInterface) {
            $appointment->addChild('end-time', $this->endTime->format('c'));
        }

        if ($this->arrivalInfo !== null) {
            $appointment->addChild('arrival-info', htmlspecialchars($this->arrivalInfo, ENT_XML1));
        }

        if ($this->place !== null) {
            $appointment->addChild('place', htmlspecialchars($this->place, ENT_XML1));
        }

        if ($this->address instanceof AppointmentAddress) {
            $this->address->addToXml($appointment);
        }

        if ($this->subTitle !== null) {
            $appointment->addChild('sub-title', htmlspecialchars($this->subTitle, ENT_XML1));
        }

        foreach ($this->info as $infoItem) {
            $infoItem->addToXml($appointment);
        }

        $appointment->addChild('language', $this->language->value);
    }
}
