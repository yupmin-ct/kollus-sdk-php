# Kollus SDK for PHP

[![Build Status](https://travis-ci.org/yupmin-ct/kollus-sdk-php.svg?branch=master)](https://travis-ci.org/yupmin-ct/kollus-sdk-php) [![Coverage Status](https://coveralls.io/repos/github/yupmin-ct/kollus-sdk-php/badge.svg?branch=master)](https://coveralls.io/github/yupmin-ct/kollus-sdk-php?branch=master)

## Introduction

**Unofficial** Kollus SDK Library for PHP

## Table of contents:
- [Requirement](#requirement)
- [Installation](#installation)
- [How to use](#how-to-use)
- [More information](#more-information)
- [License](#license)

## Requirement
- PHP 5.5 above
- PHP Module : curl
- Composer

## Installation
To use this library install it through [Composer](https://getcomposer.org/), run
```bash
php composer.phar require kollus/kollus-sdk
```

## How to use
```php
<?php

require('./vendor/autoload.php');

use Kollus\Component\KollusClient;

// Init library
$apiClient = KollusClient::getApiClientBy('kr.kollus.com', 0, 'korean', 'service_account_key', 'api_access_token');

// Get library media content list
$mediaContents = $apiClient->getLibraryMediaContents();
foreach ($mediaContents as $mediaContent) {
    echo $mediaContent->getUploadFileKey() . PHP_EOL;
}
...
```

## For laravel

Add the service provider to config/app.php 

```php
    'providers' => [

        /*
         * Package Service Providers...
         */
        Kollus\Component\KollusServiceProvider::class,
    ]

```

publish the config:

```bash
php artisan vendor:publish
```

fix config's values in the config `config/kollus.php`:

```php
return [
    'service_account' => [
        'key' => '[fill it]',
        'id' =>'[fill it]',
        'api_access_token' =>'[fill it]',
        'custom_key' => '[fill it]',
    ],
    'domain' => 'kr.kollus.com',
    'api_version' => '0',
    'language' => 'korean',
    'use_https' => 0,
];
```

## More information

Please see [Wiki](https://github.com/yupmin-ct/kollus-sdk-php/wiki)

## License
See `LICENSE` for more information
