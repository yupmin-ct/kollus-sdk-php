<?php

namespace Kollus\Test\KollusClient\Client;

use Kollus\Component\Client;
use Kollus\Component\Container;

class ApiClientUploadFileTest extends \PHPUnit_Framework_TestCase
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

    /**
     * @param string $body
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockHttpClient($body)
    {
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

        $mockStream->method('getContents')->willReturn($body);
        $mockResponse->method('getStatusCode')->willReturn(200);
        $mockResponse->method('getBody')->willReturn($mockStream);
        $mockClient->method('request')->willReturn($mockResponse);

        return $mockClient;
    }

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
                        (object)array('media_content_id' => 4, 'upload_file_key' => 'upload_file_key4'),
                        (object)array('media_content_id' => 3, 'upload_file_key' => 'upload_file_key3'),
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
                        (object)array('media_content_id' => 2, 'upload_file_key' => 'upload_file_key2'),
                        (object)array('media_content_id' => 1, 'upload_file_key' => 'upload_file_key1'),
                    ]
                )
            )
        );

        // create mock client & request ... more
        $mockClient = $this->getMockBuilder('GuzzleHttp\Client')
            ->disableOriginalConstructor()
            ->getMock();

        $mockResponse = $this->getMockBuilder('GuzzleHttp\Psr7\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $mockStream = $this->getMockBuilder('GuzzleHttp\Psr7\Stream')
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

    public function testFindUploadFilesByPage()
    {
        $mockResponseObject = (object)array(
            'error' => 0,
            'result' => (object)array(
                'count' => 2,
                'per_page' => 10,
                'items' => (object)array(
                    'item' => [
                        (object)array('media_content_id' => 2, 'upload_file_key' => 'upload_file_key2'),
                        (object)array('media_content_id' => 1, 'upload_file_key' => 'upload_file_key1'),
                    ]
                )
            )
        );
        /**
         * @var Client\ApiClient $mockClient
         */
        $mockClient = $this->getMockClient($mockResponseObject);

        $response = $mockClient->findUploadFilesByPage(1, ['transcoding_stage' => 21]);
        $this->assertEquals('stdClass', get_class($response));
        $this->assertInstanceOf('Kollus\Component\Container\ContainerArray', $response->items);
        $this->assertNotempty($response->items);

        /**
         * @var Container\UploadFile $firstUploadFile
         */
        $firstUploadFile = $response->items[0];
        $this->assertInstanceOf('Kollus\Component\Container\UploadFile', $firstUploadFile);

        $this->assertEquals('upload_file_key2', $firstUploadFile->getUploadFileKey());
        $this->assertEquals(2, $firstUploadFile->getMediaContentId());
    }

    public function testGetUploadFiles()
    {
        /**
         * @var Client\ApiClient $mockClient
         */
        $mockClient = $this->getMockClientForPage();

        $uploadFiles = $mockClient->getUploadFiles(['transcoding_stage' => 21]);

        $this->assertInstanceOf('Kollus\Component\Container\ContainerArray', $uploadFiles);
        $this->assertEquals(4, count($uploadFiles));

        $firstUploadFile = $uploadFiles[0];
        $this->assertInstanceOf('Kollus\Component\Container\UploadFile', $firstUploadFile);

        $lastUploadFile = $uploadFiles[3];
        $this->assertInstanceOf('Kollus\Component\Container\UploadFile', $lastUploadFile);
        $this->assertEquals(4, $firstUploadFile->getMediaContentId());
        $this->assertEquals(1, $lastUploadFile->getMediaContentId());
    }

    public function testUploadURLResponse()
    {
        $uploadURL = 'http://upload.com/upload';
        $mockResponseObject = (object)array(
            'error' => 0,
            'result' => (object)array(
                'upload_url' => $uploadURL,
                'upload_file_key' => 'upload_file_key',
                'will_be_expired_at' => 1234
            )
        );
        /**
         * @var Client\ApiClient $mockClient
         */
        $mockClient = $this->getMockClient($mockResponseObject);

        $response = $mockClient->getUploadURLResponse('media_content_key');

        $this->assertEquals('stdClass', get_class($response));
        $this->assertObjectHasAttribute('upload_url', $response);
        $this->assertObjectHasAttribute('upload_file_key', $response);
        $this->assertEquals($uploadURL, $response->upload_url);
        $this->assertEquals('upload_file_key', $response->upload_file_key);
    }

    public function testUploadFileByUploadURL()
    {
        $uploadURL = 'http://upload.com/upload';
        $mockResponseObject = (object)array(
            'error' => 0,
            'result' => (object)array(
                'upload_url' => $uploadURL,
                'upload_file_key' => 'upload_file_key',
                'will_be_expired_at' => 1234
            )
        );
        $filePath = realpath(__DIR__ . '/../../..') . '/tests/fixtures/kollus-video.mp4';
        $mockHttpResponse = '{"error":0, "upload_url" : "http://test.com/url"}';

        $mockClient = $this->getMockClient($mockResponseObject);

        $mockHttpClient = $this->getMockHttpClient($mockHttpResponse);

        $this->assertInstanceOf(
            'Kollus\Component\Client\ApiClient',
            $mockClient->uploadFileByUploadURL($filePath, null, false, false, '', $mockHttpClient)
        );
    }

    /**
     * @expectedException \Kollus\Component\Client\ClientException
     * @expectedExceptionMessage error_message
     * @throws Client\ClientException
     */
    public function testUploadFileByUploadURLError()
    {
        $uploadURL = 'http://upload.com/upload';
        $mockResponseObject = (object)array(
            'error' => 0,
            'result' => (object)array(
                'upload_url' => $uploadURL,
                'upload_file_key' => 'upload_file_key',
                'will_be_expired_at' => 1234
            )
        );
        $filePath = realpath(__DIR__ . '/../../..') . '/tests/fixtures/kollus-video.mp4';
        $mockHttpResponse = '{"error":1, "message" : "error_message"}';

        $mockClient = $this->getMockClient($mockResponseObject);

        $mockHttpClient = $this->getMockHttpClient($mockHttpResponse);

        $mockClient->uploadFileByUploadURL($filePath, null, false, false, '', $mockHttpClient);
    }
}
