<?php

namespace Kollus\Test\KollusClient\Container;

use Kollus\Component\Client;
use Kollus\Component\Container;

class ServiceAccountTest extends \PHPUnit_Framework_TestCase
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
        $firstServiceAccount = new Container\ServiceAccount();
        $this->assertInstanceOf('Kollus\Component\Container\ServiceAccount', $firstServiceAccount);

        $testKey = 'test1';
        $testApiAccessToken = 'test2';
        $secondServiceAccount = new Container\ServiceAccount(
            ['key' => $testKey, 'api_access_token' => $testApiAccessToken]
        );
        $this->assertEquals($secondServiceAccount->getKey(), $testKey);
        $this->assertEquals($secondServiceAccount->getApiAccessToken(), $testApiAccessToken);
    }

    public function testGetCategories()
    {
        $mockResponseObject = (object)array(
            'error' => 0,
            'result' => (object)array(
                'count' => 9,
                'order' => 'id_asc',
                'items' => (object)array(
                    'item' => [
                        (object)array('id' => 2, 'name' => 'name2', 'key' => 'key2', 'count_of_media_contents' => 2),
                        (object)array('id' => 1, 'name' => 'name1', 'key' => 'key1', 'count_of_media_contents' => 1),
                    ]
                )
            )
        );
        $this->getMockClient($mockResponseObject);

        $testKey = 'key';
        $serviceAccount = new Container\ServiceAccount(['key' => $testKey]);

        $categories = $serviceAccount->getCategories();
        $this->assertInstanceOf('Kollus\Component\Container\ContainerArray', $categories);
        $this->assertNotEmpty($categories);

        $firstCategory = $categories[0];

        $this->assertInstanceOf('Kollus\Component\Container\Category', $firstCategory);
    }

    public function testGetChannels()
    {
        $mockResponseObject = (object)array(
            'error' => 0,
            'result' => (object)array(
                'count' => 9,
                'order' => 'id_asc',
                'items' => (object)array(
                    'item' => [
                        (object)array('id' => 2, 'name' => 'name2', 'key' => 'key2', 'count_of_media_contents' => 2),
                        (object)array('id' => 1, 'name' => 'name1', 'key' => 'key1', 'count_of_media_contents' => 1),
                    ]
                )
            )
        );
        $this->getMockClient($mockResponseObject);

        $testKey = 'key';
        $serviceAccount = new Container\ServiceAccount(['key' => $testKey]);

        $channels = $serviceAccount->getChannels();
        $this->assertInstanceOf('Kollus\Component\Container\ContainerArray', $channels);
        $this->assertNotEmpty($channels);

        $firstChannel = $channels[0];

        $this->assertInstanceOf('Kollus\Component\Container\Channel', $firstChannel);
    }

    public function testGetLibraryMediaContents()
    {
        $mockResponseObject = (object)array(
            'error' => 0,
            'result' => (object)array(
                'count' => 2,
                'per_page' => 10,
                'items' => (object)array(
                    'item' => [
                        (object)array('id' => 2, 'upload_file_key' => 'upload_file_key2'),
                        (object)array('id' => 1, 'upload_file_key' => 'upload_file_key1'),
                    ]
                )
            )
        );
        $this->getMockClient($mockResponseObject);

        $testKey = 'key';
        $testName = 'name';
        $serviceAccount = new Container\ServiceAccount(['key' => $testKey, 'name' => $testName]);

        $mediaContents = $serviceAccount->getLibraryMediaContents();
        $this->assertInstanceOf('Kollus\Component\Container\ContainerArray', $mediaContents);
        $this->assertNotEmpty($mediaContents);

        $firstMediaContent = $mediaContents[0];

        $this->assertInstanceOf('Kollus\Component\Container\MediaContent', $firstMediaContent);
    }

    public function testGetChannelMediaContents()
    {
        $mockResponseObject = (object)array(
            'error' => 0,
            'result' => (object)array(
                'count' => 2,
                'per_page' => 10,
                'items' => (object)array(
                    'item' => [
                        (object)array('id' => 2, 'upload_file_key' => 'upload_file_key2'),
                        (object)array('id' => 1, 'upload_file_key' => 'upload_file_key1'),
                    ]
                )
            )
        );
        $this->getMockClient($mockResponseObject);

        $testKey = 'key';
        $testName = 'name';
        $testChannelKey = 'channel_key';
        $serviceAccount = new Container\ServiceAccount(['key' => $testKey, 'name' => $testName]);

        $mediaContents = $serviceAccount->getChannelMediaContents($testChannelKey);
        $this->assertInstanceOf('Kollus\Component\Container\ContainerArray', $mediaContents);
        $this->assertNotEmpty($mediaContents);

        $firstMediaContent = $mediaContents[0];

        $this->assertInstanceOf('Kollus\Component\Container\MediaContent', $firstMediaContent);
    }
}
