<?php

namespace G4\Crypto\Adapter;

class OpenSSL implements AdapterInterface
{

    private $chiper = 'aes-256-cbc';

    private $option = OPENSSL_RAW_DATA;


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

    public function encrypt($key, $data, $iv)
    {
        return openssl_encrypt($data, $this->chiper, $key, $this->option, $iv);
    }

    public function decrypt($key, $data, $iv)
    {
        return openssl_decrypt($data, $this->chiper, $key, $this->option, $iv);
    }

    public function getIvSize()
    {
        return openssl_cipher_iv_length($this->chiper);
    }
}