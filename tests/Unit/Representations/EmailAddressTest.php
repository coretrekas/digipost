<?php

declare(strict_types=1);

use Coretrek\Digipost\Representations\EmailAddress;

describe('EmailAddress', function (): void {
    it('can be created with a valid email', function (): void {
        $email = new EmailAddress('john@example.com');

        expect($email->value)->toBe('john@example.com');
    });

    it('accepts complex email addresses', function (): void {
        $email = new EmailAddress('john.doe+tag@subdomain.example.com');

        expect($email->value)->toBe('john.doe+tag@subdomain.example.com');
    });

    it('throws exception for invalid email', function (): void {
        new EmailAddress('not-an-email');
    })->throws(InvalidArgumentException::class, 'Invalid email address');

    it('throws exception for email without domain', function (): void {
        new EmailAddress('john@');
    })->throws(InvalidArgumentException::class, 'Invalid email address');

    it('throws exception for email without local part', function (): void {
        new EmailAddress('@example.com');
    })->throws(InvalidArgumentException::class, 'Invalid email address');

    it('can be converted to string', function (): void {
        $email = new EmailAddress('john@example.com');

        expect((string) $email)->toBe('john@example.com');
    });
});
