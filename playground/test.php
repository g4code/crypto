<?php

error_reporting(-1);

require_once realpath(join(DIRECTORY_SEPARATOR, [__DIR__, '..', 'vendor', 'autoload.php']));

$crypto = new \G4\Crypto\Crypt();
$crypto->setEncryptionKey('tHi5Is');

$encryptedMessage = $crypto->encode('new message');

$message = $crypto->decode($encryptedMessage);