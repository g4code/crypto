<?php

namespace G4\Crypto\Adapter;

class OpenSSL implements AdapterInterface
{

    /**
     * OpenSSL cipher method constant
     * @var string
     */
    private $cipher = 'aes-256-cbc';

    /**
     * OpenSSL input/output option constant
     * @var int
     */
    private $option = OPENSSL_RAW_DATA;


    /**
     * OpenSSL constructor.
     * Determine if openssl extension is installed
     */
    public function __construct()
    {
        if (!function_exists('openssl_encrypt')) {
            throw new \Exception('Extension openssl is not installed.');
        }
    }

    public function createIv($size)
    {
        return openssl_random_pseudo_bytes($size);
    }

    public function decrypt($key, $data, $iv)
    {
        return openssl_decrypt($data, $this->cipher, $key, $this->option, $iv);
    }

    public function encrypt($key, $data, $iv)
    {
        return openssl_encrypt($data, $this->cipher, $key, $this->option, $iv);
    }

    public function getIvSize()
    {
        return openssl_cipher_iv_length($this->cipher);
    }
}