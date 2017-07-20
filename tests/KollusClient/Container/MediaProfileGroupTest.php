<?php

namespace Kollus\Test\KollusClient\Container;

use Kollus\Component\Client;
use Kollus\Component\Container;

class MediaProfileGroupTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $firstMediaProfileGroup = new Container\MediaProfileGroup();
        $this->assertInstanceOf(Container\MediaProfileGroup::class, $firstMediaProfileGroup);

        $testKey = 'key1';
        $testName = 'name1';
        $secondMediaProfileGroup = new Container\MediaProfileGroup(
            ['key' => $testKey, 'name' => $testName]
        );
        $this->assertEquals($testKey, $secondMediaProfileGroup->getKey());
        $this->assertEquals($testName, $secondMediaProfileGroup->getName());
    }
}
