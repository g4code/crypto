<?php

use G4\Crypto\Crypt;
use G4\Crypto\Adapter\OpenSSL;
use PHPUnit\Framework\TestCase;

class UsageOpenSSLTest extends TestCase
{

    private $crypto;

    protected function setUp(): void
    {
        $this->crypto = new Crypt(new OpenSSL());
        $this->crypto->setEncryptionKey('tHi5Is');
    }

    protected function tearDown(): void
    {
        $this->crypto = null;
    }

    public function testUsage(): void
    {
        $encryptedMessage = $this->crypto->encode('new message');

        $message = $this->crypto->decode($encryptedMessage);

        $this->assertEquals('new message', $message);
    }

}