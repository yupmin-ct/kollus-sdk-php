<?php

namespace Kollus\Test\KollusClient\Client;

use Kollus\Component\Client;
use Kollus\Component\Container;

class ApiClientCreationTest extends \PHPUnit_Framework_TestCase
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

    public function testUniqueness()
    {
        $firstClient = Client\ApiClient::getInstance($this->domain, $this->version);
        $this->assertInstanceOf('Kollus\Component\Client\ApiClient', $firstClient);

        $secondClient = Client\ApiClient::getInstance($this->domain, $this->version);
        $this->assertSame($firstClient, $secondClient);
    }

//    public function testNoConstructor()
//    {
//        $object = Client\ApiClient::getInstance($this->domain, $this->version);
//
//        $refl = new \ReflectionObject($object);
//        $meth = $refl->getMethod('__construct');
//        $this->assertTrue($meth->isProtected());
//    }

    public function testConnect()
    {
        $client = Client\ApiClient::getInstance($this->domain, $this->version);
        $client->setServiceAccount($this->serviceAccount);
        $this->assertSame($client->getServiceAccount(), $this->serviceAccount);

        $this->assertInstanceOf('Kollus\Component\Client\ApiClient', $client->connect());

        $this->assertInstanceOf('GuzzleHttp\Client', $client->getClient());
    }

    public function testGetResponseJSON()
    {
        $mockResponseObject = (object)array('error' => 0, 'result' => []);
        $mockClient = $this->getMockClient($mockResponseObject);

        $this->assertEquals($mockResponseObject, $mockClient->getResponseJSON('GET', 'test/test'));
    }

    /**
     * @expectedException \Kollus\Component\Client\ClientException
     * @expectedExceptionMessage error_message
     * @throws Client\ClientException
     */
    public function testGetResponseJSONError()
    {
        $mockResponseObject = (object)array('error' => 1, 'message' => 'error_message');
        $mockClient = $this->getMockClient($mockResponseObject);

        // invoke error exception
        $mockClient->getResponseJSON('GET', 'test/test');
    }
}
