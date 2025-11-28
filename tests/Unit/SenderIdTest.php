<?php

declare(strict_types=1);

use Coretrek\Digipost\SenderId;

describe('SenderId', function (): void {
    it('can be created with a valid ID', function (): void {
        $senderId = SenderId::of(123456);

        expect($senderId->value)->toBe(123456);
    });

    it('throws exception for negative ID', function (): void {
        SenderId::of(-1);
    })->throws(InvalidArgumentException::class, 'Sender ID must be a positive integer');

    it('throws exception for zero ID', function (): void {
        SenderId::of(0);
    })->throws(InvalidArgumentException::class, 'Sender ID must be a positive integer');

    it('can be converted to string', function (): void {
        $senderId = SenderId::of(123456);

        expect((string) $senderId)->toBe('123456');
    });
});
