<?php

namespace G4\Crypto;

class Adapter
{

    public function __construct()
    {
        if (!function_exists('mcrypt_encrypt')) {
            throw new \Exception('Extension mcrypt is not installed.');
        }
    }

    public function createIv($size, $source = MCRYPT_DEV_URANDOM)
    {
        return mcrypt_create_iv($size, $source);
    }

    public function encrypt($cipher, $key, $data, $mode, $iv = null)
    {
        return mcrypt_encrypt($cipher, $key, $data, $mode, $iv);
    }

    public function decrypt($cipher, $key, $data, $mode, $iv = null)
    {
        return mcrypt_decrypt($cipher, $key, $data, $mode, $iv);
    }

    public function getIvSize($cipher, $mode)
    {
        return mcrypt_get_iv_size($cipher, $mode);
    }
}