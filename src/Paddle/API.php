<?php
namespace Paddle;

use Exception;

class API
{
    /**
     * @var string Paddle Checkout API base URL.
     */
    const PADDLE_CHECKOUT_API_URL = 'https://checkout.paddle.com/api';

    /**
     * @var string Paddle Product API, Subscription API and Alert API base URL.
     */
    const PADDLE_VENDOR_API_URL = 'https://vendors.paddle.com/api';

    /**
     * @var int $vendorID The vendor ID identifies your seller account.
     *      This can be found in Developer Tools > Authentication.
     * @var string $vendorAuthCode The vendor auth code is a private API key for authenticating API requests.
     *      This key should never be used in client side code or shared publicly.
     *      This can be found in Developer Tools > Authentication.
     * @var int $requestTimeout Request timeout in seconds. Default is 30.
     */
    protected $vendorID;
    protected $vendorAuthCode;
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
     */
    public function __construct($vendorID = null, $vendorAuthCode = null, $requestTimeout = null)
    {
        $this->init($vendorID, $vendorAuthCode, $requestTimeout);
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
            $requestURL = self::PADDLE_VENDOR_API_URL . $uri;
            curl_setopt($curlClient, CURLOPT_POST, true);
            curl_setopt($curlClient, CURLOPT_POSTFIELDS, $parameters);
        } else {
            $requestURL = self::PADDLE_CHECKOUT_API_URL . $uri . '?' . $parameters;
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