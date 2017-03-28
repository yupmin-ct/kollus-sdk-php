<?php

namespace Kollus\Test\KollusClient\Container;

use Kollus\Component\Client;
use Kollus\Component\Container;

class UploadFileTest extends \PHPUnit_Framework_TestCase
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

    public function testCreation()
    {
        $firstUploadFile = new Container\UploadFile();
        $this->assertInstanceOf('Kollus\Component\Container\UploadFile', $firstUploadFile);

        $testUploadFileKey = 'key';
        $secondUploadFile = new Container\UploadFile(['upload_file_key' => $testUploadFileKey]);
        $this->assertEquals($testUploadFileKey, $secondUploadFile->getUploadFileKey());
    }

    public function testGetLibraryMediaContent()
    {
        $mockResponseObject = (object)array(
            'error' => 0,
            'result' => (object)array(
                'item' => (object)array(
                    'upload_file_key' => 'upload_file_key'
                )
            )
        );
        $this->getMockClient($mockResponseObject);

        $testUploadFileKey = 'key';
        $uploadFile = new Container\UploadFile(['upload_file_key' => $testUploadFileKey]);

        $mediaContent = $uploadFile->getLibraryMediaContent();

        $this->assertInstanceOf('Kollus\Component\Container\MediaContent', $mediaContent);

        $this->assertEquals('upload_file_key', $mediaContent->getUploadFileKey());
    }
}
