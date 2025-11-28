<?php

declare(strict_types=1);

namespace Coretrek\Digipost;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Configuration for the Digipost client.
 */
final readonly class DigipostClientConfig
{
    public const PRODUCTION_API_URI = 'https://api.digipost.no';

    public const TEST_API_URI = 'https://api.test.digipost.no';

    public const NHN_API_URI = 'https://api.nhn.digipost.no';

    /**
     * @internal Use DigipostClientConfig::builder() to create instances
     */
    public function __construct(
        public string $apiUri,
        public int $connectionTimeout,
        public int $requestTimeout,
        public LoggerInterface $logger,
        public bool $skipMetaDataValidation,
    ) {}

    /**
     * Create a new configuration builder.
     */
    public static function builder(): DigipostClientConfigBuilder
    {
        return new DigipostClientConfigBuilder;
    }

    /**
     * Create a production configuration with default settings.
     */
    public static function production(): self
    {
        return self::builder()->build();
    }

    /**
     * Create a test environment configuration.
     */
    public static function test(): self
    {
        return self::builder()
            ->apiUri(self::TEST_API_URI)
            ->build();
    }

    /**
     * Create a Norsk Helsenett (NHN) configuration.
     */
    public static function nhn(): self
    {
        return self::builder()
            ->apiUri(self::NHN_API_URI)
            ->build();
    }
}

/**
 * Builder for DigipostClientConfig.
 */
final class DigipostClientConfigBuilder
{
    private string $apiUri = DigipostClientConfig::PRODUCTION_API_URI;

    private int $connectionTimeout = 10;

    private int $requestTimeout = 30;

    private LoggerInterface $logger;

    private bool $skipMetaDataValidation = false;

    public function __construct()
    {
        $this->logger = new NullLogger;
    }

    /**
     * Set the API URI.
     */
    public function apiUri(string $uri): self
    {
        $this->apiUri = rtrim($uri, '/');

        return $this;
    }

    /**
     * Set the connection timeout in seconds.
     */
    public function connectionTimeout(int $seconds): self
    {
        $this->connectionTimeout = $seconds;

        return $this;
    }

    /**
     * Set the request timeout in seconds.
     */
    public function requestTimeout(int $seconds): self
    {
        $this->requestTimeout = $seconds;

        return $this;
    }

    /**
     * Set the logger.
     */
    public function logger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Skip metadata validation.
     */
    public function skipMetaDataValidation(bool $skip = true): self
    {
        $this->skipMetaDataValidation = $skip;

        return $this;
    }

    /**
     * Build the configuration.
     */
    public function build(): DigipostClientConfig
    {
        return new DigipostClientConfig(
            apiUri: $this->apiUri,
            connectionTimeout: $this->connectionTimeout,
            requestTimeout: $this->requestTimeout,
            logger: $this->logger,
            skipMetaDataValidation: $this->skipMetaDataValidation,
        );
    }
}
