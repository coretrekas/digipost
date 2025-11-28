<?php

declare(strict_types=1);

use Coretrek\Digipost\Representations\Print\NondeliverableHandling;
use Coretrek\Digipost\Representations\Print\NorwegianAddress;
use Coretrek\Digipost\Representations\Print\PrintColors;
use Coretrek\Digipost\Representations\Print\PrintDetails;
use Coretrek\Digipost\Representations\Print\PrintRecipient;

describe('PrintDetails', function (): void {
    it('can be created with required fields', function (): void {
        $recipientAddress = new NorwegianAddress(
            addressLine1: 'Testgata 1',
            postalCode: '0123',
            city: 'Oslo',
        );

        $returnAddress = new NorwegianAddress(
            addressLine1: 'Returgate 2',
            postalCode: '0456',
            city: 'Bergen',
        );

        $recipient = new PrintRecipient(
            name: 'John Doe',
            address: $recipientAddress,
        );

        $returnRecipient = new PrintRecipient(
            name: 'Company AS',
            address: $returnAddress,
        );

        $printDetails = new PrintDetails(
            recipient: $recipient,
            returnAddress: $returnRecipient,
        );

        expect($printDetails->recipient)->toBe($recipient);
        expect($printDetails->returnAddress)->toBe($returnRecipient);
        expect($printDetails->color)->toBe(PrintColors::MONOCHROME);
        expect($printDetails->nondeliverableHandling)->toBe(NondeliverableHandling::RETURN_TO_SENDER);
    });

    it('can have custom color setting', function (): void {
        $recipientAddress = new NorwegianAddress(
            addressLine1: 'Testgata 1',
            postalCode: '0123',
            city: 'Oslo',
        );

        $recipient = new PrintRecipient(
            name: 'John Doe',
            address: $recipientAddress,
        );

        $printDetails = new PrintDetails(
            recipient: $recipient,
            returnAddress: $recipient,
            color: PrintColors::COLORS,
        );

        expect($printDetails->color)->toBe(PrintColors::COLORS);
    });

    it('can have custom nondeliverable handling', function (): void {
        $recipientAddress = new NorwegianAddress(
            addressLine1: 'Testgata 1',
            postalCode: '0123',
            city: 'Oslo',
        );

        $recipient = new PrintRecipient(
            name: 'John Doe',
            address: $recipientAddress,
        );

        $printDetails = new PrintDetails(
            recipient: $recipient,
            returnAddress: $recipient,
            nondeliverableHandling: NondeliverableHandling::SHRED,
        );

        expect($printDetails->nondeliverableHandling)->toBe(NondeliverableHandling::SHRED);
    });
});
