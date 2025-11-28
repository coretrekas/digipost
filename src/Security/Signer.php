<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Security;

use Coretrek\Digipost\Exceptions\CertificateException;
use OpenSSLAsymmetricKey;
use OpenSSLCertificate;

/**
 * Handles signing of API requests using a PKCS#12 certificate.
 */
final readonly class Signer
{
    private function __construct(private OpenSSLAsymmetricKey $privateKey, private OpenSSLCertificate $certificate) {}

    /**
     * Create a Signer from a PKCS#12 keystore file.
     *
     * @param string $path Path to the .p12 file
     * @param string $password Password for the keystore
     *
     * @throws CertificateException If the certificate cannot be loaded
     */
    public static function fromPkcs12File(string $path, string $password): self
    {
        if (!file_exists($path)) {
            throw CertificateException::fileNotFound($path);
        }

        $content = file_get_contents($path);
        if ($content === false) {
            throw CertificateException::cannotRead($path);
        }

        return self::fromPkcs12String($content, $password);
    }

    /**
     * Create a Signer from a PKCS#12 keystore string.
     *
     * @param string $pkcs12Content The PKCS#12 content
     * @param string $password Password for the keystore
     *
     * @throws CertificateException If the certificate cannot be loaded
     */
    public static function fromPkcs12String(string $pkcs12Content, string $password): self
    {
        $certs = [];
        if (!openssl_pkcs12_read($pkcs12Content, $certs, $password)) {
            throw CertificateException::invalidPkcs12();
        }

        $privateKey = openssl_pkey_get_private($certs['pkey']);
        if ($privateKey === false) {
            throw CertificateException::invalidPrivateKey();
        }

        $certificate = openssl_x509_read($certs['cert']);
        if ($certificate === false) {
            throw CertificateException::invalidCertificate();
        }

        return new self($privateKey, $certificate);
    }

    /**
     * Create a Signer from separate PEM files.
     *
     * @param string $certificatePath Path to the certificate PEM file
     * @param string $privateKeyPath Path to the private key PEM file
     * @param string|null $privateKeyPassword Password for the private key (if encrypted)
     *
     * @throws CertificateException If the certificate or key cannot be loaded
     */
    public static function fromPemFiles(
        string $certificatePath,
        string $privateKeyPath,
        ?string $privateKeyPassword = null,
    ): self {
        if (!file_exists($certificatePath)) {
            throw CertificateException::fileNotFound($certificatePath);
        }

        if (!file_exists($privateKeyPath)) {
            throw CertificateException::fileNotFound($privateKeyPath);
        }

        $certContent = file_get_contents($certificatePath);
        $keyContent = file_get_contents($privateKeyPath);

        if ($certContent === false || $keyContent === false) {
            throw CertificateException::cannotRead($certificatePath);
        }

        $certificate = openssl_x509_read($certContent);
        if ($certificate === false) {
            throw CertificateException::invalidCertificate();
        }

        $privateKey = openssl_pkey_get_private($keyContent, $privateKeyPassword ?? '');
        if ($privateKey === false) {
            throw CertificateException::invalidPrivateKey();
        }

        return new self($privateKey, $certificate);
    }

    /**
     * Sign data using the private key.
     *
     * @param string $data The data to sign
     *
     * @throws CertificateException If signing fails
     *
     * @return string The signature in base64 format
     */
    public function sign(string $data): string
    {
        $signature = '';
        $result = openssl_sign($data, $signature, $this->privateKey, OPENSSL_ALGO_SHA256);

        if (!$result) {
            throw CertificateException::signingFailed();
        }

        return base64_encode((string) $signature);
    }

    /**
     * Get the certificate in PEM format.
     */
    public function getCertificatePem(): string
    {
        $output = '';
        openssl_x509_export($this->certificate, $output);

        return $output;
    }

    /**
     * Get the certificate fingerprint (SHA-256).
     */
    public function getCertificateFingerprint(): string
    {
        $fingerprint = openssl_x509_fingerprint($this->certificate, 'sha256');

        return $fingerprint !== false ? $fingerprint : '';
    }

    /**
     * Get certificate information.
     *
     * @return array<string, mixed>
     */
    public function getCertificateInfo(): array
    {
        $info = openssl_x509_parse($this->certificate);

        return $info !== false ? $info : [];
    }
}
