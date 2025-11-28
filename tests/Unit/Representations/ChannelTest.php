<?php

declare(strict_types=1);

use Coretrek\Digipost\Representations\Channel;

describe('Channel', function (): void {
    it('has DIGIPOST channel', function (): void {
        expect(Channel::DIGIPOST->value)->toBe('DIGIPOST');
    });

    it('has PRINT channel', function (): void {
        expect(Channel::PRINT->value)->toBe('PRINT');
    });
});
