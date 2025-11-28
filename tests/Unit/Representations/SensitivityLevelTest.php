<?php

declare(strict_types=1);

use Coretrek\Digipost\Representations\SensitivityLevel;

describe('SensitivityLevel', function (): void {
    it('has NORMAL level', function (): void {
        expect(SensitivityLevel::NORMAL->value)->toBe('NORMAL');
    });

    it('has SENSITIVE level', function (): void {
        expect(SensitivityLevel::SENSITIVE->value)->toBe('SENSITIVE');
    });
});
