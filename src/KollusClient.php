<?php

namespace Kollus\Component;

use Kollus\Component\Client;
use Kollus\Component\Container;

/**
 * Class KollusClient
 * @package Kollus\Component
 */
final class KollusClient
{
    /**
     * @return Client\AbstractClient|Client\VideoGatewayClient|Client\ApiClient
     * @throws Client\ClientException
     */
    public static function getDefaultClient()
    {
        return Client\AbstractClient::getDefaultInstance();
    }

    /**
     * @return string
     */
    public static function getDefaultClientName()
    {
        return Client\AbstractClient::getDefaultInstanceName();
    }

    /**
     * @param string $domain
     * @param int $version
     * @param string $languageKey
     * @param Container\ServiceAccount $serviceAccount
     * @param array $optParams
     * @return Client\ApiClient
     * @throws Client\ClientException
     */
    public static function getApiClient(
        $domain,
        $version,
        $languageKey,
        Container\ServiceAccount $serviceAccount,
        array $optParams = []
    ) {
        /**
         * @var Client\ApiClient $client
         */
        $client = Client\ApiClient::getInstance($domain, $version, $languageKey, $optParams);
        $client->setServiceAccount($serviceAccount);
        $client->connect();

        return $client;
    }

    /**
     * @param string $domain
     * @param int $version
     * @param string $languageKey
     * @param string $serviceAccountKey
     * @param string $apiAccessToken
     * @param array $optParams
     * @return Client\ApiClient
     */
    public static function getApiClientBy(
        $domain,
        $version,
        $languageKey,
        $serviceAccountKey,
        $apiAccessToken,
        array $optParams = []
    ) {
        $serviceAccount = new Container\ServiceAccount(
            ['key' => $serviceAccountKey, 'api_access_token' => $apiAccessToken]
        );

        return $client = self::getApiClient($domain, $version, $languageKey, $serviceAccount, $optParams);
    }

    /**
     * @param string $domain
     * @param int $version
     * @param string $languageKey
     * @param Container\ServiceAccount $serviceAccount
     * @param array $optParams
     * @return Client\VideoGatewayClient
     * @throws Client\ClientException
     */
    public static function getVideoGatewayClient(
        $domain,
        $version,
        $languageKey,
        Container\ServiceAccount $serviceAccount,
        array $optParams = []
    ) {
        /**
         * @var Client\VideoGatewayClient $client
         */
        $client = Client\VideoGatewayClient::getInstance($domain, $version, $languageKey, $optParams);
        $client->setServiceAccount($serviceAccount);
        $client->connect();

        return $client;
    }

    /**
     * @param string $domain
     * @param int $version
     * @param string $languageKey
     * @param string $serviceAccountKey
     * @param string $customKey
     * @param array $optParams
     * @return Client\VideoGatewayClient
     * @throws Client\ClientException
     */
    public static function getVideoGatewayClientBy(
        $domain,
        $version,
        $languageKey,
        $serviceAccountKey,
        $customKey,
        array $optParams = []
    ) {
        $serviceAccount = new Container\ServiceAccount(
            ['key' => $serviceAccountKey, 'custom_key' => $customKey]
        );

        return $client = self::getVideoGatewayClient($domain, $version, $languageKey, $serviceAccount, $optParams);
    }
}
