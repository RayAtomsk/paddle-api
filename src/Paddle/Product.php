<?php
namespace Paddle;

use Exception;

/**
 * Class Product
 * @package Paddle
 * The Paddle Product API allows you to get information about your dashboard products and coupons,
 * generate licenses and coupons, and create custom payment links.
 */
class Product extends Type
{
    /**
     * Return any available coupons valid for a specified one-time product or subscription plan.
     * @link https://developer.paddle.com/api-reference/product-api/coupons/listcoupons
     *
     * @param int $productID The specific product/subscription ID.
     *
     * @return array
     *
     * @throws Exception
     */
    public function listCoupons($productID)
    {
        return $this->api->request('POST', '/2.0/product/list_coupons', [
            'product_id' => (int) $productID
        ]);
    }

    /**
     * Create a new coupon for the given product or a checkout.
     * @link https://developer.paddle.com/api-reference/product-api/coupons/createcoupon
     *
     * @param string $couponType Either 'product' (valid for specified products or subscription plans)
     *      or 'checkout' (valid for any checkout).
     * @param string $discountType Either 'flat' or 'percentage'.
     * @param int|float $discountAmount A currency amount (eg. 10.00) if $discountType is 'flat',
     *      or a percentage amount (eg. 10 for 10%) if $discountType is 'percentage'.
     * @param null|string $couponCode Will be randomly generated if not specified.
     * @param null|string $couponPrefix Prefix for generated codes. Not valid if $couponCode is specified.
     * @param null|int $numberOfCoupons Number of coupons to generate. Not valid if $couponCode is specified.
     * @param string $description Description of the coupon. This will be displayed in the Seller Dashboard.
     * @param null|string $productIDs Comma-separated list of product IDs. Required if $couponType is 'product'.
     * @param null|string $currency The currency must match the balance currency specified in your account.
     *      Required if $discountType is 'flat'. Allowed Values: USD, GBP, EUR
     * @param int $allowedUses Number of times a coupon can be used in a checkout.
     *      This will be set to 999,999 by default, if not specified.
     * @param null|string $expires The date (in format YYYY-MM-DD) the coupon is valid until.
     *      The coupon will expire on the date at 00:00:00 UTC.
     * @param null|int $recurring If the coupon is used on subscription products,
     *      this indicates whether the discount should apply to recurring payments after the initial purchase.
     *      Allowed Values: 0, 1.
     * @param null|string $groupName The name of the coupon group this coupon should be assigned to.
     *
     * @return array
     *
     * @throws Exception
     */
    public function createCoupon(
        $couponType,
        $discountType,
        $discountAmount,
        $couponCode = null,
        $couponPrefix = null,
        $numberOfCoupons = null,
        $description = '',
        $productIDs = null,
        $currency = null,
        $allowedUses = 999999,
        $expires = null,
        $recurring = null,
        $groupName = null
    ) {
        return $this->api->request('POST', '/2.1/product/create_coupon', [
            'coupon_code'       => (is_null($couponCode)) ? $couponCode : (string) $couponCode,
            'coupon_prefix'     => (is_null($couponPrefix)) ? $couponPrefix : (string) $couponPrefix,
            'num_coupons'       => (is_null($numberOfCoupons)) ? $numberOfCoupons : (int) $numberOfCoupons,
            'description'       => (string) $description,
            'coupon_type'       => (string) $couponType,
            'product_ids'       => (is_null($productIDs)) ? $productIDs : (string) $productIDs,
            'discount_type'     => (string) $discountType,
            'discount_amount'   => (float) $discountAmount,
            'currency'          => (is_null($currency)) ? $currency : (string) $currency,
            'allowed_uses'      => (int) $allowedUses,
            'expires'           => (is_null($expires)) ? $expires : (string) $expires,
            'recurring'         => (is_null($recurring)) ? $recurring : (int) $recurring,
            'group'             => (is_null($groupName)) ? $groupName : (string) $groupName,
        ]);
    }

    /**
     * Delete a given coupon and prevent it from being further used.
     * @link https://developer.paddle.com/api-reference/product-api/coupons/deletecoupon
     *
     * @param string $couponCode Identify the coupon to delete.
     * @param null|int $productID The specific product/subscription ID.
     *
     * @return array
     *
     * @throws Exception
     */
    public function deleteCoupon($couponCode, $productID = null)
    {
        return $this->api->request('POST', '/2.0/product/delete_coupon', [
            'coupon_code'   => (string) $couponCode,
            'product_id'    => (is_null($productID)) ? $productID : (int) $productID
        ]);
    }

    /**
     * Update one coupon or entire group of coupons.
     * @link https://developer.paddle.com/api-reference/product-api/coupons/updatecoupon
     *
     * @param null|string $couponCode Identify the coupon to update
     *      (You must specify either $couponCode or $groupName, but not both).
     * @param null|string $groupName The name of the group of coupons you want to update.
     * @param null|string $newCouponCode New code to rename the coupon to.
     * @param null|string $newGroupName New group name to move coupon to.
     * @param null|string $productIDs Comma-separated list of products e.g. 499531,1234,123546.
     *      If blank then remove associated products.
     * @param null|string $expires The date (in format YYYY-MM-DD) the coupon is valid until.
     *      The coupon will expire on the date at 00:00:00 UTC.
     * @param null|int $allowedUses Number of times each coupon can be used.
     * @param null|string $currency Currency of the $discountAmount (required if the coupon's discount_type is 'flat').
     *      The currency must match the balance currency specified in your account.
     * @param null|int|float $discountAmount A currency amount (eg. 10.00) if discount_type is 'flat',
     *      or a percentage amount (eg. 10 for 10%) if discount_type is 'percentage'.
     * @param null|int $recurring If the coupon is used on subscription products,
     *      this indicates whether the discount should apply to recurring payments after the initial purchase.
     *      Allowed Values: 0, 1.
     *
     * @return array
     *
     * @throws Exception
     */
    public function updateCoupon(
        $couponCode = null,
        $groupName = null,
        $newCouponCode = null,
        $newGroupName = null,
        $productIDs = null,
        $expires = null,
        $allowedUses = null,
        $currency = null,
        $discountAmount = null,
        $recurring = null
    ) {
        return $this->api->request('POST', '/2.1/product/update_coupon', [
            'coupon_code'       => (is_null($couponCode)) ? $couponCode : (string) $couponCode,
            'group'             => (is_null($groupName)) ? $groupName : (string) $groupName,
            'new_coupon_code'   => (is_null($newCouponCode)) ? $newCouponCode : (string) $newCouponCode,
            'new_group'         => (is_null($newGroupName)) ? $newGroupName : (string) $newGroupName,
            'product_ids'       => (is_null($productIDs)) ? $productIDs : (string) $productIDs,
            'expires'           => (is_null($expires)) ? $expires : (string) $expires,
            'allowed_uses'      => (is_null($allowedUses)) ? $allowedUses : (int) $allowedUses,
            'currency'          => (is_null($currency)) ? $currency : (string) $currency,
            'discount_amount'   => (is_null($discountAmount)) ? $discountAmount : (float) $discountAmount,
            'recurring'         => (is_null($recurring)) ? $recurring : (int) $recurring
        ]);
    }

    /**
     * List all published one-time products associated with your account.
     * @link https://developer.paddle.com/api-reference/product-api/products/getproducts
     *
     * @return array
     *
     * @throws Exception
     */
    public function listProducts()
    {
        return $this->api->request('POST', '/2.0/product/get_products');
    }

    /**
     * Generate a Paddle-framework license.
     * @link https://developer.paddle.com/api-reference/product-api/licenses/createlicense
     *
     * @param int $productID Product ID the license is to be associated to.
     * @param int $allowedUses Number of activations allowed for the license.
     * @param null|string $expiresAt Specifies which date (in format YYYY-MM-DD) the license should expire on.
     *      Leave empty for license to never expire.
     *
     * @return array
     *
     * @throws Exception
     */
    public function generateLicense($productID, $allowedUses, $expiresAt = null)
    {
        return $this->api->request('POST', '/2.0/product/generate_license', [
            'product_id'    => (int) $productID,
            'allowed_uses'  => (int) $allowedUses,
            'expires_at'    => (is_null($expiresAt)) ? $expiresAt : (string) $expiresAt
        ]);
    }

    /**
     * Generate a link with custom attributes set for a one-time or subscription checkout.
     * @link https://developer.paddle.com/api-reference/product-api/pay-links/createpaylink
     *
     * @param null|int $productID The Paddle Product ID/Plan ID that you want to base this custom checkout on.
     *      Required if not using custom products. If no $productID is set, custom non-subscription product checkouts
     *      can be generated instead by specifying the required fields: $title, $webhookURL and $prices.
     * @param string $title The name of the product/title of the checkout. Required if $productID is not set.
     * @param null|string $webhookURL Endpoint that will be called with transaction information on successful checkout,
     *      to allow you to fulfill the purchase. Only valid (and required) if $productID is not set.
     * @param array $prices Price(s) of the checkout for a one-time purchase or initial payment of a subscription.
     *      If $productID is set, you must also provide the price for the product's default currency.
     *      If a given currency is enabled in the dashboard, it will default to a conversion
     *      of the product's default currency price set in this field unless specified here as well.
     * @param array $recurringPrices Recurring price(s) of the checkout (excluding the initial payment) only
     *      if the $productID specified is a subscription. To override the initial payment and all recurring
     *      payment amounts, both $prices and $recurringPrices must be set. You must also provide the price
     *      for the subscription's default currency. If a given currency is enabled in the dashboard,
     *      it will default to a conversion of the subscription's default currency price set in this field
     *      unless specified here as well.
     * @param null|int $trialDays For subscription plans only. The number of days before Paddle starts charging
     *      the customer the recurring price. If you leave this field empty, the trial days of the plan will be used.
     * @param string $customMessage A short message displayed below the product name on the checkout.
     * @param null|string $couponCode A coupon to be applied to the checkout.
     * @param int $discountable Specifies if a coupon can be applied to the checkout.
     *      'Add Coupon' button on the checkout will be hidden as well if set to '0'.
     * @param null|string $imageURL A URL for the product image/icon displayed on the checkout.
     * @param null|string $returnURL A URL to redirect to once the checkout is completed.
     *      If the variable {checkout_hash} is included within the UR
     *      (e.g. https://mysite.com/thanks?checkout={checkout_hash}), the API will automatically populate
     *      the Paddle checkout ID in the redirected URL.
     * @param int $quantityVariable Specifies if the user is allowed to alter the quantity of the checkout.
     * @param int $quantity Pre-fills the quantity selector on the checkout.
     *      Please note that free products/subscription plans are fixed to a $quantity of '1'.
     * @param null|string $expires Specifies if the checkout link should expire.
     *      The generated checkout URL will be accessible until 23:59:59 (UTC)
     *      on the date specified (date in format YYYY-MM-DD).
     * @param array $affiliates Other Paddle vendor IDs whom you would like to split the funds from this checkout with.
     * @param null|int $recurringAffiliateLimit Limit the number of times other Paddle vendors will receive funds from
     *      the recurring payments (for subscription products). The initial checkout payment is included in the limit.
     *      If you leave this field empty, the limit will not be applied.
     * @param int $marketingConsent Whether you have gathered consent to market to the customer.
     *      $customerEmail is required if this property is set and you want to opt the customer into marketing.
     * @param null|string $customerEmail Pre-fills the customer email field on the checkout.
     * @param null|string $customerCountry Pre-fills the customer country field on the checkout.
     * @param null|string $customerPostcode Pre-fills the customer postcode field on the checkout.
     *      This field is required if the $customerCountry requires postcode.
     * @param string $passthrough A string of metadata you wish to store with the checkout.
     *      Will be sent alongside all webhooks associated with the order.
     * @param null|string $vatNumber Pre-fills the sales tax identifier (VAT number) field on the checkout.
     * @param null|string $vatCompanyName Pre-fills the Company Name field on the checkout.
     *      Required if $vatNumber is set.
     * @param null|string $vatStreet Pre-fills the Street field on the checkout. Required if $vatNumber is set.
     * @param null|string $vatCity Pre-fills the Town/City field on the checkout. Required if $vatNumber is set.
     * @param null|string $vatState Pre-fills the State field on the checkout.
     * @param null|string $vatCountry Pre-fills the Country field on the checkout. Required if $vatNumber is set.
     * @param null|string $vatPostcode Pre-fills the Postcode field on the checkout.
     *      This field is required if $vatNumber is set and the $vatCountry requires postcode.
     *
     * @return array
     *
     * @throws Exception
     */
    public function generatePayLink(
        $productID = null,
        $title = '',
        $webhookURL = null,
        $prices = [],
        $recurringPrices = [],
        $trialDays = null,
        $customMessage = '',
        $couponCode = null,
        $discountable = 1,
        $imageURL = null,
        $returnURL = null,
        $quantityVariable = 1,
        $quantity = 1,
        $expires = null,
        $affiliates = [],
        $recurringAffiliateLimit = null,
        $marketingConsent = 0,
        $customerEmail = null,
        $customerCountry = null,
        $customerPostcode = null,
        $passthrough = '',
        $vatNumber = null,
        $vatCompanyName = null,
        $vatStreet = null,
        $vatCity = null,
        $vatState = null,
        $vatCountry = null,
        $vatPostcode = null
    ) {
        return $this->api->request('POST', '/2.0/product/generate_pay_link', [
            'product_id'                => (is_null($productID)) ? $productID : (int) $productID,
            'title'                     => (string) $title,
            'webhook_url'               => (is_null($webhookURL)) ? $webhookURL : (string) $webhookURL,
            'prices'                    => (is_array($prices)) ? $prices : [],
            'recurring_prices'          => (is_array($recurringPrices)) ? $recurringPrices : [],
            'trial_days'                => (is_null($trialDays)) ? $trialDays : (int) $trialDays,
            'custom_message'            => (string) $customMessage,
            'coupon_code'               => (is_null($couponCode)) ? $couponCode : (string) $couponCode,
            'discountable'              => ((int) $discountable === 1) ? 1 : 0,
            'image_url'                 => (is_null($imageURL)) ? $imageURL : (string) $imageURL,
            'return_url'                => (is_null($returnURL)) ? $returnURL : (string) $returnURL,
            'quantity_variable'         => ((int) $quantityVariable === 1) ? 1 : 0,
            'quantity'                  => ((int) $quantity >= 1 && (int) $quantity <= 100) ? (int) $quantity : 1,
            'expires'                   => (is_null($expires)) ? $expires : (string) $expires,
            'affiliates'                => (is_array($affiliates)) ? $affiliates : [],
            'recurring_affiliate_limit' =>
                (is_null($recurringAffiliateLimit)) ? $recurringAffiliateLimit : (int) $recurringAffiliateLimit,
            'marketing_consent'         => ((int) $marketingConsent === 1) ? 1 : 0,
            'customer_email'            => (is_null($customerEmail)) ? $customerEmail : (string) $customerEmail,
            'customer_country'          => (is_null($customerCountry)) ? $customerCountry : (string) $customerCountry,
            'customer_postcode'         => (is_null($customerPostcode)) ? $customerPostcode : (string) $customerPostcode,
            'passthrough'               => (string) $passthrough,
            'vat_number'                => (is_null($vatNumber)) ? $vatNumber : (string) $vatNumber,
            'vat_company_name'          => (is_null($vatCompanyName)) ? $vatCompanyName : (string) $vatCompanyName,
            'vat_street'                => (is_null($vatStreet)) ? $vatStreet : (string) $vatStreet,
            'vat_city'                  => (is_null($vatCity)) ? $vatCity : (string) $vatCity,
            'vat_state'                 => (is_null($vatState)) ? $vatState : (string) $vatState,
            'vat_country'               => (is_null($vatCountry)) ? $vatCountry : (string) $vatCountry,
            'vat_postcode'              => (is_null($vatPostcode)) ? $vatPostcode : (string) $vatPostcode
        ]);
    }

    /**
     * Retrieve transactions for related entities within Paddle.
     * @link https://developer.paddle.com/api-reference/product-api/transactions/listtransactions
     *
     * @param string $entity Filter: Entity type of the $id.
     *      Allowed Values: user, subscription, order, checkout, product
     * @param string $id Filter: ID number for the specified $entity
     * @param int $page The page of results to return. Each response page return 15 results each.
     *
     * @return array
     *
     * @throws Exception
     */
    public function listTransactions($entity, $id, $page = 1)
    {
        return $this->api->request(
            'POST',
            '/2.0/' . (string) $entity . '/' . (string) $id . '/transactions',
            [ 'page' => (int) $page ]
        );
    }

    /**
     * Request a refund for a one-time or subscription payment, either in full or partial.
     * Note that refunds are not immediate and will be reviewed and approved by our buyer support team.
     * @link https://developer.paddle.com/api-reference/product-api/payments/refundpayment
     *
     * @param string $orderID The order ID of the payment you wish to refund.
     *      NB. Subscription orders are hyphenated and one-time orders are an integer.
     * @param null|int|float $amount Partial amount to refund in the currency of the order.
     *      The full payment is refunded if this parameter is not provided.
     * @param string $reason Reason for providing the refund. This will be displayed in the Seller Dashboard.
     *
     * @return array
     *
     * @throws Exception
     */
    public function refundPayment($orderID, $amount = null, $reason = '')
    {
        return $this->api->request('POST', '/2.0/payment/refund', [
            'order_id'  => (string) $orderID,
            'amount'    => (is_null($amount)) ? $amount : (float) $amount,
            'reason'    => (string) $reason
        ]);
    }
}