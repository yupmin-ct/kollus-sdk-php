<?php

namespace Kollus\Test\KollusClient\Client;

use Kollus\Component\Client;
use Kollus\Component\Container;

class ApiClientAccountTest extends \PHPUnit_Framework_TestCase
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

    public function testGetVersionsForKollus()
    {
        $mockResponseObject = (object)array(
            'error' => 0,
            'result' => (object)array(
                'kollus_player_pc_windows' => (object)array(
                    'activex' => (object)array(
                        'version' => '2,0,2,1'
                    ),
                    'np' => (object)array(
                        'version' => '2021'
                    ),
                    'agent' => (object)array(
                        'version' => '3.0.1.0',
                        'file_url' => 'http://file.kollus.com/public/agent/KollusAgent-3.0.1.0.exe'
                    )
                ),
                'kollus_player_pc_mac' => (object)array(
                    'version' => '0.9.8.4',
                    'file_url' => 'http://file.kollus.com/public/kollus_mac/KollusPlayer_OSX_v.0.9.8.4.pkg'
                ),
                'kollus_player_mobile_android' => (object)array(
                    'version' => '1.1.16',
                ),
                'kollus_player_mobile_ios' => '1.4.5',
                'kollus_uploader' => (object)array(
                    'version' => '1.4.0',
                    'file_url' => 'http://file.kollus.com/public/kollus_uploader/kollus-uploader-kr-1.4.0.air'
                )
            )
        );
        $mockClient = $this->getMockClient($mockResponseObject);

        $playerPcWindowsActivex = $mockClient->getVersionsForKollus('player_pc_windows_activex');

        $this->assertInstanceOf(Container\KollusPlayer::class, $playerPcWindowsActivex);
        $this->assertEquals('2,0,2,1', $playerPcWindowsActivex->getVersion());
        $this->assertEquals(
            'http://file.kollus.com/public/kollus2/KollusPlayer-2.0.2.1.cap',
            $playerPcWindowsActivex->getFileUrl()
        );

        $playerPcWindowsNp = $mockClient->getVersionsForKollus('player_pc_windows_np');

        $this->assertInstanceOf(Container\KollusPlayer::class, $playerPcWindowsNp);
        $this->assertEquals('2021', $playerPcWindowsNp->getVersion());
        $this->assertEquals(
            'http://file.kollus.com/public/kollus2/KollusPlayer-2.0.2.1.exe',
            $playerPcWindowsNp->getFileUrl()
        );

        $playerPcWindowsAgent = $mockClient->getVersionsForKollus('player_pc_windows_agent');

        $this->assertInstanceOf(Container\KollusPlayer::class, $playerPcWindowsAgent);
        $this->assertEquals('3.0.1.0', $playerPcWindowsAgent->getVersion());
        $this->assertEquals(
            'http://file.kollus.com/public/agent/KollusAgent-3.0.1.0.exe',
            $playerPcWindowsAgent->getFileUrl()
        );

        $playerMobileIos = $mockClient->getVersionsForKollus('player_mobile_ios');

        $this->assertInstanceOf(Container\KollusPlayer::class, $playerMobileIos);
        $this->assertEquals('1.4.5', $playerMobileIos->getVersion());
        $this->assertEquals(
            'https://itunes.apple.com/app/id760006888',
            $playerMobileIos->getFileUrl()
        );

        $playerMobileAndroid = $mockClient->getVersionsForKollus('player_mobile_android');

        $this->assertInstanceOf(Container\KollusPlayer::class, $playerMobileAndroid);
        $this->assertEquals('1.1.16', $playerMobileAndroid->getVersion());
        $this->assertEquals(
            'market://details?id=com.kollus.media',
            $playerMobileAndroid->getFileUrl()
        );

        $playerUploader = $mockClient->getVersionsForKollus('uploader');

        $this->assertInstanceOf(Container\KollusPlayer::class, $playerUploader);
        $this->assertEquals('1.4.0', $playerUploader->getVersion());
        $this->assertEquals(
            'http://file.kollus.com/public/kollus_uploader/kollus-uploader-kr-1.4.0.air',
            $playerUploader->getFileUrl()
        );
    }
}
