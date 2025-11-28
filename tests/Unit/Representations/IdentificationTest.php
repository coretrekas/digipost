<?php

declare(strict_types=1);

use Coretrek\Digipost\Representations\DigipostAddress;
use Coretrek\Digipost\Representations\Identification;

describe('Identification', function (): void {
    it('can be created from Digipost address', function (): void {
        $address = new DigipostAddress('john.doe');
        $identification = Identification::fromDigipostAddress($address);

        expect($identification->digipostAddress)->toBe($address);
        expect($identification->personalIdentificationNumber)->toBeNull();
    });

    it('can generate XML for Digipost address', function (): void {
        $address = new DigipostAddress('john.doe');
        $identification = Identification::fromDigipostAddress($address);

        $xml = $identification->toXml();

        expect($xml)->toContain('john.doe');
        expect($xml)->toContain('digipost-address');
    });
});
