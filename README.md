crypto
======

> crypto - [php](http://php.net) library

## Install
Via Composer

```sh
composer require g4/crypto
```

## Usage

``` php
<?php
    
$crypto = new \G4\Crypto\Crypt();
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