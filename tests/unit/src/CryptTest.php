<?php

namespace G4\Crypto\Tests\Unit;

use G4\Crypto\Crypt;
use G4\Crypto\Adapter\AdapterInterface;
use PHPUnit\Framework\TestCase;

class CryptTest extends TestCase
{
    private $adapterMock;
    private $crypt;

    protected function setUp(): void
    {
        $this->adapterMock = $this->createMock(AdapterInterface::class);
        $this->adapterMock
            ->method('getIvSize')
            ->willReturn(16);
        
        $this->crypt = new Crypt($this->adapterMock);
    }

    protected function tearDown(): void
    {
        $this->crypt = null;
        $this->adapterMock = null;
    }

    public function testConstructorSetsInitVectorSize(): void
    {
        $this->adapterMock
            ->expects($this->once())
            ->method('getIvSize')
            ->willReturn(16);

        $crypt = new Crypt($this->adapterMock);
        
        $this->assertInstanceOf(Crypt::class, $crypt);
    }

    public function testSetEncryptionKeyReturnsInstance(): void
    {
        $result = $this->crypt->setEncryptionKey('test-key');
        
        $this->assertSame($this->crypt, $result);
    }

    public function testEncodeReturnsString(): void
    {
        $this->adapterMock
            ->method('createIv')
            ->willReturn(str_repeat('a', 16));
        
        $this->adapterMock
            ->method('encrypt')
            ->willReturn('encrypted-data');

        $this->crypt->setEncryptionKey('test-key');
        $result = $this->crypt->encode('test message');
        
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testEncodeWithEmptyMessage(): void
    {
        $this->adapterMock
            ->method('createIv')
            ->willReturn(str_repeat('a', 16));
        
        $this->adapterMock
            ->method('encrypt')
            ->willReturn('');

        $this->crypt->setEncryptionKey('test-key');
        $result = $this->crypt->encode('');
        
        $this->assertIsString($result);
    }

    public function testDecodeReturnsOriginalMessage(): void
    {
        $originalMessage = 'test message';
        $iv = str_repeat('a', 16);
        
        $this->adapterMock
            ->method('createIv')
            ->willReturn($iv);
        
        $this->adapterMock
            ->method('encrypt')
            ->willReturn('encrypted-data');
        
        $this->adapterMock
            ->method('decrypt')
            ->willReturn($originalMessage);

        $this->crypt->setEncryptionKey('test-key');
        
        $encrypted = $this->crypt->encode($originalMessage);
        $decrypted = $this->crypt->decode($encrypted);
        
        $this->assertEquals($originalMessage, $decrypted);
    }

    public function testDecodeWithInvalidDataReturnsFalse(): void
    {
        $this->adapterMock
            ->method('decrypt')
            ->willReturn('');

        $this->crypt->setEncryptionKey('test-key');
        $result = $this->crypt->decode('invalid-short');
        
        $this->assertFalse($result);
    }

    public function testBase64UrlEncodeDecode(): void
    {
        $data = 'test data with special chars +/=';
        
        $this->crypt->setEncryptionKey('test-key');
        
        $encoded = $this->invokeMethod($this->crypt, 'base64urlEncode', [$data]);
        
        $this->assertStringNotContainsString('+', $encoded);
        $this->assertStringNotContainsString('/', $encoded);
        $this->assertStringNotContainsString('=', $encoded);
        
        $decoded = $this->invokeMethod($this->crypt, 'base64urlDecode', [$encoded]);
        
        $this->assertEquals($data, $decoded);
    }

    public function testEncodeDecodeWithDifferentKeys(): void
    {
        $message = 'secret message';
        $iv = str_repeat('a', 16);
        
        $this->adapterMock
            ->method('createIv')
            ->willReturn($iv);
        
        $this->adapterMock
            ->method('encrypt')
            ->willReturn('encrypted-with-key1');
        
        $this->adapterMock
            ->method('decrypt')
            ->willReturn('');

        $this->crypt->setEncryptionKey('key1');
        $encrypted = $this->crypt->encode($message);
        
        $this->crypt->setEncryptionKey('key2');
        $decrypted = $this->crypt->decode($encrypted);
        
        $this->assertNotEquals($message, $decrypted);
    }

    public function testEncodeWithLongMessage(): void
    {
        $longMessage = str_repeat('Lorem ipsum dolor sit amet. ', 1000);
        $iv = str_repeat('a', 16);
        
        $this->adapterMock
            ->method('createIv')
            ->willReturn($iv);
        
        $this->adapterMock
            ->method('encrypt')
            ->willReturn('encrypted-long-data');

        $this->crypt->setEncryptionKey('test-key');
        $result = $this->crypt->encode($longMessage);
        
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testEncodeWithSpecialCharacters(): void
    {
        $specialMessage = "Test with special chars: !@#$%^&*()_+-=[]{}|;':\",./<>?`~\n\t\r";
        $iv = str_repeat('a', 16);
        
        $this->adapterMock
            ->method('createIv')
            ->willReturn($iv);
        
        $this->adapterMock
            ->method('encrypt')
            ->willReturn('encrypted-special-data');

        $this->crypt->setEncryptionKey('test-key');
        $result = $this->crypt->encode($specialMessage);
        
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testEncodeWithUnicodeCharacters(): void
    {
        $unicodeMessage = 'Тест са ћирилицом и другим Unicode знаковима: 你好世界 🎉';
        $iv = str_repeat('a', 16);
        
        $this->adapterMock
            ->method('createIv')
            ->willReturn($iv);
        
        $this->adapterMock
            ->method('encrypt')
            ->willReturn('encrypted-unicode-data');

        $this->crypt->setEncryptionKey('test-key');
        $result = $this->crypt->encode($unicodeMessage);
        
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    private function invokeMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
