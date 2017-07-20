<?php

namespace Kollus\Test\KollusClient\Client;

use Kollus\Component\Client;
use Kollus\Component\Container;

class ApiClientCategoryTest extends \PHPUnit_Framework_TestCase
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
        $mockClient = $this->getMockClient($mockResponseObject);

        $categories = $mockClient->getCategories();
        $this->assertInstanceOf(Container\ContainerArray::class, $categories);
        $this->assertNotEmpty($categories);

        $firstCategory = $categories[0];

        $this->assertInstanceOf(Container\Category::class, $firstCategory);

        $this->assertEquals(2, $firstCategory->getId());
        $this->assertEquals('name2', $firstCategory->getName());
        $this->assertEquals(2, $firstCategory->getCountOfMediaContents());
    }

    public function testGetCategorieEmpty()
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

        $items = $mockClient->getCategories();
        $this->assertInstanceOf(Container\ContainerArray::class, $items);
        $this->assertEmpty($items);
    }

    public function testCreateCategory()
    {
        $mockResponseObject = (object)array('error' => 0, 'result' => []);
        $mockClient = $this->getMockClient($mockResponseObject);

        $categoryName = 'category_name';
        $this->assertInstanceOf(Client\ApiClient::class, $mockClient->createCategory($categoryName));
    }

    public function testEditCategory()
    {
        $mockResponseObject = (object)array('error' => 0, 'result' => []);
        $mockClient = $this->getMockClient($mockResponseObject);

        $categoryKey = 'category_key';
        $categoryName = 'changed_category_name';
        $this->assertInstanceOf(Client\ApiClient::class, $mockClient->editCategory($categoryKey, [
            'name' => $categoryName,
        ]));
    }

    public function testDeleteCategory()
    {
        $mockResponseObject = (object)array('error' => 0, 'result' => []);
        $mockClient = $this->getMockClient($mockResponseObject);

        $categoryKey = 'category_key';
        $this->assertInstanceOf(Client\ApiClient::class, $mockClient->deleteCategory($categoryKey));
    }
}
