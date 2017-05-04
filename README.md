# Kollus SDK for PHP

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

## More information

Please see [Wiki](https://github.com/yupmin-ct/kollus-sdk-php/wiki)

## License
See `LICENSE` for more information
