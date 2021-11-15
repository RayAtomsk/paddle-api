<?php

use PHPUnit\Framework\TestCase;
use Paddle\API;
use Paddle\Subscription;
use InvalidArgumentException;

class SubscriptionTest extends TestCase
{
    public function testUpdateUserInvalidArguments()
    {
        $api = new API();
        $subscription = new Subscription($api);

        $this->expectException(InvalidArgumentException::class);
        
        $subscription->updateUser(123, "not an array");
    }
}