<?php

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

date_default_timezone_set('Europe/Belgrade');

require __DIR__.'/../../vendor/autoload.php';

//TODO: remove after all work with openssl is done;
PHPUnit_Framework_Error_Deprecated::$enabled = false;