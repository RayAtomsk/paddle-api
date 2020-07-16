<?php
namespace Paddle;

use Exception;

/**
 * Class Checkout
 * @package Paddle
 * The Paddle Checkout API allows you to retrieve order details from completed checkouts,
 * fetch product prices and retrieve historical purchases and license codes.
 */
class Checkout extends Type
{
    /**
     * Get information about an order after a transaction completes.
     * @link https://developer.paddle.com/api-reference/checkout-api/order-details/getorder
     *
     * @param string $checkoutID The identifier of the buyer's checkout
     *
     * @return array
     *
     * @throws Exception
     */
    public function getOrderDetails($checkoutID)
    {
        return $this->api->request('get', '/1.0/order', [
            'checkout_id' => (string) $checkoutID
        ]);
    }

    /**
     * Retrieve customer purchase or license history
     * @link https://developer.paddle.com/api-reference/checkout-api/user-history/getuserhistory
     *
     * @param string $email The email address of the customer.
     * @param int $vendorID Required if $productID is not specified.
     * @param int $productID Required if $vendorID is not specified.
     *
     * @return array
     *
     * @throws Exception
     */
    public function getUserHistory($email, $vendorID = null, $productID = null)
    {
        return $this->api->request('get', '/2.0/user/history', [
            'email'         => (string) $email,
            'vendor_id'     => $vendorID,
            'product_id'    => $productID
        ]);
    }

    /**
     * Retrieve prices for one or multiple products or plans.
     * @link https://developer.paddle.com/api-reference/checkout-api/prices/getprices
     *
     * @param string $productIDs A comma-separated list of product (or subscription plan) IDs to return prices for.
     *      Please note that the product needs to be fully set-up in the dashboard (checkout link is available);
     *      Failing to do so will result in the API not returning the product prices.
     * @param string $customerCountry A two character ISO country code (eg. GB) for localised pricing.
     *      See 'Supported Countries' (https://developer.paddle.com/reference/platform-parameters/supported-countries).
     * @param string $customerIP An IP address of the customer to return pricing for.
     * @param string $coupons A comma-separated list of coupon codes to apply to the product prices
     *      (one-time purchase products only).
     *
     * @return array
     *
     * @throws Exception
     */
    public function getPrices($productIDs, $customerCountry = '', $customerIP = '', $coupons = '')
    {
        return $this->api->request('get', '/2.0/prices', [
            'product_ids'       => (string) $productIDs,
            'customer_country'  => (string) $customerCountry,
            'customer_ip'       => (string) $customerIP,
            'coupons'           => (string) $coupons,
        ]);
    }
}