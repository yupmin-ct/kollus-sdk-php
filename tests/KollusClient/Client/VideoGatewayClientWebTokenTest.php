<?php

namespace Kollus\Test\KollusClient\Client;

use Kollus\Component\KollusClient;
use Kollus\Component\Container;
use Firebase\JWT\JWT;

class VideoGatewayClientWebTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $domain;

    /**
     * @var int
     */
    private $version;

    /**
     * @var string;
     */
    private $languageKey;

    /**
     * @var Container\ServiceAccount
     */
    private $serviceAccount;

    public function setUp()
    {
        $this->domain = 'kr.kollus.com';
        $this->version = 0;

        $this->serviceAccount = new Container\ServiceAccount(
            ['key' => 'service_account_key', 'custom_key' => 'custom_key']
        );
    }

    public function testGetWebToken()
    {
        $videoGatewayClient = KollusClient::getVideoGatewayClient(
            $this->domain,
            $this->version,
            $this->languageKey,
            $this->serviceAccount
        );
        $token = $videoGatewayClient->getWebToken('media_content_key');
        $this->assertNotEmpty($token);
    }

    public function testGetWebTokenByMediaItem()
    {
        $videoGatewayClient = KollusClient::getVideoGatewayClient(
            $this->domain,
            $this->version,
            $this->languageKey,
            $this->serviceAccount
        );

        $mediaItem1 = new Container\MediaItem([
            'media_content_key' => 'media_content_key1',
            'profile_key' => 'profile_key1',
            'is_intro' => 1,
            'is_seekable' => 0,
        ]);

        $mediaItem2 = new Container\MediaItem([
            'media_content_key' => 'media_content_key2',
            'profile_key' => 'profile_key2',
        ]);

        $token = $videoGatewayClient->getWebToken([
            $mediaItem1,
            $mediaItem2,
        ]);
        $this->assertNotEmpty($token);

        $decoded_token = JWT::decode($token, $this->serviceAccount->getSecurityKey(), ['HS256']);

        $this->assertEquals($mediaItem1->getMediaContentKey(), $decoded_token->mc[0]->mckey);
        $this->assertEquals($mediaItem2->getMediaContentKey(), $decoded_token->mc[1]->mckey);
    }

    public function testGetWebTokenURL()
    {
        $videoGatewayClient = KollusClient::getVideoGatewayClient(
            $this->domain,
            $this->version,
            $this->languageKey,
            $this->serviceAccount
        );
        $webTokenURL = $videoGatewayClient->getWebTokenURL('media_content_key');
        $this->assertNotEmpty($webTokenURL);
    }

    public function testGetWebTokenURLByMediaItem()
    {
        $videoGatewayClient = KollusClient::getVideoGatewayClient(
            $this->domain,
            $this->version,
            $this->languageKey,
            $this->serviceAccount
        );

        $mediaItem1 = new Container\MediaItem([
            'media_content_key' => 'media_content_key1',
            'profile_key' => 'profile_key1',
            'is_intro' => 1,
            'is_seekable' => 0,
        ]);

        $mediaItem2 = new Container\MediaItem([
            'media_content_key' => 'media_content_key2',
            'profile_key' => 'profile_key2',
        ]);

        $webTokenURL = $videoGatewayClient->getWebTokenURL([
            $mediaItem1,
            $mediaItem2,
        ]);

        $this->assertNotEmpty($webTokenURL);

        $parsedWebTokenUrl = parse_url($webTokenURL);

        $this->assertEquals('v.' . $this->domain, $parsedWebTokenUrl['host']);
        $this->assertEquals('/s', $parsedWebTokenUrl['path']);

        parse_str($parsedWebTokenUrl['query'], $parsedQuery);

        $this->assertArrayHasKey('jwt', $parsedQuery);
        $this->assertArrayHasKey('custom_key', $parsedQuery);
        $this->assertEquals($this->serviceAccount->getCustomKey(), $parsedQuery['custom_key']);

        $decoded_token = JWT::decode($parsedQuery['jwt'], $this->serviceAccount->getSecurityKey(), ['HS256']);

        $this->assertEquals($mediaItem1->getMediaContentKey(), $decoded_token->mc[0]->mckey);
        $this->assertEquals($mediaItem2->getMediaContentKey(), $decoded_token->mc[1]->mckey);
    }
}
