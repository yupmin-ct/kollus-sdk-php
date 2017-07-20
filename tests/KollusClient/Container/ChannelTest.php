<?php

namespace Kollus\Test\KollusClient\Container;

use Kollus\Component\Client;
use Kollus\Component\Container;

class ChannelTest extends \PHPUnit_Framework_TestCase
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
        $mockClient = $this->getMockBuilder(\GuzzleHttp\Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockResponse = $this->getMockBuilder(\GuzzleHttp\Psr7\Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockStream = $this->getMockBuilder(\GuzzleHttp\Psr7\Stream::class)
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
        $firstChannel = new Container\Channel();
        $this->assertInstanceOf(Container\Channel::class, $firstChannel);

        $testKey = 'key1';
        $testName = 'name1';
        $testMediaContentKey1 = 'xxxxxx';
        $secondChannel = new Container\Channel(
            [
                'key' => $testKey,
                'name' => $testName,
                'use_pingback' => 1,
                'status' => 1,
                'media_content_key' => $testMediaContentKey1,
            ]
        );
        $this->assertEquals($testKey, $secondChannel->getKey());
        $this->assertEquals($testName, $secondChannel->getName());
        $this->assertEquals(1, $secondChannel->getUsePingback());
        $this->assertEquals(1, $secondChannel->getStatus());
        $this->assertEquals($testMediaContentKey1, $secondChannel->getMediaContentKey());

        $secondChannel->setUsePingback(0);
        $secondChannel->setStatus(0);
        $testMediaContentKey2 = 'yyyyyy';
        $secondChannel->setMediaContentKey($testMediaContentKey2);

        $this->assertEquals(0, $secondChannel->getUsePingback());
        $this->assertEquals(0, $secondChannel->getStatus());
        $this->assertEquals($testMediaContentKey2, $secondChannel->getMediaContentKey());
    }

    public function testCreationAtMediaContent()
    {
        $mediaContent = new Container\MediaContent(
            [
                'channels' => [
                    (object)array(
                        'channel_name' => 'channel_name1',
                        'channel_key' => 'channel_key1',
                    ),
                    (object)array(
                        'channel_name' => 'channel_name2',
                        'channel_key' => 'channel_key2',
                    ),
                ]
            ]
        );

        $channels = $mediaContent->getChannels();

        $this->assertInstanceOf(Container\ContainerArray::class, $channels);
        $this->assertNotEmpty($channels);

        $firstChannel = $channels[0];

        $this->assertInstanceOf(Container\Channel::class, $firstChannel);

        $this->assertEquals('channel_key1', $firstChannel->getKey());
        $this->assertEquals('channel_name1', $firstChannel->getName());
    }

    public function testFindChannelMediaContentsByPage()
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
        $channel = new Container\Channel(['key' => $testKey, 'name' => $testName]);

        $response = $channel->findChannelMediaContentsByPage();

        $this->assertInstanceOf('stdClass', $response);

        $this->assertInstanceOf(Container\ContainerArray::class, $response->items);
        $this->assertNotEmpty($response->items);

        $firstMediaContent = $response->items[0];

        $this->assertInstanceOf(Container\MediaContent::class, $firstMediaContent);
    }

    /**
     * @expectedException \Kollus\Component\Container\ContainerException
     * @throws Container\ContainerException
     */
    public function testInvalidFindChannelMediaContentsByPage()
    {
        $mockResponseObject = (object)array(
            'error' => 0,
            'result' => (object)array(
                'count' => 2,
                'per_page' => 10,
                'items' => (object)array(
                    'item' => []
                )
            )
        );
        $this->getMockClient($mockResponseObject);

        $channel = new Container\Channel(array('key' => '', 'name' => ''));

        $channel->findChannelMediaContentsByPage();
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
        $channel = new Container\Channel(['key' => $testKey, 'name' => $testName]);

        $mediaContents = $channel->getChannelMediaContents();

        $this->assertInstanceOf(Container\ContainerArray::class, $mediaContents);
        $this->assertNotEmpty($mediaContents);

        $firstMediaContent = $mediaContents[0];

        $this->assertInstanceOf(Container\MediaContent::class, $firstMediaContent);
    }

    /**
     * @expectedException \Kollus\Component\Container\ContainerException
     * @throws Container\ContainerException
     */
    public function testInvalidGetChannelMediaContents()
    {
        $mockResponseObject = (object)array(
            'error' => 0,
            'result' => (object)array(
                'count' => 2,
                'per_page' => 10,
                'items' => (object)array(
                    'item' => []
                )
            )
        );
        $this->getMockClient($mockResponseObject);

        $channel = new Container\Channel(array('key' => '', 'name' => ''));

        $channel->GetChannelMediaContents();
    }
}
