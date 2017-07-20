<?php

namespace KollusClient\Container;

use Kollus\Component\Container;

class UservaluesTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $firstUservalues = new Container\Uservalues();
        $this->assertInstanceOf(Container\Uservalues::class, $firstUservalues);

        $testUservalues0 = 'test';
        $secondUservalues = new Container\Uservalues(['uservalue0' => $testUservalues0]);
        $this->assertEquals(['uservalue0' => $testUservalues0], $secondUservalues->getUservalues());
        $this->assertEquals($testUservalues0, $secondUservalues->getUservalueAt(0));

        $testUservalues1 = 'abcd';
        $secondUservalues->setUservalues(['uservalue1' => $testUservalues1]);
        $this->assertEquals($testUservalues1, $secondUservalues->getUservalueAt(1));
    }
}
