<?php

namespace G4\Crypto\Adapter;

interface AdapterInterface
{

    public function createIv($size);

    public function decrypt($key, $data, $iv);

    public function encrypt($key, $data, $iv);

    public function getIvSize();
}