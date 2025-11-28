<?php

declare(strict_types=1);

use Coretrek\Digipost\Exceptions\CertificateException;

describe('CertificateException', function (): void {
    it('can create file not found exception', function (): void {
        $exception = CertificateException::fileNotFound('/path/to/file.p12');

        expect($exception->getMessage())->toContain('/path/to/file.p12');
        expect($exception->getMessage())->toContain('not found');
    });

    it('can create cannot read exception', function (): void {
        $exception = CertificateException::cannotRead('/path/to/file.p12');

        expect($exception->getMessage())->toContain('/path/to/file.p12');
        expect($exception->getMessage())->toContain('read');
    });

    it('can create invalid PKCS12 exception', function (): void {
        $exception = CertificateException::invalidPkcs12();

        expect($exception->getMessage())->toContain('PKCS#12');
    });

    it('can create invalid private key exception', function (): void {
        $exception = CertificateException::invalidPrivateKey();

        expect($exception->getMessage())->toContain('private key');
    });

    it('can create invalid certificate exception', function (): void {
        $exception = CertificateException::invalidCertificate();

        expect($exception->getMessage())->toContain('certificate');
    });

    it('can create signing failed exception', function (): void {
        $exception = CertificateException::signingFailed();

        expect($exception->getMessage())->toContain('sign');
    });
});
