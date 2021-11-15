<?php
namespace Paddle;

use Exception;
use InvalidArgumentException;

/**
 * Class Subscription
 * @package Paddle
 * The Paddle Subscription API allows you to get information about your dashboard plans,
 * update and cancel user subscriptions, add and remove subscription modifiers, and reschedule subscription payments.
 */
class Subscription extends Type
{
    /**
     * List all of the available subscription plans on the Paddle account.
     * @link https://developer.paddle.com/api-reference/subscription-api/plans/listplans
     *
     * @param null|int $planID Filter: The product/plan ID
     *
     * @return array
     *
     * @throws Exception
     */
    public function listPlans($planID = null)
    {
        return $this->api->request('POST', '/2.0/subscription/plans', [
            'plan' => (is_null($planID)) ? $planID : (int) $planID
        ]);
    }

    /**
     * Create a new subscription billing plan with the supplied parameters.
     * @link https://developer.paddle.com/api-reference/subscription-api/plans/createplan
     *
     * @return array
     *
     * @throws Exception
     */
    public function createPlan()
    {
        return $this->api->request('POST', '/2.0/subscription/plans_create', [

        ]);
    }

    /**
     * List all users subscribed to any of your subscription plans.
     * @link https://developer.paddle.com/api-reference/subscription-api/users/listusers
     *
     * @param null|int $subscriptionID Filter: A specific user subscription ID
     * @param null|int $planID Filter: The subscription plan ID
     * @param null|string $state Filter: The user subscription status.
     *      Returns all active, past_due, trialing and paused subscription plans if not specified.
     *      Allowed Values: active, past_due, trialing, paused, deleted.
     * @param int $page Paginate return results.
     * @param int $resultsPerPage Number of subscription records to return per page.
     *
     * @return array
     *
     * @throws Exception
     */
    public function listUsers($subscriptionID = null, $planID = null, $state = null, $page = 1, $resultsPerPage = 1)
    {
        return $this->api->request('POST', '/2.0/subscription/users', [
            'subscription_id'   => (is_null($subscriptionID)) ? $subscriptionID : (int) $subscriptionID,
            'plan_id'           => (is_null($planID)) ? $planID : (int) $planID,
            'state'             => (is_null($state)) ? $state : (string) $state,
            'page'              => ((int) $page > 0) ? (int) $page : 1,
            'results_per_page'  =>
                ((int) $resultsPerPage >= 1 && (int) $resultsPerPage <= 200) ? (int) $resultsPerPage : 1
        ]);
    }

    /**
     * Update the quantity, price, and/or plan of a user's subscription.
     * https://developer.paddle.com/api-reference/subscription-api/users/updateuser
     *
     * @param int $subscriptionID The specific user subscription ID.
     * @param array<string,mixed> $changes
     *         Available options:  
     *              `quantity`          => (int) The new quantity to be applied to the subscription.  
     *              `currency`          => (string) The currency that the recurring price should be charged in. E.g. USD, GBP, EUR, etc. This must be the same as the currency of the existing subscription. Optional, but required if setting `recurring_price`.   
     *              `recurring_price`   => (int|float) The new recurring price per quantity unit to apply to the subscription. Please note this is a singular price, i.e 11.00.  
     *              `plan_id`           => (int) The new plan ID to move the subscription to.  
     *              `prorate`           => (boolean) Whether the change in subscription should be prorated.  
     *              `bill_immediately`  => (boolean) If the subscription should bill for the next interval at the revised figures immediately.  
     *              `keep_modifiers`    => (boolean) Retain the existing modifiers on the user subscription.  
     *              `passthrough`       => (string) Update the additional data associated with this subscription, like additional features, add-ons and seats. This will be included in all subsequent webhooks, and is often a JSON string of relevant data.  
     *              `pause`             => (boolean) Pause or restart a subscription. If the subscription is not paused and this is set to true, the  will be changed to “paused” when the subscription’s next scheduled payment date is reached. If this is specified, the following modifications cannot be included as well: `bill_immediately`, `keep_modifiers`, `prorate`, `quantity`, `recurring_price`, `passthrough`, `plan_id`.  
     *
     * @return array
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function updateUser($subscriptionID, $changes)
    {
        if (!is_array($changes)) {
            throw new InvalidArgumentException(
                sprintf(
                    "The updateUser method expects \$changes to be an array, but a received %s.",
                    gettype($changes)
                )
            );
        }

        $changes["subscription_id"] = (int) $subscriptionID;

        return $this->api->request('POST', '/2.0/subscription/users/update', $changes);
    }

    /**
     * Cancel the specified subscription.
     * @link https://developer.paddle.com/api-reference/subscription-api/users/canceluser
     *
     * @param int $subscriptionID The specific user subscription ID.
     *
     * @return array
     *
     * @throws Exception
     */
    public function cancelUser($subscriptionID)
    {
        return $this->api->request('POST', '/2.0/subscription/users_cancel', [
            'subscription_id' => (int) $subscriptionID
        ]);
    }

    /**
     * List all the modifiers.
     * @link https://developer.paddle.com/api-reference/subscription-api/modifiers/listmodifiers
     *
     * @param null|int $subscriptionID Filter: Modifiers for a specific subscription.
     * @param null|int $planID Filter: The product/plan ID
     *
     * @return array
     *
     * @throws Exception
     */
    public function listModifiers($subscriptionID = null, $planID = null)
    {
        return $this->api->request('POST', '/2.0/subscription/modifiers', [
            'subscription_id'   => (is_null($subscriptionID)) ? $subscriptionID : (int) $subscriptionID,
            'plan_id'           => (is_null($planID)) ? $planID : (int) $planID
        ]);
    }

    /**
     * Dynamically change the subscription payment amount.
     * @link https://developer.paddle.com/api-reference/subscription-api/modifiers/createmodifier
     *
     * @return array
     *
     * @throws Exception
     */
    public function createModifier()
    {
        return $this->api->request('POST', '/2.0/subscription/modifiers/create', [

        ]);
    }

    /**
     * Delete an existing subscription price modifier.
     * @link https://developer.paddle.com/api-reference/subscription-api/modifiers/deletemodifier
     *
     * @param int $modifierID A specific modifier ID.
     *
     * @return array
     *
     * @throws Exception
     */
    public function deleteModifier($modifierID)
    {
        return $this->api->request('POST', '/2.0/subscription/modifiers/delete', [
            'modifier_id' => (int) $modifierID
        ]);
    }

    /**
     * List all paid and upcoming (unpaid) payments.
     * @link https://developer.paddle.com/api-reference/subscription-api/payments/listpayments
     *
     * @param null|int $subscriptionID Filter: Payments for a specific subscription.
     * @param null|int $planID Filter: The product/plan ID (single or comma-separated values).
     * @param null|int $isPaid Filter: Payment is paid (0 = No, 1 = Yes).
     * @param null|string $startDate Filter: Payments starting from (date in format YYYY-MM-DD).
     * @param null|string $endDate Filter: Payments up to (date in format YYYY-MM-DD).
     * @param null|bool $isOneOffCharge Filter: Non-recurring payments created from the Charges API.
     *
     * @return array
     *
     * @throws Exception
     */
    public function listPayments(
        $subscriptionID = null,
        $planID = null,
        $isPaid = null,
        $startDate = null,
        $endDate = null,
        $isOneOffCharge = null
    ) {
        return $this->api->request('POST', '/2.0/subscription/payments', [
            'subscription_id'   => (is_null($subscriptionID)) ? $subscriptionID : (int) $subscriptionID,
            'plan'              => (is_null($planID)) ? $planID : (int) $planID,
            'is_paid'           => (is_null($isPaid)) ? $isPaid : (int) $isPaid,
            'from'              => (is_null($startDate)) ? $startDate : (string) $startDate,
            'to'                => (is_null($endDate)) ? $endDate : (string) $endDate,
            'is_one_off_charge' => (is_null($isOneOffCharge)) ? $isOneOffCharge : (bool) $isOneOffCharge
        ]);
    }

    /**
     * Change the due date on an upcoming subscription payment.
     * @link https://developer.paddle.com/api-reference/subscription-api/payments/updatepayment
     *
     * @param int $paymentID The upcoming subscription payment ID.
     *      This can be obtained by calling the List Payments API.
     * @param string $date The date (in format YYYY-MM-DD) you want to move the payment to.
     *
     * @return array
     *
     * @throws Exception
     */
    public function reschedulePayment($paymentID, $date)
    {
        return $this->api->request('POST', '/2.0/subscription/payments_reschedule', [
            'payment_id'    => (int) $paymentID,
            'date'          => (string) $date
        ]);
    }

    /**
     * Make immediate one-time charges on top of an existing subscription.
     * @link https://developer.paddle.com/api-reference/subscription-api/one-off-charges/createcharge
     *
     * @param int $subscriptionID Subscription ID.
     * @param float $amount The amount for the one-time charge.
     *      This amount will be charged in the currency of the subscription.
     * @param string $chargeName The name of the one-time charge - this will be visible to the buyers
     *      and will show up in the invoice as a line item, so that a buyer can always refer back to the invoice
     *      to know how much they were charged and what for.
     *
     * @return array
     *
     * @throws Exception
     */
    public function createOneOffCharge($subscriptionID, $amount, $chargeName)
    {
        return $this->api->request('POST', '/2.0/subscription/' . $subscriptionID . '/charge', [
            'amount'        => (float) $amount,
            'charge_name'   => (string) $chargeName
        ]);
    }
}