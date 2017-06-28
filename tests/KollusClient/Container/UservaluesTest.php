<?php

namespace KollusClient\Container;

use Kollus\Component\Container;

class UservaluesTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $firstUservalues = new Container\Uservalues();
        $this->assertInstanceOf('Kollus\Component\Container\Uservalues', $firstUservalues);

        $testUservalues0 = 'test';
        $secondUservalues = new Container\Uservalues(['uservalue0' => $testUservalues0]);
        $this->assertEquals(['uservalue0' => $testUservalues0], $secondUservalues->getUservalues());
        $this->assertEquals($testUservalues0, $secondUservalues->getUservalueAt(0));
    }
}
