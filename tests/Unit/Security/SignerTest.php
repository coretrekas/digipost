<?php

declare(strict_types=1);

use Coretrek\Digipost\Exceptions\CertificateException;
use Coretrek\Digipost\Security\Signer;

describe('Signer', function (): void {
    it('throws exception for non-existent PKCS12 file', function (): void {
        Signer::fromPkcs12File('/non/existent/file.p12', 'password');
    })->throws(CertificateException::class);

    it('throws exception for invalid PKCS12 content', function (): void {
        Signer::fromPkcs12String('invalid content', 'password');
    })->throws(CertificateException::class);

    it('throws exception for non-existent PEM files', function (): void {
        Signer::fromPemFiles('/non/existent/cert.pem', '/non/existent/key.pem');
    })->throws(CertificateException::class);
});
