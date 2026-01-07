<?php

declare(strict_types=1);

use Coretrek\Digipost\Representations\Recipients\PeppolAddress;
use Coretrek\Digipost\Representations\Recipients\PeppolAddresses;

describe('PeppolAddress', function (): void {
    it('can be created with scheme and endpoint', function (): void {
        $address = new PeppolAddress('0192', '123456789');

        expect($address->schemeId)->toBe('0192');
        expect($address->endpointId)->toBe('123456789');
    });

    it('generates correct XML', function (): void {
        $address = new PeppolAddress('0192', '123456789');

        $xml = new SimpleXMLElement('<root></root>');
        $address->addToXml($xml, 'receiver');

        expect((string) $xml->receiver->schemeID)->toBe('0192');
        expect((string) $xml->receiver->endpointID)->toBe('123456789');
    });
});

describe('PeppolAddresses', function (): void {
    it('can be created with receiver and sender', function (): void {
        $receiver = new PeppolAddress('0192', '987654321');
        $sender = new PeppolAddress('0192', '123456789');

        $addresses = new PeppolAddresses($receiver, $sender);

        expect($addresses->receiver)->toBe($receiver);
        expect($addresses->sender)->toBe($sender);
    });

    it('generates correct XML', function (): void {
        $receiver = new PeppolAddress('0192', '987654321');
        $sender = new PeppolAddress('0192', '123456789');

        $addresses = new PeppolAddresses($receiver, $sender);

        $xml = new SimpleXMLElement('<root></root>');
        $addresses->addToXml($xml);

        expect((string) $xml->{'peppol-addresses'}->receiver->schemeID)->toBe('0192');
        expect((string) $xml->{'peppol-addresses'}->receiver->endpointID)->toBe('987654321');
        expect((string) $xml->{'peppol-addresses'}->sender->schemeID)->toBe('0192');
        expect((string) $xml->{'peppol-addresses'}->sender->endpointID)->toBe('123456789');
    });
});
