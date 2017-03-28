<?php

namespace Kollus\Test\KollusClient\Container;

use Kollus\Component\Client;
use Kollus\Component\Container;

class MediaInformationTest extends \PHPUnit_Framework_TestCase
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
        $firstMediaInformation = new Container\MediaInformation();
        $this->assertInstanceOf(
            'Kollus\Component\Container\MediaInformation',
            $firstMediaInformation
        );

        $testFileSize = 1000;
        $testVideoCodec = 'test1';
        $secondMediaInformation = new Container\MediaInformation();
        $secondMediaInformation->setFile(
            (object)array('file_size' => $testFileSize, 'video_codec' => $testVideoCodec)
        );
        $secondMediaInformationFile = $secondMediaInformation->getFile();
        $this->assertInstanceOf('stdClass', $secondMediaInformationFile);
        $this->assertEquals($testFileSize, $secondMediaInformationFile->file_size);
        $this->assertEquals($testVideoCodec, $secondMediaInformationFile->video_codec);
    }
}
