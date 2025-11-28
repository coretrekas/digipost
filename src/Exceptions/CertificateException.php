<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Exceptions;

/**
 * Exception thrown when there are issues with certificates.
 */
final class CertificateException extends DigipostException
{
    public static function fileNotFound(string $path): self
    {
        return new self(
            message: "Certificate file not found: {$path}",
            context: ['path' => $path],
        );
    }

    public static function cannotRead(string $path): self
    {
        return new self(
            message: "Cannot read certificate file: {$path}",
            context: ['path' => $path],
        );
    }

    public static function invalidPkcs12(): self
    {
        return new self(
            message: 'Invalid PKCS#12 file or wrong password',
        );
    }

    public static function invalidPrivateKey(): self
    {
        return new self(
            message: 'Invalid private key',
        );
    }

    public static function invalidCertificate(): self
    {
        return new self(
            message: 'Invalid certificate',
        );
    }

    public static function signingFailed(): self
    {
        return new self(
            message: 'Failed to sign data with private key',
        );
    }
}
