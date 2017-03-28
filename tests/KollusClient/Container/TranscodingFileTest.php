<?php

namespace Kollus\Test\KollusClient\Container;

use Kollus\Component\Container;

class TranscodingFileTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $firstTranscodingFile = new Container\TranscodingFile();
        $this->assertInstanceOf('Kollus\Component\Container\TranscodingFile', $firstTranscodingFile);

        $testProfileKey = 'profile_key';
        $testProfileName = 'profile_name';
        $secondTranscodingFile = new Container\TranscodingFile(
            ['profile_name' => $testProfileName, 'profile_key' => $testProfileKey]
        );

        $this->assertEquals($testProfileKey, $secondTranscodingFile->getProfileKey());

        $testProfileGroupKey = 'profile_group_key';
        $testProfileGroupName = 'profile_group_name';
        $secondTranscodingFile = new Container\TranscodingFile(
            ['media_profile_group_name' => $testProfileGroupName, 'media_profile_group_key' => $testProfileGroupKey]
        );
        $mediaProfileGroup = $secondTranscodingFile->getMediaProfileGroup();
        $this->assertInstanceOf('Kollus\Component\Container\MediaProfileGroup', $mediaProfileGroup);
    }

    public function testCreationAtMediaContent()
    {
        $mediaContent = new Container\MediaContent(
            [
                'transcoding_files' => [
                    (object)array(
                        'profile_name' => 'profile_name1',
                        'profile_key' => 'profile_key1',
                    ),
                    (object)array(
                        'profile_name' => 'profile_name2',
                        'profile_key' => 'profile_key2',
                    ),
                ]
            ]
        );

        $transcodingFiles = $mediaContent->getTranscodingFiles();

        $this->assertInstanceOf('Kollus\Component\Container\ContainerArray', $transcodingFiles);
        $this->assertNotEmpty($transcodingFiles);

        $firstTranscodingFile = $transcodingFiles[0];

        $this->assertInstanceOf('Kollus\Component\Container\TranscodingFile', $firstTranscodingFile);
        $this->assertEquals('profile_key1', $firstTranscodingFile->getProfileKey());
    }
}
