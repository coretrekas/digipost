<?php

declare(strict_types=1);

use Coretrek\Digipost\BrokerId;
use Coretrek\Digipost\SenderId;

describe('BrokerId', function (): void {
    it('can be created with a valid ID', function (): void {
        $brokerId = BrokerId::of(123456);

        expect($brokerId->value)->toBe(123456);
    });

    it('throws exception for negative ID', function (): void {
        BrokerId::of(-1);
    })->throws(InvalidArgumentException::class, 'Broker ID must be a positive integer');

    it('throws exception for zero ID', function (): void {
        BrokerId::of(0);
    })->throws(InvalidArgumentException::class, 'Broker ID must be a positive integer');

    it('can be converted to string', function (): void {
        $brokerId = BrokerId::of(123456);

        expect((string) $brokerId)->toBe('123456');
    });

    it('can be converted to SenderId', function (): void {
        $brokerId = BrokerId::of(123456);
        $senderId = $brokerId->asSenderId();

        expect($senderId)->toBeInstanceOf(SenderId::class);
        expect($senderId->value)->toBe(123456);
    });
});
