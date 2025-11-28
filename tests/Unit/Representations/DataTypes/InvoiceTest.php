<?php

declare(strict_types=1);

use Coretrek\Digipost\Representations\DataTypes\Invoice;

describe('Invoice', function (): void {
    it('can be created with required fields', function (): void {
        $dueDate = new DateTimeImmutable('2024-12-31');

        $invoice = new Invoice(
            dueDate: $dueDate,
            amount: '1500.00',
            kid: '1234567890123',
            accountNumber: '12345678901',
        );

        expect($invoice->dueDate)->toBe($dueDate);
        expect($invoice->amount)->toBe('1500.00');
        expect($invoice->kid)->toBe('1234567890123');
        expect($invoice->accountNumber)->toBe('12345678901');
    });

    it('can have creditor account', function (): void {
        $dueDate = new DateTimeImmutable('2024-12-31');

        $invoice = new Invoice(
            dueDate: $dueDate,
            amount: '1500.00',
            kid: '1234567890123',
            accountNumber: '12345678901',
            creditorAccount: '98765432109',
        );

        expect($invoice->creditorAccount)->toBe('98765432109');
    });

    it('can generate XML', function (): void {
        $dueDate = new DateTimeImmutable('2024-12-31');

        $invoice = new Invoice(
            dueDate: $dueDate,
            amount: '1500.00',
            kid: '1234567890123',
            accountNumber: '12345678901',
        );

        $xml = new SimpleXMLElement('<document></document>');
        $invoice->addToXml($xml);

        $xmlString = $xml->asXML();

        expect($xmlString)->toContain('invoice');
        expect($xmlString)->toContain('1500.00');
        expect($xmlString)->toContain('1234567890123');
    });
});
