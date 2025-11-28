<?php

declare(strict_types=1);

use Coretrek\Digipost\Representations\DigipostAddress;

describe('DigipostAddress', function (): void {
    it('can be created with a valid address', function (): void {
        $address = new DigipostAddress('john.doe');

        expect($address->value)->toBe('john.doe');
    });

    it('accepts alphanumeric addresses', function (): void {
        $address = new DigipostAddress('user123');

        expect($address->value)->toBe('user123');
    });

    it('accepts addresses with dots', function (): void {
        $address = new DigipostAddress('john.doe.smith');

        expect($address->value)->toBe('john.doe.smith');
    });

    it('accepts addresses with hyphens', function (): void {
        $address = new DigipostAddress('john-doe');

        expect($address->value)->toBe('john-doe');
    });

    it('throws exception for too short address', function (): void {
        new DigipostAddress('a');
    })->throws(InvalidArgumentException::class, 'Invalid Digipost address');

    it('throws exception for address starting with dot', function (): void {
        new DigipostAddress('.john');
    })->throws(InvalidArgumentException::class, 'Invalid Digipost address');

    it('throws exception for address ending with dot', function (): void {
        new DigipostAddress('john.');
    })->throws(InvalidArgumentException::class, 'Invalid Digipost address');

    it('can be converted to string', function (): void {
        $address = new DigipostAddress('john.doe');

        expect((string) $address)->toBe('john.doe');
    });
});
