<?php
use PHPUnit\Framework\TestCase;
use Paddle\API;

class APITest extends TestCase
{
    public function testAPIInstance()
    {
        $api = new API();
        $this->assertInstanceOf('Paddle\API', $api);
    }

    public function testEmptyAPICredentials()
    {
        $api = new API();
        $this->assertFalse($api->isSetCredentials());
    }

    public function testNotEmptyAPICredentials()
    {
        $api = new API(100000, 'vendor_auth_code');
        $this->assertNotFalse($api->isSetCredentials());
    }

    public function testAPICheckoutInstance()
    {
        $api = new API();
        $this->assertInstanceOf('Paddle\Checkout', $api->checkout());
    }

    public function testAPIProductInstance()
    {
        $api = new API();
        $this->assertInstanceOf('Paddle\Product', $api->product());
    }

    public function testAPISubscriptionInstance()
    {
        $api = new API();
        $this->assertInstanceOf('Paddle\Subscription', $api->subscription());
    }

    public function testAPIAlertInstance()
    {
        $api = new API();
        $this->assertInstanceOf('Paddle\Alert', $api->alert());
    }
}