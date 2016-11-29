<?php

namespace G4\Crypto;

class Adapter
{

    /**
     * Mcrypt cipher consts
     *
     * @var string
     */
    private $cipher = MCRYPT_RIJNDAEL_256;

    /**
     * Mcrypt mode consts
     *
     * @var string
     */
    private $mode = MCRYPT_MODE_CBC;

    /**
     * Mcrypt source consts
     *
     * @var string
     */
    private $source = MCRYPT_RAND;


    public function __construct()
    {
        if (!function_exists('mcrypt_encrypt')) {
            throw new \Exception('Extension mcrypt is not installed.');
        }
    }

    public function createIv($size)
    {
        return mcrypt_create_iv($size, $this->source);
    }

    public function encrypt($key, $data, $iv = null)
    {
        return mcrypt_encrypt($this->cipher, $key, $data, $this->mode, $iv);
    }

    public function decrypt($key, $data, $iv = null)
    {
        return mcrypt_decrypt($this->cipher, $key, $data, $this->mode, $iv);
    }

    public function getIvSize()
    {
        return mcrypt_get_iv_size($this->cipher, $this->mode);
    }
}