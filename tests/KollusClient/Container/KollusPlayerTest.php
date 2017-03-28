<?php

namespace Kollus\Test\KollusClient\Container;

use Kollus\Component\Client;
use Kollus\Component\Container;

class KollusPlayerTest extends \PHPUnit_Framework_TestCase
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

    public function testCreation()
    {
        $firstKollusPlayer = new Container\KollusPlayer();
        $this->assertInstanceOf('Kollus\Component\Container\KollusPlayer', $firstKollusPlayer);

        $testVersion = '1.0';
        $testFileUrl = 'test1';
        $secondKollusPlayer = new Container\KollusPlayer(
            ['version' => $testVersion, 'file_url' => $testFileUrl]
        );
        $this->assertEquals($testVersion, $secondKollusPlayer->getVersion());
        $this->assertEquals($testFileUrl, $secondKollusPlayer->getFileUrl());
    }
}
