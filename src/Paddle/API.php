<?php
namespace Paddle;

class API
{
    /**
     * @var string Paddle Checkout API base URL
     */
    const PADDLE_CHECKOUT_API_URL = 'https://checkout.paddle.com/api';

    /**
     * @var string Paddle Product API, Subscription API and Alert API base URL
     */
    const PADDLE_VENDOR_API_URL = 'https://vendors.paddle.com/api';

    /**
     * @var int $vendorID The vendor ID identifies your seller account.
     *      This can be found in Developer Tools > Authentication.
     * @var string $vendorAuthCode The vendor auth code is a private API key for authenticating API requests.
     *      This key should never be used in client side code or shared publicly.
     *      This can be found in Developer Tools > Authentication.
     */
    protected $vendorID;
    protected $vendorAuthCode;

    /**
     * Paddle constructor.
     * Optionally sets vendor ID and/or vendor auth code.
     *
     * @param null|int $vendorID [optional]
     * @param null|string $vendorAuthCode [optional]
     */
    public function __construct($vendorID = null, $vendorAuthCode = null)
    {
        $this->init($vendorID, $vendorAuthCode);
    }

    /**
     * Initialize Paddle API credentials.
     * Optionally sets vendor ID and/or vendor auth code.
     * But use it only to set actual vendor values.
     *
     * @param null|int $vendorID [optional]
     * @param null|string $vendorAuthCode [optional]
     */
    public function init($vendorID = null, $vendorAuthCode = null)
    {
        if (!empty($vendorID)) $this->vendorID = (int) $vendorID;
        if (!empty($vendorAuthCode)) $this->vendorAuthCode = (string) $vendorAuthCode;
    }

    /**
     * Check if credentials is set.
     *
     * @return bool
     */
    public function isSetCredentials()
    {
       return (!empty($this->vendorID) && !empty($this->vendorAuthCode));
    }

    /**
     * Get Checkout API object.
     *
     * @return Checkout
     */
    public function checkout()
    {
        return new Checkout($this);
    }

    /**
     * Get Product API object.
     *
     * @return Product
     */
    public function product()
    {
        return new Product($this);
    }

    /**
     * Get Subscription API object.
     *
     * @return Subscription
     */
    public function subscription()
    {
        return new Subscription($this);
    }

    /**
     * Get Alert API object.
     *
     * @return Alert
     */
    public function alert()
    {
        return new Alert($this);
    }
}