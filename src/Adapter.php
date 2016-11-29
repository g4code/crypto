<?php

namespace G4\Crypto;

class Adapter
{

    public static function createIv($size, $source = MCRYPT_DEV_URANDOM)
    {
        return mcrypt_create_iv($size, $source);
    }

    public static function encrypt($cipher, $key, $data, $mode, $iv = null)
    {
        return mcrypt_encrypt($cipher, $key, $data, $mode, $iv);
    }

    public static function decrypt ($cipher, $key, $data, $mode, $iv = null)
    {
        return mcrypt_decrypt($cipher, $key, $data, $mode, $iv);
    }

    public static function getIvSize($cipher, $mode)
    {
        return mcrypt_get_iv_size($cipher, $mode);
    }
}