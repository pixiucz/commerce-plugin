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

    public function checkStatusFor($token)
    {
        return "fuck you";
    }
}