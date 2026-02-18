<?php

namespace G4\Crypto\Tests\Unit\Adapter;

use G4\Crypto\Adapter\Mcrypt;
use PHPUnit\Framework\TestCase;

class McryptTest extends TestCase
{
    private $mcrypt;

    protected function setUp(): void
    {
        if (!function_exists('mcrypt_encrypt')) {
            $this->markTestSkipped('Mcrypt extension is not available.');
        }
        
        $this->mcrypt = new Mcrypt();
    }

    protected function tearDown(): void
    {
        $this->mcrypt = null;
    }

    public function testConstructorThrowsExceptionWhenMcryptNotInstalled(): void
    {
        if (function_exists('mcrypt_encrypt')) {
            $this->markTestSkipped('Mcrypt is installed, cannot test exception.');
        }

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Extension mcrypt is not installed.');
        
        new Mcrypt();
    }

    public function testGetIvSizeReturnsInteger(): void
    {
        $ivSize = $this->mcrypt->getIvSize();
        
        $this->assertIsInt($ivSize);
        $this->assertGreaterThan(0, $ivSize);
    }

    public function testGetIvSizeReturnsCorrectSizeForRijndael256CBC(): void
    {
        $ivSize = $this->mcrypt->getIvSize();
        
        $this->assertEquals(32, $ivSize);
    }

    public function testCreateIvReturnsString(): void
    {
        $size = 32;
        $iv = $this->mcrypt->createIv($size);
        
        $this->assertIsString($iv);
    }

    public function testCreateIvReturnsCorrectLength(): void
    {
        $size = 32;
        $iv = $this->mcrypt->createIv($size);
        
        $this->assertEquals($size, strlen($iv));
    }

    public function testCreateIvReturnsDifferentValuesOnMultipleCalls(): void
    {
        $size = 32;
        $iv1 = $this->mcrypt->createIv($size);
        $iv2 = $this->mcrypt->createIv($size);
        
        $this->assertNotEquals($iv1, $iv2);
    }

    public function testCreateIvWithDifferentSizes(): void
    {
        $sizes = [8, 16, 32, 64];
        
        foreach ($sizes as $size) {
            $iv = $this->mcrypt->createIv($size);
            $this->assertEquals($size, strlen($iv));
        }
    }

    public function testEncryptReturnsString(): void
    {
        $key = md5('test-key');
        $data = 'test data';
        $iv = $this->mcrypt->createIv(32);
        
        $encrypted = $this->mcrypt->encrypt($key, $data, $iv);
        
        $this->assertIsString($encrypted);
        $this->assertNotEmpty($encrypted);
    }

    public function testEncryptChangesData(): void
    {
        $key = md5('test-key');
        $data = 'test data';
        $iv = $this->mcrypt->createIv(32);
        
        $encrypted = $this->mcrypt->encrypt($key, $data, $iv);
        
        $this->assertNotEquals($data, $encrypted);
    }

    public function testEncryptWithEmptyData(): void
    {
        $key = md5('test-key');
        $data = '';
        $iv = $this->mcrypt->createIv(32);
        
        $encrypted = $this->mcrypt->encrypt($key, $data, $iv);
        
        $this->assertIsString($encrypted);
    }

    public function testEncryptWithLongData(): void
    {
        $key = md5('test-key');
        $data = str_repeat('Lorem ipsum dolor sit amet. ', 100);
        $iv = $this->mcrypt->createIv(32);
        
        $encrypted = $this->mcrypt->encrypt($key, $data, $iv);
        
        $this->assertIsString($encrypted);
        $this->assertNotEmpty($encrypted);
    }

    public function testDecryptReturnsString(): void
    {
        $key = md5('test-key');
        $data = 'test data';
        $iv = $this->mcrypt->createIv(32);
        
        $encrypted = $this->mcrypt->encrypt($key, $data, $iv);
        $decrypted = $this->mcrypt->decrypt($key, $encrypted, $iv);
        
        $this->assertIsString($decrypted);
    }

    public function testDecryptReturnsOriginalData(): void
    {
        $key = md5('test-key');
        $originalData = 'test data for encryption';
        $iv = $this->mcrypt->createIv(32);
        
        $encrypted = $this->mcrypt->encrypt($key, $originalData, $iv);
        $decrypted = $this->mcrypt->decrypt($key, $encrypted, $iv);
        
        $this->assertEquals(rtrim($originalData, "\0"), rtrim($decrypted, "\0"));
    }

    public function testDecryptWithWrongKey(): void
    {
        $correctKey = md5('correct-key');
        $wrongKey = md5('wrong-key');
        $data = 'test data';
        $iv = $this->mcrypt->createIv(32);
        
        $encrypted = $this->mcrypt->encrypt($correctKey, $data, $iv);
        $decrypted = $this->mcrypt->decrypt($wrongKey, $encrypted, $iv);
        
        $this->assertNotEquals($data, rtrim($decrypted, "\0"));
    }

    public function testDecryptWithWrongIv(): void
    {
        $key = md5('test-key');
        $data = 'test data';
        $iv1 = $this->mcrypt->createIv(32);
        $iv2 = $this->mcrypt->createIv(32);
        
        $encrypted = $this->mcrypt->encrypt($key, $data, $iv1);
        $decrypted = $this->mcrypt->decrypt($key, $encrypted, $iv2);
        
        $this->assertNotEquals($data, rtrim($decrypted, "\0"));
    }

    public function testEncryptDecryptWithEmptyString(): void
    {
        $key = md5('test-key');
        $data = '';
        $iv = $this->mcrypt->createIv(32);
        
        $encrypted = $this->mcrypt->encrypt($key, $data, $iv);
        $decrypted = $this->mcrypt->decrypt($key, $encrypted, $iv);
        
        $this->assertEquals($data, rtrim($decrypted, "\0"));
    }

    public function testEncryptDecryptWithSpecialCharacters(): void
    {
        $key = md5('test-key');
        $data = "Special chars: !@#$%^&*()_+-=[]{}|;':\",./<>?`~\n\t\r";
        $iv = $this->mcrypt->createIv(32);
        
        $encrypted = $this->mcrypt->encrypt($key, $data, $iv);
        $decrypted = $this->mcrypt->decrypt($key, $encrypted, $iv);
        
        $this->assertEquals($data, rtrim($decrypted, "\0"));
    }

    public function testEncryptDecryptWithUnicodeCharacters(): void
    {
        $key = md5('test-key');
        $data = 'Тест са ћирилицом и другим Unicode знаковима: 你好世界 🎉';
        $iv = $this->mcrypt->createIv(32);
        
        $encrypted = $this->mcrypt->encrypt($key, $data, $iv);
        $decrypted = $this->mcrypt->decrypt($key, $encrypted, $iv);
        
        $this->assertEquals($data, rtrim($decrypted, "\0"));
    }

    public function testEncryptDecryptWithBinaryData(): void
    {
        $key = md5('test-key');
        $data = "\x00\x01\x02\x03\x04\x05\xFF\xFE\xFD";
        $iv = $this->mcrypt->createIv(32);
        
        $encrypted = $this->mcrypt->encrypt($key, $data, $iv);
        $decrypted = $this->mcrypt->decrypt($key, $encrypted, $iv);
        
        $this->assertStringStartsWith($data, $decrypted);
    }

    public function testEncryptWithDifferentIvsProducesDifferentResults(): void
    {
        $key = md5('test-key');
        $data = 'test data';
        $iv1 = $this->mcrypt->createIv(32);
        $iv2 = $this->mcrypt->createIv(32);
        
        $encrypted1 = $this->mcrypt->encrypt($key, $data, $iv1);
        $encrypted2 = $this->mcrypt->encrypt($key, $data, $iv2);
        
        $this->assertNotEquals($encrypted1, $encrypted2);
    }

    public function testEncryptDecryptWithVeryLongData(): void
    {
        $key = md5('test-key');
        $data = str_repeat('Lorem ipsum dolor sit amet, consectetur adipiscing elit. ', 1000);
        $iv = $this->mcrypt->createIv(32);
        
        $encrypted = $this->mcrypt->encrypt($key, $data, $iv);
        $decrypted = $this->mcrypt->decrypt($key, $encrypted, $iv);
        
        $this->assertEquals($data, rtrim($decrypted, "\0"));
    }

    public function testEncryptDecryptWithNumericString(): void
    {
        $key = md5('test-key');
        $data = '1234567890';
        $iv = $this->mcrypt->createIv(32);
        
        $encrypted = $this->mcrypt->encrypt($key, $data, $iv);
        $decrypted = $this->mcrypt->decrypt($key, $encrypted, $iv);
        
        $this->assertEquals($data, rtrim($decrypted, "\0"));
    }

    public function testEncryptDecryptMultipleTimes(): void
    {
        $key = md5('test-key');
        $originalData = 'test data';
        $iv = $this->mcrypt->createIv(32);
        
        $encrypted1 = $this->mcrypt->encrypt($key, $originalData, $iv);
        $decrypted1 = rtrim($this->mcrypt->decrypt($key, $encrypted1, $iv), "\0");
        
        $encrypted2 = $this->mcrypt->encrypt($key, $decrypted1, $iv);
        $decrypted2 = rtrim($this->mcrypt->decrypt($key, $encrypted2, $iv), "\0");
        
        $this->assertEquals($originalData, $decrypted1);
        $this->assertEquals($originalData, $decrypted2);
    }

    public function testEncryptDecryptWithDifferentKeyLengths(): void
    {
        $keys = [
            md5('short'),
            md5('medium length key'),
            md5('very long encryption key with many characters'),
        ];
        
        $data = 'test data';
        $iv = $this->mcrypt->createIv(32);
        
        foreach ($keys as $key) {
            $encrypted = $this->mcrypt->encrypt($key, $data, $iv);
            $decrypted = $this->mcrypt->decrypt($key, $encrypted, $iv);
            
            $this->assertEquals($data, rtrim($decrypted, "\0"));
        }
    }
}
