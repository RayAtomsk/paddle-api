<?php
namespace Paddle;

use Exception;

class API
{
    /** @var string Paddle Production Checkout API base URL */
    const PADDLE_CHECKOUT_API_URL = 'https://checkout.paddle.com/api';
    /** @var string Paddle Sandbox Checkout API base URL */
    const PADDLE_SANDBOX_CHECKOUT_API_URL = 'https://sandbox-vendors.paddle.com/api';
    /** @var string Paddle Production Product API, Subscription API and Alert API base URL */
    const PADDLE_VENDOR_API_URL = 'https://vendors.paddle.com/api';
    /** @var string Paddle Sandbox Product API, Subscription API and Alert API base URL */
    const PADDLE_SANDBOX_VENDOR_API_URL = 'https://sandbox-vendors.paddle.com/api';

    /** @var string $checkoutURL Paddle Checkout API base URL depending on Environment configuration */
    protected $checkoutURL = self::PADDLE_CHECKOUT_API_URL;
    /** @var string $vendorURL Paddle Vendor API base URL depending on Environment configuration */
    protected $vendorURL = self::PADDLE_VENDOR_API_URL;

    /** @var int $vendorID The vendor ID identifies your seller account.
     *      This can be found in Developer Tools > Authentication. */
    protected $vendorID;
    /** @var string $vendorAuthCode The vendor auth code is a private API key for authenticating API requests.
     *      This key should never be used in client side code or shared publicly.
     *      This can be found in Developer Tools > Authentication. */
    protected $vendorAuthCode;
    /** @var int $vendorID The vendor ID identifies your seller account.
     *      This can be found in Developer Tools > Authentication. */
    protected $requestTimeout = 30;

    /**
     * Paddle constructor.
     * Optionally sets vendor ID and/or vendor auth code.
     *
     * @param null|int $vendorID [optional] The vendor ID identifies your seller account.
     *      This can be found in Developer Tools > Authentication.
     * @param null|string $vendorAuthCode [optional]
     *      The vendor auth code is a private API key for authenticating API requests.
     *      This key should never be used in client side code or shared publicly.
     *      This can be found in Developer Tools > Authentication.
     * @param int $requestTimeout Request timeout in seconds. Default is 30.
     * @param bool $sandbox [optional] Configure Client as Production or Sandbox.
     */
    public function __construct($vendorID = null, $vendorAuthCode = null, $requestTimeout = null, $sandbox = false)
    {
        $this->init($vendorID, $vendorAuthCode, $requestTimeout);

        if ($sandbox) {
            $this->setEnvironment($sandbox);
        }
    }

    /**
     * Initialize Paddle API credentials.
     * Optionally sets vendor ID and/or vendor auth code.
     * But use it only to set actual vendor values.
     *
     * @param null|int $vendorID [optional] The vendor ID identifies your seller account.
     *      This can be found in Developer Tools > Authentication.
     * @param null|string $vendorAuthCode [optional]
     *      The vendor auth code is a private API key for authenticating API requests.
     *      This key should never be used in client side code or shared publicly.
     *      This can be found in Developer Tools > Authentication.
     * @param int $requestTimeout Request timeout in seconds. Default is 30.
     */
    public function init($vendorID = null, $vendorAuthCode = null, $requestTimeout = null)
    {
        if (!empty($vendorID)) $this->vendorID = (int) $vendorID;
        if (!empty($vendorAuthCode)) $this->vendorAuthCode = (string) $vendorAuthCode;
        if (!empty($requestTimeout)) $this->requestTimeout = (int) $requestTimeout;
    }

    /**
     * Set Environment for Client.
     *
     * @param bool $sandbox [optional] Sandbox environment flag. Default is false.
     */
    protected function setEnvironment($sandbox = false)
    {
        if ($sandbox) {
            $this->checkoutURL = self::PADDLE_SANDBOX_CHECKOUT_API_URL;
            $this->vendorURL = self::PADDLE_SANDBOX_VENDOR_API_URL;
        } else {
            $this->checkoutURL = self::PADDLE_CHECKOUT_API_URL;
            $this->vendorURL = self::PADDLE_VENDOR_API_URL;
        }
    }

    /**
     * Set Sandbox Environment for Client.
     */
    public function setSandboxEnv()
    {
        $this->setEnvironment(true);
    }

    /**
     * Set Production Environment for Client.
     */
    public function setProductionEnv()
    {
        $this->setEnvironment();
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
     * Check which request functionality is available on server and use it to request Paddle API server.
     *
     * @param string $method HTTP request method (GET|POST).
     * @param string $uri Paddle API endpoint URI.
     * @param array $parameters [optional] Request parameters for GET request or request body for POST request.
     *
     * @return array
     *
     * @throws Exception
     */
    public function request($method, $uri, $parameters = [])
    {
        return $this->curlRequest($method, $uri, $parameters);
    }

    /**
     * Send request to Paddle API using cURL client.
     *
     * @param string $method HTTP request method (GET|POST).
     * @param string $uri Paddle API endpoint URI.
     * @param array $parameters [optional] Request parameters for GET request or request body for POST request.
     *
     * @return array
     *
     * @throws Exception
     */
    public function curlRequest($method, $uri, $parameters = [])
    {
        $method = strtolower($method);

        if ($method === 'post' && !$this->isSetCredentials()) {
            throw new Exception(Errors::ERROR_403, 403);
        }

        if (!empty($this->vendorID) && !isset($parameters['vendor_id'])) {
            $parameters['vendor_id'] = $this->vendorID;
        }

        if (!empty($this->vendorAuthCode) && !isset($parameters['vendor_auth_code'])) {
            $parameters['vendor_auth_code'] = $this->vendorAuthCode;
        }

        $parameters = http_build_query($parameters);

        $curlClient = curl_init();
        curl_setopt($curlClient, CURLOPT_TIMEOUT, $this->requestTimeout);
        curl_setopt($curlClient, CURLOPT_RETURNTRANSFER, true);

        if ($method === 'post') {
            $requestURL = $this->vendorURL . $uri;
            curl_setopt($curlClient, CURLOPT_POST, true);
            curl_setopt($curlClient, CURLOPT_POSTFIELDS, $parameters);
        } else {
            $requestURL = $this->checkoutURL . $uri . '?' . $parameters;
        }

        curl_setopt($curlClient, CURLOPT_URL, $requestURL);
        $responseString = curl_exec($curlClient);
        $httpStatus = curl_getinfo($curlClient, CURLINFO_RESPONSE_CODE);
        $curlError = curl_error($curlClient);
        curl_close($curlClient);

        if (!empty($curlError)) {
            throw new Exception(Errors::ERROR_400, 400);
        }

        if ($httpStatus !== 200) {
            throw new Exception(Errors::ERROR_401 . $httpStatus, 401);
        }

        $response = json_decode($responseString, true);

        if (!is_array($response)) {
            throw new Exception(Errors::ERROR_402 . $responseString, 402);
        }

        if (isset($response['success']) && $response['success'] === false) {
            throw new Exception('API error: ' . $response['error']['message'], $response['error']['code']);
        }

        return (isset($response['response'])) ? $response['response'] : $response;
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