<?php

namespace Kollus\Test\KollusClient\Client;

use Kollus\Component\Client;
use Kollus\Component\Container;

class ApiClientMediaContentTest extends \PHPUnit_Framework_TestCase
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

    /**
     * @return Client\ApiClient|Client\AbstractClient
     * @throws Client\ClientException
     */
    private function getMockClientForPage()
    {
        $client = Client\ApiClient::getInstance($this->domain, $this->version);
        $client->setServiceAccount($this->serviceAccount);

        $responseObject1 = (object)array(
            'error' => 0,
            'result' => (object)array(
                'count' => 4,
                'per_page' => 2,
                'items' => (object)array(
                    'item' => [
                        (object)array('id' => 4, 'upload_file_key' => 'upload_file_key4'),
                        (object)array('id' => 3, 'upload_file_key' => 'upload_file_key3'),
                    ]
                )
            )
        );
        $responseObject2 = (object)array(
            'error' => 0,
            'result' => (object)array(
                'count' => 4,
                'per_page' => 2,
                'items' => (object)array(
                    'item' => [
                        (object)array('id' => 2, 'upload_file_key' => 'upload_file_key2'),
                        (object)array('id' => 1, 'upload_file_key' => 'upload_file_key1'),
                    ]
                )
            )
        );

        // create mock client & request ... more
        $mockClient = $this->getMockBuilder(\GuzzleHttp\Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockResponse = $this->getMockBuilder(\GuzzleHttp\Psr7\Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockStream = $this->getMockBuilder(\GuzzleHttp\Psr7\Stream::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockStream->method('getContents')->will(
            $this->onConsecutiveCalls(
                json_encode($responseObject1),
                json_encode($responseObject2)
            )
        );
        $mockResponse->method('getStatusCode')->willReturn(200);
        $mockResponse->method('getBody')->willReturn($mockStream);
        $mockClient->method('request')->willReturn($mockResponse);

        $client->connect($mockClient);

        return $client;
    }

    public function testGetOptParams()
    {
        $client = Client\ApiClient::getInstance($this->domain, $this->version, 'korean', ['timeout' => 30]);

        $this->assertInstanceOf(Client\ApiClient::class, $client);

        $optParam = $client->getOptParams();

        $this->assertNotEmpty($optParam);
        $this->assertArrayHasKey('timeout', $optParam);
    }

    public function testGetLibraryMediaContent()
    {
        $mockResponseObject = (object)array(
            'error' => 0,
            'result' => (object)array(
                'item' => (object)array('id' => 2, 'upload_file_key' => 'upload_file_key2')
            )
        );
        $mockClient = $this->getMockClient($mockResponseObject);

        $mediaContent = $mockClient->getLibraryMediaContent('upload_file_key2');

        $this->assertInstanceOf(Container\MediaContent::class, $mediaContent);
        $this->assertEquals(2, $mediaContent->getId());
    }

    public function testFindLibraryMediaContentsByPage()
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
        /**
         * @var Client\ApiClient $mockClient
         */
        $mockClient = $this->getMockClient($mockResponseObject);

        $response = $mockClient->findLibraryMediaContentsByPage(1, ['transcoding_stage' => 21]);
        $this->assertEquals('stdClass', get_class($response));
        $this->assertInstanceOf(Container\ContainerArray::class, $response->items);
        $this->assertNotEmpty($response->items);

        /**
         * @var Container\MediaContent $firstMediaContent
         */
        $firstMediaContent = $response->items[0];
        $this->assertInstanceOf(Container\MediaContent::class, $firstMediaContent);

        $this->assertEquals(2, $firstMediaContent->getId());
        $this->assertEquals('upload_file_key2', $firstMediaContent->getUploadFileKey());
    }

    public function testFindLibraryMediaContentsByPageEmpty()
    {
        $mockResponseObject = (object)array(
            'error' => 0,
            'result' => (object)array(
                'count' => 0,
                'per_page' => 10,
                'items' => []
            )
        );
        $mockClient = $this->getMockClient($mockResponseObject);

        $response = $mockClient->findLibraryMediaContentsByPage(1, ['transcoding_stage' => 21]);
        $this->assertEquals('stdClass', get_class($response));
        $this->assertInstanceOf(Container\ContainerArray::class, $response->items);
        $this->assertEmpty($response->items);
    }

    public function testGetLibraryMediaContents()
    {
        $mockClient = $this->getMockClientForPage();

        $mediaContents = $mockClient->getLibraryMediaContents(['transcoding_stage' => 21]);

        $this->assertInstanceOf(Container\ContainerArray::class, $mediaContents);
        $this->assertEquals(4, count($mediaContents));
        $firstMediaContent = $mediaContents[0];
        $this->assertInstanceOf(Container\MediaContent::class, $firstMediaContent);

        $lastMediaContent = $mediaContents[3];
        $this->assertInstanceOf(Container\MediaContent::class, $lastMediaContent);

        $this->assertEquals(4, $firstMediaContent->getId());
        $this->assertEquals(1, $lastMediaContent->getId());
    }

    public function testDeleteMediaContent()
    {
        $uploadFileKey = 'upload_file_key';
        $mockResponseObject = (object)array('error' => 0, 'result' => []);
        $mockClient = $this->getMockClient($mockResponseObject);

        $this->assertInstanceOf(Client\ApiClient::class, $mockClient->deleteMediaContent($uploadFileKey));
    }

    /**
     * @expectedException \Kollus\Component\Client\ClientException
     * @expectedExceptionMessage error_message
     * @throws Client\ClientException
     */
    public function testGetLibraryMediaContentResponseError()
    {
        $mockResponseObject = (object)array('error' => 1, 'message' => 'error_message');
        $mockClient = $this->getMockClient($mockResponseObject);

        // invoke error exception
        $mockClient->getLibraryMediaContent('xxxxx');
    }

    public function testGetChannelMediaContent()
    {
        $mockResponseObject = (object)array(
            'error' => 0,
            'result' => (object)array(
                'item' => (object)array(
                    'id' => 2, 'upload_file_key' => 'upload_file_key', 'media_content_key' => 'media_content_key'
                )
            )
        );
        $mockClient = $this->getMockClient($mockResponseObject);

        $mediaContent = $mockClient->getChannelMediaContent('channel_key', 'upload_file_key');

        $this->assertInstanceOf(Container\MediaContent::class, $mediaContent);
        $this->assertEquals('media_content_key', $mediaContent->getMediaContentKey());
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
                        (object)array('id' => 2, 'media_content_key' => 'media_content_key2'),
                        (object)array('id' => 1, 'media_content_key' => 'media_content_key1'),
                    ]
                )
            )
        );
        $mockClient = $this->getMockClient($mockResponseObject);

        $response = $mockClient->findChannelMediaContentsByPage('channel_key', 1);
        $this->assertEquals('stdClass', get_class($response));
        $this->assertInstanceOf(Container\ContainerArray::class, $response->items);
        $this->assertNotempty($response->items);

        /**
         * @var Container\MediaContent $firstMediaContent
         */
        $firstMediaContent = $response->items[0];
        $this->assertInstanceOf(Container\MediaContent::class, $firstMediaContent);
        $this->assertEquals('media_content_key2', $firstMediaContent->getMediaContentKey());
    }

    public function testGetChannelMediaContents()
    {
        $mockClient = $this->getMockClientForPage();

        $mediaContents = $mockClient->getChannelMediaContents('channel_key');
        $this->assertInstanceOf(Container\ContainerArray::class, $mediaContents);
        $this->assertEquals(4, count($mediaContents));
        $firstMediaContent = $mediaContents[0];
        $this->assertInstanceOf(Container\MediaContent::class, $firstMediaContent);

        $lastMediaContent = $mediaContents[3];
        $this->assertInstanceOf(Container\MediaContent::class, $lastMediaContent);

        $this->assertEquals(4, $firstMediaContent->getId());
        $this->assertEquals(1, $lastMediaContent->getId());
    }

    public function testSetEnableMediaContent()
    {
        $uploadFileKey = 'upload_file_key';
        $mockResponseObject = (object)array('error' => 0, 'result' => []);
        $mockClient = $this->getMockClient($mockResponseObject);

        $this->assertInstanceOf(Client\ApiClient::class, $mockClient->setEnableMediaContent($uploadFileKey));
    }

    public function testSetDisableMediaContent()
    {
        $uploadFileKey = 'upload_file_key';
        $mockResponseObject = (object)array('error' => 0, 'result' => []);
        $mockClient = $this->getMockClient($mockResponseObject);

        $this->assertInstanceOf(Client\ApiClient::class, $mockClient->setDisableMediaContent($uploadFileKey));
    }

    public function testChangeCategoryMediaContent()
    {
        $uploadFileKey = 'upload_file_key';
        $categoryKey = 'category_key';
        $mockResponseObject = (object)array('error' => 0, 'result' => []);
        $mockClient = $this->getMockClient($mockResponseObject);

        $this->assertInstanceOf(
           Client\ApiClient::class,
            $mockClient->changeCategoryMediaContent($uploadFileKey, $categoryKey)
        );
    }

    public function testUploadPoster()
    {
        $uploadFileKey = 'upload_file_key';
        $filePath = realpath(__DIR__ . '/../../..') . '/tests/fixtures/kollus-poster.jpg';

        $mockResponseObject = (object)array('error' => 0, 'result' => []);
        $mockClient = $this->getMockClient($mockResponseObject);

        $this->assertInstanceOf(Client\ApiClient::class, $mockClient->uploadPoster($uploadFileKey, $filePath));
    }

    public function testAddAdditionalTranscodingFile()
    {
        $uploadFileKey = 'upload_file_key';
        $profileKey = 'profile_key';

        $mockResponseObject = (object)array('error' => 0, 'result' => []);
        $mockClient = $this->getMockClient($mockResponseObject);

        $this->assertInstanceOf(Client\ApiClient::class, $mockClient->addAdditionalTranscodingFile($uploadFileKey, $profileKey));
    }
}
