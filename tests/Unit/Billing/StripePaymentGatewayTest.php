<?php

namespace Billing;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentFailedException;
use Tests\TestCase;

/**
 * Class StripePaymentGatewayTest
 * @package Billing
 */
class StripePaymentGatewayTest extends TestCase
{
    /**
     * @test
     */
    function charges_with_a_valid_payment_token_are_successful()
    {
        $paymentGateway = new StripePaymentGateway();

        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());

        $this->assertEquals(2500, $paymentGateway->totalCharges());
    }


}
