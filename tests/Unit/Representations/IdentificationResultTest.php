<?php

declare(strict_types=1);

use Coretrek\Digipost\Representations\IdentificationResult;
use Coretrek\Digipost\Representations\IdentificationResultCode;

describe('IdentificationResult', function (): void {
    it('can check if recipient is Digipost user', function (): void {
        $result = new IdentificationResult(
            result: IdentificationResultCode::DIGIPOST,
        );

        expect($result->isDigipostUser())->toBeTrue();
        expect($result->isIdentified())->toBeFalse();
        expect($result->isInvalid())->toBeFalse();
        expect($result->isUnidentified())->toBeFalse();
    });

    it('can check if recipient is identified', function (): void {
        $result = new IdentificationResult(
            result: IdentificationResultCode::IDENTIFIED,
        );

        expect($result->isDigipostUser())->toBeFalse();
        expect($result->isIdentified())->toBeTrue();
    });

    it('can check if identification is invalid', function (): void {
        $result = new IdentificationResult(
            result: IdentificationResultCode::INVALID,
            invalidReason: 'Invalid format',
        );

        expect($result->isInvalid())->toBeTrue();
        expect($result->invalidReason)->toBe('Invalid format');
    });

    it('can check if recipient is unidentified', function (): void {
        $result = new IdentificationResult(
            result: IdentificationResultCode::UNIDENTIFIED,
            unidentifiedReason: 'Not found',
        );

        expect($result->isUnidentified())->toBeTrue();
        expect($result->unidentifiedReason)->toBe('Not found');
    });

    it('can parse from XML', function (): void {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <identification-result xmlns="http://api.digipost.no/schema/v8">
                <result>DIGIPOST</result>
                <digipost-address>john.doe</digipost-address>
            </identification-result>';

        $result = IdentificationResult::fromXml($xml);

        expect($result->result)->toBe(IdentificationResultCode::DIGIPOST);
        expect($result->digipostAddress)->not->toBeNull();
        expect($result->digipostAddress?->value)->toBe('john.doe');
    });
});
