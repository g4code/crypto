<?php

namespace G4\Crypto\Tests\Unit\Adapter;

use G4\Crypto\Adapter\OpenSSL;
use PHPUnit\Framework\TestCase;

class OpenSSLTest extends TestCase
{
    private $openssl;

    protected function setUp(): void
    {
        if (!function_exists('openssl_encrypt')) {
            $this->markTestSkipped('OpenSSL extension is not available.');
        }
        
        $this->openssl = new OpenSSL();
    }

    protected function tearDown(): void
    {
        $this->openssl = null;
    }

    public function testConstructorThrowsExceptionWhenOpenSSLNotInstalled(): void
    {
        if (function_exists('openssl_encrypt')) {
            $this->markTestSkipped('OpenSSL is installed, cannot test exception.');
        }

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Extension openssl is not installed.');
        
        new OpenSSL();
    }

    public function testGetIvSizeReturnsInteger(): void
    {
        $ivSize = $this->openssl->getIvSize();
        
        $this->assertIsInt($ivSize);
        $this->assertGreaterThan(0, $ivSize);
    }

    public function testGetIvSizeReturnsCorrectSizeForAES256CBC(): void
    {
        $ivSize = $this->openssl->getIvSize();
        
        $this->assertEquals(16, $ivSize);
    }

    public function testCreateIvReturnsString(): void
    {
        $size = 16;
        $iv = $this->openssl->createIv($size);
        
        $this->assertIsString($iv);
    }

    public function testCreateIvReturnsCorrectLength(): void
    {
        $size = 16;
        $iv = $this->openssl->createIv($size);
        
        $this->assertEquals($size, strlen($iv));
    }

    public function testCreateIvReturnsDifferentValuesOnMultipleCalls(): void
    {
        $size = 16;
        $iv1 = $this->openssl->createIv($size);
        $iv2 = $this->openssl->createIv($size);
        
        $this->assertNotEquals($iv1, $iv2);
    }

    public function testCreateIvWithDifferentSizes(): void
    {
        $sizes = [8, 16, 32, 64];
        
        foreach ($sizes as $size) {
            $iv = $this->openssl->createIv($size);
            $this->assertEquals($size, strlen($iv));
        }
    }

    public function testEncryptReturnsString(): void
    {
        $key = md5('test-key');
        $data = 'test data';
        $iv = $this->openssl->createIv(16);
        
        $encrypted = $this->openssl->encrypt($key, $data, $iv);
        
        $this->assertIsString($encrypted);
        $this->assertNotEmpty($encrypted);
    }

    public function testEncryptChangesData(): void
    {
        $key = md5('test-key');
        $data = 'test data';
        $iv = $this->openssl->createIv(16);
        
        $encrypted = $this->openssl->encrypt($key, $data, $iv);
        
        $this->assertNotEquals($data, $encrypted);
    }

    public function testEncryptWithEmptyData(): void
    {
        $key = md5('test-key');
        $data = '';
        $iv = $this->openssl->createIv(16);
        
        $encrypted = $this->openssl->encrypt($key, $data, $iv);
        
        $this->assertIsString($encrypted);
    }

    public function testEncryptWithLongData(): void
    {
        $key = md5('test-key');
        $data = str_repeat('Lorem ipsum dolor sit amet. ', 100);
        $iv = $this->openssl->createIv(16);
        
        $encrypted = $this->openssl->encrypt($key, $data, $iv);
        
        $this->assertIsString($encrypted);
        $this->assertNotEmpty($encrypted);
    }

    public function testDecryptReturnsString(): void
    {
        $key = md5('test-key');
        $data = 'test data';
        $iv = $this->openssl->createIv(16);
        
        $encrypted = $this->openssl->encrypt($key, $data, $iv);
        $decrypted = $this->openssl->decrypt($key, $encrypted, $iv);
        
        $this->assertIsString($decrypted);
    }

    public function testDecryptReturnsOriginalData(): void
    {
        $key = md5('test-key');
        $originalData = 'test data for encryption';
        $iv = $this->openssl->createIv(16);
        
        $encrypted = $this->openssl->encrypt($key, $originalData, $iv);
        $decrypted = $this->openssl->decrypt($key, $encrypted, $iv);
        
        $this->assertEquals($originalData, $decrypted);
    }

    public function testDecryptWithWrongKey(): void
    {
        $correctKey = md5('correct-key');
        $wrongKey = md5('wrong-key');
        $data = 'test data';
        $iv = $this->openssl->createIv(16);
        
        $encrypted = $this->openssl->encrypt($correctKey, $data, $iv);
        $decrypted = $this->openssl->decrypt($wrongKey, $encrypted, $iv);
        
        $this->assertNotEquals($data, $decrypted);
    }

    public function testDecryptWithWrongIv(): void
    {
        $key = md5('test-key');
        $data = 'test data';
        $iv1 = $this->openssl->createIv(16);
        $iv2 = $this->openssl->createIv(16);
        
        $encrypted = $this->openssl->encrypt($key, $data, $iv1);
        $decrypted = $this->openssl->decrypt($key, $encrypted, $iv2);
        
        $this->assertNotEquals($data, $decrypted);
    }

    public function testEncryptDecryptWithEmptyString(): void
    {
        $key = md5('test-key');
        $data = '';
        $iv = $this->openssl->createIv(16);
        
        $encrypted = $this->openssl->encrypt($key, $data, $iv);
        $decrypted = $this->openssl->decrypt($key, $encrypted, $iv);
        
        $this->assertEquals($data, $decrypted);
    }

    public function testEncryptDecryptWithSpecialCharacters(): void
    {
        $key = md5('test-key');
        $data = "Special chars: !@#$%^&*()_+-=[]{}|;':\",./<>?`~\n\t\r";
        $iv = $this->openssl->createIv(16);
        
        $encrypted = $this->openssl->encrypt($key, $data, $iv);
        $decrypted = $this->openssl->decrypt($key, $encrypted, $iv);
        
        $this->assertEquals($data, $decrypted);
    }

    public function testEncryptDecryptWithUnicodeCharacters(): void
    {
        $key = md5('test-key');
        $data = 'Тест са ћирилицом и другим Unicode знаковима: 你好世界 🎉';
        $iv = $this->openssl->createIv(16);
        
        $encrypted = $this->openssl->encrypt($key, $data, $iv);
        $decrypted = $this->openssl->decrypt($key, $encrypted, $iv);
        
        $this->assertEquals($data, $decrypted);
    }

    public function testEncryptDecryptWithBinaryData(): void
    {
        $key = md5('test-key');
        $data = "\x00\x01\x02\x03\x04\x05\xFF\xFE\xFD";
        $iv = $this->openssl->createIv(16);
        
        $encrypted = $this->openssl->encrypt($key, $data, $iv);
        $decrypted = $this->openssl->decrypt($key, $encrypted, $iv);
        
        $this->assertEquals($data, $decrypted);
    }

    public function testEncryptWithDifferentIvsProducesDifferentResults(): void
    {
        $key = md5('test-key');
        $data = 'test data';
        $iv1 = $this->openssl->createIv(16);
        $iv2 = $this->openssl->createIv(16);
        
        $encrypted1 = $this->openssl->encrypt($key, $data, $iv1);
        $encrypted2 = $this->openssl->encrypt($key, $data, $iv2);
        
        $this->assertNotEquals($encrypted1, $encrypted2);
    }

    public function testEncryptDecryptWithVeryLongData(): void
    {
        $key = md5('test-key');
        $data = str_repeat('Lorem ipsum dolor sit amet, consectetur adipiscing elit. ', 1000);
        $iv = $this->openssl->createIv(16);
        
        $encrypted = $this->openssl->encrypt($key, $data, $iv);
        $decrypted = $this->openssl->decrypt($key, $encrypted, $iv);
        
        $this->assertEquals($data, $decrypted);
    }

    public function testEncryptDecryptWithNumericString(): void
    {
        $key = md5('test-key');
        $data = '1234567890';
        $iv = $this->openssl->createIv(16);
        
        $encrypted = $this->openssl->encrypt($key, $data, $iv);
        $decrypted = $this->openssl->decrypt($key, $encrypted, $iv);
        
        $this->assertEquals($data, $decrypted);
    }

    public function testEncryptDecryptMultipleTimes(): void
    {
        $key = md5('test-key');
        $originalData = 'test data';
        $iv = $this->openssl->createIv(16);
        
        $encrypted1 = $this->openssl->encrypt($key, $originalData, $iv);
        $decrypted1 = $this->openssl->decrypt($key, $encrypted1, $iv);
        
        $encrypted2 = $this->openssl->encrypt($key, $decrypted1, $iv);
        $decrypted2 = $this->openssl->decrypt($key, $encrypted2, $iv);
        
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
        $iv = $this->openssl->createIv(16);
        
        foreach ($keys as $key) {
            $encrypted = $this->openssl->encrypt($key, $data, $iv);
            $decrypted = $this->openssl->decrypt($key, $encrypted, $iv);
            
            $this->assertEquals($data, $decrypted);
        }
    }
}
