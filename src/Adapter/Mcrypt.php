<?php

namespace G4\Crypto\Adapter;

class Mcrypt implements AdapterInterface
{

    /**
     * Mcrypt cipher constant
     *
     * @var string
     */
    private $cipher = MCRYPT_RIJNDAEL_256;

    /**
     * Mcrypt mode constant
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


    /**
     * Mcrypt constructor.
     * Determine if mcrypt extension is installed
     */
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

    public function decrypt($key, $data, $iv)
    {
        return mcrypt_decrypt($this->cipher, $key, $data, $this->mode, $iv);
    }

    public function encrypt($key, $data, $iv)
    {
        return mcrypt_encrypt($this->cipher, $key, $data, $this->mode, $iv);
    }

    public function getIvSize()
    {
        return mcrypt_get_iv_size($this->cipher, $this->mode);
    }
}