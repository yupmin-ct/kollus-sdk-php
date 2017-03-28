<?php

namespace Kollus\Test\KollusClient\Client;

use Kollus\Component\Client;
use Kollus\Component\Container;

class ApiClientMediaProfileTest extends \PHPUnit_Framework_TestCase
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


    public function testGetMediaProfileGroups()
    {
        $mockResponseObject = (object)array(
            'error' => 0,
            'result' => (object)array(
                'count' => 9,
                'order' => 'id_asc',
                'items' => (object)array(
                    'item' => [
                        (object)array('id' => 2, 'name' => 'name2', 'key' => 'key2'),
                        (object)array('id' => 1, 'name' => 'name1', 'key' => 'key1'),
                    ]
                )
            )
        );
        $mockClient = $this->getMockClient($mockResponseObject);

        $mediaProfileGroups = $mockClient->getMediaProfileGroups();

        $this->assertInstanceOf('Kollus\Component\Container\ContainerArray', $mediaProfileGroups);
        $this->assertNotEmpty($mediaProfileGroups);

        $firstMediaProfileGroup = $mediaProfileGroups[0];

        $this->assertInstanceOf('Kollus\Component\Container\MediaProfileGroup', $firstMediaProfileGroup);
        $this->assertEquals(2, $firstMediaProfileGroup->getId());
        $this->assertEquals('name2', $firstMediaProfileGroup->getName());
    }

    public function testGetMediaProfileGroupsEmpty()
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

        $mediaProfileGroups = $mockClient->getMediaProfileGroups();
        $this->assertEmpty($mediaProfileGroups);
    }

    public function testGetMediaProfiles()
    {
        $mockResponseObject = (object)array(
            'error' => 0,
            'result' =>
            (object)array(
                'count' => 9,
                'order' => 'id_asc',
                'items' => [
                    (object)array(
                        'id' => 2,
                        'name' => 'name2',
                        'key' => 'key2',
                        'media_profile_group_key' => 'media_profile_group_key2',
                        'container_format' => 'mp4',
                    ),
                    (object)array(
                        'id' => 1,
                        'name' => 'name1',
                        'key' => 'key1',
                        'media_profile_group_key' => 'media_profile_group_key1',
                        'container_format' => 'mp4',
                    ),
                ]
            )
        );
        /**
         * @var Client\ApiClient $mockClient
         */
        $mockClient = $this->getMockClient($mockResponseObject);

        $mediaProfiles = $mockClient->getMediaProfiles();

        $this->assertInstanceOf('Kollus\Component\Container\ContainerArray', $mediaProfiles);
        $this->assertNotEmpty($mediaProfiles);

        $firstMediaProfile = $mediaProfiles[0];

        $this->assertInstanceOf('Kollus\Component\Container\MediaProfile', $firstMediaProfile);
        $this->assertEquals(2, $firstMediaProfile->getId());
        $this->assertEquals('name2', $firstMediaProfile->getName());

        /**
         * @var Container\MediaProfileGroup $mediaProfileGroup
         */
        $mediaProfileGroup = $firstMediaProfile->getMediaProfileGroup();
        $this->assertInstanceOf(
            'Kollus\Component\Container\MediaProfileGroup',
            $mediaProfileGroup
        );

        $this->assertEquals('media_profile_group_key2', $mediaProfileGroup->getKey());
        $this->assertEquals('mp4', $firstMediaProfile->getContainerFormat());
    }

    public function testGetMediaProfilesEmpty()
    {
        $mockResponseObject = (object)array(
            'error' => 0,
            'result' => (object)array(
                'count' => 0,
                'per_page' => 10,
                'items' => []
            )
        );
        /**
         * @var Client\ApiClient $mockClient
         */
        $mockClient = $this->getMockClient($mockResponseObject);

        $mediaProfiles = $mockClient->getMediaProfiles();
        $this->assertInstanceOf('Kollus\Component\Container\ContainerArray', $mediaProfiles);
        $this->assertEmpty($mediaProfiles);
    }

    public function testGetMediaProfilePresets()
    {
        $mockResponseObject = (object)array(
            'error' => 0,
            'result' => (object)array(
                'count' => 9,
                'order' => 'id_asc',
                'items' => [
                    (object)array(
                        'id' => 2,
                        'name' => 'name2',
                        'key' => 'key2',
                        'media_profile_group_key' => 'media_profile_group_key2',
                        'container_format' => 'mp4',
                    ),
                    (object)array(
                        'id' => 1,
                        'name' => 'name1',
                        'key' => 'key1',
                        'media_profile_group_key' => 'media_profile_group_key1',
                        'container_format' => 'mp4',
                    ),
                ]
            )
        );
        /**
         * @var Client\ApiClient $mockClient
         */
        $mockClient = $this->getMockClient($mockResponseObject);

        $mediaProfiles = $mockClient->getMediaProfilePresets();

        $this->assertInstanceOf('Kollus\Component\Container\ContainerArray', $mediaProfiles);
        $this->assertNotEmpty($mediaProfiles);

        $firstMediaProfile = $mediaProfiles[0];

        $this->assertInstanceOf('Kollus\Component\Container\MediaProfile', $firstMediaProfile);
        $this->assertEquals(2, $firstMediaProfile->getId());
        $this->assertEquals('name2', $firstMediaProfile->getName());

        /**
         * @var Container\MediaProfileGroup $mediaProfileGroup
         */
        $mediaProfileGroup = $firstMediaProfile->getMediaProfileGroup();
        $this->assertInstanceOf(
            'Kollus\Component\Container\MediaProfileGroup',
            $mediaProfileGroup
        );

        $this->assertEquals('media_profile_group_key2', $mediaProfileGroup->getKey());
        $this->assertEquals('mp4', $firstMediaProfile->getContainerFormat());
    }

    public function testGetMediaProfilePresetsEmpty()
    {
        $mockResponseObject = (object)array(
            'error' => 0,
            'result' => (object)array(
                'count' => 0,
                'per_page' => 10,
                'items' => [],
            )
        );
        $mockClient = $this->getMockClient($mockResponseObject);

        $mediaProfilePresets = $mockClient->GetMediaProfilePresets();
        $this->assertEmpty($mediaProfilePresets);
    }

    public function testCreateMediaProfile()
    {
        $mockResponseObject = (object)array('error' => 0, 'result' => []);
        $mockClient = $this->getMockClient($mockResponseObject);
        $mediaProfilePresetId = 1;

        $this->assertInstanceOf(
            'Kollus\Component\Client\ApiClient',
            $mockClient->createMediaProfile($mediaProfilePresetId)
        );
    }

    public function testEditMediaProfile()
    {
        $mockResponseObject = (object)array('error' => 0, 'result' => []);
        $mockClient = $this->getMockClient($mockResponseObject);
        $mediaProfileId = 1;
        $postParams = ['name' => 'changed_name'];

        $this->assertInstanceOf(
            'Kollus\Component\Client\ApiClient',
            $mockClient->editMediaProfile($mediaProfileId, $postParams)
        );
    }
}
