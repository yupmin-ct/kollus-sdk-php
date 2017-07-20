<?php

namespace Kollus\Test\KollusClient\Container;

use Kollus\Component\Client;
use Kollus\Component\Container;

class MediaProfileTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $firstMediaProfile = new Container\MediaProfile();
        $this->assertInstanceOf(Container\MediaProfile::class, $firstMediaProfile);

        $testKey = 'key1';
        $testName = 'name1';
        $secondMediaProfile = new Container\MediaProfile(
            [
                'key' => $testKey,
                'name' => $testName,
                'media_profile_group_name' => 'test1',
                'media_profile_group_key' => 'test1',
            ]
        );
        $this->assertEquals($testKey, $secondMediaProfile->getKey());
        $this->assertEquals($testName, $secondMediaProfile->getName());

        $mediaProfileGroup = $secondMediaProfile->getMediaProfileGroup();
        $this->assertInstanceOf(Container\MediaProfileGroup::class, $mediaProfileGroup);
    }
}
