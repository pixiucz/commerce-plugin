<?php namespace Pixiu\Commerce\api\Classes;

use GuzzleHttp\Client;


class CheckoutApi
{
    const URL = "http://checkout.dev/api/";

    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    // Returns token
    public function createCheckout($checkoutData)
    {
        $response = $this->client->post(self::URL . __FUNCTION__, ['json' => $checkoutData]);
        return json_decode($response->getBody()->getContents(), true);
    }

    public function getStatusesFor($tokens)
    {
        $response = $this->client->post(self::URL . 'statuses', ['json' => ['tokens' => json_encode($tokens)]]);
        return json_decode($response->getBody()->getContents(), true);
    }
}