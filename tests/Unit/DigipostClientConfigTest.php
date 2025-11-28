<?php

declare(strict_types=1);

use Coretrek\Digipost\DigipostClientConfig;

describe('DigipostClientConfig', function (): void {
    it('can create production config', function (): void {
        $config = DigipostClientConfig::production();

        expect($config->apiUri)->toBe(DigipostClientConfig::PRODUCTION_API_URI);
    });

    it('can create test config', function (): void {
        $config = DigipostClientConfig::test();

        expect($config->apiUri)->toBe(DigipostClientConfig::TEST_API_URI);
    });

    it('can create NHN config', function (): void {
        $config = DigipostClientConfig::nhn();

        expect($config->apiUri)->toBe(DigipostClientConfig::NHN_API_URI);
    });

    it('can create custom config using builder', function (): void {
        $config = DigipostClientConfig::builder()
            ->apiUri('https://custom.api.example.com')
            ->requestTimeout(60)
            ->connectionTimeout(15)
            ->build();

        expect($config->apiUri)->toBe('https://custom.api.example.com');
        expect($config->requestTimeout)->toBe(60);
        expect($config->connectionTimeout)->toBe(15);
    });

    it('has default timeout values', function (): void {
        $config = DigipostClientConfig::production();

        expect($config->requestTimeout)->toBe(30);
        expect($config->connectionTimeout)->toBe(10);
    });
});
