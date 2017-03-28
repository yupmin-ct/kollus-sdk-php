<?php

namespace Kollus\Test\KollusClient\Client;

use Kollus\Component\Client;
use Kollus\Component\Container;

class ApiClientMediaAuthTest extends \PHPUnit_Framework_TestCase
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
            ['key' => 'service_account_key', 'api_access_token' => 'api_access_token']
        );
    }

    /**
     * @param object|array $responseObject
     * @return Client\ApiClient|Client\AbstractClient
     */
    private function getMockClient($responseObject)
    {
        $client = Client\ApiClient::getInstance($this->domain, $this->version);
        $client->setServiceAccount($this->serviceAccount);

        // create mock client & response ... more
        $mockClient = $this->getMockBuilder('GuzzleHttp\Client')
            ->disableOriginalConstructor()
            ->getMock();

        $mockResponse = $this->getMockBuilder('GuzzleHttp\Psr7\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $mockStream = $this->getMockBuilder('GuzzleHttp\Psr7\Stream')
            ->disableOriginalConstructor()
            ->getMock();

        $mockStream->method('getContents')->willReturn(json_encode($responseObject));
        $mockResponse->method('getStatusCode')->willReturn(200);
        $mockResponse->method('getBody')->willReturn($mockStream);
        $mockClient->method('request')->willReturn($mockResponse);

        $client->connect($mockClient);

        return $client;
    }


    public function testGetAudioWaterMarkingCode()
    {
        $code = 'encrypted_string|V1.0';
        $mockResponseObject = (object)array('error' => 0, 'result' => (object)array('code' => $code));
        $mockClient = $this->getMockClient($mockResponseObject);
        $this->assertEquals($mockClient->getAudioWaterMarkingCode('media_content_key'), $code);
    }

    public function testGetMediaToken()
    {
        $mediaToken = 'encrypted_string|V1.0';
        $mockResponseObject = (object)array('error' => 0, 'result' => (object)array('media_token' => $mediaToken));
        $mockClient = $this->getMockClient($mockResponseObject);
        $this->assertEquals($mediaToken, $mockClient->getMediaToken('media_content_key'));
    }

    public function testGetKollusEncrypt()
    {
        $encryptedString = 'encrypted_string|V1.0';
        $mockResponseObject = (object)array(
            'error' => 0,
            'result' => (object)array('encrypt_string' => $encryptedString)
        );
        $mockClient = $this->getMockClient($mockResponseObject);
        $this->assertNotEmpty($mockClient->getKollusEncrypt('source_string'));

        $this->assertEquals('encrypted_string|V1.0', $mockClient->getKollusEncrypt('source_string'));
    }
}
