<?php

declare(strict_types=1);

use Coretrek\Digipost\Representations\Recipients\OrganisationNumber;

describe('OrganisationNumber', function (): void {
    it('can be created with a valid 9-digit number', function (): void {
        $orgNumber = new OrganisationNumber('123456789');

        expect($orgNumber->value)->toBe('123456789');
    });

    it('throws exception for invalid length', function (): void {
        new OrganisationNumber('12345678');
    })->throws(InvalidArgumentException::class, 'Organisation number must be exactly 9 digits');

    it('throws exception for too long number', function (): void {
        new OrganisationNumber('1234567890');
    })->throws(InvalidArgumentException::class, 'Organisation number must be exactly 9 digits');

    it('throws exception for non-numeric value', function (): void {
        new OrganisationNumber('12345678a');
    })->throws(InvalidArgumentException::class, 'Organisation number must contain only digits');

    it('removes dots and spaces', function (): void {
        $orgNumber = new OrganisationNumber('123 456 789');

        expect($orgNumber->value)->toBe('123456789');
    });

    it('removes dots', function (): void {
        $orgNumber = new OrganisationNumber('123.456.789');

        expect($orgNumber->value)->toBe('123456789');
    });

    it('can be converted to string', function (): void {
        $orgNumber = new OrganisationNumber('123456789');

        expect((string) $orgNumber)->toBe('123456789');
    });
});
