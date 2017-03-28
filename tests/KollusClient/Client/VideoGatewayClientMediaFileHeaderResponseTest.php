<?php

namespace Kollus\Test\KollusClient\Client;

use Kollus\Component\Client;
use Kollus\Component\Container;

class VideoGatewayClientMediaFileHeaderResponseTest extends \PHPUnit_Framework_TestCase
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

    /**
     * @param int $responseStatusCode
     * @param array $responseHeaders
     * @return Client\VideoGatewayClient|Client\AbstractClient
     */
    private function getMockClient($responseStatusCode, $responseHeaders)
    {
        $client = Client\VideoGatewayClient::getInstance($this->domain, $this->version);
        $client->setServiceAccount($this->serviceAccount);

        // create mock client & response ... more
        $mockClient = $this->getMockBuilder('GuzzleHttp\Client')
            ->disableOriginalConstructor()
            ->getMock();

        $mockResponse = $this->getMockBuilder('GuzzleHttp\Psr7\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $mockResponse->method('getStatusCode')->willReturn($responseStatusCode);
        $mockResponse->method('getHeader')->willReturn($responseHeaders);
        $mockClient->method('request')->willReturn($mockResponse);

        $client->connect($mockClient);

        return $client;
    }

    public function testGetMediaFileHeaderResponse()
    {
        $mockResponseHeaders = ['http://test.com/url'];
        /**
         * @var Client\VideoGatewayClient $mockClient
         */
        $mockClient = $this->getMockClient(302, $mockResponseHeaders);
        $headerResponse = $mockClient->getMediaFileHeaderResponse('media_content_key', 'client_user_id');

        $this->assertInstanceOf('GuzzleHttp\Psr7\Response', $headerResponse);
        $this->assertEquals(302, $headerResponse->getStatusCode());
        $this->assertEquals($mockResponseHeaders, $headerResponse->getHeader('location'));
    }

    /**
     * @expectedException \Kollus\Component\Client\ClientException
     * @expectedExceptionMessage Status code is not 302.
     * @throws Client\ClientException
     */
    public function testInvalidStatusCode()
    {
        $mockResponseHeaders = ['http://test.com/url'];
        $mockClient = $this->getMockClient(200, $mockResponseHeaders);
        $mockClient->getMediaFileHeaderResponse('media_content_key', 'client_user_id');
    }

    /**
     * @expectedException \Kollus\Component\Client\ClientException
     * @expectedExceptionMessage Locations is empty.
     * @throws Client\ClientException
     */
    public function testInvalidLocations()
    {
        $mockResponseHeaders = [];
        $mockClient = $this->getMockClient(302, $mockResponseHeaders);
        $mockClient->getMediaFileHeaderResponse('media_content_key', 'client_user_id');
    }
}
