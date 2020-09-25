<?php

namespace App\Billing;

use Stripe\Charge;
use Stripe\Exception\ApiErrorException;

class StripePaymentGateway implements PaymentGateway
{
    public $beforeFirstChargeCallback;

    private $api_key;

    public function __construct($api_key)
    {
        $this->api_key = $api_key;
    }

    public function getValidTestToken()
    {
        return 'valid-token';
    }

    public function charge($amount, $token)
    {
        try {
            Charge::create(
                [
                    'amount' => $amount,
                    'currency' => 'usd',
                    'source' => $token,
                    //'description' => '',
                ],
                ['api_key' => $this->api_key]
            );
        } catch (ApiErrorException $e) {
            throw new PaymentFailedException();
        }
    }

    public function totalCharges()
    {
        return $this->charges->sum();
    }

    public function beforeFirstCharge($callback)
    {
        $this->beforeFirstChargeCallback = $callback;
    }
}
