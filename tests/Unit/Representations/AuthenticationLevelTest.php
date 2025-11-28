<?php

declare(strict_types=1);

use Coretrek\Digipost\Representations\AuthenticationLevel;

describe('AuthenticationLevel', function (): void {
    it('has PASSWORD level', function (): void {
        expect(AuthenticationLevel::PASSWORD->value)->toBe('PASSWORD');
    });

    it('has TWO_FACTOR level', function (): void {
        expect(AuthenticationLevel::TWO_FACTOR->value)->toBe('TWO_FACTOR');
    });

    it('has IDPORTEN_3 level', function (): void {
        expect(AuthenticationLevel::IDPORTEN_3->value)->toBe('IDPORTEN_3');
    });

    it('has IDPORTEN_4 level', function (): void {
        expect(AuthenticationLevel::IDPORTEN_4->value)->toBe('IDPORTEN_4');
    });
});
