<?php
namespace Paddle;

/**
 * Class Errors
 * @package Paddle
 */
class Errors
{
    const ERROR_400 = 'cURL produced error: ';
    const ERROR_401 = 'HTTP response code: ';
    const ERROR_402 = 'API response is in wrong format: ';
    const ERROR_403 = 'A vendor_id and vendor_auth_code pair is required';
}