<?php

namespace Kollus\Test\KollusClient\Container;

use Kollus\Component\Container;

class LoginAccountTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $firstLoginAccount = new Container\LoginAccount();
        $this->assertInstanceOf(Container\LoginAccount::class, $firstLoginAccount);

        $testEmail = 'email';
        $secondLoginAccount = new Container\LoginAccount(['email' => $testEmail]);
        $this->assertEquals($testEmail, $secondLoginAccount->getEmail());
    }
}
