<?php

use G4\Crypto\Crypt;
use G4\Crypto\Adapter;

class UsageTest extends \PHPUnit_Framework_TestCase
{

    private $crypto;

    protected function setUp()
    {
        $this->crypto = new Crypt(new Adapter());
        $this->crypto->setEncryptionKey('tHi5Is');
    }

    protected function tearDown()
    {
        $this->crypto = null;
    }

    public function testUsage()
    {
        $encryptedMessage = $this->crypto->encode('new message');

        $message = $this->crypto->decode($encryptedMessage);

        $this->assertEquals('new message', $message);
    }

}