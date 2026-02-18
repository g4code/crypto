<?php

namespace G4\Crypto\Tests\Unit;

use G4\Crypto\Cipher;
use PHPUnit\Framework\TestCase;

class CipherTest extends TestCase
{
    public function testConstructorSetsDataAndKey(): void
    {
        $data = 'test data';
        $key = 'test key';
        
        $cipher = new Cipher($data, $key);
        
        $this->assertInstanceOf(Cipher::class, $cipher);
    }

    public function testAddCipherNoiseReturnsString(): void
    {
        $data = 'test data';
        $key = 'test key';
        
        $cipher = new Cipher($data, $key);
        $result = $cipher->addCipherNoise();
        
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testAddCipherNoiseChangesData(): void
    {
        $data = 'test data';
        $key = 'test key';
        
        $cipher = new Cipher($data, $key);
        $noisedData = $cipher->addCipherNoise();
        
        $this->assertNotEquals($data, $noisedData);
    }

    public function testAddCipherNoisePreservesLength(): void
    {
        $data = 'test data';
        $key = 'test key';
        
        $cipher = new Cipher($data, $key);
        $noisedData = $cipher->addCipherNoise();
        
        $this->assertEquals(strlen($data), strlen($noisedData));
    }

    public function testRemoveCipherNoiseReturnsString(): void
    {
        $data = 'test data';
        $key = 'test key';
        
        $cipher = new Cipher($data, $key);
        $result = $cipher->removeCipherNoise();
        
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testRemoveCipherNoisePreservesLength(): void
    {
        $data = 'test data';
        $key = 'test key';
        
        $cipher = new Cipher($data, $key);
        $denoisedData = $cipher->removeCipherNoise();
        
        $this->assertEquals(strlen($data), strlen($denoisedData));
    }

    public function testAddAndRemoveCipherNoiseAreInverse(): void
    {
        $originalData = 'test data for encryption';
        $key = 'encryption key';
        
        $cipherAdd = new Cipher($originalData, $key);
        $noisedData = $cipherAdd->addCipherNoise();
        
        $cipherRemove = new Cipher($noisedData, $key);
        $denoisedData = $cipherRemove->removeCipherNoise();
        
        $this->assertEquals($originalData, $denoisedData);
    }

    public function testAddCipherNoiseWithEmptyData(): void
    {
        $data = '';
        $key = 'test key';
        
        $cipher = new Cipher($data, $key);
        $result = $cipher->addCipherNoise();
        
        $this->assertIsString($result);
        $this->assertEmpty($result);
    }

    public function testRemoveCipherNoiseWithEmptyData(): void
    {
        $data = '';
        $key = 'test key';
        
        $cipher = new Cipher($data, $key);
        $result = $cipher->removeCipherNoise();
        
        $this->assertIsString($result);
        $this->assertEmpty($result);
    }

    public function testAddCipherNoiseWithLongData(): void
    {
        $data = str_repeat('Lorem ipsum dolor sit amet. ', 100);
        $key = 'test key';
        
        $cipher = new Cipher($data, $key);
        $noisedData = $cipher->addCipherNoise();
        
        $this->assertEquals(strlen($data), strlen($noisedData));
        $this->assertNotEquals($data, $noisedData);
    }

    public function testRemoveCipherNoiseWithLongData(): void
    {
        $data = str_repeat('Lorem ipsum dolor sit amet. ', 100);
        $key = 'test key';
        
        $cipherAdd = new Cipher($data, $key);
        $noisedData = $cipherAdd->addCipherNoise();
        
        $cipherRemove = new Cipher($noisedData, $key);
        $denoisedData = $cipherRemove->removeCipherNoise();
        
        $this->assertEquals($data, $denoisedData);
    }

    public function testAddCipherNoiseWithShortKey(): void
    {
        $data = 'test data for encryption with short key';
        $key = 'ab';
        
        $cipher = new Cipher($data, $key);
        $noisedData = $cipher->addCipherNoise();
        
        $this->assertEquals(strlen($data), strlen($noisedData));
        $this->assertNotEquals($data, $noisedData);
    }

    public function testAddCipherNoiseWithLongKey(): void
    {
        $data = 'test data';
        $key = str_repeat('very long encryption key ', 10);
        
        $cipher = new Cipher($data, $key);
        $noisedData = $cipher->addCipherNoise();
        
        $this->assertEquals(strlen($data), strlen($noisedData));
        $this->assertNotEquals($data, $noisedData);
    }

    public function testDifferentKeysProduceDifferentNoise(): void
    {
        $data = 'test data';
        $key1 = 'key1';
        $key2 = 'key2';
        
        $cipher1 = new Cipher($data, $key1);
        $noisedData1 = $cipher1->addCipherNoise();
        
        $cipher2 = new Cipher($data, $key2);
        $noisedData2 = $cipher2->addCipherNoise();
        
        $this->assertNotEquals($noisedData1, $noisedData2);
    }

    public function testRemoveCipherNoiseWithWrongKey(): void
    {
        $originalData = 'test data';
        $correctKey = 'correct key';
        $wrongKey = 'wrong key';
        
        $cipherAdd = new Cipher($originalData, $correctKey);
        $noisedData = $cipherAdd->addCipherNoise();
        
        $cipherRemove = new Cipher($noisedData, $wrongKey);
        $denoisedData = $cipherRemove->removeCipherNoise();
        
        $this->assertNotEquals($originalData, $denoisedData);
    }

    public function testAddCipherNoiseWithBinaryData(): void
    {
        $data = "\x00\x01\x02\x03\x04\x05\xFF\xFE\xFD";
        $key = 'test key';
        
        $cipher = new Cipher($data, $key);
        $noisedData = $cipher->addCipherNoise();
        
        $this->assertEquals(strlen($data), strlen($noisedData));
    }

    public function testRemoveCipherNoiseWithBinaryData(): void
    {
        $originalData = "\x00\x01\x02\x03\x04\x05\xFF\xFE\xFD";
        $key = 'test key';
        
        $cipherAdd = new Cipher($originalData, $key);
        $noisedData = $cipherAdd->addCipherNoise();
        
        $cipherRemove = new Cipher($noisedData, $key);
        $denoisedData = $cipherRemove->removeCipherNoise();
        
        $this->assertEquals($originalData, $denoisedData);
    }

    public function testAddCipherNoiseWithUnicodeData(): void
    {
        $data = 'Тест са ћирилицом 你好世界 🎉';
        $key = 'test key';
        
        $cipher = new Cipher($data, $key);
        $noisedData = $cipher->addCipherNoise();
        
        $this->assertIsString($noisedData);
        $this->assertNotEmpty($noisedData);
    }

    public function testRemoveCipherNoiseWithUnicodeData(): void
    {
        $originalData = 'Тест са ћирилицом 你好世界 🎉';
        $key = 'test key';
        
        $cipherAdd = new Cipher($originalData, $key);
        $noisedData = $cipherAdd->addCipherNoise();
        
        $cipherRemove = new Cipher($noisedData, $key);
        $denoisedData = $cipherRemove->removeCipherNoise();
        
        $this->assertEquals($originalData, $denoisedData);
    }

    public function testKeyIsHashedWithSha1(): void
    {
        $data = 'test data';
        $key = 'test key';
        $expectedHashedKey = sha1($key);
        
        $cipher = new Cipher($data, $key);
        
        $reflection = new \ReflectionClass($cipher);
        $keyProperty = $reflection->getProperty('key');
        $keyProperty->setAccessible(true);
        $actualKey = $keyProperty->getValue($cipher);
        
        $this->assertEquals($expectedHashedKey, $actualKey);
    }

    public function testMultipleCallsToAddCipherNoiseAccumulate(): void
    {
        $data = 'test data';
        $key = 'test key';
        
        $cipher = new Cipher($data, $key);
        $noisedData1 = $cipher->addCipherNoise();
        $noisedData2 = $cipher->addCipherNoise();
        
        $this->assertStringStartsWith($noisedData1, $noisedData2);
        $this->assertGreaterThan(strlen($noisedData1), strlen($noisedData2));
    }
}
