<?php

declare(strict_types=1);

use Coretrek\Digipost\Representations\PhoneNumber;

describe('PhoneNumber', function (): void {
    it('can be created with a valid international number', function (): void {
        $phone = new PhoneNumber('+4712345678');

        expect($phone->value)->toBe('+4712345678');
    });

    it('normalizes Norwegian 8-digit numbers', function (): void {
        $phone = new PhoneNumber('12345678');

        expect($phone->value)->toBe('+4712345678');
    });

    it('removes whitespace', function (): void {
        $phone = new PhoneNumber('+47 123 45 678');

        expect($phone->value)->toBe('+4712345678');
    });

    it('throws exception for too short number', function (): void {
        new PhoneNumber('+471234');
    })->throws(InvalidArgumentException::class, 'Invalid phone number');

    it('throws exception for too long number', function (): void {
        new PhoneNumber('+471234567890123456');
    })->throws(InvalidArgumentException::class, 'Invalid phone number');

    it('can be converted to string', function (): void {
        $phone = new PhoneNumber('+4712345678');

        expect((string) $phone)->toBe('+4712345678');
    });
});
