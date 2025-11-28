<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Representations\DataTypes;

use DateTimeInterface;
use SimpleXMLElement;

/**
 * Invoice data type for documents.
 */
final readonly class Invoice implements DataType
{
    public function __construct(
        public DateTimeInterface $dueDate,
        public string $amount,
        public string $kid,
        public string $accountNumber,
        public ?string $creditorAccount = null,
    ) {}

    public function addToXml(SimpleXMLElement $parent): void
    {
        $dataType = $parent->addChild('data-type');

        if ($dataType === null) {
            return;
        }

        $invoice = $dataType->addChild('invoice');

        if ($invoice === null) {
            return;
        }

        $invoice->addChild('due-date', $this->dueDate->format('Y-m-d'));
        $invoice->addChild('sum', $this->amount);
        $invoice->addChild('kid', $this->kid);
        $invoice->addChild('account', $this->accountNumber);

        if ($this->creditorAccount !== null) {
            $invoice->addChild('creditor-account', $this->creditorAccount);
        }
    }
}
