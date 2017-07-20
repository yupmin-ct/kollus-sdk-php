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
        $testKey = 'key1';
        $testName = 'name1';
        $testCountOfMediaContents = 2;
        $testLevel = 2;
        $testParentId = 1;
        $category = new Container\Category(
            [
                'key' => $testKey,
                'name' => $testName,
                'count_of_media_contents' => $testCountOfMediaContents,
                'level' => $testLevel,
                'parent_id' => $testParentId,
            ]
        );
        $this->assertEquals($testKey, $category->getKey());
        $this->assertEquals($testName, $category->getName());
        $this->assertEquals($testCountOfMediaContents, $category->getCountOfMediaContents());
        $this->assertEquals($testLevel, $category->getLevel());
        $this->assertEquals($testParentId, $category->getParentId());

        $category->setLevel(1);
        $this->assertEquals(1, $category->getLevel());
        $category->setParentId(0);
        $this->assertEquals(0, $category->getParentId());
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

        $this->assertInstanceOf(Container\ContainerArray::class, $mediaContents);
        $this->assertNotEmpty($mediaContents);

        $firstMediaContent = $mediaContents[0];

        $this->assertInstanceOf(Container\MediaContent::class, $firstMediaContent);
    }

    /**
     * @expectedException \Kollus\Component\Container\ContainerException
     * @throws Container\ContainerException
     */
    public function testInvalidGetLibraryMediaContents()
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

        $category = new Container\Category(array('key' => '', 'name' => ''));

        $category->GetLibraryMediaContents();
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

        $this->assertInstanceOf(Container\ContainerArray::class, $response->items);
        $this->assertNotEmpty($response->items);

        $firstMediaContent = $response->items[0];

        $this->assertInstanceOf(Container\MediaContent::class, $firstMediaContent);
    }

    /**
     * @expectedException \Kollus\Component\Container\ContainerException
     * @throws Container\ContainerException
     */
    public function testInvalidFindLibraryMediaContentsByPage()
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

        $category = new Container\Category(array('key' => '', 'name' => ''));

        $category->findLibraryMediaContentsByPage();
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

        $this->assertInstanceOf(Container\Category::class, $category->edit(['name' => 'changed_name']));
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

        $this->assertInstanceOf(Container\Category::class, $category->delete());
    }
}
