<?php
namespace Paddle;

use Exception;

/**
 * Class Alert
 * @package Paddle
 * The Paddle Alert API allows you to get the history of all your subscribed webhook alerts.
 */
class Alert extends Type
{
    /**
     * Retrieve past events and alerts that Paddle has sent to webhooks on your account.
     * @link https://developer.paddle.com/api-reference/alert-api/webhooks/webhooks
     *
     * @param int $page Paginate returned results.
     * @param int $alertsPerPage Number of webhook alerts to return per page. Returns 10 alerts by default.
     * @param null|string $queryHead The date and time at which the webhook occurred before (end date).
     * @param null|string $queryTail The date and time at which the webhook occurred after (start date).
     *
     * @return array
     *
     * @throws Exception
     */
    public function getWebhookHistory($page, $alertsPerPage = 10, $queryHead = null, $queryTail = null)
    {
        return $this->api->request('POST', '/2.0/alert/webhooks', [
            'page'              => (int) $page,
            'alerts_per_page'   => (int) $alertsPerPage,
            'query_head'        => (is_null($queryHead)) ? $queryHead : (string) $queryHead,
            'query_tail'        => (is_null($queryTail)) ? $queryTail : (string) $queryTail
        ]);
    }
}