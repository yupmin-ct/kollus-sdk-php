<?php

namespace Kollus\Test\KollusClient\Container;

use Kollus\Component\Client;
use Kollus\Component\Container;

class CategoryTest extends \PHPUnit_Framework_TestCase
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
        $testKey = 'key1';
        $testName = 'name1';
        $testCountOfMediaContents = 2;
        $category = new Container\Category(
            ['key' => $testKey, 'name' => $testName, 'count_of_media_contents' => $testCountOfMediaContents]
        );
        $this->assertEquals($testKey, $category->getKey());
        $this->assertEquals($testName, $category->getName());
        $this->assertEquals($testCountOfMediaContents, $category->getCountOfMediaContents());
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
        $category = new Container\Category(['key' => $testKey, 'name' => $testName]);

        $mediaContents = $category->getLibraryMediaContents();

        $this->assertInstanceOf('Kollus\Component\Container\ContainerArray', $mediaContents);
        $this->assertNotEmpty($mediaContents);

        $firstMediaContent = $mediaContents[0];

        $this->assertInstanceOf('Kollus\Component\Container\MediaContent', $firstMediaContent);
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
        $this->getMockClient($mockResponseObject);

        $testKey = 'key';
        $testName = 'name';
        $category = new Container\Category(array('key' => $testKey, 'name' => $testName));

        $response = $category->findLibraryMediaContentsByPage();

        $this->assertInstanceOf('stdClass', $response);

        $this->assertInstanceOf('Kollus\Component\Container\ContainerArray', $response->items);
        $this->assertNotEmpty($response->items);

        $firstMediaContent = $response->items[0];

        $this->assertInstanceOf('Kollus\Component\Container\MediaContent', $firstMediaContent);
    }

    public function testEdit()
    {
        $mockResponseObject = (object)array(
            'error' => 0,
            'result' => (object)array()
        );
        $this->getMockClient($mockResponseObject);

        $testKey = 'key';
        $testName = 'name';
        $category = new Container\Category(['key' => $testKey, 'name' => $testName]);

        $this->assertInstanceOf('Kollus\Component\Container\Category', $category->edit(['name' => 'changed_name']));
    }

    public function testDelete()
    {
        $mockResponseObject = (object)array(
            'error' => 0,
            'result' => (object)array()
        );
        $this->getMockClient($mockResponseObject);

        $testKey = 'key';
        $testName = 'name';
        $category = new Container\Category(['key' => $testKey, 'name' => $testName]);

        $this->assertInstanceOf('Kollus\Component\Container\Category', $category->delete());
    }
}
