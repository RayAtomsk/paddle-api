# Paddle.com API PHP library

This package created and maintained by [Andrii Holubenko](https://github.com/rayatomsk), and provides a [Paddle API](https://developer.paddle.com/api-reference/intro) integration.

## Requirements

PHP 5.5 or later.

## Installation via Composer

```bash
composer require rayatomsk/paddle-api
```

## Getting Started

``` php
$api = new \Paddle\API();
$api->init($vendorID, $vendorAuthCode);
```
Authorization can be set while creating new API object:
``` php
$api = new \Paddle\API($vendorID, $vendorAuthCode);
```


## Security

If you discover a security vulnerability in this library package, please send an e-mail to rayatomsk@gmail.com.