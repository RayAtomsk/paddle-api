<?php
namespace Paddle;

/**
 * Class Type
 * @package Paddle
 */
abstract class Type
{
    /**
     * @var API $api
     */
    protected $api;

    /**
     * Checkout constructor.
     *
     * @param API $api
     */
    function __construct($api)
    {
        $this->api = $api;
    }
}