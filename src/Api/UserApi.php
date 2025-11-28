<?php

declare(strict_types=1);

namespace Coretrek\Digipost\Api;

use Coretrek\Digipost\Http\DigipostHttpClient;
use Coretrek\Digipost\Representations\PersonalIdentificationNumber;
use Coretrek\Digipost\Representations\UserAccount;
use Coretrek\Digipost\SenderId;
use SimpleXMLElement;

/**
 * API for user account operations.
 */
final readonly class UserApi
{
    public function __construct(
        private DigipostHttpClient $httpClient,
        private SenderId $senderId,
    ) {}

    /**
     * Create or activate a user account.
     */
    public function createOrActivateUserAccount(PersonalIdentificationNumber $pin): UserAccount
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><user-account-request xmlns="http://api.digipost.no/schema/v8"></user-account-request>');
        $xml->addChild('personal-identification-number', $pin->value);

        $xmlString = $xml->asXML();
        $response = $this->httpClient->post(
            "/api/v8/{$this->senderId}/user-accounts",
            $xmlString !== false ? $xmlString : '',
        );

        return UserAccount::fromXml($response);
    }
}
