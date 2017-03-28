<?php

namespace Kollus\Test\KollusClient\Client;

use Kollus\Component\Client;
use Kollus\Component\Container;

class ApiClientChannelTest extends \PHPUnit_Framework_TestCase
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
        $mockClient = $this->getMockClient($mockResponseObject);

        $channels = $mockClient->getChannels();
        $this->assertInstanceOf('Kollus\Component\Container\ContainerArray', $channels);
        $this->assertNotEmpty($channels);

        $firstChannel = $channels[0];

        $this->assertInstanceOf('Kollus\Component\Container\Channel', $firstChannel);

        $this->assertEquals(2, $firstChannel->getId());
        $this->assertEquals('name2', $firstChannel->getName());
        $this->assertEquals(2, $firstChannel->getCountOfMediaContents());
    }

    public function testCreateChannel()
    {
        $mockResponseObject = (object)array('error' => 0, 'result' => []);
        $mockClient = $this->getMockClient($mockResponseObject);

        $channelName = 'channel_name';
        $this->assertInstanceOf(
            'Kollus\Component\Client\ApiClient',
            $mockClient->createChannel($channelName)
        );
    }

    public function testDeleteChannel()
    {
        $mockResponseObject = (object)array('error' => 0, 'result' => []);
        $mockClient = $this->getMockClient($mockResponseObject);

        $channelKey = 'channel_key';
        $this->assertInstanceOf(
            'Kollus\Component\Client\ApiClient',
            $mockClient->deleteChannel($channelKey)
        );
    }

    public function testAttachChannel()
    {
        $mockResponseObject = (object)array('error' => 0, 'result' => []);
        $mockClient = $this->getMockClient($mockResponseObject);

        $channelKey = 'channel_key';
        $uploadFileKey = 'upload_file_key';
        $this->assertInstanceOf(
            'Kollus\Component\Client\ApiClient',
            $mockClient->attachChannel($channelKey, $uploadFileKey)
        );
    }

    public function testDetachChannel()
    {
        $mockResponseObject = (object)array('error' => 0, 'result' => []);
        $mockClient = $this->getMockClient($mockResponseObject);

        $channelKey = 'channel_key';
        $uploadFileKey = 'upload_file_key';
        $this->assertInstanceOf(
            'Kollus\Component\Client\ApiClient',
            $mockClient->detachChannel($channelKey, $uploadFileKey)
        );
    }
}
