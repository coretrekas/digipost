<?php

declare(strict_types=1);

use Coretrek\Digipost\Representations\Recipients\EmailDetails;

describe('EmailDetails', function (): void {
    it('can be created with a single email', function (): void {
        $emailDetails = new EmailDetails(['test@example.com']);

        expect($emailDetails->emailAddresses)->toBe(['test@example.com']);
    });

    it('can be created with multiple emails', function (): void {
        $emails = ['test1@example.com', 'test2@example.com'];
        $emailDetails = new EmailDetails($emails);

        expect($emailDetails->emailAddresses)->toBe($emails);
    });

    it('can be created from a single email using factory method', function (): void {
        $emailDetails = EmailDetails::fromEmail('test@example.com');

        expect($emailDetails->emailAddresses)->toBe(['test@example.com']);
    });

    it('throws exception for empty array', function (): void {
        new EmailDetails([]);
    })->throws(InvalidArgumentException::class, 'At least one email address is required');

    it('throws exception for more than 50 emails', function (): void {
        $emails = array_map(fn ($i) => "test{$i}@example.com", range(1, 51));
        new EmailDetails($emails);
    })->throws(InvalidArgumentException::class, 'Maximum 50 email addresses allowed');

    it('throws exception for invalid email', function (): void {
        new EmailDetails(['invalid-email']);
    })->throws(InvalidArgumentException::class, 'Invalid email address: invalid-email');

    it('accepts exactly 50 emails', function (): void {
        $emails = array_map(fn ($i) => "test{$i}@example.com", range(1, 50));
        $emailDetails = new EmailDetails($emails);

        expect(count($emailDetails->emailAddresses))->toBe(50);
    });

    it('generates correct XML', function (): void {
        $emailDetails = new EmailDetails(['test1@example.com', 'test2@example.com']);

        $xml = new SimpleXMLElement('<root></root>');
        $emailDetails->addToXml($xml);

        expect((string) $xml->{'email-details'}->{'email-address'}[0])->toBe('test1@example.com');
        expect((string) $xml->{'email-details'}->{'email-address'}[1])->toBe('test2@example.com');
    });
});
