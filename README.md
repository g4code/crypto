crypto
======

> crypto - [php](http://php.net) library

## Install
Via Composer

```sh
composer require g4/crypto
```

## Usage

OpenSSL extension

``` php
<?php

use G4\Crypto\Crypt;
use G4\Crypto\Adapter\OpenSSL;

$crypto = new Crypt(new OpenSSL());
$crypto->setEncryptionKey('tHi5Is');

$encryptedMessage = $crypto->encode('new message');

$message = $crypto->decode($encryptedMessage);

```

Mcrypt extension - obsolete

[mcrypt-viking-funeral](https://wiki.php.net/rfc/mcrypt-viking-funeral)

``` php
<?php

use G4\Crypto\Crypt;
use G4\Crypto\Adapter\Mcrypt;

$crypto = new Crypt(new Mcrypt());
$crypto->setEncryptionKey('tHi5Is');

$encryptedMessage = $crypto->encode('new message');

$message = $crypto->decode($encryptedMessage);

```

## Development

### Install dependencies

    $ make install

### Run tests

    $ make test

## License

(The MIT License)
see LICENSE file for details...